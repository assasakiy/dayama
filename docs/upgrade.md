# Upgrade Guide

## Laravel Framework

```bash
composer update laravel/framework
# Check upgrade guide: https://laravel.com/docs/upgrade
```

## Frontend Assets

```bash
npm update
npm run build
```

## Database Migrations

```bash
php artisan migrate
```

## Clearing Cache

```bash
php artisan optimize:clear
php artisan optimize
```

---

## Modular Monolith Migration (App\Models → Modules\{Domain}\Models)

### 1. Namespace Migration

Semua model dipindahkan dari `App\Models\*` ke `Modules\{Domain}\Models\*`:

| Old Namespace | New Namespace |
|---|---|
| `App\Models\User` | `Modules\Core\Models\User` |
| `App\Models\Post` | `Modules\CMS\Models\Post` |
| `App\Models\Category` | `Modules\CMS\Models\Category` |
| `App\Models\Tag` | `Modules\CMS\Models\Tag` |
| `App\Models\Comment` | `Modules\CMS\Models\Comment` |
| `App\Models\Media` | `Modules\Core\Models\Media` |
| `App\Models\SystemAsset` | `Modules\System\Models\SystemAsset` |

### 2. Post-Migration Fixes

After moving models, fix polymorphic `model_type` references in the database:

```sql
-- Spatie Permission pivot tables
UPDATE core_model_has_roles SET model_type = 'Modules\Core\Models\User' WHERE model_type = 'App\Models\User';
UPDATE core_model_has_permissions SET model_type = 'Modules\Core\Models\User' WHERE model_type = 'App\Models\User';

-- Activity log
UPDATE system_activity_logs SET causer_type = 'Modules\Core\Models\User' WHERE causer_type = 'App\Models\User';
UPDATE system_activity_logs SET subject_type = 'Modules\CMS\Models\Post' WHERE subject_type = 'App\Models\Post';
UPDATE system_activity_logs SET subject_type = 'Modules\CMS\Models\Category' WHERE subject_type = 'App\Models\Category';
UPDATE system_activity_logs SET subject_type = 'Modules\CMS\Models\Comment' WHERE subject_type = 'App\Models\Comment';
UPDATE system_activity_logs SET subject_type = 'Modules\CMS\Models\Tag' WHERE subject_type = 'App\Models\Tag';
UPDATE system_activity_logs SET subject_type = 'Modules\Core\Models\Media' WHERE subject_type = 'App\Models\Media';
UPDATE system_activity_logs SET subject_type = 'Modules\Core\Models\User' WHERE subject_type = 'App\Models\User';

-- Media library
UPDATE core_media SET model_type = 'Modules\Core\Models\User' WHERE model_type = 'App\Models\User';
UPDATE core_media SET model_type = 'Modules\System\Models\SystemAsset' WHERE model_type = 'App\Models\SystemAsset';
```

### 3. Config Updates

Ensure these configs point to the correct model classes:

- **config/permission.php**: `models.role` → `Modules\Core\Models\Role`, `models.permission` → `Modules\Core\Models\Permission`
- **config/media-library.php**: `media_model` → `Modules\Core\Models\Media`
- **config/activitylog.php**: `activity_model` → `Modules\System\Models\ActivityLog`
- **config/auth.php**: `providers.users.model` → `Modules\Core\Models\User`

### 4. Missing Tables

Beberapa tabel mungkin belum ada migration-nya. Jalankan migrasi yang belum dijalankan:

```bash
php artisan migrate
```

Tabel yang perlu ada di database:

| Table | Module |
|---|---|
| `core_permission_groups` | Core |
| `core_permission_group_permission` | Core (pivot) |

### 5. Permission Orphan Cleanup

If you find permission orphans — scoped permissions (`*.own`/`*.all`) in non-scoped modules or base permissions in scoped modules — run the seeder after cleaning them:

```bash
# Check for orphans (run via php script or tinker)
# Non-scoped modules: categories, tags, users, roles — delete where scope IS NOT NULL
# Scoped modules: posts, pages, media, comments, bookmarks, reading_history, activity_logs — delete where scope IS NULL AND action IN (view, edit, delete, publish, restore, force-delete)

php artisan db:seed --class=RoleAndPermissionSeeder
```

Current modules: `dashboard`, `posts`, `pages`, `media`, `comments`, `categories`, `tags`, `users`, `roles`, `settings`, `analytics`, `activity_logs`, `bookmarks`, `reading_history`.

### 6. Method Signature Compatibility

When overriding methods from package traits/interfaces, use the **base package class** in the parameter type hint, not the custom subclass:

```php
// ✅ CORRECT
use Spatie\MediaLibrary\MediaCollections\Models\Media;

public function registerMediaConversions(?Media $media = null): void { ... }

// ❌ WRONG - PHP parameter type must match the interface
use Modules\Core\Models\Media; // subclass

public function registerMediaConversions(?Media $media = null): void { ... }
```
