<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Academic\Models\AClass;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Models\EducationLevel;
use Modules\Core\Models\Person;

class ClassController extends Controller
{
    public function index(): Response
    {
        $classes = AClass::with(['academicYear', 'educationLevel'])
            ->orderBy('name')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'slug' => $class->slug,
                    'academic_year_id' => $class->academic_year_id,
                    'education_level_id' => $class->education_level_id,
                    'homeroom_teacher_id' => $class->homeroom_teacher_id,
                    'capacity' => $class->capacity,
                    'is_active' => $class->is_active,
                    'academic_year' => $class->academicYear ? ['id' => $class->academicYear->id, 'nama' => $class->academicYear->nama] : null,
                    'education_level' => $class->educationLevel ? ['id' => $class->educationLevel->id, 'nama' => $class->educationLevel->nama] : null,
                    'created_at' => $class->created_at,
                ];
            });

        return Inertia::render('Academic/Classes/Index', [
            'classes' => $classes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Classes/Form', [
            'academicYears' => AcademicYear::orderBy('nama', 'desc')->get(['id', 'nama', 'is_active']),
            'educationLevels' => EducationLevel::orderBy('urutan')->get(['id', 'nama']),
            'teachers' => Person::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_academic_years,id',
            'education_level_id' => 'nullable|exists:core_education_levels,id',
            'homeroom_teacher_id' => 'nullable|exists:core_persons,id',
            'capacity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        AClass::create($validated);

        return to_route('dashboard.academic.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(string $id): Response
    {
        $class = AClass::findOrFail($id);

        return Inertia::render('Academic/Classes/Form', [
            'class' => $class,
            'academicYears' => AcademicYear::orderBy('nama', 'desc')->get(['id', 'nama', 'is_active']),
            'educationLevels' => EducationLevel::orderBy('urutan')->get(['id', 'nama']),
            'teachers' => Person::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $class = AClass::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_academic_years,id',
            'education_level_id' => 'nullable|exists:core_education_levels,id',
            'homeroom_teacher_id' => 'nullable|exists:core_persons,id',
            'capacity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? false;

        $class->update($validated);

        return to_route('dashboard.academic.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $class = AClass::findOrFail($id);
        $class->delete();

        return to_route('dashboard.academic.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
