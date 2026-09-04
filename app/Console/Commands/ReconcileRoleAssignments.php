<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\RoleAssignmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\RoleUser;

final class ReconcileRoleAssignments extends Command
{
    protected $signature = 'rbac:reconcile-role-assignments {--apply : Repair valid pivots missing their Spatie assignment}';

    protected $description = 'Audit role assignment consistency; dry-run by default';

    public function handle(RoleAssignmentService $assignments): int
    {
        $modelType = config('auth.providers.users.model');
        $roleTable = config('permission.table_names.roles');
        $spatieTable = config('permission.table_names.model_has_roles');

        $orphanRoles = DB::table('core_role_user as ru')->leftJoin("{$roleTable} as r", 'r.id', '=', 'ru.role_id')->whereNull('r.id')->count();
        $orphanUsers = DB::table('core_role_user as ru')->leftJoin('core_users as u', 'u.id', '=', 'ru.user_id')->whereNull('u.id')->count();
        $missingInstitutions = DB::table('core_role_user as ru')->leftJoin('core_institutions as i', 'i.id', '=', 'ru.institution_id')->whereNotNull('ru.institution_id')->whereNull('i.id')->count();
        $inactiveInstitutions = DB::table('core_role_user as ru')->join('core_institutions as i', 'i.id', '=', 'ru.institution_id')->where('i.is_active', false)->count();
        $invalidBindings = DB::table('core_role_user as ru')->join("{$roleTable} as r", 'r.id', '=', 'ru.role_id')->where(fn ($query) => $query->where('r.scope', '!=', 'lembaga')->orWhereNull('ru.institution_id'))->count();
        $duplicates = DB::table('core_role_user')->select('user_id', 'role_id', 'institution_id')->groupBy('user_id', 'role_id', 'institution_id')->havingRaw('COUNT(*) > 1')->get()->count();

        $missingSpatie = RoleUser::query()
            ->with(['user.roles', 'role', 'institution'])
            ->whereHas('user')
            ->whereHas('role', fn ($query) => $query->where('scope', 'lembaga'))
            ->whereNotNull('institution_id')
            ->whereHas('institution', fn ($query) => $query->where('is_active', true))
            ->orderBy('user_id')->orderBy('role_id')->orderBy('institution_id')
            ->get()
            ->filter(fn (RoleUser $pivot): bool => ! $pivot->user->roles->contains('id', $pivot->role_id));

        $spatieWithoutBinding = DB::table("{$spatieTable} as mr")
            ->join("{$roleTable} as r", 'r.id', '=', 'mr.role_id')
            ->where('mr.model_type', $modelType)
            ->where('r.scope', 'lembaga')
            ->whereNotExists(fn ($query) => $query->selectRaw('1')->from('core_role_user as ru')->whereColumn('ru.user_id', 'mr.model_id')->whereColumn('ru.role_id', 'mr.role_id'))
            ->count();

        $this->table(['Anomaly', 'Count'], [
            ['orphan role', $orphanRoles],
            ['orphan user', $orphanUsers],
            ['institution missing', $missingInstitutions],
            ['institution inactive', $inactiveInstitutions],
            ['invalid institution binding', $invalidBindings],
            ['duplicate assignments', $duplicates],
            ['Spatie role missing', $missingSpatie->count()],
            ['institutional role without binding', $spatieWithoutBinding],
        ]);

        if (! $this->option('apply')) {
            $this->info('Dry-run only. Use --apply to repair valid pivots missing Spatie roles.');

            return self::SUCCESS;
        }

        foreach ($missingSpatie as $pivot) {
            $assignments->assignInstitution($pivot->user, $pivot->role, $pivot->institution_id);
        }

        $this->info("Repaired {$missingSpatie->count()} assignment(s).");

        return self::SUCCESS;
    }
}
