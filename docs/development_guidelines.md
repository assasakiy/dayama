# Standard Operating Procedures & Development Guidelines

Dokumen ini berisi panduan standar, aturan, dan status implementasi proyek agar setiap pengembang (atau AI Agent) dapat memahami konteks, tidak melupakan aturan yang sudah disepakati, dan menjaga konsistensi _codebase_ sebelum mengeksekusi tugas baru.

---

## 1. Persyaratan Sebelum Memulai Eksekusi (Pre-Flight Checklist)

Sebelum membuat perubahan pada _codebase_, pastikan Anda telah membaca dan memahami area berikut:

1. **Aturan Otorisasi & RBAC (Role-Based Access Control)**
   - **WAJIB BACA:** `docs/rbac/README.md`
   - Jangan pernah mengubah logika *policy*, *middleware* otoritas, atau *permissions* tanpa mengacu pada arsitektur RBAC yang sudah ditetapkan di dokumen tersebut.
   - Semua *Policy* baru atau yang diubah wajib menggunakan `OwnershipRule` atau _trait_ terkait (seperti `AuthorizesMedia` untuk media) dan harus didaftarkan di `AuthServiceProvider` jika tidak menggunakan *auto-discovery*.

2. **Periksa Implementasi Saat Ini**
   - Periksa `docs/Implementasi plan.md` untuk gambaran besar sistem Multi-Domain dan pengaturan dinamis.
   - Baca status implementasi di bawah (Bagian 3) untuk mengetahui _progress_ terakhir.

---

## 2. Aturan UI & UX Dashboard (Frontend Rules)

Sistem menggunakan **React (Inertia.js)** dengan antarmuka berbasis **Tailwind CSS**. Berikut aturan baku yang telah ditetapkan berdasarkan iterasi sebelumnya dan standar komponen aplikasi:

1. **Gunakan Komponen UI Standar (WAJIB):**
   - **Tombol (Buttons):** Jangan menggunakan tag `<button>` mentah dengan _class_ Tailwind (seperti `bg-primary`, `hover:bg-primary/90`, dll). Wajib menggunakan komponen `<Btn>` dari `@dashboard/Components/ui/btn` (contoh: `<Btn variant="primary" size="sm" loading={processing}>`). Komponen ini sudah menangani _spinner_ (_loading state_) dan konsistensi ikon.
   - **Toggle / Switch:** Jangan membuat elemen _switch_ secara manual dengan tag `input[type="checkbox"]` dan gaya Tailwind kustom. Wajib menggunakan komponen `<Switch>` dari `@dashboard/Components/ui/switch`.
   - **Dialog / Modal / Tabs:** Selalu impor komponen dari `@dashboard/Components/ui/dialog` atau `@dashboard/Components/ui/tabs` untuk konsistensi _Pop-up_ di seluruh halaman (seperti pada pengaturan Categories, Tags, dan Profile).
2. **Tata Letak (Layout) Formulir:**
   - Formulir yang memiliki banyak field (seperti Settings) harus menggunakan **Grid Layout (2-kolom)** (`grid grid-cols-1 md:grid-cols-2`) agar tidak memakan banyak ruang vertikal.
   - Elemen yang membutuhkan _full width_ seperti _textarea_, input JSON, atau teks penjelasan (`Note`) harus menggunakan `md:col-span-2`.
3. **Posisi Toggle / Switch:**
   - _Toggle_ utama (misalnya pengaktifan fitur) **harus selalu diletakkan di paling atas** dari daftar *field* pada form, sehingga mudah ditemukan.
4. **Menu Navigasi Dashboard:**
   - Navigasi menu fitur, terutama Settings, **tidak boleh** disembunyikan menggunakan antarmuka sistem *Tab* di dalam sebuah halaman.
   - Semua *group* pengaturan utama harus diakses melalui **Sidebar** sistem (*Sidebar navigation*) untuk mempermudah navigasi bagi sistem CMS modern.
5. **Interaktivitas Tombol Save:**
   - Tombol "Simpan" **wajib dinonaktifkan** (*disabled*) jika tidak ada perubahan data pada *form* (gunakan properti `!isDirty` bawaan _Inertia useForm_ dan properti `loading={processing}` pada komponen `<Btn>`).
6. **Konfigurasi Lingkungan (.env) vs Database:**
   - Berikan fleksibilitas pada sistem. Jika konfigurasi bisa di-*set* melalui `.env`, buatlah pengaturan *Database* sebagai *override* (opsional).
   - Contoh: Konfigurasi Email SMTP. Sistem menggunakan `.env` secara bawaan, namun menyediakan fitur _toggle_ "Gunakan Custom SMTP" di Dashboard agar *user* bisa menimpanya.
   - Elemen UI untuk konfigurasi yang berasal dari `.env` tanpa _override_ harus menggunakan tampilan *Read-only* (Nonaktif/Disabled).

---

## 3. Status Implementasi Fitur & Progress Saat Ini

Proyek saat ini menggunakan arsitektur **File-Based Multi-Domain & CMS**. Berikut adalah progres terakhir dari iterasi pengembangan:

- **[SELESAI] Phase 1: File-Based Multi-Domain Architecture**
  - Implementasi murni arsitektur monolith terisolasi.
  - File `config/projects.php` sebagai otak kendali domain (`core` vs `projects`).
  - Pemisahan total rute API, Dashboard, Auth, Blog, dan Landing.
  - Implementasi Middleware `CheckDashboardAccess` untuk mengamankan akses admin.
  - Pendelegasian pemuatan rute dinamis ke `RoutesServiceProvider`.
- **[BELUM SELESAI] Phase 2: Penyempurnaan Settings UI & Email Templates**
  - Implementasi UI React/Inertia untuk `SettingController` secara penuh.
  - Pembuatan *Email Templates CRUD* dengan *WYSIWYG editor*.
  - Menghubungkan *TemplateMailable* dengan database *Email Templates*.
- **[SELESAI] Refactor UI Standar**
  - Pemindahan antarmuka Settings sepenuhnya ke *Sidebar*.
  - Optimasi antarmuka formulir *Settings* (Grid 2-Kolom).
  - Integrasi Inertia `useForm` (mendukung `isDirty` dan indikasi _loading_ `processing`).
- **[DIBATALKAN] Dynamic Static Pages (via DB)**
  - Karena adopsi `File-Based Multi-Domain`, pembuatan Landing Page atau halaman kustom baru harus di-hardcode sebagai sebuah *Project Module* di file `.blade.php` agar tidak *over-engineering*.

---

## 4. Konvensi Code (Backend / Laravel)

1. **Settings Injection:** 
   - Konfigurasi sistem dinamis disuntikkan secara dinamis saat _runtime_ menggunakan `SettingsServiceProvider`.
2. **Kemanusiaan Template (Security):** 
   - Jangan biarkan pengguna menyetel path template mentah secara langsung. Selalu gunakan sistem _Whitelist_ array untuk menentukan template apa yang tersedia secara legal di sistem untuk meminimalisasi kerentanan _Path Traversal_.
3. **Clean Code & Namespace:**
   - Gunakan *namespace import* (*use statement*) di bagian atas file dan hapus *Fully Qualified Class Names* yang redundan pada _route/controller_.

---
**Catatan untuk AI Agent:**
Setiap kali Anda ditugaskan pada fitur baru atau _refactoring_, pastikan *checklist* dalam panduan ini diikuti secara ketat.
