# Dayama — Modular Monolith CMS

Dayama adalah sistem manajemen sekolah/pondok pesantren berbasis **Modular Monolith** dengan **12 domain bisnis** yang diisolasi di `app/Modules/`. Dibangun di atas Laravel, mengadopsi arsitektur Multi-Domain untuk frontend (blog, landing, dashboard, api, account) dan Modular Monolith untuk backend. Mendukung **multi-institusi** (yayasan → lembaga) dengan RBAC berbasis *scope*.

**Dokumentasi Arsitektur Lengkap:** [`docs/arsitektur.md`](docs/arsitektur.md)
*(Mencakup struktur direktori, seluruh tabel database per domain, dan filosofi desain)*

## Arsitektur Domain

Proyek ini mengadopsi arsitektur berbasis File untuk memetakan domain. Pengaturannya dikelola sepenuhnya di dalam `config/projects.php`.

### Core System (Sistem Inti)
Aplikasi inti tidak dapat dihapus dan beroperasi di subdomainnya masing-masing:
- `api.test-blog.test` : Sistem REST API murni (Sanctum/Token).
- `account.test-blog.test` : Sistem autentikasi (Login, Register, Lupa Password).
- `dashboard.test-blog.test` : Dasbor admin berbasis React/Inertia.

### Frontends & Projects
Tampilan publik yang dapat ditambah/dimatikan dengan mudah melalui file konfigurasi:
- `test-blog.test` : Halaman pendaratan (Landing page) utama yayasan.
- `blog.test-blog.test` : Sistem publikasi artikel (Blog utama).
- *(Bisa ditambahkan proyek lain seperti `promo.test-blog.test` dengan mengisolasi rutenya di `routes/projects/`).*

### Domain Modules (Backend)
Seluruh model bisnis diisolasi per domain di `app/Modules/`, terdaftar di `composer.json` sebagai namespace `Modules\`:

| Modul | Fokus |
|---|---|
| `Core` | User, Role, Permission, Person, Media, Institution & master data |
| `CMS` | Post, Category, Tag, Comment, Reaksi, Bookmark |
| `Academic` | Santri/Siswa, Rombel, Kelas, Semester, Nilai, Kehadiran |
| `HR` | Karyawan, Departemen, Jabatan, Riwayat Kerja |
| `CRM` | Donatur, Wali, Relasi Keluarga, Partner |
| `Finance` | Donasi, Invoice, Pembayaran, Transaksi |
| `Library` | Katalog buku, Peminjaman |
| `Inventory` | Aset, Inventaris, Ruangan, Stok |
| `AI` | Agen, Percakapan, Embedding, Knowledge |
| `Landing` | Halaman statis, Hero, CTA, FAQ, Testimoni |
| `Yayasan` | Index orang, Log transfer antar lembaga |
| `System` | Setting, Backup, Activity Log, Email Template |

## Multi-Institusi & RBAC

Sistem mendukung hierarki **yayasan → lembaga** (madrasah/sekolah/pondok). Setiap role memiliki *scope* (`yayasan` / `lembaga`):

- **Yayasan scope**: melihat data semua lembaga.
- **Lembaga scope**: hanya melihat data milik institusinya sendiri — diberlakukan otomatis melalui `InstitutionScope` (Eloquent Global Scope) + `ScopeRule` di pipeline autorisasi.

Enforcement dilakukan berlapis: **rules pipeline** (`app/Authorization/`) → **Global Scope** → **middleware** (`CheckInstitutionScope`). Panduan lengkap: [`docs/rbac/README.md`](docs/rbac/README.md).

## Persyaratan Sistem
- PHP 8.3 atau lebih baru.
- Composer
- Node.js (untuk kompilasi Vite)
- MySQL/MariaDB
- [Laragon](https://laragon.org/) (direkomendasikan untuk lingkungan lokal)

## Instalasi (Lingkungan Lokal/Laragon)

1. **Kloning repositori & Install Dependensi**
   ```bash
   git clone <repo_url> test-blog
   cd test-blog
   composer install
   npm install
   ```

2. **Pengaturan `.env`**
   Salin file `.env.example` ke `.env`, jalankan `php artisan key:generate`, lalu pastikan konfigurasi Multi-Domain berikut telah diatur:
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
   Buat database (contoh: `modern_blog`) sesuai konfigurasi di `.env`, lalu:
   ```bash
   php artisan migrate --seed
   ```

4. **Kompilasi Aset Frontend**
   ```bash
   npm run build
   ```
   Untuk pengembangan dengan hot-reload, jalankan script `composer run dev` (server, queue, log, dan Vite sekaligus).

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

## Dokumentasi
*   [Arsitektur Modular Monolith (Lengkap)](docs/arsitektur.md) — Struktur file & database 12 domain
*   [Skema Database Multi-Institusi](docs/schema-institusi.md)
*   [Panduan Sistem RBAC](docs/rbac/README.md) — Permission `module.action.scope`, pipeline autorisasi & scoping institusi
*   [Peta Jalan Pengembangan (Roadmap)](docs/roadmap.md)
*   [Alur Implementasi Multi-Domain](docs/Implementasi%20plan.md)
*   [Panduan Instalasi Detail](docs/installation.md) / [Deployment](docs/deployment.md)
*   [SEO & Sitemap](docs/seo.md)
*   [Media Library](docs/media-library.md) / [Sistem Icon](docs/icon-system.md)
*   [Panduan UI & Pengembangan Frontend](docs/development_guidelines.md)