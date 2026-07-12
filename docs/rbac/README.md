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

### A. Bypass Super Admin
Role **Super Admin** memiliki akses ke seluruh fitur tanpa perlu mendaftarkan permission satu per satu di database. Ini diatur melalui `Gate::before` di `App\Providers\AppServiceProvider`:
```php
Gate::before(function ($user, $ability) {
    return $user->hasRole('Super Admin') ? true : null;
});
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
`AuthorizationService` akan mengeksekusi sebuah *Pipeline* (`Illuminate\Pipeline`) yang berisi sekumpulan *Rules* (seperti `PrimarySuperAdminRule`, `PermissionRule`, `OwnershipRule`, `RankRule`). Desain ini memastikan aturan perizinan selalu berjalan secara konsisten di seluruh aplikasi, tanpa duplikasi kode.

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

## 4. Cara Menambah Modul Baru

Jika Anda membuat tabel/fitur baru (misal: `Products`), langkah-langkah mengamankannya adalah:
1. Buat permission di database (atau UI *Permissions*): `products.view.all`, `products.create.all`, `products.edit.all`, dll.
2. Buat `ProductPolicy` sebagai _thin adapter_ yang mendelegasikan pengecekan ke `AuthorizationService`.
3. Tambahkan `Gate::authorize()` di awal *method* `ProductController` Anda.
4. (Opsional) Jika perlu mengirim flag permission kompleks ke React, gunakan `AuthorizationService::class->capabilities($user, Product::class)`.
5. Di frontend React, gunakan `can` flag dari Controller (atau hook `usePermissions()` jika cocok) untuk menyembunyikan tombol aksi.
