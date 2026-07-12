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
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", self::CACHE_TTL, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

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
    public static function group(string $group): array
    {
        return Cache::remember("setting_group:{$group}", self::CACHE_TTL, function () use ($group) {
            return Setting::where('group', $group)
                ->get()
                ->mapWithKeys(function (Setting $setting) {
                    $shortKey = ltrim(str_replace($setting->group . '.', '', $setting->key), '.');
                    $value = $setting->is_env
                        ? static::fromEnv($setting->key)
                        : $setting->value;
                    return [$shortKey => $value];
                })
                ->toArray();
        });
    }

    /**
     * Get all settings as a nested array grouped by group name.
     */
    public static function all(): array
    {
        return Cache::remember('settings_all', self::CACHE_TTL, function () {
            return Setting::all()
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
    public static function groupWithMeta(string $group): array
    {
        return Setting::where('group', $group)
            ->orderBy('key')
            ->get()
            ->map(function (Setting $setting) {
                return [
                    'key'         => $setting->key,
                    'value'       => $setting->is_env ? static::fromEnv($setting->key) : $setting->value,
                    'type'        => $setting->type,
                    'is_env'      => $setting->is_env,
                    'is_locked'   => $setting->is_locked,
                    'description' => $setting->description,
                ];
            })
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Write
    // -------------------------------------------------------------------------

    /**
     * Set a single setting value. Throws if the setting is env-managed.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = Setting::where('key', $key)->first();

        if ($setting?->is_env) {
            throw new \RuntimeException(
                "Setting [{$key}] is environment-managed and cannot be written from the UI. " .
                'Please update your .env file instead.'
            );
        }

        if ($setting?->is_locked) {
            throw new \RuntimeException("Setting [{$key}] is locked and cannot be modified.");
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting:{$key}");
    }

    /**
     * Bulk-set an array of key→value pairs.
     * Silently skips is_env and is_locked fields.
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting?->is_env || $setting?->is_locked) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            Cache::forget("setting:{$key}");
        }
    }

    // -------------------------------------------------------------------------
    // Cache Invalidation
    // -------------------------------------------------------------------------

    public static function forget(string $key): void
    {
        Cache::forget("setting:{$key}");
    }

    public static function forgetGroup(string $group): void
    {
        Cache::forget("setting_group:{$group}");
        Cache::forget('settings_all');

        Setting::where('group', $group)
            ->pluck('key')
            ->each(fn (string $key) => Cache::forget("setting:{$key}"));
    }

    public static function forgetAll(): void
    {
        Cache::forget('settings_all');

        Setting::pluck('key', 'group')
            ->each(function (string $key, string $group) {
                Cache::forget("setting:{$key}");
                Cache::forget("setting_group:{$group}");
            });
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

