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
  test-blog.test/        -> Proyek Eksternal / Landing Utama
  blog.test-blog.test/   -> Proyek Blog (Frontend Artikel)
  dashboard.test-blog.test/ -> Admin CMS + LMS + HR (Inertia/React), Multi-domain modules
  account.test-blog.test/   -> Sistem Auth murni (Login, Register, Logout)
  api.test-blog.test/       -> REST API murni (Diuji dan berjalan dengan respon JSON)
  config/projects.php   -> File otak konfigurasi multi-domain
  RoutesServiceProvider -> Otomatis membagi rute berdasarkan file konfigurasi

✅ TAMBAHAN (Track B - Multi-Tenant LMS & HR):
  Dashboard terstruktur 8 domain: Core, Academic, HR, CMS, Landing, Yayasan, System, Workspace
  12 modul CRUD: Academic Years, Semesters, Kelas, Subjects, Rombel, Students, Employees, Positions, Departments, Attendance, PersonIndex, TransferLogs, Stats
  RBAC: 124 permissions, 7 permission groups, Role Operator
  Gate::before bypass hanya untuk is_primary_super_admin
  Institution management (tanpa switcher topbar)
  Employee & Student form pages standalone (layout sidebar kanan)

✅ TAMBAHAN (Schema Rev — 8 migrations):
  core_persons: UNIQUE(nik, institution_id), institution_id NOT NULL
  academic_students: UNIQUE(nis, institution_id), UNIQUE(person_id, institution_id), hapus kelas
  core_person_positions: UUID PK, UNIQUE(person, position, institution, tanggal_mulai)
  hr_positions: +jenis_jabatan, hr_employees: +department_id
  hr_departments: +institution_id NOT NULL, +kepala_person_id
  crm_donors: +institution_id, +jenis_donatur
  hr_employee_positions: dihapus (duplicate)

❌ BELUM ADA (Target Berikutnya):
  C1 — Yayasan Person Index (Observer + Service + popup "tarik data")
  C2 — Data Copy antar lembaga (via NIK match)
  C3 — Embedded Tabs Person Detail (skills, pendidikan, kontak, dll)
  C4 — Portal Santri (jadwal, nilai, absensi)
  C5 — Yayasan Institution Detail/Edit
  Automated Testing    -> Test suite gagal 404
  Phase 5 (Discovery)  -> Popular Score, Search Analytics
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

## Prioritas Berikutnya (Sesuai Fakta Lapangan)

1. **Perbaikan & Penambahan Automated Tests (Kritis):**
   - Lingkungan pengujian standar (`php artisan test`) mengembalikan error 404 karena `routes/web.php` dikosongkan untuk arsitektur multi-domain.
   - Perlu merekonfigurasi `TestCase` agar mendukung pengujian lintas domain.
   - Menulis *Unit/Feature Tests* untuk logika yang sudah ada (Settings, Email Templates, Bookmarks, dll). Walaupun UI dan Service sudah ada, pengujian masih sangat kurang.

2. **Memulai Phase 5 (Discovery):**
   - **Popular Score:** Membuat fungsionalitas untuk mengkalkulasi popularitas artikel menggunakan sistem peluruhan berdasar waktu (*time-decay formula*). Saat ini `PostMetricsService` baru sebatas menyimpan *view count*.
   - **Search Analytics:** Merekam dan menambang histori pencarian di `SearchService`.

3. **Penyesuaian Lanjutan (Fase 1-4):**
   - Meskipun secara fundamental UI Settings dan Email Templates (Phase 1), serta Bookmarks & Komentar (Phase 3 & 4) sudah diimplementasikan, masih diperlukan *review* kode, penyempurnaan, dan *bug-fixing* lebih lanjut. Jangan asumsikan sudah *100% passed* sebelum di-test secara menyeluruh.

---

## Pedoman Penambahan Proyek Baru
Langkah baku untuk pengembang ketika menambah proyek/landing page eksternal di masa depan:

1. **Folder Rute:** Buat file `routes/projects/{nama_proyek}.php`.
2. **Folder View:** Buat file layout mandiri di `resources/views/projects/{nama_proyek}/index.blade.php`.
3. **Folder Controller:** Buat namespace isolasi di `app/Http/Controllers/Projects/{NamaProyek}/`.
4. **Vite (Khusus Aset Modern):** Daftarkan *entry point* di `vite.config.ts`.
5. **Aktivasi Konfigurasi:** Daftarkan proyek di `config/projects.php` pada kunci `projects` dengan atribut `"active" => true`.
6. **Deployment:** Daftarkan domain di DNS / file `hosts`, lalu jalankan `npm run build` dan `php artisan optimize:clear`.
