# Schema Database — Data Institusi (Madrasah)

> **Keputusan arsitektur:** Data person bersifat **copy per lembaga** (bukan shared).
> Satu NIK boleh muncul di banyak lembaga dengan baris `core_persons` berbeda.
> Edit data di lembaga A tidak memengaruhi lembaga B.
> Pencocokan NIK lintas lembaga melalui tabel index terpisah `yayasan_person_index`.

---

## 1. Institusi (`core_institutions` + turunannya)

```sql
CREATE TABLE core_institutions (
    id                  CHAR(36) PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    slug                VARCHAR(255) UNIQUE NOT NULL,
    logo_url            VARCHAR(255) NULL,
    cover_url           VARCHAR(255) NULL,
    short_description   TEXT NULL,
    content             LONGTEXT NULL,
    facilities          JSON NULL,
    extracurriculars    JSON NULL,
    registration_url    VARCHAR(255) NULL,
    is_active           BOOLEAN DEFAULT TRUE,
    sort_order          INT DEFAULT 0,
    parent_id           CHAR(36) NULL,
    institution_type_id CHAR(36) NULL,
    status              VARCHAR(30) DEFAULT 'draft',
    kode                VARCHAR(100) UNIQUE NULL,
    alamat              TEXT NULL,
    created_by          CHAR(36) NULL,
    completed_by        CHAR(36) NULL,
    completed_at        TIMESTAMP NULL,
    verified_at         TIMESTAMP NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,

    FOREIGN KEY (parent_id)           REFERENCES core_institutions(id) ON DELETE SET NULL,
    FOREIGN KEY (institution_type_id) REFERENCES core_institution_types(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)          REFERENCES core_users(id) ON DELETE SET NULL,
    FOREIGN KEY (completed_by)        REFERENCES core_users(id) ON DELETE SET NULL
);

CREATE TABLE core_institution_types (
    id          CHAR(36) PRIMARY KEY,
    nama        VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) UNIQUE NOT NULL,
    deskripsi   TEXT NULL,
    sort_order  INT DEFAULT 0,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);

CREATE TABLE core_institution_addresses (
    id              CHAR(36) PRIMARY KEY,
    institution_id  CHAR(36) NOT NULL UNIQUE,
    alamat_jalan    VARCHAR(255) NULL,
    rt              VARCHAR(5) NULL,
    rw              VARCHAR(5) NULL,
    kode_pos        VARCHAR(10) NULL,
    provinsi        VARCHAR(255) NULL,
    kabupaten_kota  VARCHAR(255) NULL,
    kecamatan       VARCHAR(255) NULL,
    desa_kelurahan  VARCHAR(255) NULL,
    latitude        DECIMAL(10,7) NULL,
    longitude       DECIMAL(10,7) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE
);

CREATE TABLE core_institution_contacts (
    id              CHAR(36) PRIMARY KEY,
    institution_id  CHAR(36) NOT NULL,
    contact_type_id CHAR(36) NULL,
    value           VARCHAR(255) NOT NULL,
    is_primary      BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (institution_id)  REFERENCES core_institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_type_id) REFERENCES core_contact_types(id) ON DELETE SET NULL
);

CREATE TABLE core_institution_legalities (
    id                      CHAR(36) PRIMARY KEY,
    institution_id          CHAR(36) NOT NULL UNIQUE,
    nspp                    VARCHAR(20) UNIQUE NULL,
    npsn                    VARCHAR(20) UNIQUE NULL,
    kode_registrasi         VARCHAR(255) NULL,
    nomor_ijop              VARCHAR(255) NULL,
    tanggal_ijop            DATE NULL,
    nomor_akta_yayasan      VARCHAR(255) NULL,
    npwp                    VARCHAR(20) NULL,
    tahun_berdiri_masehi    INT NULL,
    tahun_berdiri_hijriyah  INT NULL,
    created_at              TIMESTAMP NULL,
    updated_at              TIMESTAMP NULL,

    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE
);
```

**Alur:**
```
core_institutions (1) ──→ core_institution_addresses (1)
core_institutions (1) ──→ core_institution_contacts (N)
core_institutions (1) ──→ core_institution_legalities (1)
core_institutions (N) ──→ core_institution_types (1)
```

---

## 2. Person — Identitas Riil Seseorang

> **Model COPY:** Setiap institusi punya salinan data person sendiri.
> `UNIQUE(nik, institution_id)` — NIK yang sama boleh ada di institusi berbeda.

```sql
CREATE TABLE core_persons (
    id              CHAR(36) PRIMARY KEY,
    institution_id  CHAR(36) NOT NULL,          -- -- kepemilikan: person milik institusi mana
    nik             VARCHAR(20) NULL,
    passport        VARCHAR(50) NULL,
    nama_depan      VARCHAR(255) NOT NULL,
    nama_belakang   VARCHAR(255) NULL,
    nama_lengkap    VARCHAR(255) NOT NULL,
    gelar_depan     VARCHAR(255) NULL,
    gelar_belakang  VARCHAR(255) NULL,
    gender          ENUM('L','P') NULL,
    tempat_lahir    VARCHAR(255) NULL,
    tanggal_lahir   DATE NULL,
    agama           VARCHAR(30) NULL,
    status_hidup    BOOLEAN DEFAULT TRUE,
    photo           VARCHAR(255) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_nik_per_institution (nik, institution_id),
    INDEX idx_nik (nik),
    INDEX idx_nama (nama_lengkap)
);
```

### Index NIK Lintas Lembaga (Yayasan)

Tabel terpisah untuk mencocokkan person yang sama di dua lembaga berbeda:

```sql
CREATE TABLE yayasan_person_index (
    id              CHAR(36) PRIMARY KEY,
    nik             VARCHAR(20) UNIQUE NULL,     -- index global — satu baris per NIK se-yayasan
    nama_lengkap    VARCHAR(255) NOT NULL,
    tanggal_lahir   DATE NULL,
    refs            JSON NOT NULL,                -- [{institution_id, person_id, created_at}, ...]
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    INDEX idx_nik (nik)
);
```

Cara kerja:
1. Saat person dibuat di lembaga A, sistem cek `yayasan_person_index` via NIK.
2. Jika NIK sudah ada → person diduga sudah terdaftar di lembaga lain (tampilkan info ke user).
3. Jika NIK baru → insert baris baru di index, refs berisi `[{institution_id, person_id}]`.
4. Saat person dicopy ke lembaga B → tambah entry refs, jangan ganti.

### Turunan Person (1:1)

```sql
CREATE TABLE core_users (
    id                      CHAR(36) PRIMARY KEY,
    person_id               CHAR(36) NULL UNIQUE,
    username                VARCHAR(255) UNIQUE NULL,
    email                   VARCHAR(255) UNIQUE NOT NULL,
    password                VARCHAR(255) NOT NULL,
    is_primary_super_admin  BOOLEAN DEFAULT FALSE,
    is_protected            BOOLEAN DEFAULT FALSE,
    status                  VARCHAR(50) DEFAULT 'active',
    -- remember_token, email_verified_at, timestamps, dll

    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE SET NULL
);
```

### Atribut Person (1:N)

```sql
CREATE TABLE core_contacts (
    id              CHAR(36) PRIMARY KEY,
    person_id       CHAR(36) NOT NULL,
    contact_type_id CHAR(36) NULL,
    value           VARCHAR(255) NOT NULL,
    is_primary      BOOLEAN DEFAULT FALSE,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (person_id)       REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_type_id) REFERENCES core_contact_types(id) ON DELETE SET NULL
);

CREATE TABLE core_addresses (
    id                CHAR(36) PRIMARY KEY,
    person_id         CHAR(36) NOT NULL,
    address_type_id   CHAR(36) NULL,
    alamat            TEXT NULL,
    provinsi          VARCHAR(255) NULL,
    kabupaten_kota    VARCHAR(255) NULL,
    kecamatan         VARCHAR(255) NULL,
    desa_kelurahan    VARCHAR(255) NULL,
    kode_pos          VARCHAR(10) NULL,
    latitude          DECIMAL(10,7) NULL,
    longitude         DECIMAL(10,7) NULL,
    is_primary        BOOLEAN DEFAULT FALSE,
    created_at        TIMESTAMP NULL,
    updated_at        TIMESTAMP NULL,

    FOREIGN KEY (person_id)       REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (address_type_id) REFERENCES core_address_types(id) ON DELETE SET NULL
);

CREATE TABLE core_person_educations (
    id                  CHAR(36) PRIMARY KEY,
    person_id           CHAR(36) NOT NULL,
    education_level_id  CHAR(36) NULL,
    institution_name    VARCHAR(255) NULL,
    jurusan             VARCHAR(255) NULL,
    tahun_masuk         INT NULL,
    tahun_lulus         INT NULL,
    status              VARCHAR(30) DEFAULT 'selesai',

    FOREIGN KEY (person_id)          REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (education_level_id) REFERENCES academic_education_levels(id) ON DELETE SET NULL
);

CREATE TABLE core_certificates (
    id              CHAR(36) PRIMARY KEY,
    person_id       CHAR(36) NOT NULL,
    nama            VARCHAR(255) NOT NULL,
    penerbit        VARCHAR(255) NULL,
    nomor           VARCHAR(255) NULL,
    tanggal_terbit  DATE NULL,
    expired_at      DATE NULL,
    file            VARCHAR(255) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE
);

CREATE TABLE crm_family_relations (
    id                    CHAR(36) PRIMARY KEY,
    person_id             CHAR(36) NOT NULL,
    related_person_id     CHAR(36) NOT NULL,
    relationship_type_id  CHAR(36) NOT NULL,
    created_at            TIMESTAMP NULL,
    updated_at            TIMESTAMP NULL,

    FOREIGN KEY (person_id)            REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (related_person_id)    REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (relationship_type_id) REFERENCES core_relationship_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_family (person_id, related_person_id, relationship_type_id)
);
```

**Alur:**
```
core_persons (1) ──→ core_users (0..1)
core_persons (1) ──→ core_contacts (N)
core_persons (1) ──→ core_addresses (N)
core_persons (1) ──→ core_person_educations (N)
core_persons (1) ──→ core_certificates (N)
core_persons (1) ──→ crm_family_relations (N) ──→ core_persons (N)
```

---

## 3. Pendidik & Tenaga Kependidikan (PTK)

### Status Kepegawaian

```sql
CREATE TABLE hr_employment_statuses (
    id          CHAR(36) PRIMARY KEY,
    nama        VARCHAR(255) UNIQUE NOT NULL,    -- PNS, PPPK, Honorer, Non-PNS, dll
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);
```

### Data PTK

```sql
CREATE TABLE hr_employees (
    id                          CHAR(36) PRIMARY KEY,
    person_id                   CHAR(36) NOT NULL,
    institution_id              CHAR(36) NOT NULL,
    employment_status_id        CHAR(36) NULL,
    department_id               CHAR(36) NULL,          -- FK → hr_departments
    nuptk                       VARCHAR(20) UNIQUE NULL,
    nip                         VARCHAR(20) NULL,
    sudah_sertifikasi           BOOLEAN DEFAULT FALSE,
    nomor_sertifikat_pendidik   VARCHAR(255) NULL,
    jam_mengajar_per_minggu     INT NULL,
    created_at                  TIMESTAMP NULL,
    updated_at                  TIMESTAMP NULL,

    FOREIGN KEY (person_id)            REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id)       REFERENCES core_institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (employment_status_id) REFERENCES hr_employment_statuses(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id)        REFERENCES hr_departments(id) ON DELETE SET NULL,
    UNIQUE KEY unique_employee (person_id, institution_id)
);
```

### Jabatan

> Jabatan menjawab: **"dia kerja sebagai apa?"** — peran dalam organisasi.
> Bukan pekerjaan. Satu orang bisa punya banyak jabatan (guru + wali kelas).
> Hanya posisi kerja/struktural — Santri, Alumni, Wali Santri, Donatur bukan jabatan.

```sql
CREATE TABLE hr_positions (
    id              CHAR(36) PRIMARY KEY,
    nama            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) UNIQUE NOT NULL,
    deskripsi       TEXT NULL,
    jenis_jabatan   VARCHAR(30) NOT NULL DEFAULT 'fungsional_pendidikan',
        -- 'struktural_yayasan'   → Ketua Yayasan, Dewan Pembina, Dewan Pengawas
        -- 'fungsional_pendidikan' → Guru, Guru Piket, Staf TU, Pustakawan
        -- 'tambahan'             → Wali Kelas, Waka Kurikulum, Pembina Ekstra
        -- 'struktural_lembaga'   → Kepala Madrasah, Wakil Kepala, Mudir
    sort_order      INT DEFAULT 0,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
);
```

### Pivot Person ↔ Position

> Composite key diganti surrogate UUID agar satu orang bisa menjabat posisi yang sama
> di periode berbeda (misal: Kepala Madrasah 2019-2021, lalu lagi 2024-sekarang).

```sql
CREATE TABLE core_person_positions (
    id              CHAR(36) PRIMARY KEY,
    person_id       CHAR(36) NOT NULL,
    position_id     CHAR(36) NOT NULL,
    institution_id  CHAR(36) NULL,
    nomor_induk     VARCHAR(255) NULL,          -- nomor SK / nomor induk jabatan
    tanggal_mulai   DATE NULL,
    tanggal_selesai DATE NULL,
    status          VARCHAR(255) NULL,           -- aktif / nonaktif
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (person_id)       REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (position_id)     REFERENCES hr_positions(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id)  REFERENCES core_institutions(id) ON DELETE SET NULL,
    UNIQUE KEY unique_position_tenure (person_id, position_id, institution_id, tanggal_mulai)
);
```

### Departemen / Bidang

> Departemen menjawab: **"dia di bawah koordinasi mana?"** — unit kerja.
> Satu pegawai biasanya dalam satu departemen utama.
> Struktur departemen berbeda tiap lembaga → `institution_id` NOT NULL.

```sql
CREATE TABLE hr_departments (
    id                  CHAR(36) PRIMARY KEY,
    institution_id      CHAR(36) NOT NULL,
    nama                VARCHAR(255) NOT NULL,
    parent_id           CHAR(36) NULL,
    kepala_person_id    CHAR(36) NULL,
    sort_order          INT DEFAULT 0,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,

    FOREIGN KEY (institution_id)   REFERENCES core_institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id)        REFERENCES hr_departments(id) ON DELETE SET NULL,
    FOREIGN KEY (kepala_person_id) REFERENCES core_persons(id) ON DELETE SET NULL
);
```

**Contoh isi departemen (konteks madrasah/pesantren):**
- Bidang Kurikulum & Pengajaran
- Bidang Kesiswaan
- Bidang Sarana & Prasarana
- Bidang Keuangan
- Bidang Humas & Alumni
- Bidang Keasramaan / Kepesantrenan
- Tata Usaha & Administrasi

### Riwayat & Absensi

```sql
CREATE TABLE hr_employment_histories (
    id              CHAR(36) PRIMARY KEY,
    person_id       CHAR(36) NOT NULL,
    institution_id  CHAR(36) NULL,
    jabatan         VARCHAR(255) NOT NULL,     -- free text (pekerjaan sebelumnya)
    mulai           DATE NULL,
    selesai         DATE NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (person_id)       REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id)  REFERENCES core_institutions(id) ON DELETE SET NULL
);

CREATE TABLE hr_attendances (
    id              CHAR(36) PRIMARY KEY,
    employee_id     CHAR(36) NOT NULL,
    date            DATE NOT NULL,
    check_in        DATETIME NULL,
    check_out       DATETIME NULL,
    status          VARCHAR(50) NOT NULL,       -- hadir / izin / sakit / alpha / cuti
    notes           TEXT NULL,
    recorded_by     CHAR(36) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (employee_id)  REFERENCES hr_employees(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by)  REFERENCES core_users(id) ON DELETE SET NULL,
    INDEX idx_attendance_employee (employee_id),
    INDEX idx_attendance_date (date)
);
```

**Alur PTK:**
```
core_institutions (1) ──→ hr_employees (N) ──→ core_persons (1)
                                          └──→ hr_attendances (N)
                                          └──→ hr_departments (N:1)
core_persons (N) ──→ core_person_positions (N) ──→ hr_positions (1) [+ jenis_jabatan]
```

---

## 4. Donatur

> Status Santri, Alumni, dan Wali Santri sudah terwakili oleh tabel lain:
> - Santri → `academic_students`
> - Alumni → `academic_students.status = 'alumni'`
> - Wali Santri → `crm_family_relations` dengan tipe hubungan orang tua/wali
>
> Donatur adalah satu-satunya yang belum punya tempat → tabel baru.

```sql
CREATE TABLE crm_donors (
    id              CHAR(36) PRIMARY KEY,
    person_id       CHAR(36) NOT NULL,
    institution_id  CHAR(36) NULL,           -- NULL = donatur yayasan, terisi = donatur lembaga spesifik
    jenis_donatur   VARCHAR(30) NULL,         -- perorangan / lembaga / rutin / insidental
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (person_id)      REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE SET NULL
);
```

---

## 5. Siswa

```sql
CREATE TABLE academic_students (
    id                  CHAR(36) PRIMARY KEY,
    person_id           CHAR(36) NOT NULL,
    institution_id      CHAR(36) NOT NULL,
    nis                 VARCHAR(30) NOT NULL,
    nisn                VARCHAR(20) NULL,
    angkatan            VARCHAR(10) NOT NULL,
    status              VARCHAR(20) DEFAULT 'aktif',     -- aktif / alumni / keluar / mutasi
    nama_ibu_kandung    VARCHAR(255) NULL,
    tempat_tinggal      VARCHAR(30) NULL,
    nomor_kk            VARCHAR(20) NULL,
    nomor_kip           VARCHAR(20) NULL,
    cita_cita           VARCHAR(255) NULL,
    hobi                VARCHAR(255) NULL,
    foto                VARCHAR(255) NULL,
    waktu_tempuh_menit  INT NULL,
    is_locked           BOOLEAN DEFAULT FALSE,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,

    FOREIGN KEY (person_id)       REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id)  REFERENCES core_institutions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_nis_per_institution (nis, institution_id),
    UNIQUE KEY unique_student_person_institution (person_id, institution_id)
);
```

> **Perubahan dari skema awal:**
> - `nis` → `UNIQUE(nis, institution_id)` — NIS bebas per lembaga (tidak perlu global).
> - `nisn` → tetap nullable, tanpa unique global (nisn nasional unik, tapi bisa NULL untuk siswa baru).
> - `kelas` → **DIHAPUS**. Sumber kebenaran kelas ada di `academic_classroom_student`.
> - `UNIQUE(person_id, institution_id)` ditambah — cegah duplikasi data siswa per lembaga.

### Akademik Siswa

```sql
CREATE TABLE academic_academic_years (
    id          CHAR(36) PRIMARY KEY,
    nama        VARCHAR(255) UNIQUE NOT NULL,
    is_active   BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);

CREATE TABLE academic_classrooms (
    id                      CHAR(36) PRIMARY KEY,
    institution_id          CHAR(36) NOT NULL,
    academic_year_id        CHAR(36) NOT NULL,
    wali_kelas_person_id    CHAR(36) NULL,
    nama                    VARCHAR(255) NOT NULL,
    tingkat                 VARCHAR(255) NULL,
    created_at              TIMESTAMP NULL,
    updated_at              TIMESTAMP NULL,

    FOREIGN KEY (institution_id)        REFERENCES core_institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id)      REFERENCES academic_academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (wali_kelas_person_id)  REFERENCES core_persons(id) ON DELETE SET NULL
);

CREATE TABLE academic_classroom_student (
    id              CHAR(36) PRIMARY KEY,
    classroom_id    CHAR(36) NOT NULL,
    student_id      CHAR(36) NOT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (classroom_id) REFERENCES academic_classrooms(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id)   REFERENCES academic_students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_class_student (classroom_id, student_id)
);

CREATE TABLE academic_subjects (
    id          CHAR(36) PRIMARY KEY,
    nama        VARCHAR(255) UNIQUE NOT NULL,
    kode        VARCHAR(20) NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);

CREATE TABLE academic_teaching_assignments (
    id              CHAR(36) PRIMARY KEY,
    person_id       CHAR(36) NOT NULL,
    subject_id      CHAR(36) NOT NULL,
    classroom_id    CHAR(36) NOT NULL,
    jam_per_minggu  INT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (person_id)    REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id)   REFERENCES academic_subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES academic_classrooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_teaching (person_id, subject_id, classroom_id)
);

CREATE TABLE academic_student_transfers (
    id                      CHAR(36) PRIMARY KEY,
    student_id              CHAR(36) NOT NULL,
    from_institution_id     CHAR(36) NULL,
    to_institution_id       CHAR(36) NULL,
    jenis                   VARCHAR(10) NOT NULL,       -- masuk / keluar
    alasan                  VARCHAR(255) NULL,
    nomor_dokumen_emis      VARCHAR(255) NULL,
    tanggal                 DATE NULL,
    status                  VARCHAR(20) DEFAULT 'diajukan',
    created_at              TIMESTAMP NULL,
    updated_at              TIMESTAMP NULL,

    FOREIGN KEY (student_id)            REFERENCES academic_students(id) ON DELETE CASCADE,
    FOREIGN KEY (from_institution_id)   REFERENCES core_institutions(id) ON DELETE SET NULL,
    FOREIGN KEY (to_institution_id)     REFERENCES core_institutions(id) ON DELETE SET NULL
);
```

**Alur Siswa:**
```
core_institutions (1) ──→ academic_students (N) ──→ core_persons (1)
academic_students (1) ──→ academic_classroom_student (N) ──→ academic_classrooms (1)
academic_classrooms (N) ──→ academic_academic_years (1)
academic_classrooms (1) ──→ core_persons (1) [wali kelas]
academic_teaching_assignments (N) ──→ core_persons (1) [guru] + academic_subjects (1) + academic_classrooms (1)
```

---

## 6. Role & Akses (Institution-Scoped)

```sql
CREATE TABLE core_role_user (
    id              CHAR(36) PRIMARY KEY,
    user_id         CHAR(36) NOT NULL,
    role_id         CHAR(36) NOT NULL,
    institution_id  CHAR(36) NULL,          -- NULL = global (PSA), terisi = scoped ke institusi itu
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (user_id)         REFERENCES core_users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id)         REFERENCES core_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id)  REFERENCES core_institutions(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_role_institution (user_id, role_id, institution_id)
);
```

**Aturan:**
- `institution_id` = NULL → role global (hanya PSA / super admin lintas)
- `institution_id` = `core_institutions.id` → role hanya berlaku di institusi itu
- Satu user bisa punya role A di institusi 1, role B di institusi 2
- Filter institution switcher hanya menampilkan institusi tempat user punya role

---

## Ringkasan Perubahan dari Skema Awal

| Item | Skema Awal | Skema Revisi |
|------|-----------|-------------|
| `core_persons.nik` | `UNIQUE` global | `UNIQUE(nik, institution_id)` |
| `core_persons.institution_id` | NULL | NOT NULL |
| Model data person | Shared (satu baris dipakai bersama) | Copy (tiap lembaga punya salinan) |
| Index NIK lintas lembaga | Tidak ada | `yayasan_person_index` (json refs) |
| `hr_positions` | Flat tanpa kategori | + `jenis_jabatan` (4 kategori) |
| Santri, Alumni, Wali, Donatur | Campur di jabatan | Santri/Alumni/Wali → tabel existing. Donatur → `crm_donors` |
| `core_person_positions` PK | Composite `(person_id, position_id)` | Surrogate UUID + `UNIQUE(person_id, position_id, institution_id, tanggal_mulai)` |
| `academic_students.nis` | `UNIQUE` global | `UNIQUE(nis, institution_id)` |
| `academic_students.kelas` | Kolom teks bebas | **Dihapus** (gunakan `academic_classroom_student`) |
| `academic_students` person+institution | Tidak ada constraint | `UNIQUE(person_id, institution_id)` |
| `hr_employees` | Tanpa department | + `department_id` FK |
| `hr_departments` | — (belum ada di skema) | `institution_id`, `parent_id`, `kepala_person_id` |
