# Rencana Implementasi: Arsitektur Multi-Domain Berbasis File & CMS
## (Revisi v4 - Pembangunan Multi-Domain Selesai)

---

## Ringkasan Perubahan Arah Arsitektur
Berdasarkan evaluasi, membangun *Page Builder* dinamis (menyimpan layout di DB) dan mengatur *Routing* Multi-Domain lewat UI Dashboard terlalu kompleks (*over-engineering*) untuk skala dan kebutuhan proyek blog ini saat ini.

**Keputusan Desain Baru:**
1. **Pages Dinamis (Dibatalkan):** Tidak ada pembuatan tabel `pages`. Jika butuh Landing Page atau Proyek Tambahan (Microsite), hal tersebut dibuat menggunakan struktur file tersendiri (*hardcoded layout* dengan *compiled assets*).
2. **Multi-Domain Routing (Selesai):** Diatur menggunakan konfigurasi statis (`config/projects.php`), bukan disimpan di database.
3. **Isolasi Modul (Selesai):** Setiap "sistem" memiliki rute dan pemetaan *middleware* masing-masing yang diatur cerdas melalui `RoutesServiceProvider`.
4. **Fokus Tetap:** Fitur dasar seperti Email Templates, Settings UI, dan fondasi awal tetap dipertahankan untuk dikerjakan.

---

## Kondisi Saat Ini (Telah Diimplementasikan)

```
✅ ADA:
  test-blog.test/       -> Proyek Eksternal / Landing Utama
  blog.test-blog.test/  -> Proyek Blog (Frontend Artikel)
  dashboard.test-blog.test/ -> Admin CMS (Inertia/React), Dilindungi Middleware `CheckDashboardAccess`
  account.test-blog.test/   -> Sistem Auth murni (Login, Register, Logout)
  api.test-blog.test/       -> REST API murni (Diuji dan berjalan dengan respon JSON)
  config/projects.php   -> File otak konfigurasi multi-domain
  RoutesServiceProvider -> Otomatis membagi rute berdasarkan file konfigurasi

❌ BELUM ADA (Target Berikutnya):
  Email Templates      -> Migrasi tabel sudah ada, integrasi Mailable dan UI belum.
  Settings UI          -> UI Dashboard untuk mengelola konfigurasi umum belum lengkap.
```

---

## Arsitektur Target (Monolith Multi-Subdomain)

```
Proyek Inti (Core):
  dashboard.contoh.com/ -> Sistem Admin (Inertia)
  account.contoh.com/   -> Login / Register / Reset Password
  api.contoh.com/       -> REST API

Proyek Tambahan (Frontend Modules):
  contoh.com/           -> Halaman Pendaratan (Landing) Utama
  blog.contoh.com/      -> Halaman baca artikel Blog
  promo.contoh.com/     -> (Contoh Implementasi Eksternal Masa Depan)
```

---

## PHASE 1 - Penyelesaian CMS Foundation
**Prioritas: PERTAMA**

1. **Email Templates:**
   - Mengonfigurasi `TemplateMailable` agar dinamis.
   - Menyempurnakan fungsionalitas `EmailTemplateController` untuk operasi CRUD di Dashboard.
2. **Settings UI:**
   - Memastikan antarmuka React/Inertia untuk `SettingsController` beroperasi secara penuh untuk membaca dan menyimpan variabel non-env ke database.

---

## Pedoman Penambahan Proyek Baru
Langkah baku untuk pengembang ketika menambah proyek/landing page eksternal di masa depan:

1. **Folder Rute:** Buat file `routes/projects/{nama_proyek}.php`.
2. **Folder View:** Buat file layout mandiri di `resources/views/projects/{nama_proyek}/index.blade.php`.
3. **Folder Controller:** Buat namespace isolasi di `app/Http/Controllers/Projects/{NamaProyek}/`.
4. **Vite (Khusus Aset Modern):** Daftarkan *entry point* di `vite.config.ts`.
5. **Aktivasi Konfigurasi:** Daftarkan proyek di `config/projects.php` pada kunci `projects` dengan atribut `"active" => true`.
6. **Deployment:** Daftarkan domain di DNS / file `hosts`, lalu jalankan `npm run build` dan `php artisan optimize:clear`.
