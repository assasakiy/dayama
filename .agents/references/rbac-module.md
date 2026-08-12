# RBAC (Role-Based Access Control) Module

## Overview
The RBAC module manages authentication, authorization, and user access levels across the dashboard. Uses Spatie Laravel Permission with a custom authorization pipeline.

## Database Schema / Core Entities

1. **Users** (`users`)
   - The main account entity.
   - Belongs to Roles and/or has direct Permissions.
   - `is_primary_super_admin` (bool): bypasses all authorization checks.
   - `is_protected` (bool): prevents deletion.

2. **Roles** (`core_roles`)
   - Grouping of permissions.
   - `name`: machine-readable slug (e.g., `super-admin`, `administrator`).
   - `display_name`: human-readable for UI (e.g., `Super Admin`). Always use `display_name` in the frontend.
   - `scope`: `null` (global), `yayasan` (all institutions), `lembaga` (specific institution via pivot).
   - `rank` (0-100): hierarchy level for protection rules.
   - `color`, `icon`, `is_system`, `status`, `sort_order`.

3. **Permissions** (`core_permissions`)
   - Granular access controls.
   - Format: `module.action.scope` (e.g., `posts.view.own`, `users.edit.all`).
   - Has `module`, `action`, `scope` columns.
   - Belongs to Permission Groups via `core_permission_group_permission`.

4. **Permission Groups** (`core_permission_groups`)
   - Custom table to group permissions logically in the UI (e.g., grouping all `posts.*` under a "Konten" group).

5. **Role-User Pivot** (`core_role_user`)
   - `user_id`, `role_id`, `institution_id` — links users to roles within a specific institution.
   - Used by `ScopeRule` and `InstitutionScope` to enforce lembaga-scoped access.

## Authorization Pipeline (`app/Authorization`)
Runs in strict order:
1. **PrimarySuperAdminRule** — bypass if `is_primary_super_admin = true`
2. **ScopeRule** — deny if lembaga user accesses resource outside their institution
3. **PermissionRule** — check `$user->hasPermissionTo()`
4. **OwnershipRule** — check `.own` vs `.all` permission scope
5. **RankRule** — deny if target has equal/higher rank

Rules are configured in `config/authorization.php` and validated at boot in `AuthorizationServiceProvider`.

## Institution Scoping
- **Super Admin**: bypass all scoping (Gate::before + PrimarySuperAdminRule).
- **Role scope `yayasan`**: unrestricted — can see all institutions.
- **Role scope `lembaga`**: restricted — only data belonging to institutions in `core_role_user.institution_id`.
- **Role scope `null`**: global role — unrestricted.

Three enforcement layers:
1. **ScopeRule** (gate-level): for models using policies + `Gate::authorize()`.
2. **InstitutionScope** (Eloquent Global Scope): for Academic/HR/Core models — auto-filters all queries.
3. **ActiveInstitution::authorizeAccess()**: manual check in middleware/controllers.

See `docs/rbac/README.md` for full documentation.

## Controller Routing (Dashboard)
| Module | Typical Routes |
|--------|----------------|
| Users | `/users` |
| Roles | `/roles` |
| Permissions | `/permissions` |
| Permission Groups | `/permission-groups` |

## Frontend File Map

```
Pages/
├── Users/
│   ├── Index.tsx
│   └── ...
├── Roles/
│   ├── Index.tsx
│   └── ...
├── Permissions/
│   ├── Index.tsx
│   └── ...
└── PermissionGroups/
    ├── Index.tsx
    └── ...
```

## How It's Used in the Frontend
- **Permissions Hook**: The `usePermissions()` hook (located in `hooks/`) provides the `can(permissionName)` function.
- **Sidebar Protection**: In `DashboardLayout.tsx`, the `baseMenuGroups` array uses the `permission` property to filter which menus a user can see (e.g., `permission: 'settings.view'`).
- **Action Protection**: Before rendering buttons (like "Tambah", "Edit", "Hapus"), check if the user has the permission using the `can()` function.
