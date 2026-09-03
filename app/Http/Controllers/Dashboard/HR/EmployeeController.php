<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\HR;

use App\Http\Controllers\Controller;
use App\Support\ActiveInstitution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Models\Address;
use Modules\Core\Models\AddressType;
use Modules\Core\Models\Contact;
use Modules\Core\Models\ContactType;
use Modules\Core\Models\InstitutionMembership;
use Modules\Core\Models\Person;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmploymentStatus;

class EmployeeController extends Controller
{
    public function index(): Response
    {
        $employees = Employee::with('person', 'employmentStatus', 'department')
            ->tap(fn ($q) => ActiveInstitution::applyToQuery($q))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'nip' => $e->nip,
                'nuptk' => $e->nuptk,
                'sudah_sertifikasi' => $e->sudah_sertifikasi,
                'person' => $e->person ? ['id' => $e->person->id, 'nama_lengkap' => $e->person->nama_lengkap, 'photo' => $e->person->photo] : null,
                'employment_status' => $e->employmentStatus ? ['id' => $e->employmentStatus->id, 'nama' => $e->employmentStatus->nama] : null,
                'department' => $e->department ? ['id' => $e->department->id, 'name' => $e->department->name] : null,
            ]);

        return Inertia::render('HR/Employees/Index', [
            'employees' => $employees,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('HR/Employees/Form', [
            'employee' => null,
            'employmentStatuses' => EmploymentStatus::select('id', 'nama')->orderBy('nama')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'contactTypes' => ContactType::select('id', 'nama')->orderBy('nama')->get(),
            'addressTypes' => AddressType::select('id', 'nama')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Person
            'person_id' => 'nullable|uuid|exists:core_persons,id',
            'nama_lengkap' => 'required|string|max:200',
            'nik' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:30',
            // Employee
            'nip' => 'nullable|string|max:255|unique:hr_employees,nip',
            'nuptk' => 'nullable|string|max:255',
            'employment_status_id' => 'nullable|exists:hr_employment_statuses,id',
            'department_id' => 'nullable|exists:hr_departments,id',
            'sudah_sertifikasi' => 'boolean',
            'nomor_sertifikat_pendidik' => 'nullable|string|max:255',
            'jam_mengajar_per_minggu' => 'nullable|integer|min:0|max:168',
            // Contacts
            'contacts' => 'nullable|array',
            'contacts.*.contact_type_id' => 'required_with:contacts|exists:core_contact_types,id',
            'contacts.*.value' => 'required_with:contacts|string|max:255',
            // Address
            'alamat' => 'nullable|string|max:500',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        $institutionId = ActiveInstitution::id();

        return DB::transaction(function () use ($validated, $institutionId) {
            // Global Person Resolver: reuse by person_id, reuse by NIK, or create
            if (! empty($validated['person_id'])) {
                $person = Person::findOrFail($validated['person_id']);
                $person->update(array_filter([
                    'nama_lengkap'  => $validated['nama_lengkap'] ?? $person->nama_lengkap,
                    'nik'           => $validated['nik'] ?? $person->nik,
                    'gender'        => $validated['gender'] ?? $person->gender,
                    'tempat_lahir'  => $validated['tempat_lahir'] ?? $person->tempat_lahir,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? $person->tanggal_lahir,
                    'agama'         => $validated['agama'] ?? $person->agama,
                ], fn ($v) => $v !== null));
            } elseif (! empty($validated['nik']) && ($existingPerson = Person::where('nik', $validated['nik'])->first())) {
                $person = $existingPerson;
                $person->update(array_filter([
                    'nama_lengkap'  => $validated['nama_lengkap'] ?? $person->nama_lengkap,
                    'gender'        => $validated['gender'] ?? $person->gender,
                    'tempat_lahir'  => $validated['tempat_lahir'] ?? $person->tempat_lahir,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? $person->tanggal_lahir,
                    'agama'         => $validated['agama'] ?? $person->agama,
                ], fn ($v) => $v !== null));
            } else {
                $person = Person::create([
                    'nama_lengkap'  => $validated['nama_lengkap'],
                    'nik'           => $validated['nik'] ?? null,
                    'gender'        => $validated['gender'] ?? null,
                    'tempat_lahir'  => $validated['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'agama'         => $validated['agama'] ?? null,
                ]);
            }

            // Ensure Institution Membership
            InstitutionMembership::ensureMembership($person->id, $institutionId);

            // Create employee
            Employee::create([
                'person_id' => $person->id,
                'institution_id' => $institutionId,
                'nip' => $validated['nip'] ?? null,
                'nuptk' => $validated['nuptk'] ?? null,
                'employment_status_id' => $validated['employment_status_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'sudah_sertifikasi' => $validated['sudah_sertifikasi'] ?? false,
                'nomor_sertifikat_pendidik' => $validated['nomor_sertifikat_pendidik'] ?? null,
                'jam_mengajar_per_minggu' => $validated['jam_mengajar_per_minggu'] ?? null,
            ]);

            // Sync contacts
            $this->syncContacts($person, $validated['contacts'] ?? []);

            // Sync address
            $this->syncAddress($person, $validated);

            return redirect('/hr/employees')
                ->with('success', 'Guru / staf berhasil ditambahkan.');
        });
    }

    public function edit(string $id): Response
    {
        $employee = Employee::with([
            'person.contacts.type',
            'person.addresses.type',
            'employmentStatus',
            'department',
        ])->findOrFail($id);

        return Inertia::render('HR/Employees/Form', [
            'employee' => $employee,
            'employmentStatuses' => EmploymentStatus::select('id', 'nama')->orderBy('nama')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'contactTypes' => ContactType::select('id', 'nama')->orderBy('nama')->get(),
            'addressTypes' => AddressType::select('id', 'nama')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $employee = Employee::with('person')->findOrFail($id);

        $validated = $request->validate([
            // Person
            'nama_lengkap' => 'required|string|max:200',
            'nik' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:30',
            // Employee
            'nip' => 'nullable|string|max:255|unique:hr_employees,nip,' . $id,
            'nuptk' => 'nullable|string|max:255',
            'employment_status_id' => 'nullable|exists:hr_employment_statuses,id',
            'department_id' => 'nullable|exists:hr_departments,id',
            'sudah_sertifikasi' => 'boolean',
            'nomor_sertifikat_pendidik' => 'nullable|string|max:255',
            'jam_mengajar_per_minggu' => 'nullable|integer|min:0|max:168',
            // Contacts
            'contacts' => 'nullable|array',
            'contacts.*.contact_type_id' => 'required_with:contacts|exists:core_contact_types,id',
            'contacts.*.value' => 'required_with:contacts|string|max:255',
            // Address
            'alamat' => 'nullable|string|max:500',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        return DB::transaction(function () use ($employee, $validated) {
            // Update person
            $employee->person->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'agama' => $validated['agama'] ?? null,
            ]);

            // Update employee
            $employee->update([
                'nip' => $validated['nip'] ?? null,
                'nuptk' => $validated['nuptk'] ?? null,
                'employment_status_id' => $validated['employment_status_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'sudah_sertifikasi' => $validated['sudah_sertifikasi'] ?? false,
                'nomor_sertifikat_pendidik' => $validated['nomor_sertifikat_pendidik'] ?? null,
                'jam_mengajar_per_minggu' => $validated['jam_mengajar_per_minggu'] ?? null,
            ]);

            // Sync contacts
            $this->syncContacts($employee->person, $validated['contacts'] ?? []);

            // Sync address
            $this->syncAddress($employee->person, $validated);

            return redirect('/hr/employees')
                ->with('success', 'Guru / staf berhasil diperbarui.');
        });
    }

    public function destroy(string $id): RedirectResponse
    {
        Employee::findOrFail($id)->delete();

        return redirect('/hr/employees')
            ->with('success', 'Guru / staf berhasil dihapus.');
    }

    private function syncContacts(Person $person, array $contacts): void
    {
        // Delete existing then re-create
        $person->contacts()->delete();

        foreach ($contacts as $c) {
            if (!empty($c['value'])) {
                $person->contacts()->create([
                    'contact_type_id' => $c['contact_type_id'],
                    'value' => $c['value'],
                    'is_primary' => $c['is_primary'] ?? false,
                ]);
            }
        }
    }

    private function syncAddress(Person $person, array $data): void
    {
        $hasAddress = !empty($data['alamat']) || !empty($data['provinsi']) || !empty($data['kabupaten_kota']);

        if (!$hasAddress) {
            return;
        }

        // Upsert primary address
        $address = $person->addresses()->where('is_primary', true)->first();

        $addressData = [
            'alamat' => $data['alamat'] ?? null,
            'provinsi' => $data['provinsi'] ?? null,
            'kabupaten_kota' => $data['kabupaten_kota'] ?? null,
            'kecamatan' => $data['kecamatan'] ?? null,
            'desa_kelurahan' => $data['desa_kelurahan'] ?? null,
            'kode_pos' => $data['kode_pos'] ?? null,
            'is_primary' => true,
        ];

        if ($address) {
            $address->update($addressData);
        } else {
            $firstAddressType = AddressType::first();
            $person->addresses()->create(array_merge($addressData, [
                'address_type_id' => $firstAddressType?->id,
            ]));
        }
    }
}
