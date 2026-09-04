<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\RoleAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Models\ContactType;
use Modules\Core\Models\Institution;
use Modules\Core\Models\InstitutionContact;
use Modules\Core\Models\InstitutionType;
use Modules\Core\Models\Role;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::with('type')->orderBy('sort_order')->get();

        return Inertia::render('Institutions/Index', [
            'institutions' => $institutions,
            'roles' => Role::orderBy('sort_order')->get(['id', 'name', 'display_name']),
            'institutionTypes' => InstitutionType::orderBy('sort_order')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:core_institutions,slug',
            'institution_type_id' => 'nullable|string|max:36',
            'assign_role_id' => 'nullable|string|max:36|exists:core_roles,id',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = 'draft';

        $institution = Institution::create($validated);

        // Assign role to the creator if specified
        if (! empty($validated['assign_role_id']) && $request->user()) {
            $role = Role::find($validated['assign_role_id']);
            if ($role) {
                app(RoleAssignmentService::class)->assign($request->user(), $role, $institution->id);
            }
        }

        return back()->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function edit(Institution $institution): Response
    {
        $institution->load([
            'type',
            'legality',
            'address',
            'institutionContacts.type',
        ]);

        return Inertia::render('Institutions/Edit', [
            'institution' => $institution,
            'institutionTypes' => InstitutionType::orderBy('sort_order')->get(),
            'contact_types' => ContactType::orderBy('nama')->get(['id', 'nama', 'icon']),
        ]);
    }

    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:core_institutions,slug,'.$institution->id,
            'short_description' => 'nullable|string',
            'content' => 'nullable|string',
            'registration_url' => 'nullable|string',
            'is_active' => 'boolean',
            'facilities' => 'nullable|array',
            'extracurriculars' => 'nullable|array',
            'logo_url' => 'nullable|string',
            'cover_url' => 'nullable|string',
            'institution_type_id' => 'nullable|string|max:36',
            'kode' => 'nullable|string|max:100|unique:institutions,kode,'.$institution->id,
            'alamat' => 'nullable|string',
            'status' => 'nullable|string|in:draft,menunggu_kelengkapan,lengkap,terverifikasi',
        ]);

        $institution->update($validated);

        return back()->with('success', 'Lembaga berhasil diperbarui.');
    }

    public function switchActive(Request $request): RedirectResponse
    {
        $request->validate(['institution_id' => 'nullable|uuid|exists:core_institutions,id']);
        $institutionId = $request->input('institution_id');

        if ($institutionId) {
            session(['active_institution_id' => $institutionId]);
        } else {
            session()->forget('active_institution_id');
        }

        return back();
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();

        return back()->with('success', 'Lembaga berhasil dihapus.');
    }

    // ── Legality ────────────────────────────────────────────────────────────

    public function updateLegality(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'nspp' => 'nullable|string|max:50',
            'npsn' => 'nullable|string|max:50',
            'kode_registrasi' => 'nullable|string|max:50',
            'nomor_ijop' => 'nullable|string|max:100',
            'tanggal_ijop' => 'nullable|date',
            'nomor_akta_yayasan' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:30',
            'tahun_berdiri_masehi' => 'nullable|integer|min:1800|max:2099',
            'tahun_berdiri_hijriyah' => 'nullable|integer|min:1200|max:1499',
        ]);

        $institution->legality()->updateOrCreate(
            ['institution_id' => $institution->id],
            $validated
        );

        return back()->with('success', 'Data legalitas berhasil diperbarui.');
    }

    // ── Address (EMIS) ──────────────────────────────────────────────────────

    public function updateAddress(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'alamat_jalan' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'kode_pos' => 'nullable|string|max:10',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $institution->address()->updateOrCreate(
            ['institution_id' => $institution->id],
            $validated
        );

        return back()->with('success', 'Alamat EMIS berhasil diperbarui.');
    }

    // ── Contacts ────────────────────────────────────────────────────────────

    public function storeContact(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'contact_type_id' => 'required|uuid|exists:contact_types,id',
            'value' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            $institution->institutionContacts()->update(['is_primary' => false]);
        }

        $institution->institutionContacts()->create($validated);

        return back()->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function updateContact(Request $request, Institution $institution, InstitutionContact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'contact_type_id' => 'required|uuid|exists:contact_types,id',
            'value' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        if ($validated['is_primary'] ?? false) {
            $institution->institutionContacts()->where('id', '!=', $contact->id)->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return back()->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroyContact(Institution $institution, InstitutionContact $contact): RedirectResponse
    {
        $contact->delete();

        return back()->with('success', 'Kontak berhasil dihapus.');
    }
}
