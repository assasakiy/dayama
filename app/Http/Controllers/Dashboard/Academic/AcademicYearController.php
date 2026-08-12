<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use Modules\Academic\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicYearController extends Controller
{
    public function index(): Response
    {
        $years = AcademicYear::orderBy('nama', 'desc')->get();

        return Inertia::render('Academic/Years/Index', [
            'years' => $years,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Years/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:academic_academic_years,nama',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate all others
        if ($validated['is_active'] ?? false) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($validated);

        return to_route('dashboard.academic.years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(string $id): Response
    {
        $year = AcademicYear::findOrFail($id);

        return Inertia::render('Academic/Years/Form', [
            'year' => $year,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $year = AcademicYear::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:academic_academic_years,nama,' . $id,
            'is_active' => 'boolean',
        ]);

        if ($validated['is_active'] ?? false) {
            AcademicYear::where('is_active', true)
                ->where('id', '!=', $id)
                ->update(['is_active' => false]);
        }

        $year->update($validated);

        return to_route('dashboard.academic.years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $year = AcademicYear::findOrFail($id);
        $year->delete();

        return to_route('dashboard.academic.years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
