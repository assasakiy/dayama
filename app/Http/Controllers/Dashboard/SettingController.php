<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController
{
    public function show(string $context, ?string $group = null): Response
    {
        if ($group) {
            $activeGroup = SettingGroup::where('key', $group)->firstOrFail();
            $groups = collect([$activeGroup]);
            $fields = SettingService::groupWithMeta($group, $context);
            $activeGroupKey = $group;
        } else {
            $allowedGroups = $context === 'global' ? ['general', 'media', 'security'] : ['general'];
            $groups = SettingGroup::whereIn('key', $allowedGroups)->orderBy('sort_order')->get();
            $fields = [];
            foreach ($allowedGroups as $g) {
                $fields = array_merge($fields, SettingService::groupWithMeta($g, $context));
            }
            $activeGroupKey = $groups->first()->key ?? 'general';
        }

        return Inertia::render('Settings/Show', [
            'groups'   => $groups,
            'context' => $context,
            'fields'  => $fields,
            'defaultActiveTab' => $activeGroupKey,
            'isSingleGroup' => $group !== null,
        ]);
    }

    /**
     * Save all settings.
     * Skips is_env and is_locked fields silently.
     */
    public function update(Request $request, string $context, ?string $group = null): RedirectResponse
    {
        if ($group) {
            abort_unless(SettingGroup::where('key', $group)->exists(), 404);
        }

        $data = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable'],
        ]);

        $settingsToSave = [];
        $asset = \App\Models\SystemAsset::firstOrCreate(['id' => 1]);

        // Check if there are any media files or media IDs submitted
        $mediaFields = ['general.logo_url', 'general.favicon_url', 'seo.og_image_url'];

        foreach ($mediaFields as $field) {
            if (isset($data['settings'][$field]) && $data['settings'][$field] instanceof \Illuminate\Http\UploadedFile) {
                // Handle file upload
                $asset->clearMediaCollection($field);
                $media = $asset->addMedia($data['settings'][$field])->toMediaCollection($field);
                $settingsToSave[$field] = parse_url($media->getUrl(), PHP_URL_PATH);
            } elseif (isset($data['settings'][$field . '_media_id'])) {
                // Handle selected media from library
                $mediaId = $data['settings'][$field . '_media_id'];
                $sourceMedia = \App\Models\Media::find($mediaId);
                if ($sourceMedia) {
                    $asset->clearMediaCollection($field);
                    $media = $sourceMedia->copy($asset, $field);
                    $settingsToSave[$field] = parse_url($media->getUrl(), PHP_URL_PATH);
                }
            }
        }

        foreach ($data['settings'] as $key => $value) {
            // Skip media ID and preview fields, and fields already processed
            if (str_ends_with($key, '_media_id') || str_ends_with($key, '_preview') || isset($settingsToSave[$key])) {
                continue;
            }
            // Skip uploaded files for non-media fields just in case
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }
            $settingsToSave[$key] = $value;
        }

        SettingService::setMany(
            $settingsToSave,
            $context
        );

        if ($group) {
            SettingService::forgetGroup($group, $context);
        } else {
            $allowedGroups = $context === 'global' ? ['general', 'media', 'security'] : ['general'];
            foreach ($allowedGroups as $g) {
                SettingService::forgetGroup($g, $context);
            }
        }

        return back()->with('success', ucfirst($context) . ' settings saved successfully.');
    }
}

