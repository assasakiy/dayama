<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use App\Support\ActiveInstitution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Models\Classroom;
use Modules\Academic\Models\Student;
use Modules\Academic\Models\Subject;
use Modules\Academic\Models\TeachingAssignment;
use Modules\Core\Models\Person;

class RombelController extends Controller
{
    public function index(): Response
    {
        $rombel = Classroom::with(['academicYear', 'waliKelas'])
            ->withCount('students')
            ->tap(fn ($q) => ActiveInstitution::applyToQuery($q, 'institution_id'))
            ->orderBy('nama')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'nama' => $r->nama,
                'tingkat' => $r->tingkat,
                'academic_year' => $r->academicYear ? ['id' => $r->academicYear->id, 'nama' => $r->academicYear->nama] : null,
                'wali_kelas' => $r->waliKelas ? ['id' => $r->waliKelas->id, 'nama_lengkap' => $r->waliKelas->nama_lengkap] : null,
                'students_count' => $r->students_count,
                'created_at' => $r->created_at,
            ]);

        return Inertia::render('Academic/Rombel/Index', [
            'rombel' => $rombel,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Rombel/Form', [
            'academicYears' => AcademicYear::orderBy('nama', 'desc')->get(['id', 'nama', 'is_active']),
            'teachers' => Person::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'nullable|string|max:50',
            'academic_year_id' => 'required|exists:academic_academic_years,id',
            'wali_kelas_person_id' => 'nullable|exists:core_persons,id',
        ]);

        $validated['institution_id'] = ActiveInstitution::id();

        Classroom::create($validated);

        return to_route('dashboard.academic.rombel.index')
            ->with('success', 'Rombel berhasil ditambahkan.');
    }

    public function show(string $id): Response
    {
        $rombel = Classroom::with(['academicYear', 'waliKelas'])->findOrFail($id);

        $students = $rombel->students()->with('person')->get()->map(fn ($s) => [
            'id' => $s->id,
            'nis' => $s->nis,
            'nisn' => $s->nisn,
            'person' => $s->person ? ['id' => $s->person->id, 'nama_lengkap' => $s->person->nama_lengkap] : null,
        ]);

        $availableStudents = Student::with('person')
            ->whereDoesntHave('studentEnrollments', function ($q) use ($id) {
                $q->where('class_id', $id)->where('is_active', true);
            })
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nis' => $s->nis,
                'person_name' => $s->person?->nama_lengkap ?? '-',
            ]);

        $teachingAssignments = $rombel->teachingAssignments()
            ->with(['teacher', 'subject'])
            ->get()
            ->map(fn ($ta) => [
                'id' => $ta->id,
                'teacher' => $ta->teacher ? ['id' => $ta->teacher->id, 'nama_lengkap' => $ta->teacher->nama_lengkap] : null,
                'subject' => $ta->subject ? ['id' => $ta->subject->id, 'nama' => $ta->subject->nama] : null,
                'jam_per_minggu' => $ta->jam_per_minggu,
            ]);

        return Inertia::render('Academic/Rombel/Show', [
            'rombel' => [
                'id' => $rombel->id,
                'nama' => $rombel->nama,
                'tingkat' => $rombel->tingkat,
                'academic_year' => $rombel->academicYear ? ['id' => $rombel->academicYear->id, 'nama' => $rombel->academicYear->nama] : null,
                'wali_kelas' => $rombel->waliKelas ? ['id' => $rombel->waliKelas->id, 'nama_lengkap' => $rombel->waliKelas->nama_lengkap] : null,
            ],
            'students' => $students,
            'availableStudents' => $availableStudents,
            'teachingAssignments' => $teachingAssignments,
            'teachers' => Person::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']),
            'subjects' => Subject::orderBy('nama')->get(['id', 'nama', 'kode']),
        ]);
    }

    public function edit(string $id): Response
    {
        $rombel = Classroom::findOrFail($id);

        return Inertia::render('Academic/Rombel/Form', [
            'rombel' => $rombel,
            'academicYears' => AcademicYear::orderBy('nama', 'desc')->get(['id', 'nama', 'is_active']),
            'teachers' => Person::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $rombel = Classroom::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'nullable|string|max:50',
            'academic_year_id' => 'required|exists:academic_academic_years,id',
            'wali_kelas_person_id' => 'nullable|exists:core_persons,id',
        ]);

        $rombel->update($validated);

        return to_route('dashboard.academic.rombel.index')
            ->with('success', 'Rombel berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $rombel = Classroom::findOrFail($id);
        $rombel->delete();

        return to_route('dashboard.academic.rombel.index')
            ->with('success', 'Rombel berhasil dihapus.');
    }

    public function addStudent(Request $request, string $rombel): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:academic_students,id',
        ]);

        $classroom = Classroom::findOrFail($rombel);
        $classroom->students()->attach($validated['student_id']);

        return back()->with('success', 'Siswa berhasil ditambahkan ke rombel.');
    }

    public function removeStudent(string $rombel, string $student): RedirectResponse
    {
        $classroom = Classroom::findOrFail($rombel);
        $classroom->students()->detach($student);

        return back()->with('success', 'Siswa berhasil dihapus dari rombel.');
    }

    public function storeTeachingAssignment(Request $request, string $rombel): RedirectResponse
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:core_persons,id',
            'subject_id' => 'required|exists:academic_subjects,id',
            'jam_per_minggu' => 'nullable|integer|min:0',
        ]);

        $validated['classroom_id'] = $rombel;

        TeachingAssignment::create($validated);

        return back()->with('success', 'Penugasan mengajar berhasil ditambahkan.');
    }

    public function destroyTeachingAssignment(string $rombel, string $assignment): RedirectResponse
    {
        $ta = TeachingAssignment::where('classroom_id', $rombel)->findOrFail($assignment);
        $ta->delete();

        return back()->with('success', 'Penugasan mengajar berhasil dihapus.');
    }
}
