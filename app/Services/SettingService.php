<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Central service for reading and writing system settings.
 *
 * Settings with is_env=true are read-only — values come from config()/env,
 * and cannot be written via this service (they must be set in .env).
 */
final class SettingService
{
    private const CACHE_TTL = 3600; // 1 hour

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * Get a single setting value by dotted key (e.g. 'general.site_name').
     */
    public static function get(string $key, mixed $default = null, string $context = 'global'): mixed
    {
        return Cache::remember("setting:{$context}:{$key}", self::CACHE_TTL, function () use ($key, $default, $context) {
            $setting = Setting::where('key', $key)
                ->whereIn('context', [$context, 'global'])
                ->orderByRaw("context = ? DESC", [$context])
                ->first();

            if (! $setting) {
                return $default;
            }

            // is_env=true: read from Laravel config/env, not from DB
            if ($setting->is_env) {
                return static::fromEnv($key) ?? $default;
            }

            return $setting->value ?? $default;
        });
    }

    /**
     * Get all settings within a group as a key→value array.
     * Keys are returned without the group prefix (e.g. 'site_name' not 'general.site_name').
     */
    public static function group(string $group, string $context = 'global'): array
    {
        return Cache::remember("setting_group:{$context}:{$group}", self::CACHE_TTL, function () use ($group, $context) {
            // Fetch global and context specific, then merge
            $globals = Setting::where('group', $group)->where('context', 'global')->get()->keyBy('key');
            $contexts = Setting::where('group', $group)->where('context', $context)->get()->keyBy('key');

            return $globals->map(function ($globalSetting) use ($contexts) {
                $actualSetting = $contexts->get($globalSetting->key) ?? $globalSetting;
                $shortKey = ltrim(str_replace($actualSetting->group . '.', '', $actualSetting->key), '.');
                $value = $actualSetting->is_env
                    ? static::fromEnv($actualSetting->key)
                    : $actualSetting->value;
                return [$shortKey => $value];
            })->collapse()->toArray();
        });
    }

    /**
     * Get all settings as a nested array grouped by group name.
     */
    public static function all(string $context = 'global'): array
    {
        return Cache::remember("settings_all:{$context}", self::CACHE_TTL, function () use ($context) {
            $globals = Setting::where('context', 'global')->get()->keyBy('key');
            $contexts = Setting::where('context', $context)->get()->keyBy('key');

            $merged = $globals->map(function ($globalSetting) use ($contexts) {
                return $contexts->get($globalSetting->key) ?? $globalSetting;
            });

            return $merged
                ->groupBy('group')
                ->map(function ($groupItems) {
                    return $groupItems->mapWithKeys(function (Setting $setting) {
                        $value = $setting->is_env
                            ? static::fromEnv($setting->key)
                            : $setting->value;
                        return [$setting->key => $value];
                    });
                })
                ->toArray();
        });
    }

    /**
     * Get raw setting models for a group (includes metadata like is_env, is_locked, type).
     * Used by the dashboard UI to render fields correctly.
     */
    public static function groupWithMeta(string $group, string $context = 'global'): array
    {
        $globals = Setting::where('group', $group)->where('context', 'global')->orderBy('key')->get()->keyBy('key');
        $contexts = Setting::where('group', $group)->where('context', $context)->get()->keyBy('key');

        return $globals->map(function (Setting $globalSetting) use ($contexts, $context) {
            $actualSetting = $contexts->get($globalSetting->key) ?? $globalSetting;
            return [
                'key'         => $globalSetting->key,
                'value'       => $actualSetting->is_env ? static::fromEnv($actualSetting->key) : $actualSetting->value,
                'type'        => $globalSetting->type,
                'is_env'      => $globalSetting->is_env,
                'is_locked'   => $globalSetting->is_locked,
                'description' => $globalSetting->description,
                'is_fallback' => !$contexts->has($globalSetting->key) && $context !== 'global',
                'context'     => $actualSetting->context,
            ];
        })->values()->toArray();
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * Set a single setting value. Throws if the setting is env-managed.
     */
    public static function set(string $key, mixed $value, string $context = 'global'): void
    {
        $globalSetting = Setting::where('key', $key)->where('context', 'global')->first();
        $actualSetting = Setting::where('key', $key)->where('context', $context)->first();

        // Use global setting for meta checks if actual doesn't exist yet
        $metaSource = $actualSetting ?? $globalSetting;

        if ($metaSource?->is_env) {
            throw new \RuntimeException(
                "Setting [{$key}] is environment-managed and cannot be written from the UI. " .
                'Please update your .env file instead.'
            );
        }

        if ($metaSource?->is_locked) {
            throw new \RuntimeException("Setting [{$key}] is locked and cannot be modified.");
        }

        Setting::updateOrCreate(
            ['key' => $key, 'context' => $context],
            [
                'value' => $value,
                'group' => $globalSetting?->group ?? 'general',
                'type'  => $globalSetting?->type ?? 'string',
                'description' => $globalSetting?->description,
            ]
        );

        self::forgetCache($key, $context);
    }

    /**
     * Bulk-set an array of key→value pairs.
     * Silently skips is_env and is_locked fields.
     */
    public static function setMany(array $values, string $context = 'global'): void
    {
        foreach ($values as $key => $value) {
            $globalSetting = Setting::where('key', $key)->where('context', 'global')->first();
            $actualSetting = Setting::where('key', $key)->where('context', $context)->first();
            
            $metaSource = $actualSetting ?? $globalSetting;

            if ($metaSource?->is_env || $metaSource?->is_locked) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key, 'context' => $context],
                [
                    'value' => $value,
                    'group' => $globalSetting?->group ?? 'general',
                    'type'  => $globalSetting?->type ?? 'string',
                    'description' => $globalSetting?->description,
                ]
            );

            self::forgetCache($key, $context);
        }
    }

    private static function forgetCache(string $key, string $context): void
    {
        // Since dashboard and blog contexts inherit from global, we must clear them all
        // to avoid stale inherited values when a global value is updated.
        $contextsToClear = array_unique(['global', 'dashboard', 'blog', $context]);

        $setting = Setting::where('key', $key)->where('context', $context)->first();

        foreach ($contextsToClear as $ctx) {
            Cache::forget("setting:{$ctx}:{$key}");
            if ($setting) {
                Cache::forget("setting_group:{$ctx}:{$setting->group}");
            }
            Cache::forget("settings_all:{$ctx}");
        }
    }

    // -------------------------------------------------------------------------
    // Cache Invalidation
    // -------------------------------------------------------------------------

    public static function forget(string $key, string $context = 'global'): void
    {
        self::forgetCache($key, $context);
    }

    public static function forgetGroup(string $group, string $context = 'global'): void
    {
        $contextsToClear = array_unique(['global', 'dashboard', 'blog', $context]);

        foreach ($contextsToClear as $ctx) {
            Cache::forget("setting_group:{$ctx}:{$group}");
            Cache::forget("settings_all:{$ctx}");

            Setting::where('group', $group)->where('context', $context)
                ->pluck('key')
                ->each(fn (string $key) => Cache::forget("setting:{$ctx}:{$key}"));
        }
    }

    public static function forgetAll(): void
    {
        // This is a bit heavy, might want to flush by tag if supported, but for now clear known patterns
        $contexts = Setting::select('context')->distinct()->pluck('context');
        
        foreach ($contexts as $context) {
            Cache::forget("settings_all:{$context}");
            Setting::where('context', $context)->pluck('key', 'group')
                ->each(function (string $key, string $group) use ($context) {
                    Cache::forget("setting:{$context}:{$key}");
                    Cache::forget("setting_group:{$context}:{$group}");
                });
        }
    }

    // -------------------------------------------------------------------------
    // Env Mapping
    // -------------------------------------------------------------------------

    /**
     * Map a setting key to its corresponding Laravel config() path.
     * Only keys with is_env=true should end up here.
     */
    private static function fromEnv(string $key): mixed
    {
        return match ($key) {
            'mail.host'       => config('mail.mailers.smtp.host'),
            'mail.port'       => config('mail.mailers.smtp.port'),
            'mail.username'   => config('mail.mailers.smtp.username'),
            'mail.password'   => config('mail.mailers.smtp.password'),
            'mail.encryption' => config('mail.mailers.smtp.encryption'),
            default           => null,
        };
    }
}

