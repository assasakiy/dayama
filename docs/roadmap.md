# ModernBlog Master Roadmap

Proyek ini bertujuan menjadi sistem manajemen konten (CMS) kelas *Enterprise* dengan fungsionalitas dan arsitektur yang sanggup bersaing dengan Ghost, Hashnode, dan WordPress modern. Pengembangan dilakukan secara iteratif dalam beberapa fase terfokus.

## Phase 1 � MVP Foundation (Selesai)
- Sistem Autentikasi & Otorisasi (*Roles & Permissions*).
- Manajemen Artikel (*Posts*), Kategori (*Categories*), dan Label (*Tags*).
- Media Library Dasar.
- API *Resources* & *Dashboard* Web dasar.

## Phase 2 � Analytics & Reactions (Selesai)
- IdentityService & CrawlerService untuk pendataan lalu lintas bersih tanpa distorsi.
- Sistem *View Count* atomik (*Atomic Unique Tracking* 24 Jam via Cache).
- Sistem *Reactions* ganda (*Guest* dan *User*) dengan struktur *Atomic Lock* untuk menjaga dari data ganda.
- Transisi rekaman *Guest* ke *User* saat melakukan login.
- Fitur stabilisasi (*CLI Command*) cms:repair dan cms:doctor.
- Audit Trails (Riwayat Aktivitas).

## Phase 3 � Personalization (Bookmarks & Reading History)
Fokus pada retensi pengunjung dengan fitur personalisasi bacaan baik untuk *User* maupun *Guest*.
- **Bookmarks**: Menyimpan artikel yang diminati.
- **Reading History**: Riwayat artikel yang telah dilihat, dengan model *upsert* atomik di database.

## Phase 4 � Engagement
Memfasilitasi interaksi yang lebih dalam dengan pengunjung dan membangun komunitas.
- Comments (Sistem Komentar)
- Comment Reactions (Reaksi pada Komentar)
- Nested Replies (Balasan berlapis)
- Mentions (Penandaan Pengguna: @username)
- Notifications (Sistem Notifikasi internal dan eksternal)

## Phase 5 � Discovery
Meningkatkan kemudahan pengunjung menemukan konten yang relevan dan viral.
- Popular Score (Kalkulasi skor popularitas artikel dengan sistem peluruhan berdasar waktu)
- Trending Algorithm (Algoritma tren harian/mingguan)
- Search Analytics (Merekam dan menambang pencarian pengunjung)
- Related Posts (Saran artikel serupa berbasis tag/kategori atau vektor AI)
- Personalized Recommendation (Saran berdasarkan riwayat baca pengguna)

## Phase 6 � Editorial
Memperkaya alur kerja penyuntingan untuk *publishers* atau majalah berskala besar.
- Scheduled Publishing (Penjadwalan publikasi)
- Content Workflow (Alur kerja draf: *Draft* -> *Review* -> *Published*)
- Draft Review (Tinjauan artikel rahasia melalui tautan khusus tanpa publikasi)
- Revision History (Versi draf masa lalu untuk *rollback*)
- Editorial Calendar (Kalender Perencanaan Editorial)

## Phase 7 � Enterprise CMS
Memantapkan platform untuk adopsi berskala masif dengan performa tingkat atas.
- Media Library *Advanced* (Pengelolaan *folders*, galeri kustom)
- Collections & Series (Pengelompokan artikel berseri)
- Newsletters (Surat Edaran Berkala / Berlangganan)
- Multi-site (Pengelolaan banyak situs/domain di 1 CMS)
- Webhooks (Integrasi pemicu sistem ke pihak ketiga)
- Public API & GraphQL API (API Akses *Headless* untuk konsumsi luas)
- Activity Dashboard (Wawasan visual lalu lintas)
- Real-time Analytics (Menggunakan WebSockets/Redis untuk pemantauan *live*)

---

## Track B: Multi-Tenant LMS & HR (Dashboard) � Selesai

Track paralel untuk mengubah dashboard dari CMS-centric menjadi domain-based, mendukung multi-institution (madrasah).

### Arsitektur & RBAC
- Restrukturasi dashboard: Core, Academic, HR, CMS, Landing, Yayasan, System, Workspace
- RBAC diperluas: 124 permissions (dari 63) — 7 permission groups
- Gate::before bypass hanya untuk `is_primary_super_admin` (bukan `hasRole('super-admin')`)
- PrimarySuperAdminRule — hapus hardcoded `hasRole('super-admin')` di semua middleware/controller
- Data ownership per-institution via `institution_id` + `ActiveInstitution` scope
- Institution management dipindah dari topbar ke Core > Institutions
- Create institution + assign default role ke user pembuat
- 🔜 Institution Switcher — UI dropdown di topbar untuk lembaga-scope user memilih konteks institusi aktif. Menu hanya menampilkan institusi yang terdaftar di pivot `core_role_user`. Diperlukan agar user lembaga yang punya akses ke >1 institusi bisa beralih konteks tanpa login ulang.

### Modul CRUD (12 modul)
- **Academic**: Academic Years, Semesters, Kelas, Subjects, Rombel (3 tabs), Students
- **HR**: Employees, Positions, Departments, Attendance
- **Yayasan**: PersonIndex, TransferLogs, Stats
- Masing-masing: Controller + Index + Form/Show page

### Layout & Frontend
- HR & Student form pages standalone (tanpa DashboardLayout) — sidebar kanan (foto + progress + simpan) sticky, tabs + fields di kiri
- Persons/Index disederhanakan (search/filter/statistik saja)
- Employee model — rewrite sesuai skema DB real (nip, nuptk, sertifikasi)
- AttendanceController return flat array (`get()->map()`) bukan paginated object
- Stale Vite chunks dibersihkan (public/build/)

### Role & Seeder
- Role Operator: akses Academic + HR (view/create/edit), tanpa CMS/System/Yayasan
- Pivot table `core_person_positions` (Person ↔ Position)
- Permission groups baru: Akademik, Kepegawaian, Yayasan, Data Inti

### Schema Revisi (8 migrations)
- **core_persons**: `UNIQUE(nik, institution_id)`, `institution_id` NOT NULL — model copy
- **academic_students**: `UNIQUE(nis, institution_id)`, `UNIQUE(person_id, institution_id)`, hapus `kelas`
- **core_person_positions**: UUID PK, `UNIQUE(person_id, position_id, institution_id, tanggal_mulai)`
- **hr_positions**: tambah `jenis_jabatan` (struktural_yayasan, fungsional_pendidikan, tambahan, struktural_lembaga)
- **hr_employees**: tambah `department_id`
- **hr_departments**: tambah `institution_id` NOT NULL + `kepala_person_id`
- **crm_donors**: tambah `institution_id` + `jenis_donatur`
- **hr_employee_positions**: dihapus (duplicate dari `core_person_positions`)

---

## Track C: Data Copy, Index NIK, & Portal Santri

### C1 — Yayasan Person Index (NIK Matching)
**Tujuan:** Popup "NIK ini sudah terdaftar di Lembaga X. Tarik data?" saat input Person baru.

| # | Task | File | Prioritas |
|---|------|------|-----------|
| 1 | Service: `findByNik()`, `syncPerson()`, `getDuplicates()` | `app/Services/YayasanPersonIndexService.php` | Tinggi |
| 2 | Observer: auto-sync ke index saat Person create/update/delete | `app/Observers/PersonObserver.php` | Tinggi |
| 3 | Register observer di `AppServiceProvider` | `app/Providers/AppServiceProvider.php` | Tinggi |
| 4 | Endpoint: cek NIK via API (return daftar institusi) | Route + Controller method | Tinggi |
| 5 | Frontend: popup konfirmasi "Tarik data dari Lembaga X?" | Modifikasi Person Form + modal | Tinggi |
| 6 | Logic: tarik data person dari institusi sumber (copy kontak, alamat, dll) | Service method `copyFromInstitution()` | Tinggi |

### C2 — Data Copy Antar Lembaga
**Tujuan:** Operator bisa menyalin data siswa/pegawai dari lembaga A ke B via NIK match.

| # | Task | File | Prioritas |
|---|------|------|-----------|
| 1 | Halaman "Copy Data" di menu Yayasan | `resources/js/dashboard/Pages/Yayasan/CopyData/` | Sedang |
| 2 | Batch: pilih institusi sumber → pilih tipe data → execute | Controller `YayasanCopyController` | Sedang |
| 3 | Conflict resolver: timestamps mana yang terbaru? | Service `DataCopyService` | Sedang |

### C3 — Embedded Tabs Person Detail
**Tujuan:** Halaman detail Person punya tabs: Skills, Pendidikan, Sertifikat, Kontak, Alamat, Keluarga (sekarang masih pending di frontend)

| # | Task | Prioritas |
|---|------|-----------|
| 1 | Person detail page dengan tab navigation | Sedang |
| 2 | Setiap tab: form inline (create/update/delete via AJAX) | Sedang |
| 3 | Skills, Pendidikan, Sertifikat, Kontak, Alamat, Keluarga | Rendah |

### C4 — Portal Santri
**Tujuan:** Halaman publik/login untuk santri lihat jadwal, nilai, absensi.

| # | Task | Prioritas |
|---|------|-----------|
| 1 | Separate login page untuk santri (beda guard) | Rendah |
| 2 | Dashboard santri: jadwal pelajaran mingguan | Rendah |
| 3 | Nilai: lihat rapor / nilai per semester | Rendah |
| 4 | Absensi: riwayat kehadiran | Rendah |

### C5 — Yayasan Institution Detail/Edit
**Tujuan:** Halaman detail/edit institution untuk data yayasan (alumni, donasi, legalitas).

| # | Task | Prioritas |
|---|------|-----------|
| 1 | Halaman Institution Show: info + stats + timeline | Sedang |
| 2 | Edit institution: tabs (profil, alamat, kontak, legalitas) | Sedang |
| 3 | Manajemen yayasan: data alumni per lembaga | Rendah |
