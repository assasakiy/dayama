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

class PermissionGroupController
{
    public function index(): Response
    {
        Gate::authorize('viewAny', PermissionGroup::class);
        $groups = PermissionGroup::with(['permissions:id,name'])->withCount('permissions')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn ($g) => [
                'id'                => $g->id,
                'name'              => $g->name,
                'slug'              => $g->slug,
                'description'       => $g->description,
                'icon'              => $g->icon,
                'color'             => $g->color,
                'sort_order'        => $g->sort_order,
                'permissions_count' => (int) $g->permissions_count,
                'permission_names'  => $g->permissions->pluck('name'),
                'created_at'        => $g->created_at,
            ]);

        $permissions = Permission::orderBy('module')->orderBy('action')
            ->get(['id', 'name', 'module', 'action', 'scope'])
            ->map(fn ($p) => [
                'id'     => $p->id,
                'name'   => $p->name,
                'module' => $p->module,
                'action' => $p->action,
                'scope'  => $p->scope,
            ]);

        // Grouped for the matrix
        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $mod = $perm['module'] ?: explode('.', $perm['name'])[0];
            $groupedPermissions[$mod][] = $perm;
        }

        return Inertia::render('PermissionGroups/Index', [
            'groups'             => $groups,
            'permissions'        => $permissions,
            'groupedPermissions' => $groupedPermissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'slug'           => ['nullable', 'string', 'max:100', 'unique:permission_groups'],
            'description'    => ['nullable', 'string'],
            'icon'           => ['nullable', 'string', 'max:50'],
            'color'          => ['nullable', 'string', 'max:20'],
            'sort_order'     => ['nullable', 'integer'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['uuid'],
        ]);

        $group = PermissionGroup::create([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? null,
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'color'       => $validated['color'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        if (! empty($validated['permission_ids'])) {
            $group->permissions()->sync($validated['permission_ids']);
        }

        return redirect()->route('dashboard.permission-groups.index')->with('success', 'Permission group created.');
    }

    public function update(Request $request, PermissionGroup $permissionGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'slug'           => ['nullable', 'string', 'max:100', 'unique:permission_groups,slug,' . $permissionGroup->id],
            'description'    => ['nullable', 'string'],
            'icon'           => ['nullable', 'string', 'max:50'],
            'color'          => ['nullable', 'string', 'max:20'],
            'sort_order'     => ['nullable', 'integer'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['uuid'],
        ]);

        $permissionGroup->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? null,
            'description' => $validated['description'] ?? null,
            'icon'        => $validated['icon'] ?? null,
            'color'       => $validated['color'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        $permissionGroup->permissions()->sync($validated['permission_ids'] ?? []);

        return redirect()->route('dashboard.permission-groups.index')->with('success', 'Permission group updated.');
    }

    public function destroy(PermissionGroup $permissionGroup): RedirectResponse
    {
        Gate::authorize('delete', $permissionGroup);
        $permissionGroup->permissions()->detach();
        $permissionGroup->delete();

        return redirect()->route('dashboard.permission-groups.index')->with('success', 'Permission group deleted.');
    }
}
