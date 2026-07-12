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
    /**
     * Show the settings page for a specific group.
     */
    public function show(string $group): Response
    {
        $activeGroup = SettingGroup::where('key', $group)->firstOrFail();
        $fields = SettingService::groupWithMeta($group);

        return Inertia::render('Settings/Show', [
            'group'  => $activeGroup,
            'fields' => $fields,
        ]);
    }

    /**
     * Save all settings for a specific group.
     * Skips is_env and is_locked fields silently.
     */
    public function update(Request $request, string $group): RedirectResponse
    {
        // Verify group exists
        abort_unless(SettingGroup::where('key', $group)->exists(), 404);

        $data = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable'],
        ]);

        SettingService::setMany(
            collect($data['settings'])
                ->mapWithKeys(fn ($value, $key) => [$key => $value])
                ->toArray()
        );

        SettingService::forgetGroup($group);

        return back()->with('success', 'Settings saved successfully.');
    }
}

