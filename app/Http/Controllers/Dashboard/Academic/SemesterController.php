<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SemesterController extends Controller
{
    public function index(): Response
    {
        $semesters = Semester::with('academicYear')
            ->orderBy('start_date', 'desc')
            ->get();

        return Inertia::render('Academic/Semesters/Index', [
            'semesters' => $semesters,
        ]);
    }

    public function create(): Response
    {
        $academicYears = AcademicYear::orderBy('nama', 'desc')->get(['id', 'nama']);

        return Inertia::render('Academic/Semesters/Form', [
            'academicYears' => $academicYears,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|uuid|exists:academic_academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        Semester::create($validated);

        return to_route('dashboard.academic.semesters.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(string $id): Response
    {
        $semester = Semester::findOrFail($id);
        $academicYears = AcademicYear::orderBy('nama', 'desc')->get(['id', 'nama']);

        return Inertia::render('Academic/Semesters/Form', [
            'semester' => $semester,
            'academicYears' => $academicYears,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'academic_year_id' => 'required|uuid|exists:academic_academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $semester->update($validated);

        return to_route('dashboard.academic.semesters.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return to_route('dashboard.academic.semesters.index')
            ->with('success', 'Semester berhasil dihapus.');
    }
}
