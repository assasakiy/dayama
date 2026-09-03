<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\Institution;
use Modules\Core\Models\InstitutionMembership;
use Modules\Core\Models\Person;

class YayasanPersonIndexService
{
    public function findByNik(string $nik): ?object
    {
        return DB::table('yayasan_person_index')->where('nik', $nik)->first();
    }

    /**
     * Sinkronkan identitas global person ke index (nama, NIK, tanggal lahir).
     * Tidak lagi menduplikasi daftar afiliasi lembaga ke JSON refs.
     */
    public function syncPerson(Person $person): void
    {
        if (empty($person->nik)) {
            return;
        }

        $existing = DB::table('yayasan_person_index')->where('nik', $person->nik)->first();

        if ($existing) {
            DB::table('yayasan_person_index')
                ->where('id', $existing->id)
                ->update([
                    'nama_lengkap'  => $person->nama_lengkap,
                    'tanggal_lahir' => $person->tanggal_lahir,
                    'updated_at'    => now(),
                ]);
        } else {
            DB::table('yayasan_person_index')->insert([
                'id'            => (string) Str::orderedUuid(),
                'nik'           => $person->nik,
                'nama_lengkap'  => $person->nama_lengkap,
                'tanggal_lahir' => $person->tanggal_lahir,
                'refs'          => json_encode([]),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function removePerson(Person $person): void
    {
        if (empty($person->nik)) {
            return;
        }

        // Cek apakah masih ada person lain dengan NIK yang sama (harus 0 karena NIK global unique)
        $stillExists = Person::where('nik', $person->nik)->where('id', '!=', $person->id)->exists();

        if (! $stillExists) {
            DB::table('yayasan_person_index')->where('nik', $person->nik)->delete();
        }
    }

    /**
     * Dapatkan afiliasi lembaga untuk NIK tertentu di luar lembaga yang sedang aktif.
     * Dibaca langsung dari tabel relasi core_institution_memberships.
     */
    public function getDuplicates(string $nik, string $currentInstitutionId): array
    {
        $person = Person::where('nik', $nik)->first();
        if (! $person) {
            return [];
        }

        return $person->memberships()
            ->where('institution_id', '!=', $currentInstitutionId)
            ->with('institution')
            ->get()
            ->map(fn ($m) => [
                'institution_id'   => $m->institution_id,
                'person_id'        => $m->person_id,
                'institution_name' => $m->institution?->name,
                'status'           => $m->status,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Tautkan Person global ke lembaga baru via InstitutionMembership.
     * Menggantikan pola lama copyFromInstitution yang mereplikasi record Person.
     */
    public function linkToInstitution(string $nik, string $targetInstitutionId): ?Person
    {
        $person = Person::where('nik', $nik)->first();
        if (! $person) {
            return null;
        }

        InstitutionMembership::ensureMembership($person->id, $targetInstitutionId);

        return $person;
    }
}

