<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Academic\Models\Student;
use Modules\Core\Models\Institution;
use Modules\Core\Models\InstitutionMembership;
use Modules\Core\Models\Person;
use Tests\TestCase;

class IdentityTahap1ATest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * Test 1: 1 Person dapat terhubung ke 2 Institution berbeda
     * menghasilkan 2 InstitutionMembership dan tetap HANYA 1 baris core_persons.
     */
    public function test_single_person_multi_institution_memberships(): void
    {
        $inst1 = Institution::create(['name' => 'MTs DAYAMA', 'slug' => 'mts-' . Str::random(5)]);
        $inst2 = Institution::create(['name' => 'MA DAYAMA', 'slug' => 'ma-' . Str::random(5)]);

        $person = Person::create([
            'nama_lengkap' => 'Ahmad Dahlan',
            'nik'          => '3201012345670001',
        ]);

        $m1 = InstitutionMembership::ensureMembership($person->id, $inst1->id);
        $m2 = InstitutionMembership::ensureMembership($person->id, $inst2->id);

        $this->assertEquals(1, Person::where('nik', '3201012345670001')->count());
        $this->assertEquals(2, InstitutionMembership::where('person_id', $person->id)->count());
        $this->assertEquals('active', $m1->status);
        $this->assertEquals('active', $m2->status);
        $this->assertCount(2, $person->institutions);
    }

    /**
     * Test 2: Reaktivasi membership yang sudah inactive
     * memastikan status kembali active dan left_at menjadi null tanpa menimpa joined_at awal.
     */
    public function test_ensure_membership_reactivates_inactive_membership(): void
    {
        $inst = Institution::create(['name' => 'MI DAYAMA', 'slug' => 'mi-' . Str::random(5)]);
        $person = Person::create([
            'nama_lengkap' => 'Fatimah Az-Zahra',
            'nik'          => '3201012345670002',
        ]);

        $m = InstitutionMembership::ensureMembership($person->id, $inst->id);
        $initialJoinedAt = $m->joined_at;

        // Simulasi non-aktif
        $m->update([
            'status'  => 'inactive',
            'left_at' => now()->subMonth()->toDateString(),
        ]);

        $this->assertEquals('inactive', $m->fresh()->status);
        $this->assertNotNull($m->fresh()->left_at);

        // Reaktivasi via ensureMembership
        $reactivated = InstitutionMembership::ensureMembership($person->id, $inst->id);

        $this->assertEquals('active', $reactivated->status);
        $this->assertNull($reactivated->left_at);
        $this->assertEquals($initialJoinedAt->toDateString(), $reactivated->joined_at->toDateString());
    }

    /**
     * Test 3: Global Person Resolver pada pembuatan Student
     * Jika NIK sama dikirim ke lembaga lain, Person direuse dan membership baru terbit.
     */
    public function test_student_registration_reuses_global_person(): void
    {
        $inst1 = Institution::create(['name' => 'Pondok Putra', 'slug' => 'pp-' . Str::random(5)]);
        $inst2 = Institution::create(['name' => 'Madrasah Aliyah', 'slug' => 'ma2-' . Str::random(5)]);

        // Register student di inst1
        $person = Person::create([
            'nama_lengkap' => 'Muhammad Ali',
            'nik'          => '3201012345670003',
        ]);
        InstitutionMembership::ensureMembership($person->id, $inst1->id);

        Student::create([
            'person_id'      => $person->id,
            'institution_id' => $inst1->id,
            'nis'            => 'NIS-001',
            'angkatan'       => '2026',
            'status'         => 'aktif',
        ]);

        $this->assertEquals(1, Person::where('nik', '3201012345670003')->count());

        // Simulasi controller logic reuse:
        $existingPerson = Person::where('nik', '3201012345670003')->first();
        $this->assertNotNull($existingPerson);

        InstitutionMembership::ensureMembership($existingPerson->id, $inst2->id);

        Student::create([
            'person_id'      => $existingPerson->id,
            'institution_id' => $inst2->id,
            'nis'            => 'NIS-002',
            'angkatan'       => '2026',
            'status'         => 'aktif',
        ]);

        // Hasil: tetap 1 person, 2 student, 2 membership
        $this->assertEquals(1, Person::where('nik', '3201012345670003')->count());
        $this->assertEquals(2, Student::where('person_id', $person->id)->count());
        $this->assertEquals(2, InstitutionMembership::where('person_id', $person->id)->count());
    }

    /**
     * Test 4: Global uniqueness NIK menolak duplikasi Person baru
     */
    public function test_nik_is_globally_unique(): void
    {
        Person::create([
            'nama_lengkap' => 'Person A',
            'nik'          => '3201019999990001',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Person::create([
            'nama_lengkap' => 'Person B (Different person, same NIK)',
            'nik'          => '3201019999990001',
        ]);
    }
}
