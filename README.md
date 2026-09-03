# DAYAMA — Platform Manajemen Yayasan & Lembaga Pendidikan

DAYAMA adalah ekosistem aplikasi yayasan dan lembaga pendidikan berbasis **Modular Monolith** dengan pendekatan **Multi-Application** dan **Multi-Runtime**. Backend diorganisasikan ke dalam 12 domain di `app/Modules/`, dashboard berbasis **React 19 (Inertia)**, dan situs publik berbasis Blade SSR.

Sistem mendukung **Single Identity** (User digital ↔ Person fisik ↔ Multi-Membership lembaga), **Failure Isolation** antar-aplikasi, serta RBAC berbasis *scope* (Yayasan, Lembaga, Personal).

**Dokumentasi Arsitektur & Roadmap:**
- Panduan Agen & Operasional: [`AGENTS.md`](AGENTS.md)
- Implementasi Plan Platform: [`docs/Implementasi plan.md`](docs/Implementasi%20plan.md)
- Arsitektur Modular Monolith: [`docs/arsitektur.md`](docs/arsitektur.md)
- Skema Database Institusi: [`docs/schema-institusi.md`](docs/schema-institusi.md)

---

## Arsitektur Platform & Domain

Pemetaan domain dikelola secara terpusat melalui `config/platform.php` dan diturunkan otomatis dari `APP_ROOT_DOMAIN`:
- Development: `*.dayama.test` (Local router / Laragon)
- Production: `*.dayama.id` (Reverse proxy server)

### 1. Platform Applications (`routes/apps/`)
- `account.dayama.test` : Identity Provider tunggal (Login, Register, Profil, 2FA, Sesi).
- `dashboard.dayama.test` : Workspace operasional staf/admin (Admin Yayasan, Kepala Lembaga, Operator, Bendahara, Guru).
- `portal.dayama.test` : Workspace personal terpadu (Santri, Wali Santri, Alumni, Guru).
- `psb.dayama.test` : Pusat Penerimaan Santri Baru terpusat seluruh lembaga.
- `data.dayama.test` : Pusat data induk manusia (Person Index) dan agregasi yayasan.
- `api.dayama.test` : REST API Gateway (Sanctum/Token) untuk integrasi eksternal & aplikasi mobile.

### 2. Sites & Content Surfaces (`routes/sites/`)
- `dayama.test` : Halaman pendaratan (Landing page) utama yayasan di root domain.
- `blog.dayama.test` : Publikasi berita, artikel, dan konten media yayasan.
- Situs Lembaga (`mts.dayama.test`, `ma.dayama.test`, dsb.) : Diselesaikan secara dinamis via Core Site Registry.

---

## Instalasi (Lingkungan Lokal / Laragon)

1. **Kloning Repositori & Install Dependensi**
   ```bash
   git clone https://github.com/assasakiy/dayama.git dayama
   cd dayama
   composer install
   npm install
   ```

2. **Pengaturan `.env`**
   Salin file `.env.example` ke `.env`, lalu jalankan `php artisan key:generate`:
   ```env
   APP_NAME="DAYAMA"
   APP_ROOT_DOMAIN=dayama.test
   SESSION_DOMAIN=.dayama.test

   DOMAIN_AUTH=account.dayama.test
   DOMAIN_DASHBOARD=dashboard.dayama.test
   DOMAIN_PORTAL=portal.dayama.test
   DOMAIN_PSB=psb.dayama.test
   DOMAIN_DATACENTER=data.dayama.test
   DOMAIN_API=api.dayama.test
   DOMAIN_BLOG=blog.dayama.test
   ```

3. **Database & Migrasi**
   ```bash
   php artisan migrate --seed
   ```

4. **Kompilasi Aset Frontend**
   ```bash
   npm run build
   # Untuk development dengan hot-reload:
   npm run dev
   ```

5. **Pengaturan File Hosts Windows**
   Tambahkan pemetaan berikut di `C:\Windows\System32\drivers\etc\hosts`:
   ```text
   127.0.0.1 dayama.test
   127.0.0.1 account.dayama.test
   127.0.0.1 dashboard.dayama.test
   127.0.0.1 portal.dayama.test
   127.0.0.1 psb.dayama.test
   127.0.0.1 data.dayama.test
   127.0.0.1 api.dayama.test
   127.0.0.1 blog.dayama.test
   ```
*   [Panduan Sistem RBAC](docs/rbac/README.md) — Permission `module.action.scope`, pipeline autorisasi & scoping institusi
*   [Peta Jalan Pengembangan (Roadmap)](docs/roadmap.md)
*   [Alur Implementasi Multi-Domain](docs/Implementasi%20plan.md)
*   [Panduan Instalasi Detail](docs/installation.md) / [Deployment](docs/deployment.md)
*   [SEO & Sitemap](docs/seo.md)
*   [Media Library](docs/media-library.md) / [Sistem Icon](docs/icon-system.md)
*   [Panduan UI & Pengembangan Frontend](docs/development_guidelines.md)