<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\YayasanPersonIndexService;
use App\Support\ActiveInstitution;
use Modules\Core\Models\Address;
use Modules\Core\Models\AddressType;
use Modules\Core\Models\Certificate;
use Modules\Core\Models\Contact;
use Modules\Core\Models\ContactType;
use Modules\Academic\Models\EducationLevel;
use Modules\Core\Models\Institution;
use Modules\Core\Models\InstitutionMembership;
use Modules\Core\Models\Language;
use Modules\Core\Models\Person;
use Modules\Academic\Models\PersonEducation;
use Modules\HR\Models\Position;
use Modules\CRM\Models\RelationshipType;
use Modules\Core\Models\Skill;
use Modules\Core\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PersonController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Person::class);

        $user = $request->user();
        $isYayasanOrAdmin = ! $user || $user->is_primary_super_admin || $user->roles()->where('scope', 'yayasan')->exists();

        $query = Person::with(['positions' => fn ($q) => $q->withPivot('institution_id', 'status')])
            ->withCount(['positions'])
            ->latest();

        if (! $isYayasanOrAdmin) {
            $instId = ActiveInstitution::id();
            if ($instId) {
                $query->whereHas('memberships', fn ($m) => $m->where('institution_id', $instId)->where('status', 'active'));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
            );
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($position = $request->input('position')) {
            $query->whereHas('positions', fn ($q) => $q->where('slug', $position));
        }

        $persons = $query->paginate(20)->withQueryString()->through(fn ($p) => [
            'id'              => $p->id,
            'nama_lengkap'    => $p->nama_lengkap,
            'nama_depan'      => $p->nama_depan,
            'nama_belakang'   => $p->nama_belakang,
            'gender'          => $p->gender,
            'tempat_lahir'    => $p->tempat_lahir,
            'tanggal_lahir'   => $p->tanggal_lahir?->format('Y-m-d'),
            'nik'             => $p->nik,
            'photo'           => $p->photo,
            'status_hidup'    => $p->status_hidup,
            'positions_count' => (int) $p->positions_count,
            'created_at'      => $p->created_at,
            'has_user'        => $p->user !== null,
        ]);

        return Inertia::render('Persons/Index', [
            'persons'   => $persons,
            'positions' => Position::orderBy('sort_order')->get(['id', 'nama', 'slug']),
            'filters'   => $request->only(['search', 'gender', 'position']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Person::class);

        $validated = $request->validate([
            'nama_depan'    => 'required|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'gender'        => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'nik'           => 'nullable|string|max:20|unique:core_persons,nik',
        ]);

        $validated['nama_lengkap'] = trim(($validated['nama_depan'] ?? '') . ' ' . ($validated['nama_belakang'] ?? ''));

        $person = Person::create($validated);

        if ($instId = ActiveInstitution::id()) {
            InstitutionMembership::ensureMembership($person->id, $instId);
        }

        return back()->with('success', 'Person berhasil ditambahkan.');
    }

    public function edit(Person $person): Response
    {
        Gate::authorize('update', $person);

        $person->load([
            'positions',
            'contacts.type',
            'addresses.type',
            'educations.level',
            'skills',
            'languages',
            'certificates',
            'familyMembers' => fn ($q) => $q->withPivot('relationship_type_id'),
        ]);

        $familyMembers = $person->familyMembers->map(fn ($m) => [
            'id'                  => $m->id,
            'nama_lengkap'        => $m->nama_lengkap,
            'relationship_type_id' => $m->pivot->relationship_type_id,
        ]);

        return Inertia::render('Persons/Form', [
            'person'           => [
                'id'             => $person->id,
                'nik'            => $person->nik,
                'passport'       => $person->passport,
                'nama_depan'     => $person->nama_depan,
                'nama_belakang'  => $person->nama_belakang,
                'nama_lengkap'   => $person->nama_lengkap,
                'gelar_depan'    => $person->gelar_depan,
                'gelar_belakang' => $person->gelar_belakang,
                'gender'         => $person->gender,
                'tempat_lahir'   => $person->tempat_lahir,
                'tanggal_lahir'  => $person->tanggal_lahir?->format('Y-m-d'),
                'agama'          => $person->agama,
                'status_hidup'   => $person->status_hidup,
                'photo'          => $person->photo,
                'has_user'       => $person->user !== null,
                'positions'     => $person->positions->map(fn ($pos) => [
                    'id'             => $pos->id,
                    'nama'           => $pos->nama,
                    'slug'           => $pos->slug,
                    'institution_id' => $pos->pivot->institution_id,
                    'nomor_induk'    => $pos->pivot->nomor_induk,
                    'tanggal_mulai'  => $pos->pivot->tanggal_mulai,
                    'tanggal_selesai' => $pos->pivot->tanggal_selesai,
                    'status'         => $pos->pivot->status,
                ]),
                'contacts'      => $person->contacts->map(fn ($c) => [
                    'id'             => $c->id,
                    'contact_type_id' => $c->contact_type_id,
                    'type'           => $c->type ? ['nama' => $c->type->nama, 'icon' => $c->type->icon] : null,
                    'value'          => $c->value,
                    'is_primary'     => $c->is_primary,
                ]),
                'addresses'     => $person->addresses->map(fn ($a) => [
                    'id'              => $a->id,
                    'address_type_id' => $a->address_type_id,
                    'type'            => $a->type ? ['nama' => $a->type->nama] : null,
                    'alamat'          => $a->alamat,
                    'provinsi'        => $a->provinsi,
                    'kabupaten_kota'  => $a->kabupaten_kota,
                    'kecamatan'       => $a->kecamatan,
                    'desa_kelurahan'  => $a->desa_kelurahan,
                    'kode_pos'        => $a->kode_pos,
                    'latitude'        => $a->latitude,
                    'longitude'       => $a->longitude,
                    'is_primary'      => $a->is_primary,
                ]),
                'educations'    => $person->educations->map(fn ($e) => [
                    'id'                => $e->id,
                    'education_level_id' => $e->education_level_id,
                    'level'             => $e->level ? ['nama' => $e->level->nama] : null,
                    'institution_name'  => $e->institution_name,
                    'jurusan'           => $e->jurusan,
                    'tahun_masuk'       => $e->tahun_masuk,
                    'tahun_lulus'       => $e->tahun_lulus,
                    'status'            => $e->status,
                ]),
                'skills'        => $person->skills->map(fn ($s) => [
                    'id'    => $s->id,
                    'nama'  => $s->nama,
                    'level' => $s->pivot->level,
                ]),
                'languages'     => $person->languages->map(fn ($l) => [
                    'id'   => $l->id,
                    'nama' => $l->nama,
                ]),
                'certificates'  => $person->certificates->map(fn ($c) => [
                    'id'             => $c->id,
                    'nama'           => $c->nama,
                    'penerbit'       => $c->penerbit,
                    'nomor'          => $c->nomor,
                    'tanggal_terbit' => $c->tanggal_terbit?->format('Y-m-d'),
                    'expired_at'     => $c->expired_at?->format('Y-m-d'),
                    'file'           => $c->file,
                ]),
                'family_members' => $familyMembers,
            ],
            'positions'         => Position::orderBy('sort_order')->get(['id', 'nama', 'slug']),
            'institutions'      => Institution::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'slug']),
            'contact_types'     => ContactType::orderBy('nama')->get(['id', 'nama', 'icon']),
            'address_types'     => AddressType::orderBy('nama')->get(['id', 'nama']),
            'education_levels'  => EducationLevel::orderBy('urutan')->get(['id', 'nama']),
            'skills_list'       => Skill::orderBy('nama')->get(['id', 'nama']),
            'languages_list'    => Language::orderBy('nama')->get(['id', 'nama']),
            'relationship_types' => RelationshipType::orderBy('nama')->get(['id', 'nama']),
            'persons_list'      => Person::orderBy('nama_lengkap')->get(['id', 'nama_lengkap']),
        ]);
    }

    public function update(Request $request, Person $person): RedirectResponse
    {
        Gate::authorize('update', $person);

        $validated = $request->validate([
            'nik'           => ['nullable', 'string', 'max:20', Rule::unique('core_persons', 'nik')->ignore($person->id)],
            'passport'      => 'nullable|string|max:50',
            'nama_depan'    => 'required|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'gelar_depan'   => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'gender'        => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama'         => 'nullable|string|max:30',
            'status_hidup'  => 'boolean',
            'photo'         => 'nullable|string',
        ]);

        $validated['nama_lengkap'] = trim(
            ($validated['gelar_depan'] ? $validated['gelar_depan'] . ' ' : '') .
            ($validated['nama_depan'] ?? '') . ' ' .
            ($validated['nama_belakang'] ?? '')
        );

        $person->update($validated);

        return back()->with('success', 'Data person berhasil diperbarui.');
    }

    // ── Positions ──────────────────────────────────────────────────────────

    public function addPosition(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'position_id'    => 'required|uuid|exists:hr_positions,id',
            'institution_id' => 'nullable|uuid|exists:core_institutions,id',
            'nomor_induk'    => 'nullable|string|max:50',
            'tanggal_mulai'  => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status'         => 'nullable|in:aktif,nonaktif,cuti',
        ]);

        $person->positions()->attach($validated['position_id'], [
            'id'              => (string) Str::orderedUuid(),
            'institution_id'  => $validated['institution_id'] ?? null,
            'nomor_induk'     => $validated['nomor_induk'] ?? null,
            'tanggal_mulai'   => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'status'          => $validated['status'] ?? 'aktif',
        ]);

        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function removePosition(Request $request, Person $person, string $position): RedirectResponse
    {
        $institutionId = $request->input('institution_id');

        $query = $person->positions()->where('positions.id', $position);

        if ($institutionId) {
            $query->wherePivot('institution_id', $institutionId);
        } else {
            $query->wherePivotNull('institution_id');
        }

        $query->detach();

        return back()->with('success', 'Jabatan berhasil dihapus.');
    }

    // ── Account ─────────────────────────────────────────────────────────────

    public function createAccount(Person $person): RedirectResponse
    {
        if ($person->user) {
            return back()->with('error', 'Person ini sudah memiliki akun.');
        }

        $base = Str::of($person->nama_depan)->lower()->replaceMatches('/[^a-z0-9]/', '')->toString();
        $username = $base;
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $suffix;
            $suffix++;
        }

        $user = User::create([
            'name'      => $person->nama_lengkap,
            'username'  => $username,
            'email'     => $username . '@example.com',
            'password'  => Hash::make('password'),
            'person_id' => $person->id,
        ]);

        $user->profile()->create([]);

        return back()->with('success', 'Akun berhasil dibuat. Username: ' . $username . ', Password: password');
    }

    // ── Contacts ────────────────────────────────────────────────────────────

    public function storeContact(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'contact_type_id' => 'required|uuid|exists:core_contact_types,id',
            'value'           => 'required|string|max:255',
            'is_primary'      => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            $person->contacts()->update(['is_primary' => false]);
        }

        $person->contacts()->create($validated);

        return back()->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function updateContact(Request $request, Person $person, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'contact_type_id' => 'required|uuid|exists:core_contact_types,id',
            'value'           => 'required|string|max:255',
            'is_primary'      => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            $person->contacts()->where('id', '!=', $contact->id)->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return back()->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroyContact(Person $person, Contact $contact): RedirectResponse
    {
        $contact->delete();

        return back()->with('success', 'Kontak berhasil dihapus.');
    }

    // ── Addresses ───────────────────────────────────────────────────────────

    public function storeAddress(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'address_type_id' => 'required|uuid|exists:core_address_types,id',
            'alamat'          => 'nullable|string',
            'provinsi'        => 'nullable|string|max:100',
            'kabupaten_kota'  => 'nullable|string|max:100',
            'kecamatan'       => 'nullable|string|max:100',
            'desa_kelurahan'  => 'nullable|string|max:100',
            'kode_pos'        => 'nullable|string|max:10',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'is_primary'      => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            $person->addresses()->update(['is_primary' => false]);
        }

        $person->addresses()->create($validated);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, Person $person, Address $address): RedirectResponse
    {
        $validated = $request->validate([
            'address_type_id' => 'required|uuid|exists:core_address_types,id',
            'alamat'          => 'nullable|string',
            'provinsi'        => 'nullable|string|max:100',
            'kabupaten_kota'  => 'nullable|string|max:100',
            'kecamatan'       => 'nullable|string|max:100',
            'desa_kelurahan'  => 'nullable|string|max:100',
            'kode_pos'        => 'nullable|string|max:10',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'is_primary'      => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            $person->addresses()->where('id', '!=', $address->id)->update(['is_primary' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroyAddress(Person $person, Address $address): RedirectResponse
    {
        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    // ── Educations ──────────────────────────────────────────────────────────

    public function storeEducation(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'education_level_id' => 'required|uuid|exists:academic_education_levels,id',
            'institution_name'   => 'nullable|string|max:255',
            'jurusan'            => 'nullable|string|max:255',
            'tahun_masuk'        => 'nullable|integer|min:1900|max:2099',
            'tahun_lulus'        => 'nullable|integer|min:1900|max:2099',
            'status'             => 'nullable|in:lulus,belum_lulus',
        ]);

        $person->educations()->create($validated);

        return back()->with('success', 'Pendidikan berhasil ditambahkan.');
    }

    public function updateEducation(Request $request, Person $person, PersonEducation $education): RedirectResponse
    {
        $validated = $request->validate([
            'education_level_id' => 'required|uuid|exists:academic_education_levels,id',
            'institution_name'   => 'nullable|string|max:255',
            'jurusan'            => 'nullable|string|max:255',
            'tahun_masuk'        => 'nullable|integer|min:1900|max:2099',
            'tahun_lulus'        => 'nullable|integer|min:1900|max:2099',
            'status'             => 'nullable|in:lulus,belum_lulus',
        ]);

        $education->update($validated);

        return back()->with('success', 'Pendidikan berhasil diperbarui.');
    }

    public function destroyEducation(Person $person, PersonEducation $education): RedirectResponse
    {
        $education->delete();

        return back()->with('success', 'Pendidikan berhasil dihapus.');
    }

    // ── Skills ──────────────────────────────────────────────────────────────

    public function storeSkill(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'skill_id' => 'required|uuid|exists:core_skills,id',
            'level'    => 'nullable|in:pemula,menengah,mahir',
        ]);

        if ($person->skills()->where('skills.id', $validated['skill_id'])->exists()) {
            return back()->with('error', 'Skill sudah ditambahkan.');
        }

        $person->skills()->attach($validated['skill_id'], [
            'level' => $validated['level'] ?? null,
        ]);

        return back()->with('success', 'Skill berhasil ditambahkan.');
    }

    public function updateSkill(Request $request, Person $person, Skill $skill): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'nullable|in:pemula,menengah,mahir',
        ]);

        $person->skills()->updateExistingPivot($skill->id, [
            'level' => $validated['level'] ?? null,
        ]);

        return back()->with('success', 'Level skill berhasil diperbarui.');
    }

    public function destroySkill(Person $person, Skill $skill): RedirectResponse
    {
        $person->skills()->detach($skill->id);

        return back()->with('success', 'Skill berhasil dihapus.');
    }

    // ── Languages ───────────────────────────────────────────────────────────

    public function storeLanguage(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'language_id' => 'required|uuid|exists:core_languages,id',
        ]);

        if ($person->languages()->where('languages.id', $validated['language_id'])->exists()) {
            return back()->with('error', 'Bahasa sudah ditambahkan.');
        }

        $person->languages()->attach($validated['language_id']);

        return back()->with('success', 'Bahasa berhasil ditambahkan.');
    }

    public function destroyLanguage(Person $person, Language $language): RedirectResponse
    {
        $person->languages()->detach($language->id);

        return back()->with('success', 'Bahasa berhasil dihapus.');
    }

    // ── Family ──────────────────────────────────────────────────────────────

    public function storeFamily(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'related_person_id'   => 'required|uuid|exists:core_persons,id|different:person.id',
            'relationship_type_id' => 'required|uuid|exists:core_relationship_types,id',
        ]);

        if ($person->familyMembers()->where('related_person_id', $validated['related_person_id'])->exists()) {
            return back()->with('error', 'Hubungan keluarga ini sudah ada.');
        }

        $person->familyMembers()->attach($validated['related_person_id'], [
            'relationship_type_id' => $validated['relationship_type_id'],
        ]);

        return back()->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    public function destroyFamily(Person $person, string $relatedPersonId): RedirectResponse
    {
        $person->familyMembers()->detach($relatedPersonId);

        Person::find($relatedPersonId)?->familyMembers()->detach($person->id);

        return back()->with('success', 'Anggota keluarga berhasil dihapus.');
    }

    // ── Certificates ────────────────────────────────────────────────────────

    public function storeCertificate(Request $request, Person $person): RedirectResponse
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'penerbit'       => 'nullable|string|max:255',
            'nomor'          => 'nullable|string|max:100',
            'tanggal_terbit' => 'nullable|date',
            'expired_at'     => 'nullable|date',
            'file'           => 'nullable|string',
        ]);

        $person->certificates()->create($validated);

        return back()->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function updateCertificate(Request $request, Person $person, Certificate $certificate): RedirectResponse
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'penerbit'       => 'nullable|string|max:255',
            'nomor'          => 'nullable|string|max:100',
            'tanggal_terbit' => 'nullable|date',
            'expired_at'     => 'nullable|date',
            'file'           => 'nullable|string',
        ]);

        $certificate->update($validated);

        return back()->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function destroyCertificate(Person $person, Certificate $certificate): RedirectResponse
    {
        $certificate->delete();

        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }

    // ── Yayasan Person Index ─────────────────────────────────────────────

    public function checkNik(string $nik): JsonResponse
    {
        $service = app(YayasanPersonIndexService::class);
        $duplicates = $service->getDuplicates($nik, ActiveInstitution::id());

        return response()->json(['duplicates' => $duplicates]);
    }

    public function copyFromInstitution(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:20',
            'source_person_id' => 'required|uuid|exists:core_persons,id',
        ]);

        $service = app(YayasanPersonIndexService::class);
        $person = $service->linkToInstitution(
            $validated['nik'],
            ActiveInstitution::id()
        );

        if (!$person) {
            return response()->json(['error' => 'Gagal menautkan data person ke lembaga ini.'], 422);
        }

        return response()->json([
            'person' => [
                'id' => $person->id,
                'nama_lengkap' => $person->nama_lengkap,
                'nik' => $person->nik,
                'gender' => $person->gender,
                'tempat_lahir' => $person->tempat_lahir,
                'tanggal_lahir' => $person->tanggal_lahir?->format('Y-m-d'),
                'agama' => $person->agama,
                'photo' => $person->photo,
            ],
        ]);
    }

    public function destroy(Person $person): RedirectResponse
    {
        Gate::authorize('delete', $person);

        $person->delete();

        return back()->with('success', 'Data person berhasil dihapus.');
    }
}
