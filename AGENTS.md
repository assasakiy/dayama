# AGENTS.md — DAYAMA Platform (dayama.test / dayama.id)

Instruksi kerja dan panduan operasional AI coding agent untuk repository DAYAMA. Baca penuh sebelum mulai bekerja.

---

## 1. Tujuan Platform (Goal)
Transformasi dari sistem CMS modular menjadi **DAYAMA Platform**:
- Ekosistem aplikasi pendidikan & yayasan berbasis **Modular Monolith, Multi-Application, Multi-Runtime**.
- **Single Identity**: 1 User digital, 1 Person (fisik), banyak relasi (Institution Membership & Role).
- **Failure Isolation**: Kegagalan satu aplikasi (misal Blog crash) tidak boleh meruntuhkan aplikasi inti lain (Account, Dashboard, Portal, PSB, Data Center).
- **Otonomi Lembaga**: Lembaga pendidikan (MTs, MA, MI, RA, dll.) adalah *source of truth* operasionalnya sendiri. Yayasan bertindak sebagai *aggregator & central index*.
- **Multi-Domain & Dynamic Branding**: Dukungan domain sistem statis dan dynamic site lembaga berbasis registry database/cache.

---

## 2. Peta Arsitektur Domain & Runtime
Domain dibangun otomatis dari root domain (`APP_ROOT_DOMAIN`):
- **Development**: `*.dayama.test` (via reverse proxy / router lokal, e.g. port 8181 atau 80/443).
- **Production**: `*.dayama.id` (via reverse proxy production port 443).

### A. Platform Applications (`routes/apps/` & `config/platform.php`)
1. **Account (`account.*`)**: Identity Provider tunggal (Login, Register, 2FA, Profile, Sessions).
2. **Dashboard (`dashboard.*`)**: Operational workspace untuk admin yayasan, kepala lembaga, operator, bendahara, TU, guru.
3. **Portal (`portal.*`)**: Personal workspace santri, wali santri, guru, pegawai, alumni (user-centric, cross-institution).
4. **PSB (`psb.*`)**: Centralized admission portal lintas lembaga (`psb.*` atau `psb.*/{institution}/register`).
5. **Data Center (`data.*`)**: Master Person Index, registry yayasan, global relationship.
6. **API Gateway (`api.*`)**: Integrasi eksternal, mobile application, webhook.

### B. Sites & Content Surfaces (`routes/sites/` & `config/platform.php`)
1. **Yayasan Landing (`dayama.test` / `dayama.id`)**: Berjalan di root domain.
2. **Blog CMS (`blog.*`)**: Konten berita & artikel.
3. **Situs Lembaga (`{host}.*` / custom domain)**: Dynamic site resolution via Core Registry + Redis cache (tidak di-load statis per domain di boot).

---

## 3. Stack & Lingkungan Kerja
- **Backend**: Laravel 13 / PHP 8.3+, MySQL 8.0, Redis (mendukung `phpredis` sebagai default prioritas atau `predis` fallback).
- **Frontend Dashboard**: React 19 + `@inertiajs/react` (SPA).
- **Public & Sites**: Blade SSR + Tailwind CSS + Alpine.js.
- **Packages Utama**: Spatie Permission (RBAC), Spatie MediaLibrary, Laravel Sanctum.
- **Struktur Modular**: `app/Modules/` (Core, CMS, Academic, HR, CRM, Finance, Library, Inventory, AI, Landing, Yayasan, System).
- **Local Dev Server**: Laragon (MySQL, Redis, PHP, Terminal) + Local Reverse Proxy/Router.

---

## 4. Aturan Arsitektur & Koding
1. **Single Source of Domain**:
   - Dilarang hardcode domain `test-blog.test` atau domain spesifik di controller/view.
   - Gunakan `config('platform.apps.*.domain')` dan `config('platform.sites.*.domain')`.
   - Gunakan `APP_ROOT_DOMAIN` sebagai basis perakitan domain otomatis.
2. **Booting Discipline**:
   - Dilarang menjalankan query database (`SELECT * FROM sites`) di `boot()` `RoutesServiceProvider` untuk meregistrasikan setiap domain dinamis. Rute dinamis harus ditangani via middleware host resolver / fallback catch-all.
3. **Identity Model (Tahap 1)**:
   - Bedakan tegas antara:
     - `User` = Akun login digital (email, password, auth status).
     - `Person` = Manusia fisik (NIK, nama lengkap, kontak, profil personal).
     - `InstitutionMembership` = Relasi many-to-many user/person dengan lembaga + role.
   - Jangan gunakan `users.institution_id` tunggal untuk menentukan kepemilikan multi-lembaga.
4. **Scope Akses**:
   - `Foundation Scope` (Akses lintas lembaga: Pengurus Yayasan, Auditor).
   - `Institution Scope` (Terikat lembaga: Kepala Madrasah, Operator MTs, Guru).
   - `Personal Scope` (Data mandiri: Santri, Wali Santri, Alumni).
5. **Simplicity First (Lazy Senior Developer)**:
   - Stdlib dan native framework first. Jangan membuat abstraksi sebelum ada kebutuhan 3x berulang.
   - Hindari boilerplate spekulatif. Minimal working diff.

---

## 5. Alur Kerja Git & Dokumentasi (WAJIB)
1. **Git Commit & Push**:
   - Setiap fitur atau refactor selesai, teruji, dan diverifikasi sukses:
     `git add -A`
     `git commit -m "feat/refactor: pesan singkat"`
     `git push origin master`
   - Remote utama: `https://github.com/assasakiy/dayama.git` (branch `master`).
2. **Dokumentasi Terpisah**:
   - Panduan arsitektur mendalam ditaruh di folder `docs/` (misal: `docs/Implementasi plan.md`, `docs/arsitektur.md`, `docs/schema-institusi.md`).
   - `AGENTS.md` ini adalah acuan operasional dan ringkasan sesi terbaru.
3. **Update Sesi Terakhir**:
   - Setiap selesai mengerjakan sesi, **GANTI (replace)** isi section "Sesi Terbaru" di bawah dengan rangkuman ringkas apa yang baru saja diselesaikan.

---

## 6. Sesi Terbaru — Finalisasi Design Tahap 1B: Organizational Context (2026-09-04)

- Memperbarui `docs/design-identity-tahap-1b-organizational-context.md` sesuai draft review terbaru; belum ada migration/code 1B.
- Menambah keputusan `core_roles.grants_global_context` agar `scope = null` tidak otomatis berarti GLOBAL.
- Mengunci `OrgContext` immutable/serializable, precedence, dan perbedaan fail-safe `null` vs `[]` vs `[IDs]`.
- Mengunci resolver sebagai Laravel scoped service per request dengan `refreshActiveInstitution()` tanpa static cache.
- Mengunci institution nonaktif dikeluarkan dari access IDs; assignment lembaga tanpa institution aktif tetap level INSTITUTION dengan `[]`.
- Menetapkan active/primary institution sebagai preference/filter, bukan bukti authorization.
- Menetapkan policy boundary GLOBAL/FOUNDATION, hard-deleted institution handling, consumer migration, dan 36-case test matrix.
- Memisahkan tegas Tahap 1C untuk relationship verification dan portal access grant/revoke.

## 7. Arsip Sesi — Eksekusi Tahap 1A.1c: Final Security Closure (2026-09-04)

- **Authorization Closure & Policies**:
  - Dibuat `StudentPolicy` dan `EmployeePolicy` sebagai thin adapter menuju `AuthorizationService`.
  - `StudentController` & `EmployeeController`: Seluruh aksi CRUD (`index`, `create`, `store`, `edit`, `update`, `destroy`) diproteksi lewat `Gate::authorize()`.
  - `PersonController`: Melindungi sub-actions (`addPosition`, `removePosition`, `copyFromInstitution`, `createAccount`) menggunakan `Gate::authorize()`. Khusus `createAccount` wajib otorisasi `update Person` dan `create User`.
- **ScopeRule Person Resolution**:
  - `ScopeRule` diperluas mengenali target model `Person`: aktor ber-scope lembaga hanya diizinkan berinteraksi dengan `Person` yang memiliki irisan keanggotaan aktif di lembaga yang dipegang aktor. Person eksklusif lembaga lain otomatis diblokir 403.
- **Visibility Sweep**:
  - `StudentController@edit` dan `PersonController@edit`: Query dropdown `persons` dibatasi ke institusi aktif untuk user non-yayasan via `whereHas('memberships')` agar data Person lembaga lain tidak bocor.
- **Database Driver Fix**:
  - Migration fix settings menambahkan import facade `DB` eksplisit dan pengecekan driver non-sqlite yang aman.
- **Verifikasi**:
  - Test regression hardening (`IdentityTahap1AHardeningTest`) 4 passed:
    1. Data-preserving early rollback (memulihkan `institution_id`).
    2. Multi-institution reuse Person via Student & Employee store.
    3. Dropdown visibility sweep (mencegah kebocoran data antar-lembaga).
    4. RBAC & scope enforcement (penolakan 403 tanpa permission & penolakan 403 saat mengedit Person lembaga lain).
  - Test model feature (`IdentityTahap1ATest`) 4 passed (total 8 tests, 31 assertions pass).
  - Seluruh 333 rute sistem aktif normal via `php artisan route:list`.
