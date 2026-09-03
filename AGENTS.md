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

## 6. Sesi Terbaru — Refaktor Fondasi Multi-Domain & Platform Config (2026-09-04)

- **Restrukturisasi Domain**: Menghapus konsep `config/projects.php` lama dan menggantinya dengan `config/platform.php`.
- **Root Domain Centric**: Menetapkan `APP_ROOT_DOMAIN=dayama.test` di `.env` lokal dan `.env.example`, otomatis merakit domain `account`, `dashboard`, `portal`, `psb`, `data`, `api`, `blog`, dan root landing `dayama.test`.
- **Rute Terstruktur**: Memisahkan rute ke dalam:
  - `routes/apps/`: `account.php`, `dashboard.php`, `portal.php`, `psb.php`, `datacenter.php`, `api.php`.
  - `routes/sites/`: `landing.php`, `blog.php`.
- **RoutesServiceProvider**: Rute aplikasi dan situs dimuat dinamis dari konfigurasi `platform.php`.
- **Kompatibilitas Blade & Controllers**: Memperbarui seluruh referensi `config('projects.*')` di `HandleInertiaRequests`, `CheckDashboardAccess`, `AuthController`, `bootstrap/app.php`, serta seluruh view Blade landing page ke `config('platform.*')`.
- **Verifikasi**: 333 rute sistem berhasil terdaftar tanpa konflik domain via `php artisan route:list`.
