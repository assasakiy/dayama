<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use App\Support\ActiveInstitution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Academic\Models\Student;
use Modules\Core\Models\AddressType;
use Modules\Core\Models\ContactType;
use Modules\Core\Models\Person;

class StudentController extends Controller
{
    public function index(): Response
    {
        $students = Student::with('person')
            ->orderBy('created_at', 'desc')
            ->tap(fn ($q) => ActiveInstitution::applyToQuery($q))
            ->paginate(20)
            ->through(fn ($student) => [
                'id' => $student->id,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'angkatan' => $student->angkatan,
                'status' => $student->status,
                'person' => $student->person ? [
                    'id' => $student->person->id,
                    'nama_lengkap' => $student->person->nama_lengkap,
                    'nik' => $student->person->nik,
                    'photo' => $student->person->photo,
                ] : null,
            ]);

        return Inertia::render('Academic/Students/Index', [
            'students' => $students,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Students/Form', [
            'student' => null,
            'persons' => Person::select('id', 'nama_lengkap', 'nik', 'gender', 'tempat_lahir', 'tanggal_lahir', 'agama', 'photo')
                ->orderBy('nama_lengkap')
                ->get(),
            'contactTypes' => ContactType::select('id', 'nama')->orderBy('nama')->get(),
            'addressTypes' => AddressType::select('id', 'nama')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $institutionId = ActiveInstitution::id();

        $validated = $request->validate([
            // Person
            'nama_lengkap' => 'required|string|max:200',
            'nik' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:30',
            // Student
            'nis' => 'required|string|unique:academic_students,nis,NULL,id,institution_id,' . $institutionId,
            'nisn' => 'nullable|string|max:20',
            'angkatan' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:20',
            'nama_ibu_kandung' => 'nullable|string|max:200',
            'tempat_tinggal' => 'nullable|string|max:30',
            'nomor_kk' => 'nullable|string|max:20',
            'nomor_kip' => 'nullable|string|max:20',
            'cita_cita' => 'nullable|string|max:200',
            'hobi' => 'nullable|string|max:200',
            'waktu_tempuh_menit' => 'nullable|integer|min:0|max:999',
            'is_locked' => 'boolean',
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

        return DB::transaction(function () use ($validated, $institutionId) {
            // Create or use existing person
            if (empty($validated['person_id'])) {
                $person = Person::create([
                    'institution_id' => $institutionId,
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'nik' => $validated['nik'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'agama' => $validated['agama'] ?? null,
                ]);
            } else {
                $person = Person::findOrFail($validated['person_id']);
                $person->update(array_filter([
                    'nama_lengkap' => $validated['nama_lengkap'] ?? $person->nama_lengkap,
                    'nik' => $validated['nik'] ?? $person->nik,
                    'gender' => $validated['gender'] ?? $person->gender,
                    'tempat_lahir' => $validated['tempat_lahir'] ?? $person->tempat_lahir,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? $person->tanggal_lahir,
                    'agama' => $validated['agama'] ?? $person->agama,
                ], fn ($v) => $v !== null));
            }

            // Create student
            Student::create([
                'person_id' => $person->id,
                'institution_id' => $institutionId,
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
                'angkatan' => $validated['angkatan'] ?? null,
                'status' => $validated['status'] ?? 'aktif',
                'nama_ibu_kandung' => $validated['nama_ibu_kandung'] ?? null,
                'tempat_tinggal' => $validated['tempat_tinggal'] ?? null,
                'nomor_kk' => $validated['nomor_kk'] ?? null,
                'nomor_kip' => $validated['nomor_kip'] ?? null,
                'cita_cita' => $validated['cita_cita'] ?? null,
                'hobi' => $validated['hobi'] ?? null,
                'waktu_tempuh_menit' => $validated['waktu_tempuh_menit'] ?? null,
                'is_locked' => $validated['is_locked'] ?? false,
            ]);

            // Sync contacts
            $this->syncContacts($person, $validated['contacts'] ?? []);

            // Sync address
            $this->syncAddress($person, $validated);

            return redirect('/academic/students')
                ->with('success', 'Siswa berhasil ditambahkan.');
        });
    }

    public function edit(string $id): Response
    {
        $student = Student::with([
            'person.contacts.type',
            'person.addresses.type',
        ])->findOrFail($id);

        return Inertia::render('Academic/Students/Form', [
            'student' => $student,
            'persons' => Person::select('id', 'nama_lengkap', 'nik', 'gender', 'tempat_lahir', 'tanggal_lahir', 'agama', 'photo')
                ->orderBy('nama_lengkap')
                ->get(),
            'contactTypes' => ContactType::select('id', 'nama')->orderBy('nama')->get(),
            'addressTypes' => AddressType::select('id', 'nama')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $student = Student::with('person')->findOrFail($id);
        $institutionId = $student->institution_id;

        $validated = $request->validate([
            // Person
            'nama_lengkap' => 'required|string|max:200',
            'nik' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:30',
            // Student
            'nis' => 'required|string|unique:academic_students,nis,' . $id . ',id,institution_id,' . $institutionId,
            'nisn' => 'nullable|string|max:20',
            'angkatan' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:20',
            'nama_ibu_kandung' => 'nullable|string|max:200',
            'tempat_tinggal' => 'nullable|string|max:30',
            'nomor_kk' => 'nullable|string|max:20',
            'nomor_kip' => 'nullable|string|max:20',
            'cita_cita' => 'nullable|string|max:200',
            'hobi' => 'nullable|string|max:200',
            'waktu_tempuh_menit' => 'nullable|integer|min:0|max:999',
            'is_locked' => 'boolean',
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

        return DB::transaction(function () use ($student, $validated) {
            // Update person
            $student->person->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'agama' => $validated['agama'] ?? null,
            ]);

            // Update student
            $student->update([
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
                'angkatan' => $validated['angkatan'] ?? null,
                'status' => $validated['status'] ?? null,
                'nama_ibu_kandung' => $validated['nama_ibu_kandung'] ?? null,
                'tempat_tinggal' => $validated['tempat_tinggal'] ?? null,
                'nomor_kk' => $validated['nomor_kk'] ?? null,
                'nomor_kip' => $validated['nomor_kip'] ?? null,
                'cita_cita' => $validated['cita_cita'] ?? null,
                'hobi' => $validated['hobi'] ?? null,
                'waktu_tempuh_menit' => $validated['waktu_tempuh_menit'] ?? null,
                'is_locked' => $validated['is_locked'] ?? false,
            ]);

            // Sync contacts
            $this->syncContacts($student->person, $validated['contacts'] ?? []);

            // Sync address
            $this->syncAddress($student->person, $validated);

            return redirect('/academic/students')
                ->with('success', 'Siswa berhasil diperbarui.');
        });
    }

    public function destroy(string $id): RedirectResponse
    {
        Student::findOrFail($id)->delete();

        return redirect('/academic/students')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    private function syncContacts(Person $person, array $contacts): void
    {
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
            $firstAddressType = \Modules\Core\Models\AddressType::first();
            $person->addresses()->create(array_merge($addressData, [
                'address_type_id' => $firstAddressType?->id,
            ]));
        }
    }
}
