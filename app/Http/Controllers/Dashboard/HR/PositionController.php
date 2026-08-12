<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Modules\HR\Models\Position;

class PositionController extends Controller
{
    public function index(): Response
    {
        $positions = Position::orderBy('sort_order')->get();

        return Inertia::render('HR/Positions/Index', [
            'positions' => $positions,
        ]);
    }

    public function create(): RedirectResponse
    {
        return to_route('dashboard.hr.positions.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Position::create($validated);

        return to_route('dashboard.hr.positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(string $id): RedirectResponse
    {
        return to_route('dashboard.hr.positions.index');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $position->update($validated);

        return to_route('dashboard.hr.positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return to_route('dashboard.hr.positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
