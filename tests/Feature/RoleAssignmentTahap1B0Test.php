<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\RoleAssignmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Models\Institution;
use Modules\Core\Models\Role;
use Modules\Core\Models\User;
use Tests\TestCase;

final class RoleAssignmentTahap1B0Test extends TestCase
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

    public function test_institution_assignments_keep_spatie_until_last_binding_removed(): void
    {
        [$user, $role, $ma, $mts] = $this->fixtures();
        $service = app(RoleAssignmentService::class);

        $service->assignInstitution($user, $role, $ma->id);
        $service->assignInstitution($user, $role, $mts->id);

        $this->assertTrue($user->fresh()->hasRole($role));
        $this->assertDatabaseCount('core_role_user', 2);

        $service->removeInstitution($user, $role, $ma->id);
        $this->assertTrue($user->fresh()->hasRole($role));

        $service->removeInstitution($user, $role, $mts->id);
        $this->assertFalse($user->fresh()->hasRole($role));
        $this->assertDatabaseCount('core_role_user', 0);
    }

    public function test_sync_accepts_multi_institution_assignment_contract(): void
    {
        [$user, $role, $ma, $mts] = $this->fixtures();
        $editor = Role::create(['name' => 'editor-'.Str::random(8), 'guard_name' => 'web']);

        app(RoleAssignmentService::class)->sync($user, [
            ['role' => $role->name, 'institution' => $ma->id],
            ['role' => $role->name, 'institution_id' => $mts->id],
            ['role' => $editor->name, 'institution' => null],
        ]);

        $this->assertTrue($user->fresh()->hasAllRoles([$role, $editor]));
        $this->assertDatabaseHas('core_role_user', ['user_id' => $user->id, 'role_id' => $role->id, 'institution_id' => $ma->id]);
        $this->assertDatabaseHas('core_role_user', ['user_id' => $user->id, 'role_id' => $role->id, 'institution_id' => $mts->id]);
    }

    public function test_reconciliation_is_dry_run_and_apply_only_restores_missing_spatie_role(): void
    {
        [$user, $role, $ma] = $this->fixtures();
        DB::table('core_role_user')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'role_id' => $role->id,
            'institution_id' => $ma->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('rbac:reconcile-role-assignments')->assertSuccessful();
        $this->assertFalse($user->fresh()->hasRole($role));

        $this->artisan('rbac:reconcile-role-assignments', ['--apply' => true])->assertSuccessful();
        $this->assertTrue($user->fresh()->hasRole($role));
        $this->assertDatabaseCount('core_role_user', 1);
    }

    public function test_bulk_assign_uses_single_transaction_and_binds_institution(): void
    {
        [$userA, $role, $ma] = $this->fixtures();
        $userB = User::create([
            'username' => 'user-'.Str::random(8),
            'email' => Str::random(8).'@example.test',
            'password' => 'password',
        ]);

        app(RoleAssignmentService::class)->bulkAssign([$userA, $userB], $role, $ma->id);

        $this->assertTrue($userA->fresh()->hasRole($role));
        $this->assertTrue($userB->fresh()->hasRole($role));
        $this->assertDatabaseHas('core_role_user', ['user_id' => $userA->id, 'role_id' => $role->id, 'institution_id' => $ma->id]);
        $this->assertDatabaseHas('core_role_user', ['user_id' => $userB->id, 'role_id' => $role->id, 'institution_id' => $ma->id]);
    }

    public function test_institutional_assignment_rejects_missing_or_inactive_institution(): void
    {
        [$user, $role] = $this->fixtures();
        $inactive = Institution::create(['name' => 'Inactive', 'slug' => 'in-'.Str::random(8), 'is_active' => false]);
        $service = app(RoleAssignmentService::class);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $service->assignInstitution($user, $role, $inactive->id);
    }

    public function test_non_institutional_role_rejects_institution_binding(): void
    {
        [$user, , $ma] = $this->fixtures();
        $editor = Role::create(['name' => 'editor-'.Str::random(8), 'guard_name' => 'web']);
        $service = app(RoleAssignmentService::class);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $service->assign($user, $editor, $ma->id);
    }

    public function test_reconciliation_does_not_guess_institution_for_spatie_only_institutional_role(): void
    {
        [$user, $role] = $this->fixtures();
        $user->assignRole($role);

        $this->artisan('rbac:reconcile-role-assignments')->assertSuccessful();
        $this->assertDatabaseCount('core_role_user', 0);

        $this->artisan('rbac:reconcile-role-assignments', ['--apply' => true])->assertSuccessful();
        $this->assertDatabaseCount('core_role_user', 0);
    }

    public function test_role_controller_assign_users_fails_closed_for_institutional_role(): void
    {
        [$user, $role] = $this->fixtures();
        $admin = User::create([
            'username' => 'admin-'.Str::random(8),
            'email' => Str::random(8).'@example.test',
            'password' => 'password',
            'is_primary_super_admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['_token' => 'test-token'])
            ->from('http://dashboard.dayama.test/roles')
            ->post('http://dashboard.dayama.test/roles/'.$role->id.'/assign-users', [
                '_token' => 'test-token',
                'user_ids' => [$user->id],
            ]);

        $response->assertSessionHasErrors(['institution_id']);
        $this->assertFalse($user->fresh()->hasRole($role));
        $this->assertDatabaseCount('core_role_user', 0);
    }

    private function fixtures(): array
    {
        $user = User::create([
            'username' => 'user-'.Str::random(8),
            'email' => Str::random(8).'@example.test',
            'password' => 'password',
        ]);
        $role = Role::create(['name' => 'guru-'.Str::random(8), 'guard_name' => 'web', 'scope' => 'lembaga']);
        $ma = Institution::create(['name' => 'MA', 'slug' => 'ma-'.Str::random(8), 'is_active' => true]);
        $mts = Institution::create(['name' => 'MTs', 'slug' => 'mts-'.Str::random(8), 'is_active' => true]);

        return [$user, $role, $ma, $mts];
    }
}
