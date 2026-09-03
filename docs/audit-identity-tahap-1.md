# LAPORAN AUDIT & STRATEGI IDENTITY TAHAP 1 (REVISI LENGKAP)
**Platform DAYAMA — Single Identity, Membership & Scoping Review**
*Tanggal: 2026-09-04*

---

## 1. Skema Aktual Saat Ini

### A. Tabel Utama
1. **`core_users` (`User`)**:
   - Kolom: `id` (UUID), `username`, `email`, `password`, `person_id` (UUID nullable), `status`, `is_primary_super_admin`, `is_protected`, `is_verified`, `two_factor_*`.
   - Pola: Akun digital login terhubung ke `core_persons` melalui `person_id` (1 User -> 1 Person).
   - *Status:* Tidak memiliki kolom `institution_id` (sudah decoupled di level User).

2. **`core_persons` (`Person`)**:
   - Kolom: `id` (UUID), `institution_id` (UUID FK ke `core_institutions`), `nik`, `passport`, `nama_lengkap`, `gelar_depan`, `gelar_belakang`, `gender`, `tempat_lahir`, `tanggal_lahir`, `agama`, `status_hidup`, `photo`.
   - Index saat ini: `UNIQUE KEY person_nik_per_institution (nik, institution_id)`.
   - Menggunakan trait: `HasInstitutionScope`.

3. **`core_institutions` (`Institution`)**:
   - Kolom: `id` (UUID), `name`, `slug`, `short_name`, `is_active`, hierarki `parent_id` (yayasan -> lembaga).

4. **`core_roles` (`Role` extends Spatie Role)**:
   - Kolom: `id` (UUID), `name`, `guard_name`, `scope` (`yayasan` vs `lembaga`), `rank`, `is_system`, `status`.

5. **`core_role_user` (`RoleUser`)**:
   - Kolom: `id` (UUID), `user_id`, `role_id`, `institution_id` (nullable).
   - Pivot yang mencampuradukkan status hubungan organisasi dengan hak akses akun.

---

## 2. Temuan Audit Khusus (A, B, C)

### A. Audit Duplikasi NIK & Kondisi Data Existing
Pemeriksaan pada database aktual (`modern_blog`):
- **Index Eksisting:** `person_nik_per_institution` pada `(nik, institution_id)`.
- **Hasil Query Pemeriksaan:**
  - Jumlah record `core_persons`: 0 baris (tabel kosong, siap dimigrasi).
  - Jumlah duplikat NIK: 0.
  - Jumlah NIK null: 0.
- **Konsekuensi Arsitektur:**
  - Karena `core_persons` saat ini belum memiliki data riil/produksi, pelepasan `institution_id` tidak menimbulkan tabrakan NIK instan.
  - Namun secara konseptual, `nik` harus diperlakukan sebagai **`UNIQUE NULLABLE` global** (karena ada santri anak kecil/WNA yang belum memiliki NIK/hanya ber-paspor).

### B. Strategi Transisi: `core_role_user` → Membership + Role Assignment
Dilarang melakukan *breaking change* atau penghapusan tabel mendadak. Transisi dilakukan bertahap (Conservative Transition):

1. **Phase 1 (Dual-Write / Co-existence)**:
   - Buat tabel baru: `core_institution_memberships` (Person-centric).
   - `core_role_user` tetap dipertahankan dan tetap berfungsi untuk layer RBAC saat ini.
2. **Phase 2 (Backfill Data)**:
   - Setiap kali ada user yang terafiliasi dengan lembaga, dibuatkan entri `InstitutionMembership` untuk Person terkait.
3. **Phase 3 (Bridge Authorization)**:
   - `ActiveInstitution` dan `ScopeRule` diperbarui secara non-breaking: memeriksa akses melalui `Membership` aktif milik user/person terkait, dengan fallback ke `core_role_user` jika entri membership belum ada.
4. **Phase 4 (Refactor Role Assignment)**:
   - Menghubungkan assignment role ke konteks membership/institution yang eksplisit.
5. **Phase 5 (Cleanup)**:
   - Setelah seluruh logika authorization stabil membaca membership, baru `core_role_user` didegradasi atau disesuaikan.

### C. Definisi Tegas: Organizational Scope vs Personal/Relationship Access
Dua ranah otorisasi ini **tidak boleh dicampur** dalam satu logika scope role:

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. ORGANIZATIONAL SCOPE (Batas Hirarki Organisasi)                          │
├─────────────────────────────────────────────────────────────────────────────┤
│ Model: Scope pada Role (Foundation vs Institution)                          │
│                                                                             │
│ - Foundation Scope:                                                         │
│   Pengurus Yayasan, Auditor, Super Admin.                                   │
│   Sifat: Unrestricted lintas seluruh lembaga di bawah yayasan.              │
│                                                                             │
│ - Institution Scope:                                                        │
│   Kepala Madrasah, TU, Operator MTs, Bendahara MA.                          │
│   Sifat: Terikat pada Active Institution context (ActiveInstitution::id()). │
│   Ditegakkan oleh: InstitutionScope (Eloquent Global Scope pada data        │
│   operasional: Student, Classroom, Employee, Invoice, dsb).                 │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. PERSONAL / RELATIONSHIP ACCESS (Batas Hubungan Subjek)                   │
├─────────────────────────────────────────────────────────────────────────────┤
│ Model: Subject Access via Relationships & Ownership (Bukan Context Switcher)│
│                                                                             │
│ - Santri:                                                                   │
│   Mengakses data dirinya sendiri (User -> Person -> Student).               │
│                                                                             │
│ - Wali Santri:                                                              │
│   Mengakses data anak-anaknya di berbagai lembaga secara simultan melalui   │
│   core_person_relationships (Person Fatimah -> mother_of -> Person Ahmad). │
│                                                                             │
│ Sifat: User-centric. Tidak memaksa wali berpindah ActiveInstitution untuk   │
│ melihat gambaran umum anak-anaknya di portal pusat.                         │
│ Ditegakkan oleh: OwnershipRule & RelationshipPolicy (Bukan InstitutionScope)│
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Desain Target: User – Person – Membership – Relationship

```text
                       ACCOUNT LAYER
                  ┌──────────────────────┐
                  │      core_users      │
                  │ (Identitas Digital)  │
                  │ - id                 │
                  │ - email, password    │
                  │ - person_id ─────────┼──────────┐
                  └──────────────────────┘          │
                                                    ▼
                                              IDENTITY LAYER
                                         ┌──────────────────────┐
                                         │     core_persons     │
                                         │   (Manusia Fisik)    │
                                         │ - id                 │
                                         │ - nik (global unique)│
                                         │ - nama_lengkap       │
                                         │ (TANPA institution)  │
                                         └──────────┬───────────┘
                                                    │
                ┌───────────────────────────────────┴───────────────────────────────────┐
                │ 1:N                                                                   │ 1:N
                ▼                                                                       ▼
┌────────────────────────────────┐                                      ┌────────────────────────────────┐
│  core_institution_memberships  │                                      │   core_person_relationships    │
│  (Hubungan Kelembagaan)        │                                      │   (Hubungan Antar Manusia)     │
│  - id                          │                                      │  - id                          │
│  - person_id                   │                                      │  - person_id (Wali/Ortu)       │
│  - institution_id              │                                      │  - related_person_id (Santri)  │
│  - status (active, alumni, ...)│                                      │  - relationship_type (ayah,...)│
│  - joined_at, left_at          │                                      └────────────────────────────────┘
└───────────────┬────────────────┘
                │
                │ Digunakan untuk otorisasi staf/operator
                ▼
┌────────────────────────────────┐
│        ROLE ASSIGNMENTS        │
│ (Hak Akses Akun per Lembaga)   │
│ - user_id                      │
│ - role_id (Spatie Role)        │
│ - institution_id               │
└────────────────────────────────┘
```

---

## 4. Rencana Langkah (Next Steps)
Audit dan strategi di atas telah disesuaikan dengan prinsip mandor:
- `Person` bebas dari `institution_id` dan `HasInstitutionScope`.
- `InstitutionMembership` bersifat **Person-centric**, independen dari `Role`.
- Global Scope (`InstitutionScope`) tetap aktif di model data operasional (`Student`, `Employee`, `Classroom`, dsb).
- Personal access dipisahkan dari organizational scope (berbasis relationship).

Menunggu review dokumen ini sebelum menyusun dokumen draf `Migration Plan Tahap 1A`.
