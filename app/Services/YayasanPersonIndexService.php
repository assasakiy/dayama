<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;

class YayasanPersonIndexService
{
    public function findByNik(string $nik): ?object
    {
        return DB::table('yayasan_person_index')->where('nik', $nik)->first();
    }

    public function syncPerson(Person $person): void
    {
        if (empty($person->nik)) {
            return;
        }

        $existing = DB::table('yayasan_person_index')->where('nik', $person->nik)->first();

        $refEntry = [
            'institution_id' => $person->institution_id,
            'person_id'      => $person->id,
            'nama_lengkap'   => $person->nama_lengkap,
            'created_at'     => now()->toDateTimeString(),
        ];

        if ($existing) {
            $refs = is_string($existing->refs) ? json_decode($existing->refs, true) : (array) $existing->refs;
            $exists = collect($refs)->first(fn ($r) => ($r['person_id'] ?? null) === $person->id);
            if (!$exists) {
                $refs[] = $refEntry;
            }

            DB::table('yayasan_person_index')
                ->where('id', $existing->id)
                ->update([
                    'nama_lengkap' => $person->nama_lengkap,
                    'tanggal_lahir' => $person->tanggal_lahir,
                    'refs' => json_encode($refs),
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('yayasan_person_index')->insert([
                'id' => (string) \Illuminate\Support\Str::orderedUuid(),
                'nik' => $person->nik,
                'nama_lengkap' => $person->nama_lengkap,
                'tanggal_lahir' => $person->tanggal_lahir,
                'refs' => json_encode([$refEntry]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function removePerson(Person $person): void
    {
        if (empty($person->nik)) {
            return;
        }

        $existing = DB::table('yayasan_person_index')->where('nik', $person->nik)->first();
        if (!$existing) {
            return;
        }

        $refs = is_string($existing->refs) ? json_decode($existing->refs, true) : (array) $existing->refs;
        $refs = collect($refs)->filter(fn ($r) => ($r['person_id'] ?? null) !== $person->id)->values()->toArray();

        if (empty($refs)) {
            DB::table('yayasan_person_index')->where('id', $existing->id)->delete();
        } else {
            DB::table('yayasan_person_index')
                ->where('id', $existing->id)
                ->update([
                    'refs' => json_encode($refs),
                    'updated_at' => now(),
                ]);
        }
    }

    public function getDuplicates(string $nik, string $currentInstitutionId): array
    {
        $entry = DB::table('yayasan_person_index')->where('nik', $nik)->first();
        if (!$entry) {
            return [];
        }

        $refs = is_string($entry->refs) ? json_decode($entry->refs, true) : (array) $entry->refs;

        return collect($refs)
            ->filter(fn ($r) => ($r['institution_id'] ?? null) !== $currentInstitutionId)
            ->map(fn ($r) => [
                'institution_id' => $r['institution_id'] ?? null,
                'person_id'      => $r['person_id'] ?? null,
                'institution_name' => Institution::find($r['institution_id'])?->name,
            ])
            ->values()
            ->toArray();
    }

    public function copyFromInstitution(string $nik, string $sourcePersonId, string $targetInstitutionId): ?Person
    {
        $sourcePerson = Person::find($sourcePersonId);
        if (!$sourcePerson) {
            return null;
        }

        $existing = DB::table('yayasan_person_index')->where('nik', $nik)->first();
        if (!$existing) {
            return null;
        }

        $refs = is_string($existing->refs) ? json_decode($existing->refs, true) : (array) $existing->refs;
        $targetRef = collect($refs)->first(fn ($r) => ($r['institution_id'] ?? null) === $targetInstitutionId);

        if ($targetRef && isset($targetRef['person_id'])) {
            return Person::find($targetRef['person_id']);
        }

        $newPerson = $sourcePerson->replicate(['id', 'institution_id', 'created_at', 'updated_at', 'deleted_at']);
        $newPerson->institution_id = $targetInstitutionId;
        $newPerson->save();

        foreach ($sourcePerson->contacts as $contact) {
            $newContact = $contact->replicate(['id', 'person_id', 'created_at', 'updated_at']);
            $newContact->person_id = $newPerson->id;
            $newContact->save();
        }

        foreach ($sourcePerson->addresses as $address) {
            $newAddress = $address->replicate(['id', 'person_id', 'created_at', 'updated_at']);
            $newAddress->person_id = $newPerson->id;
            $newAddress->save();
        }

        foreach ($sourcePerson->certificates as $cert) {
            $newCert = $cert->replicate(['id', 'person_id', 'created_at', 'updated_at']);
            $newCert->person_id = $newPerson->id;
            $newCert->save();
        }

        $this->syncPerson($newPerson);

        return $newPerson;
    }
}
