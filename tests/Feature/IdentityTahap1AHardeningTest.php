<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Academic\Models\Student;
use Modules\Core\Models\Institution;
use Modules\Core\Models\InstitutionMembership;
use Modules\Core\Models\Person;
use Modules\Core\Models\Role;
use Modules\Core\Models\User;
use Modules\HR\Models\Employee;
use Tests\TestCase;

class IdentityTahap1AHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->cleanupData();
    }

    protected function tearDown(): void
    {
        $this->cleanupData();
        parent::tearDown();
    }

    private function cleanupData(): void
    {
        DB::table('academic_students')->whereIn('nis', ['NIS-MTS-01', 'NIS-MTS-02', 'NIS-TEST-403'])->delete();
        DB::table('hr_employees')->whereIn('nip', ['NIP-MA-01', 'NIP-MA-02'])->delete();
        DB::table('core_institution_memberships')->delete();
        DB::table('core_persons')->whereIn('nik', ['3201018888880001', '3201017777770001', '3201016666660001', '3201016666660002', '3201015555550001', '3201015555550002'])->delete();
        DB::table('core_model_has_roles')->whereIn('model_id', DB::table('core_users')->where('email', 'like', '%@dayama.test')->pluck('id'))->delete();
        DB::table('core_role_user')->whereIn('user_id', DB::table('core_users')->where('email', 'like', '%@dayama.test')->pluck('id'))->delete();
        DB::table('core_users')->where('email', 'like', '%@dayama.test')->delete();
        DB::table('core_roles')->where('name', 'like', 'operator_mts_%')->delete();
        DB::table('core_institutions')->where('name', 'like', '%Testing%')->orWhere('name', 'like', '%Scope Test%')->orWhere('name', 'like', '%Rollback Test%')->orWhere('name', 'like', '%Role Test%')->delete();
    }

    /**
     * Test 1: Data-preserving rollback.
     * Membuat Person + Membership MTs, lalu saat migration decouple di-rollback,
     * core_persons.institution_id harus terisi kembali dengan MTs.
     */
    public function test_data_preserving_rollback_restores_institution_id(): void
    {
        $inst = Institution::create(['name' => 'MTs Rollback Test', 'slug' => 'mts-rb-' . Str::random(5)]);

        $person = Person::create([
            'nama_lengkap' => 'Zaid bin Tsabit',
            'nik'          => '3201018888880001',
        ]);

        $membership = InstitutionMembership::ensureMembership($person->id, $inst->id);

        // Simulasi logika down() dari decouple migration:
        // Tambahkan kembali kolom institution_id, lalu pulihkan nilainya dari memberships
        DB::statement("ALTER TABLE core_persons ADD COLUMN institution_id CHAR(36) NULL");
        try {
            DB::statement("
                UPDATE core_persons p
                INNER JOIN core_institution_memberships m ON p.id = m.person_id
                SET p.institution_id = m.institution_id
                WHERE p.id = '{$person->id}'
            ");

            $restoredInstId = DB::table('core_persons')->where('id', $person->id)->value('institution_id');
            $this->assertEquals($inst->id, $restoredInstId);
        } finally {
            DB::statement("ALTER TABLE core_persons DROP COLUMN institution_id");
        }
    }

    /**
     * Test 2: Regression Controller Store Student & Employee
     * Operator MTs mendaftarkan Student dengan NIK baru -> Person dibuat + Membership MTs.
     * Operator MA mendaftarkan Employee dengan NIK yang SAMA -> Person di-reuse + Membership MA terbit.
     */
    public function test_controllers_reuse_global_person_across_institutions(): void
    {
        $mts = Institution::create(['name' => 'MTs Testing', 'slug' => 'mts-ctrl-' . Str::random(5)]);
        $ma  = Institution::create(['name' => 'MA Testing', 'slug' => 'ma-ctrl-' . Str::random(5)]);

        $adminUser = User::create([
            'id' => (string) Str::orderedUuid(),
            'username' => 'admin_' . Str::random(5),
            'email' => 'admin_' . Str::random(5) . '@dayama.test',
            'password' => bcrypt('password'),
            'is_primary_super_admin' => true,
            'status' => 'active',
        ]);

        // 1. Post Student di MTs
        session(['active_institution_id' => $mts->id]);

        $studentPayload = [
            'nama_lengkap' => 'Hamzah bin Abdul Muthalib',
            'nik'          => '3201017777770001',
            'nis'          => 'NIS-MTS-01',
            'angkatan'     => '2026',
            'gender'       => 'L',
        ];

        $response1 = $this->actingAs($adminUser)
            ->withSession(['active_institution_id' => $mts->id])
            ->post('http://dashboard.dayama.test/academic/students', $studentPayload);

        $response1->assertSessionHasNoErrors();
        $response1->assertRedirect();

        $this->assertEquals(1, Person::where('nik', '3201017777770001')->count());
        $person = Person::where('nik', '3201017777770001')->first();
        $this->assertTrue(InstitutionMembership::where('person_id', $person->id)->where('institution_id', $mts->id)->exists());

        // 2. Post Employee di MA dengan NIK yang sama
        session(['active_institution_id' => $ma->id]);

        $employeePayload = [
            'nama_lengkap' => 'Hamzah bin Abdul Muthalib (Updated)',
            'nik'          => '3201017777770001',
            'nip'          => 'NIP-MA-01',
            'gender'       => 'L',
        ];

        $response2 = $this->actingAs($adminUser)
            ->withSession(['active_institution_id' => $ma->id])
            ->post('http://dashboard.dayama.test/hr/employees', $employeePayload);

        $response2->assertSessionHasNoErrors();

        // Tetap hanya 1 person di database, tapi memiliki 2 membership
        $this->assertEquals(1, Person::where('nik', '3201017777770001')->count());
        $this->assertEquals(2, InstitutionMembership::where('person_id', $person->id)->count());
        $this->assertTrue(InstitutionMembership::where('person_id', $person->id)->where('institution_id', $ma->id)->exists());
    }

    /**
     * Test 3: Person Visibility Sweep pada create() StudentController
     * Operator MTs hanya melihat Person dengan membership MTs di dropdown.
     */
    public function test_institution_operator_does_not_see_other_institution_persons(): void
    {
        $mts = Institution::create(['name' => 'MTs Scope Test', 'slug' => 'mts-scp-' . Str::random(5)]);
        $ma  = Institution::create(['name' => 'MA Scope Test', 'slug' => 'ma-scp-' . Str::random(5)]);

        // Person MTs
        $personMts = Person::create(['nama_lengkap' => 'Santri MTs', 'nik' => '3201016666660001']);
        InstitutionMembership::ensureMembership($personMts->id, $mts->id);

        // Person MA
        $personMa = Person::create(['nama_lengkap' => 'Santri MA Eksklusif', 'nik' => '3201016666660002']);
        InstitutionMembership::ensureMembership($personMa->id, $ma->id);

        // Operator role lembaga
        $roleLembaga = Role::create([
            'id' => (string) Str::orderedUuid(),
            'name' => 'operator_mts_' . Str::random(5),
            'scope' => 'lembaga',
            'guard_name' => 'web',
        ]);
        $roleLembaga->givePermissionTo(['academic.students.view', 'academic.students.create', 'dashboard.view']);
        $operatorMts = User::create([
            'id' => (string) Str::orderedUuid(),
            'username' => 'op_' . Str::random(5),
            'email' => 'op_' . Str::random(5) . '@dayama.test',
            'password' => bcrypt('password'),
            'is_primary_super_admin' => false,
            'status' => 'active',
        ]);
        $operatorMts->assignRole($roleLembaga);

        DB::table('core_role_user')->insert([
            'id' => (string) Str::orderedUuid(),
            'user_id' => $operatorMts->id,
            'role_id' => $roleLembaga->id,
            'institution_id' => $mts->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session(['active_institution_id' => $mts->id]);

        $response = $this->actingAs($operatorMts)
            ->withSession(['active_institution_id' => $mts->id])
            ->get('http://dashboard.dayama.test/academic/students/create');

        if ($response->getStatusCode() === 500) {
            dump($response->exception?->getMessage());
        }

        $response->assertOk();

        $pageProps = $response->viewData('page')['props'] ?? $response->original->getData()['page']['props'];
        $personIds = collect($pageProps['persons'])->pluck('id')->toArray();

        $this->assertContains($personMts->id, $personIds);
        $this->assertNotContains($personMa->id, $personIds);
    }

    /**
     * Test 4: RBAC & Scope Enforcement pada Student & Person
     * - Operator MTs tanpa permission academic.students.create dilarang POST student (403).
     * - Operator MTs dengan persons.edit boleh edit Person MTs, tapi dilarang edit Person eksklusif MA (403).
     */
    public function test_rbac_and_institution_scope_enforcement(): void
    {
        $mts = Institution::create(['name' => 'MTs Role Test', 'slug' => 'mts-rl-' . Str::random(5)]);
        $ma  = Institution::create(['name' => 'MA Role Test', 'slug' => 'ma-rl-' . Str::random(5)]);

        $personMts = Person::create([
            'id' => (string) Str::orderedUuid(),
            'nama_lengkap' => 'Santri MTs RBAC',
            'nik' => '3201015555550001'
        ]);
        InstitutionMembership::ensureMembership($personMts->id, $mts->id);

        $personMa = Person::create([
            'id' => (string) Str::orderedUuid(),
            'nama_lengkap' => 'Santri MA RBAC',
            'nik' => '3201015555550002'
        ]);
        InstitutionMembership::ensureMembership($personMa->id, $ma->id);

        // Role 1: Operator MTs dengan persons.edit & persons.view, tetapi TANPA academic.students.create
        $roleMts = Role::create([
            'id' => (string) Str::orderedUuid(),
            'name' => 'operator_mts_' . Str::random(5),
            'scope' => 'lembaga',
            'guard_name' => 'web',
        ]);
        $roleMts->givePermissionTo(['persons.edit', 'persons.view', 'academic.students.view', 'dashboard.view']);

        $operatorMts = User::create([
            'id' => (string) Str::orderedUuid(),
            'username' => 'op_mts_' . Str::random(5),
            'email' => 'op_mts_' . Str::random(5) . '@dayama.test',
            'password' => bcrypt('password'),
            'is_primary_super_admin' => false,
            'status' => 'active',
        ]);
        $operatorMts->assignRole($roleMts);

        // Pasang di core_role_user agar ActiveInstitution & ScopeRule mengenalinya
        DB::table('core_role_user')->insert([
            'id' => (string) Str::orderedUuid(),
            'user_id' => $operatorMts->id,
            'role_id' => $roleMts->id,
            'institution_id' => $mts->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session(['active_institution_id' => $mts->id]);

        // 1. Coba POST student tanpa permission academic.students.create -> 403
        $studentRes = $this->actingAs($operatorMts)
            ->withSession(['active_institution_id' => $mts->id])
            ->post('http://dashboard.dayama.test/academic/students', [
                'nama_lengkap' => 'Anak Baru',
                'nis' => 'NIS-TEST-403',
                'angkatan' => '2026',
            ]);
        $studentRes->assertForbidden();

        // 2. Coba update Person eksklusif MA oleh operator MTs -> 403 (ScopeRule memblokir)
        $personMaRes = $this->actingAs($operatorMts)
            ->withSession(['active_institution_id' => $mts->id])
            ->put("http://dashboard.dayama.test/persons/{$personMa->id}", [
                'nama_depan' => 'Hacker MA',
            ]);
        $personMaRes->assertForbidden();

        // 3. Update Person MTs oleh operator MTs -> Berhasil (Allowed 302 redirect)
        $personMts = $personMts->fresh();

        $personMtsRes = $this->actingAs($operatorMts)
            ->withSession(['active_institution_id' => $mts->id])
            ->put("http://dashboard.dayama.test/persons/{$personMts->id}", [
                'nama_depan' => 'Santri MTs Updated',
            ]);
        if ($personMtsRes->getStatusCode() === 403) {
            dump($personMtsRes->exception?->getMessage());
        }
        $personMtsRes->assertRedirect();
    }
}
