<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /** CMS modules and their actions */
    private const MODULES = [
        'dashboard'       => ['view'],
        'posts'           => ['view', 'create', 'edit', 'delete', 'publish', 'restore', 'force-delete'],

        'media'           => ['view', 'upload', 'edit', 'delete'],
        'comments'        => ['view', 'reply', 'delete', 'moderate'],
        'categories'      => ['view', 'create', 'edit', 'delete'],
        'tags'            => ['view', 'create', 'edit', 'delete'],
        'users'           => ['view', 'create', 'edit', 'delete'],
        'roles'           => ['view', 'create', 'edit', 'delete'],
        'settings'        => ['view', 'update'],
        'analytics'       => ['view'],
        'activity_logs'   => ['view', 'delete'],
        'bookmarks'       => ['view'],
        'reading_history' => ['view'],
    ];

    /** Modules that use ownership scoping */
    private const SCOPED_MODULES = ['posts', 'media', 'comments', 'bookmarks', 'reading_history', 'activity_logs'];

    /** Actions that get own/all scope variants */
    private const SCOPED_ACTIONS = ['view', 'edit', 'delete', 'publish', 'restore', 'force-delete'];

    /** Default CMS roles with metadata */
    private const DEFAULT_ROLES = [
        [
            'name'         => 'Super Admin',
            'display_name' => 'Super Admin',
            'description'  => 'Has full access to every feature and setting.',
            'color'        => '#7c3aed',
            'icon'         => 'crown',
            'is_system'    => true,
            'sort_order'   => 0,
            'rank'         => 100,
        ],
        [
            'name'         => 'Administrator',
            'display_name' => 'Administrator',
            'description'  => 'Can manage the entire website except system settings.',
            'color'        => '#dc2626',
            'icon'         => 'shield',
            'is_system'    => true,
            'sort_order'   => 1,
            'rank'         => 80,
        ],
        [
            'name'         => 'Editor',
            'display_name' => 'Editor',
            'description'  => 'Can manage all posts, comments, categories, and tags.',
            'color'        => '#2563eb',
            'icon'         => 'pen-tool',
            'is_system'    => false,
            'sort_order'   => 2,
            'rank'         => 60,
        ],
        [
            'name'         => 'Author',
            'display_name' => 'Author',
            'description'  => 'Can create, edit, publish, and delete their own posts.',
            'color'        => '#059669',
            'icon'         => 'feather',
            'is_system'    => false,
            'sort_order'   => 3,
            'rank'         => 40,
        ],
        [
            'name'         => 'Contributor',
            'display_name' => 'Contributor',
            'description'  => 'Can create and edit their own posts but cannot publish.',
            'color'        => '#d97706',
            'icon'         => 'edit-3',
            'is_system'    => false,
            'sort_order'   => 4,
            'rank'         => 20,
        ],
        [
            'name'         => 'Subscriber',
            'display_name' => 'Subscriber',
            'description'  => 'Can read content and manage their own profile.',
            'color'        => '#6b7280',
            'icon'         => 'user',
            'is_system'    => false,
            'sort_order'   => 5,
            'rank'         => 10,
        ],
    ];

    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // -------------------------
        // 1. Seed all CMS permissions
        // -------------------------
        $allPermissions = collect();

        foreach (self::MODULES as $module => $actions) {
            foreach ($actions as $action) {
                // Scoped variants: posts.edit.own / posts.edit.all
                if (in_array($module, self::SCOPED_MODULES) && in_array($action, self::SCOPED_ACTIONS)) {
                    foreach (['own', 'all'] as $scope) {
                        $scopedPerm = Permission::firstOrCreate(
                            ['name' => "{$module}.{$action}.{$scope}", 'guard_name' => 'web'],
                            ['module' => $module, 'action' => $action, 'scope' => $scope]
                        );
                        $allPermissions->push($scopedPerm);
                    }
                } else {
                    // Base: posts.create, dashboard.view
                    $perm = Permission::firstOrCreate(
                        ['name' => "{$module}.{$action}", 'guard_name' => 'web'],
                        ['module' => $module, 'action' => $action, 'scope' => null]
                    );
                    $allPermissions->push($perm);
                }
            }
        }

        // -------------------------
        // 2. Seed roles
        // -------------------------
        foreach (self::DEFAULT_ROLES as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'web'],
                [
                    'display_name' => $roleData['display_name'],
                    'description'  => $roleData['description'],
                    'color'        => $roleData['color'],
                    'icon'         => $roleData['icon'],
                    'is_system'    => $roleData['is_system'],
                    'sort_order'   => $roleData['sort_order'],
                    'rank'         => $roleData['rank'],
                ]
            );
        }

        // -------------------------
        // 3. Assign permissions to roles
        // -------------------------
        $byName = fn (string $name) => $allPermissions->firstWhere('name', $name);
        $byModule = fn (string $module) => $allPermissions->where('module', $module)->values();
        $byModuleAction = fn (string $module, string $action) => $allPermissions
            ->filter(fn ($p) => $p->module === $module && $p->action === $action)->values();

        // Super Admin — all permissions
        Role::where('name', 'Super Admin')->first()
            ->syncPermissions($allPermissions->all());

        // Administrator — all permissions
        Role::where('name', 'Administrator')->first()
            ->syncPermissions($allPermissions->all());

        // Editor — all content + comments, no user/role/settings management
        $editorPerms = collect();
        foreach (['posts', 'categories', 'tags', 'comments', 'media'] as $mod) {
            $editorPerms = $editorPerms->merge($byModule($mod));
        }
        $editorPerms->push($byName('dashboard.view'));
        $editorPerms->push($byName('analytics.view'));
        $editorPerms->push($byName('activity_logs.view.own'));
        Role::where('name', 'Editor')->first()->syncPermissions($editorPerms->filter()->all());

        // Author — create + own posts, upload media, view/reply comments
        $authorPerms = collect([
            $byName('dashboard.view'),
            $byName('posts.view.all'),
            $byName('posts.create'),
            $byName('posts.edit.own'),
            $byName('posts.delete.own'),
            $byName('posts.publish.own'),
            $byName('media.view.all'),
            $byName('media.upload'),
            $byName('media.edit.own'),
            $byName('media.delete.own'),
            $byName('comments.view.all'),
            $byName('comments.reply'),
            $byName('categories.view'),
            $byName('tags.view'),
            $byName('activity_logs.view.own'),
        ])->filter();
        Role::where('name', 'Author')->first()->syncPermissions($authorPerms->all());

        // Contributor — create/edit own posts (no publish)
        $contributorPerms = collect([
            $byName('dashboard.view'),
            $byName('posts.view.all'),
            $byName('posts.create'),
            $byName('posts.edit.own'),
            $byName('media.view.all'),
            $byName('media.upload'),
            $byName('comments.view.all'),
            $byName('categories.view'),
            $byName('tags.view'),
            $byName('activity_logs.view.own'),
        ])->filter();
        Role::where('name', 'Contributor')->first()->syncPermissions($contributorPerms->all());

        // Subscriber — minimal access + personal content features
        $subscriberPerms = collect([
            $byName('dashboard.view'),
            $byName('comments.view.all'),
            $byName('comments.reply'),
            $byName('bookmarks.view.own'),
            $byName('reading_history.view.own'),
            $byName('activity_logs.view.own'),
        ])->filter();
        Role::where('name', 'Subscriber')->first()->syncPermissions($subscriberPerms->all());

        // -------------------------
        // 4. Seed Permission Groups
        // -------------------------
        $groupDefs = [
            [
                'name'        => 'Content',
                'slug'        => 'content',
                'description' => 'Posts, Pages, Categories, and Tags management.',
                'icon'        => 'file-text',
                'color'       => '#2563eb',
                'sort_order'  => 0,
                'modules'     => ['posts', 'categories', 'tags'],
            ],
            [
                'name'        => 'Media',
                'slug'        => 'media',
                'description' => 'Media library management.',
                'icon'        => 'image',
                'color'       => '#7c3aed',
                'sort_order'  => 1,
                'modules'     => ['media'],
            ],
            [
                'name'        => 'Community',
                'slug'        => 'community',
                'description' => 'Comments and user interaction.',
                'icon'        => 'message-square',
                'color'       => '#059669',
                'sort_order'  => 2,
                'modules'     => ['comments'],
            ],
            [
                'name'        => 'Users',
                'slug'        => 'users',
                'description' => 'User and role management.',
                'icon'        => 'users',
                'color'       => '#d97706',
                'sort_order'  => 3,
                'modules'     => ['users', 'roles'],
            ],
            [
                'name'        => 'System',
                'slug'        => 'system',
                'description' => 'Settings and analytics.',
                'icon'        => 'settings',
                'color'       => '#6b7280',
                'sort_order'  => 4,
                'modules'     => ['settings', 'analytics', 'dashboard', 'activity_logs'],
            ],
            [
                'name'        => 'Personal',
                'slug'        => 'personal',
                'description' => 'Bookmarks and reading history for logged-in users.',
                'icon'        => 'bookmark',
                'color'       => '#0ea5e9',
                'sort_order'  => 5,
                'modules'     => ['bookmarks', 'reading_history'],
            ],
        ];

        foreach ($groupDefs as $def) {
            $group = PermissionGroup::firstOrCreate(
                ['slug' => $def['slug']],
                [
                    'name'        => $def['name'],
                    'description' => $def['description'],
                    'icon'        => $def['icon'],
                    'color'       => $def['color'],
                    'sort_order'  => $def['sort_order'],
                ]
            );

            $groupPerms = $allPermissions->filter(
                fn ($p) => in_array($p->module, $def['modules'])
            )->pluck('id')->all();

            $group->permissions()->sync($groupPerms);
        }
    }
}
