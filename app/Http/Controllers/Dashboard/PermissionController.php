<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Gate;
use Modules\Core\Models\Permission;
use Modules\Core\Models\PermissionGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController
{
    /** All CMS module definitions */
    private const CMS_MODULES = [
        'dashboard'  => ['view'],
        'posts'      => ['view', 'create', 'edit', 'delete', 'publish', 'restore', 'force-delete'],
        'pages'      => ['view', 'create', 'edit', 'delete', 'publish'],
        'media'      => ['view', 'upload', 'edit', 'delete'],
        'comments'   => ['view', 'reply', 'delete', 'moderate'],
        'categories' => ['view', 'create', 'edit', 'delete'],
        'tags'       => ['view', 'create', 'edit', 'delete'],
        'users'      => ['view', 'create', 'edit', 'delete'],
        'roles'      => ['view', 'create', 'edit', 'delete'],
        'settings'   => ['view', 'update'],
        'analytics'  => ['view'],
    ];

    /** Scoped actions (will generate own + all variants) */
    private const SCOPED_ACTIONS = ['edit', 'delete', 'publish'];

    public function index(): Response
    {
        Gate::authorize('viewAny', \Modules\Core\Models\Permission::class);
        $permissions = Permission::with('permissionGroups')
            ->orderBy('module')->orderBy('action')->orderBy('scope')->orderBy('name')
            ->withCount('roles')
            ->get()
            ->map(fn ($perm) => [
                'id'          => $perm->id,
                'name'        => $perm->name,
                'module'      => $perm->module,
                'action'      => $perm->action,
                'scope'       => $perm->scope,
                'description' => $perm->description,
                'guard_name'  => $perm->guard_name,
                'roles_count' => (int) $perm->roles_count,
                'created_at'  => $perm->created_at,
                'group_ids'   => $perm->relationLoaded('permissionGroups')
                    ? $perm->permissionGroups->pluck('id')->toArray()
                    : [],
            ]);

        // Group by module
        $grouped = [];
        foreach ($permissions as $perm) {
            $mod = $perm['module'] ?: $this->extractModule($perm['name']);
            $grouped[$mod][] = $perm;
        }

        $permissionGroups = PermissionGroup::orderBy('sort_order')->get(['id', 'name', 'slug', 'icon', 'color']);

        return Inertia::render('Permissions/Index', [
            'permissions'      => $permissions,
            'grouped'          => $grouped,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module'       => ['required', 'string', 'max:60'],
            'action'       => ['required', 'string', 'max:60'],
            'scope'        => ['nullable', 'string', 'max:30'],
            'description'  => ['nullable', 'string'],
            'guard_name'   => ['nullable', 'string'],
            'group_ids'    => ['nullable', 'array'],
            'group_ids.*'  => ['string', 'uuid', 'exists:core_permission_groups,id'],
        ]);

        $scope = $validated['scope'] ?? null;
        $name = $scope
            ? "{$validated['module']}.{$validated['action']}.{$scope}"
            : "{$validated['module']}.{$validated['action']}";

        $permission = Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => $validated['guard_name'] ?? 'web'],
            [
                'module'      => $validated['module'],
                'action'      => $validated['action'],
                'scope'       => $scope,
                'description' => $validated['description'] ?? null,
            ]
        );

        if (isset($validated['group_ids'])) {
            $permission->permissionGroups()->sync($validated['group_ids']);
        }

        return redirect()->route('dashboard.permissions.index')->with('success', 'Permission created.');
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
            'group_ids'   => ['nullable', 'array'],
            'group_ids.*' => ['string', 'uuid', 'exists:core_permission_groups,id'],
        ]);

        $permission->update(['description' => $validated['description'] ?? null]);

        if (isset($validated['group_ids'])) {
            $permission->permissionGroups()->sync($validated['group_ids']);
        }

        return redirect()->route('dashboard.permissions.index')->with('success', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        Gate::authorize('delete', $permission);
        $permission->delete();

        return redirect()->route('dashboard.permissions.index')->with('success', 'Permission deleted.');
    }

    /**
     * Seed all CMS default permissions (idempotent).
     */
    public function seed(): RedirectResponse
    {
        Gate::authorize('create', \Modules\Core\Models\Permission::class);
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::CMS_MODULES as $module => $actions) {
            foreach ($actions as $action) {
                // Base permission e.g. posts.view
                Permission::firstOrCreate(
                    ['name' => "{$module}.{$action}", 'guard_name' => 'web'],
                    ['module' => $module, 'action' => $action, 'scope' => null]
                );

                // Scoped variants for certain actions
                if (in_array($action, self::SCOPED_ACTIONS)) {
                    foreach (['own', 'all'] as $scope) {
                        Permission::firstOrCreate(
                            ['name' => "{$module}.{$action}.{$scope}", 'guard_name' => 'web'],
                            ['module' => $module, 'action' => $action, 'scope' => $scope]
                        );
                    }
                }
            }
        }

        return redirect()->route('dashboard.permissions.index')->with('success', 'CMS default permissions seeded successfully.');
    }

    private function extractModule(string $permName): string
    {
        if (str_contains($permName, '.')) {
            return explode('.', $permName)[0];
        }
        $lastDash = strrpos($permName, '-');
        return $lastDash !== false ? substr($permName, $lastDash + 1) : 'other';
    }
}
