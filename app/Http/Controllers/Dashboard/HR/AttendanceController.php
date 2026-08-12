<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Employee;

class AttendanceController extends Controller
{
    public function index(): Response
    {
        $attendances = Attendance::with('employee.person')
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn (Attendance $attendance) => [
                'id' => $attendance->id,
                'date' => $attendance->date,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
                'status' => $attendance->status,
                'notes' => $attendance->notes,
                'employee' => $attendance->employee ? [
                    'id' => $attendance->employee->id,
                    'nip' => $attendance->employee->nip,
                    'person' => $attendance->employee->person ? [
                        'id' => $attendance->employee->person->id,
                        'nama_lengkap' => $attendance->employee->person->nama_lengkap,
                    ] : null,
                ] : null,
            ]);

        $employees = Employee::with('person:id,nama_lengkap')
            ->select('id', 'nip', 'person_id')
            ->orderBy('nip')
            ->get()
            ->map(fn (Employee $employee) => [
                'id' => $employee->id,
                'nip' => $employee->nip,
                'person' => $employee->person ? [
                    'id' => $employee->person->id,
                    'nama_lengkap' => $employee->person->nama_lengkap,
                ] : null,
            ]);

        return Inertia::render('HR/Attendance/Index', [
            'attendances' => $attendances,
            'employees' => $employees,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect('/hr/attendance');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|string|in:hadir,izin,sakit,alpha,cuti',
            'notes' => 'nullable|string',
        ]);

        $validated['recorded_by'] = auth()->id();

        Attendance::create($validated);

        return redirect('/hr/attendance')
            ->with('success', 'Attendance berhasil ditambahkan.');
    }

    public function edit(string $id): RedirectResponse
    {
        return redirect('/hr/attendance');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'status' => 'required|string|in:hadir,izin,sakit,alpha,cuti',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect('/hr/attendance')
            ->with('success', 'Attendance berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect('/hr/attendance')
            ->with('success', 'Attendance berhasil dihapus.');
    }
}
