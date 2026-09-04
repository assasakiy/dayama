<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\StoreRoleRequest;
use App\Http\Requests\Dashboard\UpdateRoleRequest;
use App\Services\RoleAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Models\Permission;
use Modules\Core\Models\PermissionGroup;
use Modules\Core\Models\Role;
use Modules\Core\Models\User;

class RoleController
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Role::class);
        $roles = Role::with(['permissions:id,name'])->withCount('permissions', 'users')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'guard_name' => $role->guard_name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'color' => $role->color,
                'icon' => $role->icon,
                'is_system' => (bool) $role->is_system,
                'status' => $role->status ?? 'active',
                'scope' => $role->scope,
                'sort_order' => $role->sort_order,
                'rank' => $role->rank,
                'permissions_count' => (int) $role->permissions_count,
                'permission_names' => $role->permissions->pluck('name'),
                'users_count' => (int) $role->users_count,
                'created_at' => $role->created_at,
                'can' => [
                    'update' => request()->user()->can('update', $role),
                    'delete' => request()->user()->can('delete', $role),
                ],
            ]);

        // Build module-grouped permissions for the permission matrix
        $permissions = Permission::orderBy('module')->orderBy('action')->orderBy('scope')->get();

        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $module = $perm->module ?: $this->extractModule($perm->name);
            if (! isset($groupedPermissions[$module])) {
                $groupedPermissions[$module] = [];
            }
            $groupedPermissions[$module][] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'module' => $perm->module,
                'action' => $perm->action ?: $this->extractAction($perm->name),
                'scope' => $perm->scope,
                'description' => $perm->description,
            ];
        }

        // Build permission-group-based grouping
        $permissionGroups = PermissionGroup::with('permissions:id,name,module,action,scope,description')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'icon' => $group->icon,
                'color' => $group->color,
                'permissions' => $group->permissions->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'module' => $p->module,
                    'action' => $p->action,
                    'scope' => $p->scope,
                    'description' => $p->description,
                ]),
            ]);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'groupedPermissions' => $groupedPermissions,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
            'display_name' => $validated['display_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'slug' => $validated['slug'] ?? null,
            'color' => $validated['color'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'scope' => $validated['scope'] ?? null,
            'is_system' => $validated['is_system'] ?? false,
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('dashboard.roles.index')->with('success', 'Role created.');
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $role->update([
            'name' => $role->is_system ? $role->name : $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
            'display_name' => $validated['display_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'slug' => $validated['slug'] ?? null,
            'color' => $validated['color'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'scope' => $validated['scope'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('dashboard.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('delete', $role);
        if ($role->is_system) {
            return redirect()->route('dashboard.roles.index')->with('error', 'System roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('dashboard.roles.index')->with('success', 'Role deleted.');
    }

    public function duplicate(Role $role): RedirectResponse
    {
        Gate::authorize('create', Role::class);
        $newRole = Role::create([
            'name' => $role->name.' (Copy)',
            'guard_name' => $role->guard_name,
            'display_name' => $role->display_name ? $role->display_name.' (Copy)' : null,
            'description' => $role->description,
            'color' => $role->color,
            'icon' => $role->icon,
            'status' => 'active',
            'is_system' => false,
        ]);

        $newRole->syncPermissions($role->permissions);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role duplicated.');
    }

    public function assignUsers(Request $request, Role $role): RedirectResponse
    {
        Gate::authorize('update', $role);
        $request->validate([
            'user_ids' => ['array'],
            'user_ids.*' => ['uuid'],
            'institution_id' => [
                'nullable',
                'uuid',
                'exists:core_institutions,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($role): void {
                    if ($role->scope === 'lembaga' && empty($value)) {
                        $fail('Institution wajib saat menugaskan role lembaga.');
                    }
                    if ($role->scope !== 'lembaga' && ! empty($value)) {
                        $fail('Institution tidak diizinkan untuk non-institution role.');
                    }
                },
            ],
        ]);

        $userIds = $request->input('user_ids', []);

        $assignments = app(RoleAssignmentService::class);
        $existingUsers = $role->users()->pluck('core_model_has_roles.model_id')->toArray();

        foreach (array_diff($existingUsers, $userIds) as $userId) {
            $user = User::find($userId);
            if ($user) {
                $assignments->remove($user, $role);
            }
        }

        $institutionId = $request->input('institution_id');
        foreach (User::whereIn('id', $userIds)->get() as $user) {
            $assignments->assign($user, $role, $institutionId);
        }

        return redirect()->route('dashboard.roles.index')->with('success', 'Users assigned to role.');
    }

    private function extractModule(string $permName): string
    {
        // Handles both "module.action" and "action-module" legacy format
        if (str_contains($permName, '.')) {
            return explode('.', $permName)[0];
        }
        $lastDash = strrpos($permName, '-');

        return $lastDash !== false ? substr($permName, $lastDash + 1) : 'other';
    }

    private function extractAction(string $permName): string
    {
        if (str_contains($permName, '.')) {
            $parts = explode('.', $permName);

            return $parts[1] ?? $permName;
        }
        $lastDash = strrpos($permName, '-');

        return $lastDash !== false ? substr($permName, 0, $lastDash) : $permName;
    }
}
