# Panduan Sistem RBAC (Role-Based Access Control)

Sistem RBAC pada aplikasi ini dibangun menggunakan kombinasi **Spatie Laravel Permission** di sisi backend (Laravel) dan **React/Inertia** di sisi frontend, dengan format penamaan permission `module.action.scope`.

## 1. Konsep Dasar

Kami menggunakan 4 tingkatan pengaturan akses:
1. **User**: Pengguna akhir yang masuk ke sistem.
2. **Role**: Jabatan atau peran user (contoh: `Super Admin`, `Editor`, `Author`).
3. **Permission Group**: Pengelompokan izin berdasarkan modul untuk mempermudah manajemen di UI (contoh: `Posts`, `Users`).
4. **Permission**: Hak akses spesifik dengan format `module.action.scope`.

### Format Permission (`module.action.scope`)
- **Module**: Nama fitur (contoh: `posts`, `categories`, `users`).
- **Action**: Jenis aksi CRUD (contoh: `view`, `create`, `edit`, `delete`).
- **Scope**: Jangkauan data (contoh: `all` untuk semua data, `own` untuk data miliknya sendiri).
- *Contoh Lengkap:* `posts.edit.own`, `categories.delete.all`, `users.view.all`.

## 2. Pengamanan di Backend (Laravel)

Sisi backend adalah lapisan pertahanan utama yang tidak dapat dimanipulasi oleh *client*.

### A. Bypass Primary Super Admin
Hanya **Primary Super Admin** (`is_primary_super_admin = true`) yang memiliki akses penuh tanpa perlu permission di database. Sistem memiliki dua lapis bypass:

**1. Gate::before (Global)** — Hanya user dengan flag `is_primary_super_admin = true` yang bypass. Diatur di `App\Providers\AppServiceProvider`:
```php
Gate::before(function ($user, $ability) {
    return $user->is_primary_super_admin ? true : null;
});
```

> **Penting:** User dengan role "Super Admin" TAPI bukan primary (`is_primary_super_admin = false`) **tidak bypass** — mereka tetap harus memiliki permission yang sesuai di database. Role Super Admin tetap diberi semua permission via seeder untuk kemudahan, tetapi pengamanan tidak boleh bergantung pada nama role.

**2. PrimarySuperAdminRule (Authorization Pipeline)** — Hanya user dengan `is_primary_super_admin = true` yang di-bypass di authorization pipeline:
```php
// config/authorization.php
'rules' => [
    PrimarySuperAdminRule::class,     // Pertama: bypass PSA (is_primary_super_admin only)
    ScopeRule::class,                 // Kedua: cek scope institusi (lembaga-scope only)
    PermissionRule::class,            // Ketiga: cek permission
    OwnershipRule::class,             // Keempat: cek kepemilikan
    RankRule::class,                  // Kelima: cek hierarki
],
```

### B. Authorization Domain & Thin Policies
Kami telah memisahkan logika perizinan yang rumit dari _Laravel Policies_ ke sebuah **Authorization Domain** mandiri (`app/Authorization`). Policy kini hanya berfungsi sebagai *thin adapter* (perantara tipis) menuju `AuthorizationService`.

Contoh pada `MediaPolicy`:
```php
public function update(User $user, Media $media): Response
{
    $result = app(AuthorizationService::class)->check($user, 'update', $media);
    return $result->allowed() ? Response::allow() : Response::deny($result->message());
}
```
`AuthorizationService` akan mengeksekusi sebuah *Pipeline* (`Illuminate\Pipeline`) yang berisi sekumpulan *Rules* (seperti `PrimarySuperAdminRule`, `ScopeRule`, `PermissionRule`, `OwnershipRule`, `RankRule`). Desain ini memastikan aturan perizinan selalu berjalan secara konsisten di seluruh aplikasi, tanpa duplikasi kode.

### C. Controllers & Capabilities Presenter
Sebagai pintu gerbang utama (keamanan sisi server), setiap _method_ di Controller wajib memanggil `Gate` (contoh: `Gate::authorize('update', $category)`). Panggilan ini akan otomatis masuk ke Policy dan diteruskan ke *Authorization Domain*.

Namun, untuk **keperluan variabel UI (tampilan)** yang biasanya butuh mengecek kepemilikan/cakupan secara granular, *Controller* diharamkan menggunakan nama permission Spatie mentah (seperti `$user->can('media.edit.any')`). Sebagai solusinya, kami menggunakan **Capabilities Presenter**:

```php
public function index(Request $request): Response
{
    // 1. Penjaga Keamanan Utama (Akses Halaman)
    Gate::authorize('viewAny', ActivityLog::class);

    // 2. Resolve UI Capabilities tanpa mengekspos Spatie permission string
    $capabilities = app(AuthorizationService::class)->capabilities(auth()->user(), ActivityLog::class);

    return Inertia::render('ActivityLogs/Index', [
        'can' => [
            'seeAll' => $capabilities->seeAll(),
            'delete' => $capabilities->delete(),
        ]
    ]);
}
```

### D. Rank (Hierarki) & Primary Super Admin
Sistem RBAC juga dilengkapi dengan fitur proteksi berbasis hierarki (*Rank*) dan kepemilikan utama (*Primary Super Admin*):
- **Rank (0 - 100)**: Setiap Role memiliki nilai rank. Nilai yang lebih tinggi merepresentasikan posisi yang lebih tinggi dalam hierarki (100 adalah yang tertinggi, misal: Super Admin).
- **Primary Super Admin**: Terdapat user utama (ID 1) dengan properti `is_primary_super_admin = true` yang kebal terhadap pembatasan hierarki. Aturan-aturan ini terpusat di `RankRule` dan `PrimarySuperAdminRule`.

Aturan keamanan berbasis Rank:
1. User biasa atau Super Admin sekunder **tidak dapat mengubah atau menghapus** data dari entitas pengguna yang memiliki Rank setara atau lebih tinggi dari dirinya.
2. Aturan nomor 1 tidak berlaku bagi **Primary Super Admin**. Primary Super Admin berhak mengelola semua entitas tanpa dibatasi aturan setara/lebih tinggi.
3. Primary Super Admin **tidak akan pernah bisa dihapus** oleh siapapun, dan profilnya hanya bisa diubah oleh dirinya sendiri.

### E. Institution Scoping (Role Scope + Institution Scope)

Sejak penambahan kolom `scope` pada `core_roles`, sistem RBAC memiliki tiga kategori scope role:

| Scope | Akses | Contoh Role |
|-------|-------|-------------|
| `null` (global) | Semua data tanpa batasan institusi | `super-admin`, `administrator` |
| `yayasan` | Semua institusi dalam yayasan | `operator_yayasan` |
| `lembaga` | Hanya institusi tertentu (via pivot `core_role_user.institution_id`) | `operator_lembaga`, `guru`, `staf` |

Pengecekan scope berjalan di **dua lapis**:

#### Lapis 1: ScopeRule (Authorization Pipeline — Gate-level)

`ScopeRule` adalah rule ke-2 dalam pipeline, dijalankan setelah `PrimarySuperAdminRule` dan sebelum `PermissionRule`. Rule ini:

1. Mendeteksi apakah user memiliki role dengan `scope = lembaga`.
2. Jika ya, memeriksa `institution_id` pada target model yang sedang diakses.
3. Jika target tidak memiliki `institution_id`, rule melewati ke rule berikutnya.
4. Jika target memiliki `institution_id`, rule memverifikasi bahwa user memiliki pivot di `core_role_user` dengan `institution_id` yang sesuai.
5. Jika tidak cocok → deny 403.

```php
// app/Authorization/Rules/ScopeRule.php
$hasLembagaScope = $actor->roles()->where('scope', 'lembaga')->exists();
if ($hasLembagaScope && $target?->institution_id) {
    $hasAccess = RoleUser::where('user_id', $actor->id)
        ->where('institution_id', $target->institution_id)
        ->exists();
    if (! $hasAccess) {
        $context->deny('Anda tidak memiliki akses ke sumber daya di lembaga ini.');
    }
}
```

> **Catatan:** ScopeRule hanya aktif untuk *gate-based authorization* — yaitu model yang memiliki Policy dan menggunakan `Gate::authorize()` (seperti `User`, `Role`, `Permission`, `Media`, `ActivityLog`, dll).

#### Lapis 2: InstitutionScope (Eloquent Global Scope — Query-level)

Untuk model-model **Academic, HR, dan Core** yang tidak menggunakan Gate/policy, scope diterapkan via **Eloquent Global Scope** (`InstitutionScope`) yang otomatis menyisipkan `WHERE institution_id = ?` pada setiap query.

**Model yang terdaftar:**

| Model | Tabel | Modul |
|-------|-------|-------|
| `Student` | `academic_students` | Academic |
| `Classroom` | `academic_classrooms` | Academic |
| `Employee` | `hr_employees` | HR |
| `EmployeeProfile` | `hr_employee_profiles` | HR |
| `Department` | `hr_departments` | HR |
| `EmploymentHistory` | `hr_employment_histories` | HR |
| `Person` | `core_persons` | Core |

Global scope bekerja dengan prinsip:
- **Super-admin**: bypass (tidak ada filter)
- **Role scope `yayasan`**: bypass — bisa melihat semua data di semua institusi
- **Role scope `lembaga`**: otomatis filter `WHERE institution_id = <active_institution_id>`
- **Tidak login / CLI**: bypass (tidak ada user context)

```php
// app/Authorization/Scopes/InstitutionScope.php
class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! ActiveInstitution::shouldScope()) {
            return;
        }
        $id = ActiveInstitution::id();
        if ($id) {
            $builder->where($model->getTable() . '.institution_id', $id);
        } else {
            $builder->whereRaw('1 = 0');
        }
    }
}
```

#### ActiveInstitution Helper

`app\Support\ActiveInstitution.php` menyediakan helper statis untuk query scoping:

| Method | Fungsi |
|--------|--------|
| `id()` | Return `active_institution_id` dari session |
| `shouldScope()` | Return `true` jika user punya role lembaga |
| `applyToQuery($query, $column)` | Filter query builder ke institution user |
| `authorizeAccess($institutionId)` | Throw 403 jika user tidak punya akses ke institusi |
| `accessibleIds()` | Return array institution_id yang bisa diakses user lembaga (`null` jika yayasan/super-admin) |

#### Pivot core_role_user

Untuk user dengan scope `lembaga`, pivot `core_role_user` menyimpan `institution_id` yang menentukan institusi mana yang bisa diakses:

```sql
CREATE TABLE core_role_user (
    id CHAR(36) PRIMARY KEY,
    role_id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    institution_id CHAR(36) NULL,
    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_user (role_id, user_id, institution_id)
);
```

#### Aturan Scoping Lengkap

| User Type | Role Scope | Melihat Data Institusi A | Melihat Data Institusi B |
|-----------|-----------|--------------------------|--------------------------|
| Super Admin | `null` (global) | ✅ Ya | ✅ Ya |
| Operator Yayasan | `yayasan` | ✅ Ya | ✅ Ya |
| Operator Lembaga A | `lembaga` | ✅ Ya | ❌ Tidak |
| Guru di Lembaga A | `lembaga` | ✅ Ya | ❌ Tidak |
| Administrator | `null` (global) | ✅ Ya | ✅ Ya |

## 3. Pengamanan di Frontend (React + Inertia)

Sisi frontend berfungsi untuk menyesuaikan antarmuka pengguna agar bersih dari elemen yang tidak dapat mereka akses.

### A. Shared State (Middleware)
`app/Http/Middleware/HandleInertiaRequests.php` bertugas mengirimkan array `roles` dan `permissions` milik user yang login ke semua halaman Inertia.

### B. Hook `usePermissions()`
Kami menyediakan hook React `resources/js/dashboard/hooks/usePermissions.ts` untuk mempermudah pengecekan akses:
```tsx
import { usePermissions } from '@dashboard/hooks/usePermissions';

export default function MyComponent() {
    const { can, hasRole } = usePermissions();

    return (
        <div>
            {can('posts.create') && <button>Buat Artikel</button>}
        </div>
    );
}
```

### C. Dinamisasi Sidebar / Layout
Menu-menu di `DashboardLayout.tsx` dan `AccountSettingsLayout.tsx` disaring secara otomatis. Jika user tidak memiliki akses ke suatu modul (misalnya `.view.all`), maka tautan modul tersebut **tidak akan di-render** ke dalam DOM HTML.

---

## 4. Daftar Modul & Permission

Saat ini terdapat **124 permission** dari **~25 modul**, dikelompokkan dalam beberapa kategori. Scoping dibedakan antara modul **CMS** (own/all berbasis user) dan modul **Domain** (all-only, karena data multi-institution).

### Non-Scoped Modules (base permission only)

Modul global tanpa konsep kepemilikan:

| Module          | Actions                          | Jumlah Permission |
|-----------------|----------------------------------|------------------:|
| `dashboard`     | view                             | 1                |
| `settings`      | view, update                     | 2                |
| `analytics`     | view                             | 1                |
| `categories`    | view, create, edit, delete       | 4                |
| `tags`          | view, create, edit, delete       | 4                |
| `users`         | view, create, edit, delete       | 4                |
| `roles`         | view, create, edit, delete       | 4                |
| `permissions`   | view, create, edit, delete       | 4                |
| `persons`       | view, create, edit, delete       | 4                |
| `institutions`  | view, create, edit, delete       | 4                |
| `employment_statuses` | view, create, edit, delete | 4                |

### Scoped Modules (own/all) — CMS

Modul dengan kepemilikan per-user (own = data sendiri, all = semua data):

| Module           | Actions (scoped → own/all)          | Base Actions | Jumlah |
|------------------|-------------------------------------|-------------|-------:|
| `posts`          | view, edit, delete, publish, restore, force-delete | create | 13 |
| `pages`          | view, edit, delete, publish         | create      | 9     |
| `media`          | view, edit, delete                  | upload      | 7     |
| `comments`       | view, delete                        | reply, moderate | 6 |
| `bookmarks`      | view                                | —           | 2     |
| `reading_history`| view                                | —           | 2     |
| `activity_logs`  | view, delete                        | —           | 4     |

### Academic Modules (all scope)

Modul akademik — data scoped per-institution via `institution_id`.

| Module               | Actions                     | Jumlah |
|----------------------|-----------------------------|------:|
| `academic-years`     | view, create, edit, delete  | 4     |
| `semesters`          | view, create, edit, delete  | 4     |
| `classes`            | view, create, edit, delete  | 4     |
| `subjects`           | view, create, edit, delete  | 4     |
| `rombels`            | view, create, edit, delete  | 4     |
| `students`           | view, create, edit, delete  | 4     |
| `teaching-assignments` | view, create, edit, delete | 4    |

### HR Modules (all scope)

Modul kepegawaian — data scoped per-institution.

| Module        | Actions                     | Jumlah |
|---------------|-----------------------------|------:|
| `employees`   | view, create, edit, delete  | 4     |
| `positions`   | view, create, edit, delete  | 4     |
| `departments` | view, create, edit, delete  | 4     |
| `attendance`  | view, create, edit, delete  | 4     |

### Yayasan Modules (all scope)

Modul yayasan — data lintas-institution.

| Module          | Actions                       | Jumlah |
|-----------------|-------------------------------|------:|
| `person-index`  | view, create, edit, delete    | 4     |
| `transfer-logs` | view, create, edit, delete    | 4     |
| `stats`         | view                          | 1     |

### Permission Groups

Permission dikelompokkan di UI sebagai berikut:

| Group          | Modul di Dalamnya                                      |
|----------------|--------------------------------------------------------|
| Konten         | posts, pages, media, categories, tags, comments        |
| Personalisasi  | bookmarks, reading_history, activity_logs              |
| Pengguna       | users, roles, permissions                             |
| System         | dashboard, settings, analytics, activity_logs        |
| **Data Inti**  | persons, institutions                                  |
| **Akademik**   | academic-years, semesters, classes, subjects, rombels, students, teaching-assignments |
| **Kepegawaian**| employees, positions, departments, attendance, employment_statuses |
| **Yayasan**    | person-index, transfer-logs, stats                     |

### Aturan Scoping

| Aturan | Penjelasan |
|--------|-----------|
| **Non-scoped module** | Data bersifat global (kategori, tag, user, role, dll). Tidak ada permission `*.own`/`*.all`. |
| **Scoped module** | Data bisa milik perorangan. Aksi CRUD (kecuali `create`) punya varian `own` dan `all`. |
| **Base action** | `create` dan aksi non-CRUD (contoh: `upload`, `reply`, `moderate`) hanya punya varian base. |
| **Soft delete** | `delete` = soft delete (masuk trash). `force-delete` = permanent delete (dari trash). |
| **Publish** | `publish` dianggap aksi CRUD karena mengubah status visibilitas konten. |

---

## 5. Cara Menambah Modul Baru

Jika Anda membuat tabel/fitur baru (misal: `Products`), langkah-langkah mengamankannya adalah:

1. **Tambahkan ke seeder** — Daftarkan modul di `database/seeders/RoleAndPermissionSeeder.php`:
   - `MODULES`: Tambahkan `'products' => ['view', 'create', 'edit', 'delete']`
   - `SCOPED_MODULES`: Jika data bisa milik perorangan, tambahkan `'products'` ke array ini
   - `DEFAULT_ROLES`: Sesuaikan permission yang diberikan ke tiap role
   - Permission groups: Tambahkan modul ke grup yang sesuai

2. **Buat Policy** — Buat `ProductPolicy` sebagai _thin adapter_ yang mendelegasikan ke `AuthorizationService`.

3. **Gunakan Gate** — Tambahkan `Gate::authorize()` di awal method `ProductController`.

4. **UI Capabilities** — Jika perlu mengirim flag permission ke React, gunakan `AuthorizationService::capabilities($user, Product::class)`.

5. **Frontend** — Gunakan hook `usePermissions()` atau `can` flag dari controller untuk menyembunyikan tombol aksi.

6. **Jalankan seeder**:
   ```bash
   php artisan db:seed --class=RoleAndPermissionSeeder
   ```

### Aturan Penting

1. **Jangan buat permission manual di database** — Semua permission harus melalui seeder agar konsisten.
2. **Non-scoped module** → cukup `products.create`, `products.view`, dll (tanpa scope).
3. **Scoped module** → aksi CRUD (`view`, `edit`, `delete`) otomatis dibuat dengan varian `own` dan `all`. Aksi `create` tetap base.
4. **Jangan duplikasi** — Jika module sudah di `SCOPED_MODULES`, jangan buat base permission untuk aksi yang di-scope (akan dianggap orphan dan dihapus).
