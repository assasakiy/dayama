<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Landing;

use Modules\Landing\Models\StatGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StatController
{
    public function index(): Response
    {
        $statGroups = StatGroup::latest()->get();

        return Inertia::render('Landing/Stats/Index', [
            'statGroups' => $statGroups,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'items'     => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        StatGroup::create($data);

        return redirect()->route('dashboard.landing.stats.index')
            ->with('success', 'StatGroup berhasil ditambahkan.');
    }

    public function update(Request $request, StatGroup $statGroup): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'items'     => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $statGroup->update($data);

        return redirect()->route('dashboard.landing.stats.index')
            ->with('success', 'StatGroup berhasil diperbarui.');
    }

    public function destroy(StatGroup $statGroup): RedirectResponse
    {
        $statGroup->delete();

        return redirect()->route('dashboard.landing.stats.index')
            ->with('success', 'StatGroup berhasil dihapus.');
    }
}
