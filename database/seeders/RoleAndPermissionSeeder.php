<?php

declare(strict_types=1);

namespace Database\Seeders;

use Modules\Core\Models\Permission;
use Modules\Core\Models\PermissionGroup;
use Modules\Core\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /** CMS modules and their actions */
    private const MODULES = [
        'dashboard'       => ['view'],
        'posts'           => ['view', 'create', 'edit', 'delete', 'publish', 'restore', 'force-delete'],
        'pages'           => ['view', 'create', 'edit', 'delete', 'publish'],

        'media'           => ['view', 'upload', 'edit', 'delete'],
        'comments'        => ['view', 'reply', 'delete', 'moderate'],
        'categories'      => ['view', 'create', 'edit', 'delete'],
        'tags'            => ['view', 'create', 'edit', 'delete'],
        'users'           => ['view', 'create', 'edit', 'delete'],
        'roles'           => ['view', 'create', 'edit', 'delete'],
        'permissions'     => ['view', 'create', 'edit', 'delete'],
        'settings'        => ['view', 'update'],
        'analytics'       => ['view'],
        'activity_logs'   => ['view', 'delete'],
        'bookmarks'       => ['view'],
        'reading_history' => ['view'],

        // ── Core ───────────────────────────────────────────────
        'persons'         => ['view', 'create', 'edit', 'delete'],
        'institutions'    => ['view', 'create', 'edit', 'delete'],

        // ── Academic ───────────────────────────────────────────
        'academic.years'      => ['view', 'create', 'edit', 'delete'],
        'academic.semesters'  => ['view', 'create', 'edit', 'delete'],
        'academic.classes'    => ['view', 'create', 'edit', 'delete'],
        'academic.rombel'     => ['view', 'create', 'edit', 'delete'],
        'academic.subjects'   => ['view', 'create', 'edit', 'delete'],
        'academic.students'   => ['view', 'create', 'edit', 'delete'],
        'academic.attendance' => ['view', 'create', 'edit', 'delete'],
        'academic.grades'     => ['view', 'create', 'edit', 'delete'],

        // ── HR ─────────────────────────────────────────────────
        'hr.employees'    => ['view', 'create', 'edit', 'delete'],
        'hr.positions'    => ['view', 'create', 'edit', 'delete'],
        'hr.departments'  => ['view', 'create', 'edit', 'delete'],
        'hr.attendance'   => ['view', 'create', 'edit', 'delete'],

        // ── Yayasan ────────────────────────────────────────────
        'yayasan'                 => ['view'],
        'yayasan.institutions'    => ['view'],
        'yayasan.index'           => ['view'],
        'yayasan.transfer-logs'   => ['view'],
        'yayasan.stats'           => ['view'],
    ];

    /** Modules that use ownership scoping */
    private const SCOPED_MODULES = ['posts', 'pages', 'media', 'comments', 'bookmarks', 'reading_history', 'activity_logs'];

    /** Actions that get own/all scope variants */
    private const SCOPED_ACTIONS = ['view', 'edit', 'delete', 'publish', 'restore', 'force-delete'];

    /** Default CMS roles with metadata */
    private const DEFAULT_ROLES = [
        [
            'name'         => 'super-admin',
            'display_name' => 'Super Admin',
            'description'  => 'Memiliki akses penuh ke setiap fitur dan pengaturan.',
            'color'        => '#7c3aed',
            'icon'         => 'crown',
            'is_system'    => true,
            'sort_order'   => 0,
            'rank'         => 100,
            'scope'        => null,
        ],
        [
            'name'         => 'administrator',
            'display_name' => 'Administrator',
            'description'  => 'Dapat mengelola seluruh situs web kecuali pengaturan sistem.',
            'color'        => '#dc2626',
            'icon'         => 'shield',
            'is_system'    => true,
            'sort_order'   => 1,
            'rank'         => 80,
            'scope'        => null,
        ],
        [
            'name'         => 'editor',
            'display_name' => 'Editor',
            'description'  => 'Dapat mengelola semua postingan, komentar, kategori, dan tag.',
            'color'        => '#2563eb',
            'icon'         => 'pen-tool',
            'is_system'    => false,
            'sort_order'   => 2,
            'rank'         => 60,
            'scope'        => null,
        ],
        [
            'name'         => 'author',
            'display_name' => 'Penulis',
            'description'  => 'Dapat membuat, mengedit, menerbitkan, dan menghapus postingan sendiri.',
            'color'        => '#059669',
            'icon'         => 'feather',
            'is_system'    => false,
            'sort_order'   => 3,
            'rank'         => 40,
            'scope'        => null,
        ],
        [
            'name'         => 'contributor',
            'display_name' => 'Kontributor',
            'description'  => 'Dapat membuat dan mengedit postingan sendiri tetapi tidak dapat menerbitkan.',
            'color'        => '#d97706',
            'icon'         => 'edit-3',
            'is_system'    => false,
            'sort_order'   => 4,
            'rank'         => 20,
            'scope'        => null,
        ],
        [
            'name'         => 'subscriber',
            'display_name' => 'Pelanggan',
            'description'  => 'Dapat membaca konten dan mengelola profil sendiri.',
            'color'        => '#6b7280',
            'icon'         => 'user',
            'is_system'    => false,
            'sort_order'   => 5,
            'rank'         => 10,
            'scope'        => null,
        ],
        [
            'name'         => 'operator_yayasan',
            'display_name' => 'Operator Yayasan',
            'description'  => 'Role untuk pengelola tingkat yayasan. Dapat mengelola akademik, HR, dan data lembaga di semua lembaga di bawah yayasan.',
            'color'        => '#0891b2',
            'icon'         => 'user-cog',
            'is_system'    => false,
            'sort_order'   => 6,
            'rank'         => 35,
            'scope'        => 'yayasan',
        ],
        [
            'name'         => 'operator_lembaga',
            'display_name' => 'Operator Lembaga',
            'description'  => 'Role untuk pengelola tingkat lembaga (madrasah). Dapat mengelola akademik, HR, dan data di lembaga tempatnya ditugaskan.',
            'color'        => '#0e7490',
            'icon'         => 'building-2',
            'is_system'    => false,
            'sort_order'   => 7,
            'rank'         => 30,
            'scope'        => 'lembaga',
        ],
        [
            'name'         => 'guru',
            'display_name' => 'Guru',
            'description'  => 'Role untuk tenaga pendidik. Dapat mengelola presensi, nilai, dan data akademik terbatas di lembaga tempatnya ditugaskan.',
            'color'        => '#65a30d',
            'icon'         => 'graduation-cap',
            'is_system'    => false,
            'sort_order'   => 8,
            'rank'         => 25,
            'scope'        => 'lembaga',
        ],
        [
            'name'         => 'staf',
            'display_name' => 'Staf',
            'description'  => 'Role untuk staf administrasi. Dapat mengelola data kepegawaian dan administrasi di lembaga tempatnya ditugaskan.',
            'color'        => '#a21caf',
            'icon'         => 'briefcase',
            'is_system'    => false,
            'sort_order'   => 9,
            'rank'         => 20,
            'scope'        => 'lembaga',
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
                    'scope'        => $roleData['scope'],
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

        // Look up roles by their actual `name` in the database.
        // We use display_name as the lookup key since firstOrCreate creates with `$roleData['name']`.
        $roleByDisplay = collect(self::DEFAULT_ROLES)
            ->mapWithKeys(fn ($r) => [$r['display_name'] => Role::where('name', $r['name'])->first()]);

        /** @var Role|null $superAdmin */
        $superAdmin = $roleByDisplay->get('Super Admin');
        if ($superAdmin) $superAdmin->syncPermissions($allPermissions->all());

        /** @var Role|null $admin */
        $admin = $roleByDisplay->get('Administrator');
        if ($admin) $admin->syncPermissions($allPermissions->all());

        // Editor — all content + comments, no user/role/settings management
        $editorPerms = collect();
        foreach (['posts', 'categories', 'tags', 'comments', 'media'] as $mod) {
            $editorPerms = $editorPerms->merge($byModule($mod));
        }
        $editorPerms->push($byName('dashboard.view'));
        $editorPerms->push($byName('analytics.view'));
        $editorPerms->push($byName('activity_logs.view.own'));
        /** @var Role|null $editorRole */
        $editorRole = $roleByDisplay->get('Editor');
        if ($editorRole) $editorRole->syncPermissions($editorPerms->filter()->all());

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
        /** @var Role|null $authorRole */
        $authorRole = $roleByDisplay->get('Penulis');
        if ($authorRole) $authorRole->syncPermissions($authorPerms->all());

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
        /** @var Role|null $contributorRole */
        $contributorRole = $roleByDisplay->get('Kontributor');
        if ($contributorRole) $contributorRole->syncPermissions($contributorPerms->all());

        // Subscriber — minimal access + personal content features
        $subscriberPerms = collect([
            $byName('dashboard.view'),
            $byName('comments.view.all'),
            $byName('comments.reply'),
            $byName('bookmarks.view.own'),
            $byName('reading_history.view.own'),
            $byName('activity_logs.view.own'),
        ])->filter();
        /** @var Role|null $subscriberRole */
        $subscriberRole = $roleByDisplay->get('Pelanggan');
        if ($subscriberRole) $subscriberRole->syncPermissions($subscriberPerms->all());

        // Operator Yayasan — role default untuk pengelola tingkat yayasan
        $operatorYayasanPerms = collect([
            $byName('dashboard.view'),
            $byName('persons.view'), $byName('persons.create'), $byName('persons.edit'), $byName('persons.delete'),
            $byName('institutions.view'), $byName('institutions.create'), $byName('institutions.edit'), $byName('institutions.delete'),
            $byName('categories.view'), $byName('tags.view'),
            $byName('permissions.view'),
            // Academic
            $byName('academic.years.view'), $byName('academic.years.create'), $byName('academic.years.edit'), $byName('academic.years.delete'),
            $byName('academic.semesters.view'), $byName('academic.semesters.create'), $byName('academic.semesters.edit'), $byName('academic.semesters.delete'),
            $byName('academic.classes.view'), $byName('academic.classes.create'), $byName('academic.classes.edit'), $byName('academic.classes.delete'),
            $byName('academic.rombel.view'), $byName('academic.rombel.create'), $byName('academic.rombel.edit'), $byName('academic.rombel.delete'),
            $byName('academic.subjects.view'), $byName('academic.subjects.create'), $byName('academic.subjects.edit'), $byName('academic.subjects.delete'),
            $byName('academic.students.view'), $byName('academic.students.create'), $byName('academic.students.edit'), $byName('academic.students.delete'),
            $byName('academic.attendance.view'), $byName('academic.attendance.create'), $byName('academic.attendance.edit'), $byName('academic.attendance.delete'),
            $byName('academic.grades.view'), $byName('academic.grades.create'), $byName('academic.grades.edit'), $byName('academic.grades.delete'),
            // HR
            $byName('hr.employees.view'), $byName('hr.employees.create'), $byName('hr.employees.edit'), $byName('hr.employees.delete'),
            $byName('hr.positions.view'), $byName('hr.positions.create'), $byName('hr.positions.edit'), $byName('hr.positions.delete'),
            $byName('hr.departments.view'), $byName('hr.departments.create'), $byName('hr.departments.edit'), $byName('hr.departments.delete'),
            $byName('hr.attendance.view'), $byName('hr.attendance.create'), $byName('hr.attendance.edit'), $byName('hr.attendance.delete'),
            // Personal
            $byName('bookmarks.view.own'),
            $byName('reading_history.view.own'),
            $byName('activity_logs.view.own'),
        ])->filter();
        /** @var Role|null $operatorYayasanRole */
        $operatorYayasanRole = $roleByDisplay->get('Operator Yayasan');
        if ($operatorYayasanRole) $operatorYayasanRole->syncPermissions($operatorYayasanPerms->all());

        // Operator Lembaga — role default untuk pengelola tingkat lembaga (madrasah)
        $operatorLembagaPerms = collect([
            $byName('dashboard.view'),
            $byName('persons.view'), $byName('persons.create'), $byName('persons.edit'),
            $byName('institutions.view'),
            $byName('categories.view'), $byName('tags.view'),
            // Academic
            $byName('academic.years.view'), $byName('academic.years.create'), $byName('academic.years.edit'),
            $byName('academic.semesters.view'), $byName('academic.semesters.create'), $byName('academic.semesters.edit'),
            $byName('academic.classes.view'), $byName('academic.classes.create'), $byName('academic.classes.edit'),
            $byName('academic.rombel.view'), $byName('academic.rombel.create'), $byName('academic.rombel.edit'),
            $byName('academic.subjects.view'), $byName('academic.subjects.create'), $byName('academic.subjects.edit'),
            $byName('academic.students.view'), $byName('academic.students.create'), $byName('academic.students.edit'),
            $byName('academic.attendance.view'), $byName('academic.attendance.create'), $byName('academic.attendance.edit'),
            $byName('academic.grades.view'), $byName('academic.grades.create'), $byName('academic.grades.edit'),
            // HR
            $byName('hr.employees.view'), $byName('hr.employees.create'), $byName('hr.employees.edit'),
            $byName('hr.positions.view'), $byName('hr.positions.create'), $byName('hr.positions.edit'),
            $byName('hr.departments.view'), $byName('hr.departments.create'), $byName('hr.departments.edit'),
            $byName('hr.attendance.view'), $byName('hr.attendance.create'), $byName('hr.attendance.edit'),
            // Personal
            $byName('bookmarks.view.own'),
            $byName('reading_history.view.own'),
            $byName('activity_logs.view.own'),
        ])->filter();
        /** @var Role|null $operatorLembagaRole */
        $operatorLembagaRole = $roleByDisplay->get('Operator Lembaga');
        if ($operatorLembagaRole) $operatorLembagaRole->syncPermissions($operatorLembagaPerms->all());

        // Guru — tenaga pendidik (presensi, nilai, akademik terbatas)
        $guruPerms = collect([
            $byName('dashboard.view'),
            $byName('persons.view'),
            $byName('academic.classes.view'),
            $byName('academic.rombel.view'),
            $byName('academic.subjects.view'),
            $byName('academic.students.view'),
            $byName('academic.attendance.view'), $byName('academic.attendance.create'), $byName('academic.attendance.edit'),
            $byName('academic.grades.view'), $byName('academic.grades.create'), $byName('academic.grades.edit'),
            $byName('hr.employees.view'),
            // Personal
            $byName('bookmarks.view.own'),
            $byName('reading_history.view.own'),
            $byName('activity_logs.view.own'),
        ])->filter();
        /** @var Role|null $guruRole */
        $guruRole = $roleByDisplay->get('Guru');
        if ($guruRole) $guruRole->syncPermissions($guruPerms->all());

        // Staf — administrasi (kepegawaian, administrasi)
        $stafPerms = collect([
            $byName('dashboard.view'),
            $byName('persons.view'), $byName('persons.create'), $byName('persons.edit'),
            $byName('institutions.view'),
            $byName('academic.years.view'),
            $byName('academic.classes.view'),
            $byName('academic.rombel.view'),
            $byName('academic.subjects.view'),
            $byName('academic.students.view'),
            $byName('hr.employees.view'), $byName('hr.employees.create'), $byName('hr.employees.edit'),
            $byName('hr.positions.view'),
            $byName('hr.departments.view'),
            $byName('hr.attendance.view'),
            // Personal
            $byName('bookmarks.view.own'),
            $byName('reading_history.view.own'),
            $byName('activity_logs.view.own'),
        ])->filter();
        /** @var Role|null $stafRole */
        $stafRole = $roleByDisplay->get('Staf');
        if ($stafRole) $stafRole->syncPermissions($stafPerms->all());

        // -------------------------
        // 4. Seed Permission Groups
        // -------------------------
        $groupDefs = [
            [
                'name'        => 'Konten',
                'slug'        => 'content',
                'description' => 'Manajemen Postingan, Halaman, Kategori, dan Tag.',
                'icon'        => 'file-text',
                'color'       => '#2563eb',
                'sort_order'  => 0,
                'modules'     => ['posts', 'pages', 'categories', 'tags'],
            ],
            [
                'name'        => 'Media',
                'slug'        => 'media',
                'description' => 'Manajemen perpustakaan media.',
                'icon'        => 'image',
                'color'       => '#7c3aed',
                'sort_order'  => 1,
                'modules'     => ['media'],
            ],
            [
                'name'        => 'Komunitas',
                'slug'        => 'community',
                'description' => 'Komentar dan interaksi pengguna.',
                'icon'        => 'message-square',
                'color'       => '#059669',
                'sort_order'  => 2,
                'modules'     => ['comments'],
            ],
            [
                'name'        => 'Pengguna',
                'slug'        => 'users',
                'description' => 'Manajemen pengguna dan peran.',
                'icon'        => 'users',
                'color'       => '#d97706',
                'sort_order'  => 3,
                'modules'     => ['users', 'roles', 'permissions'],
            ],
            [
                'name'        => 'Sistem',
                'slug'        => 'system',
                'description' => 'Pengaturan dan analitik.',
                'icon'        => 'settings',
                'color'       => '#6b7280',
                'sort_order'  => 4,
                'modules'     => ['settings', 'analytics', 'dashboard', 'activity_logs'],
            ],
            [
                'name'        => 'Pribadi',
                'slug'        => 'personal',
                'description' => 'Markah buku dan riwayat bacaan untuk pengguna yang login.',
                'icon'        => 'bookmark',
                'color'       => '#0ea5e9',
                'sort_order'  => 5,
                'modules'     => ['bookmarks', 'reading_history'],
            ],
            [
                'name'        => 'Akademik',
                'slug'        => 'academic',
                'description' => 'Manajemen data akademik: tahun ajaran, kelas, rombel, mapel, siswa, presensi, nilai.',
                'icon'        => 'graduation-cap',
                'color'       => '#0891b2',
                'sort_order'  => 6,
                'modules'     => ['academic.years', 'academic.semesters', 'academic.classes', 'academic.rombel', 'academic.subjects', 'academic.students', 'academic.attendance', 'academic.grades'],
            ],
            [
                'name'        => 'Kepegawaian',
                'slug'        => 'hr',
                'description' => 'Manajemen data kepegawaian: guru, staf, jabatan, departemen, presensi.',
                'icon'        => 'users-cog',
                'color'       => '#7c3aed',
                'sort_order'  => 7,
                'modules'     => ['hr.employees', 'hr.positions', 'hr.departments', 'hr.attendance'],
            ],
            [
                'name'        => 'Yayasan',
                'slug'        => 'yayasan',
                'description' => 'Manajemen tingkat yayasan: lembaga, index person, log transfer, statistik.',
                'icon'        => 'landmark',
                'color'       => '#d97706',
                'sort_order'  => 8,
                'modules'     => ['yayasan', 'yayasan.institutions', 'yayasan.index', 'yayasan.transfer-logs', 'yayasan.stats'],
            ],
            [
                'name'        => 'Data Inti',
                'slug'        => 'core',
                'description' => 'Data master inti: persons, institutions, kategori, tag.',
                'icon'        => 'database',
                'color'       => '#6b7280',
                'sort_order'  => 9,
                'modules'     => ['persons', 'institutions'],
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
