<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Core\Models\Institution;
use Modules\Core\Models\Role;
use Modules\Core\Models\RoleUser;
use Modules\Core\Models\User;

final class RoleAssignmentService
{
    public function assign(User $user, Role|string $role, ?string $institutionId = null): void
    {
        $role = $this->resolveRole($role);

        if ($role->scope === 'lembaga') {
            if ($institutionId === null) {
                throw ValidationException::withMessages(['institution_id' => 'Institution wajib untuk role lembaga.']);
            }

            $this->assignInstitution($user, $role, $institutionId);

            return;
        }

        if ($institutionId !== null) {
            throw ValidationException::withMessages(['institution_id' => 'Institution tidak diizinkan untuk non-institution role.']);
        }

        DB::transaction(function () use ($user, $role): void {
            $user->assignRole($role);
            RoleUser::where('user_id', $user->id)->where('role_id', $role->id)->delete();
        });
    }

    public function remove(User $user, Role|string $role): void
    {
        $role = $this->resolveRole($role);

        DB::transaction(function () use ($user, $role): void {
            RoleUser::where('user_id', $user->id)->where('role_id', $role->id)->delete();
            $user->removeRole($role);
        });
    }

    public function sync(User $user, array $assignments): void
    {
        $normalized = $this->normalize($assignments);

        DB::transaction(function () use ($user, $normalized): void {
            RoleUser::where('user_id', $user->id)->delete();

            foreach ($normalized as $assignment) {
                if ($assignment['role']->scope === 'lembaga') {
                    RoleUser::firstOrCreate([
                        'user_id' => $user->id,
                        'role_id' => $assignment['role']->id,
                        'institution_id' => $assignment['institution_id'],
                    ]);
                }
            }

            $user->syncRoles($normalized->pluck('role')->unique('id')->values());
        });
    }

    public function bulkAssign(iterable $users, Role|string $role, ?string $institutionId = null): void
    {
        $resolvedRole = $this->resolveRole($role);
        $resolvedInstitutionId = null;

        if ($resolvedRole->scope === 'lembaga') {
            if ($institutionId === null) {
                throw ValidationException::withMessages(['institution_id' => 'Institution wajib untuk role lembaga.']);
            }
            $resolvedInstitutionId = $this->assertActiveInstitution($institutionId);
        } elseif ($institutionId !== null) {
            throw ValidationException::withMessages(['institution_id' => 'Institution tidak diizinkan untuk non-institution role.']);
        }

        DB::transaction(function () use ($users, $resolvedRole, $resolvedInstitutionId): void {
            foreach ($users as $user) {
                if ($resolvedInstitutionId !== null) {
                    $user->assignRole($resolvedRole);
                    RoleUser::firstOrCreate([
                        'user_id' => $user->id,
                        'role_id' => $resolvedRole->id,
                        'institution_id' => $resolvedInstitutionId,
                    ]);

                    continue;
                }

                $user->assignRole($resolvedRole);
                RoleUser::where('user_id', $user->id)->where('role_id', $resolvedRole->id)->delete();
            }
        });
    }

    public function assignInstitution(User $user, Role|string $role, string $institutionId): void
    {
        $role = $this->resolveRole($role);

        if ($role->scope !== 'lembaga') {
            throw ValidationException::withMessages(['role' => 'Role bukan role lembaga.']);
        }

        $activeInstitutionId = $this->assertActiveInstitution($institutionId);

        DB::transaction(function () use ($user, $role, $activeInstitutionId): void {
            $user->assignRole($role);
            RoleUser::firstOrCreate([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'institution_id' => $activeInstitutionId,
            ]);
        });
    }

    public function removeInstitution(User $user, Role|string $role, string $institutionId): void
    {
        $role = $this->resolveRole($role);

        DB::transaction(function () use ($user, $role, $institutionId): void {
            RoleUser::where('user_id', $user->id)
                ->where('role_id', $role->id)
                ->where('institution_id', $institutionId)
                ->delete();

            if (! RoleUser::where('user_id', $user->id)->where('role_id', $role->id)->exists()) {
                $user->removeRole($role);
            }
        });
    }

    private function normalize(array $assignments): Collection
    {
        return collect($assignments)->map(function (array $assignment): array {
            $role = $this->resolveRole($assignment['role']);
            $institutionId = $assignment['institution_id'] ?? $assignment['institution'] ?? null;

            if ($role->scope === 'lembaga') {
                if ($institutionId === null) {
                    throw ValidationException::withMessages(['assignments' => "Institution wajib untuk role {$role->name}."]);
                }

                return [
                    'role' => $role,
                    'institution_id' => $this->assertActiveInstitution((string) $institutionId),
                ];
            }

            if ($institutionId !== null) {
                throw ValidationException::withMessages(['assignments' => "Institution tidak diizinkan untuk non-institution role {$role->name}."]);
            }

            return [
                'role' => $role,
                'institution_id' => null,
            ];
        })->unique(fn (array $assignment): string => $assignment['role']->id.'|'.($assignment['institution_id'] ?? ''))
            ->values();
    }

    private function assertActiveInstitution(string $institutionId): string
    {
        $institution = Institution::where('id', $institutionId)->first(['id', 'is_active']);

        if (! $institution || ! $institution->is_active) {
            throw ValidationException::withMessages(['institution_id' => 'Institution tidak ditemukan atau tidak aktif.']);
        }

        return (string) $institution->id;
    }

    private function resolveRole(Role|string $role): Role
    {
        return $role instanceof Role ? $role : Role::findByName($role, 'web');
    }
}
