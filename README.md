# ModernBlog CMS (File-Based Multi-Domain Architecture)

ModernBlog adalah sistem manajemen konten (CMS) kelas *Enterprise* yang dibangun menggunakan Laravel 11. Proyek ini mengadopsi pendekatan **Monolith Multi-Domain**, di mana fungsi inti sistem diisolasi ke dalam subdomain terpisah, dan fungsionalitas tambahan (seperti landing page atau microsite) dapat dilampirkan sebagai proyek independen (*module*) tanpa merusak arsitektur inti.

## Arsitektur Domain
Proyek ini mengadopsi arsitektur berbasis File untuk memetakan domain. Pengaturannya dikelola sepenuhnya di dalam `config/projects.php`.

### Core System (Sistem Inti)
Aplikasi inti tidak dapat dihapus dan beroperasi di subdomainnya masing-masing:
- `api.test-blog.test` : Sistem REST API murni (Sanctum/Token).
- `account.test-blog.test` : Sistem autentikasi (Login, Register, Lupa Password).
- `dashboard.test-blog.test` : Dasbor admin berbasis React/Inertia.

### Frontends & Projects
Tampilan publik yang dapat ditambah/dimatikan dengan mudah melalui file konfigurasi:
- `blog.test-blog.test` : Sistem publikasi artikel (Blog utama).
- `test-blog.test` : Halaman pendaratan (Landing page) utama.
- *(Bisa ditambahkan proyek lain seperti `promo.test-blog.test` dengan mengisolasi rutenya di `routes/projects/`).*

## Persyaratan Sistem
- PHP 8.3 atau lebih baru.
- Composer
- Node.js (untuk kompilasi Vite)
- MySQL/MariaDB

## Instalasi (Lingkungan Lokal/Laragon)

1. **Kloning repositori & Install Dependensi**
   ```bash
   git clone <repo_url> test-blog
   cd test-blog
   composer install
   npm install
   ```

2. **Pengaturan `.env`**
   Salin file `.env.example` ke `.env` dan pastikan konfigurasi Multi-Domain berikut telah diatur dengan benar:
   ```env
   # Pengaturan domain agar cookie login bisa dibagikan lintas subdomain
   SESSION_DOMAIN=.test-blog.test 

   # Pemetaan Domain
   DOMAIN_MAIN=test-blog.test
   DOMAIN_BLOG=blog.test-blog.test
   DOMAIN_DASHBOARD=dashboard.test-blog.test
   DOMAIN_API=api.test-blog.test
   DOMAIN_AUTH=account.test-blog.test
   ```

3. **Database & Migrasi**
   Pastikan Anda sudah membuat database `modern_blog` (atau sesuai konfigurasi di `.env`).
   ```bash
   php artisan migrate --seed
   ```

4. **Kompilasi Aset Frontend**
   ```bash
   npm run build
   ```

5. **Pengaturan File Hosts Windows (PENTING)**
   Agar subdomain dapat diakses secara lokal, tambahkan pemetaan berikut di bagian paling bawah file `C:\Windows\System32\drivers\etc\hosts`:
   ```text
   127.0.0.1 test-blog.test
   127.0.0.1 blog.test-blog.test
   127.0.0.1 dashboard.test-blog.test
   127.0.0.1 account.test-blog.test
   127.0.0.1 api.test-blog.test
   ```

## Cara Menambah Proyek Eksternal (Microsite)
Untuk mendaftarkan *frontend* baru tanpa merusak sistem inti:
1. Buat file rute baru di `routes/projects/{namaproyek}.php`.
2. Daftarkan di file `config/projects.php` di bawah array `projects`:
   ```php
   'promo_2026' => [
       'domain'     => 'promo.test-blog.test',
       'route_file' => 'routes/projects/promo_2026.php',
       'active'     => true,
   ],
   ```
3. Tambahkan domain baru tersebut ke dalam file `hosts` Windows Anda.
4. Jalankan `php artisan optimize:clear`.

## Dokumentasi Tambahan
*   [Alur Implementasi Multi-Domain (Implementasi plan.md)](docs/Implementasi%20plan.md)
*   [Peta Jalan Pengembangan (Roadmap)](docs/roadmap.md)
*   [Panduan UI & Pengembangan Frontend](docs/development_guidelines.md)
*   [Arsitektur RBAC (Role Based Access Control)](docs/rbac/roles_and_permissions.md)
