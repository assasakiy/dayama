<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\HR;

use App\Http\Controllers\Controller;
use App\Support\ActiveInstitution;
use Inertia\Inertia;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['parent', 'headEmployee.person'])
            ->tap(fn ($q) => ActiveInstitution::applyToQuery($q))
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('HR/Departments/Index', [
            'departments' => $departments,
            'allDepartments' => Department::orderBy('name')->get(['id', 'name']),
            'employees' => Employee::with('person')->get()->map(fn($e) => [
                'id' => $e->id,
                'name' => $e->person?->nama_lengkap ?? 'Unknown',
            ]),
        ]);
    }

    public function create()
    {
        return to_route('dashboard.hr.departments.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hr_departments,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|uuid|exists:hr_departments,id',
            'head_employee_id' => 'nullable|uuid|exists:hr_employees,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Department::create($validated);

        return to_route('dashboard.hr.departments.index');
    }

    public function edit(string $id)
    {
        return to_route('dashboard.hr.departments.index');
    }

    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:50', Rule::unique('hr_departments', 'code')->ignore($department->id)],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|uuid|exists:hr_departments,id',
            'head_employee_id' => 'nullable|uuid|exists:hr_employees,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return to_route('dashboard.hr.departments.index');
    }

    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return to_route('dashboard.hr.departments.index');
    }
}
