# MIGRATION PLAN TAHAP 1A — IDENTITY FOUNDATION
**Platform DAYAMA — Decoupling Person & Introducing InstitutionMembership**
*Dokumen Rencana Teknis (Tanpa Eksekusi Skema)*
*Tanggal: 2026-09-04*

---

## 1. Ringkasan & Tujuan Tahap 1A

Tahap 1A bertujuan meletakkan fondasi identitas yang bersih tanpa merusak (*non-breaking*) otorisasi eksisting:
1. **Membebaskan `core_persons`** dari kepemilikan satu lembaga (`institution_id`), menjadikannya representasi manusia fisik global.
2. **Memperkenalkan `core_institution_memberships`** sebagai representasi hubungan kelembagaan yang bersifat *Person-centric*.
3. **Mempertahankan `core_role_user`** sebagai jembatan kompatibilitas (*compatibility bridge*) agar RBAC dan seluruh pipeline otorisasi saat ini tetap berjalan normal 100%.

---

## 2. Batasan Ruang Lingkup (Scope Boundaries)

### A. Di dalam Scope Tahap 1A
- DDL Migration: Modifikasi tabel `core_persons` (drop constraint & kolom `institution_id`, set global nullable unique NIK).
- DDL Migration: Pembuatan tabel baru `core_institution_memberships`.
- Model Updates: `Person`, `Institution`, dan pembuatan model `InstitutionMembership`.
- Menghapus trait `HasInstitutionScope` dari model `Person`.
- Script / prosedur Backfill & Rollback yang teruji aman.
- Unit / feature test untuk integritas relasi Person ↔ Membership ↔ Institution.

### B. EKSPLISIT DI LUAR SCOPE (OUT OF SCOPE TAHAP 1A)
```text
❌ DILARANG refactor ActiveInstitution (tetap gunakan session & role_user)
❌ DILARANG refactor ScopeRule (tetap gunakan role_user)
❌ DILARANG menghapus atau mengubah struktur tabel core_role_user
❌ DILARANG implementasi Personal/Relationship authorization (Wali/Santri Portal)
❌ DILARANG implementasi Portal, PSB, atau Data Center UI
❌ DILARANG implementasi Dynamic Sites Resolution (DB-driven hosts)
❌ DILARANG multi-runtime isolation (tetap 1 runtime Laravel)
```

---

## 3. Rencana Langkah Rinci

### Bagian A: Preflight Checks
Sebelum migration dieksekusi di lingkungan mana pun:
1. **Target Database Verification**:
   - Catat dan pastikan nama database aktif pada koneksi runtime (e.g. database lokal aktif: `modern_blog` atau `dayama`).
   - Periksa status koneksi via `php artisan db:show`.
2. **Snapshot / Backup**:
   - Jalankan dump data & skema: `mysqldump -u root -p [database_name] > backup_pre_tahap1a.sql`.
3. **Pemeriksaan Integritas Data**:
   - Pastikan count data `core_persons`.
   - Deteksi potensi bentrok NIK sebelum constraint diubah:
     ```sql
     SELECT nik, COUNT(*) as c FROM core_persons WHERE nik IS NOT NULL GROUP BY nik HAVING c > 1;
     ```
   - Periksa foreign key dan index eksisting pada `core_persons`:
     - Index: `person_nik_per_institution`
     - FK: `core_persons_institution_id_foreign`

---

### Bagian B: Person Decoupling
1. **Transformasi Index NIK**:
   - Drop composite unique index: `person_nik_per_institution` (`nik`, `institution_id`).
   - Buat index baru: `UNIQUE KEY core_persons_nik_unique (nik)` bersifat nullable (hanya mengunci nilai non-null).
2. **Pelepasan Kolom Lembaga**:
   - Drop foreign key constraint: `core_persons_institution_id_foreign`.
   - Drop kolom: `institution_id` dari tabel `core_persons`.
3. **Pembersihan Model `Person`**:
   - Hapus `use \App\Authorization\Concerns\HasInstitutionScope;`.
   - Hapus `'institution_id'` dari array `$fillable`.
   - Ubah relasi `institution()`: bukan lagi `belongsTo(Institution::class)`, melainkan melalui relasi `memberships()`.

---

### Bagian C: Skema `core_institution_memberships`
Tabel dirancang *Person-centric* dan murni mencatat siklus hubungan organisasi:

```sql
CREATE TABLE core_institution_memberships (
    id CHAR(36) NOT NULL PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    institution_id CHAR(36) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    joined_at DATE NULL,
    left_at DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_memberships_person FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    CONSTRAINT fk_memberships_institution FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE,
    UNIQUE KEY uq_person_institution (person_id, institution_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Aturan Atribut:**
- `status`: Murni status hubungan (`active` | `inactive`). Status domain akademik (`student`, `alumni`, `graduated`) atau HR (`employee`, `teacher`, `resigned`) disimpan di tabel modul masing-masing (`academic_students`, `hr_employees`).
- `uq_person_institution`: 1 Person hanya memiliki 1 baris membership per lembaga. Perubahan status historis dicatat melalui `joined_at` dan `left_at`.

---

### Bagian D: Compatibility Bridge & Role Assignment Target

1. **Kondisi Selama Tahap 1A**:
   - `core_role_user` **tetap menjadi Single Source of Truth Otorisasi** bagi `ActiveInstitution`, `CheckInstitutionScope`, dan `ScopeRule`.
   - Tidak ada kode middleware atau RBAC yang dialihkan ke `core_institution_memberships` pada Tahap 1A.
2. **Target Akhir (Diskusi Arsitektur Masa Depan - Phase 4)**:
   - Target jangka panjang:
     ```text
     core_role_user
     ├── user_id
     ├── role_id
     └── institution_membership_id  ──►  core_institution_memberships (person_id + institution_id)
     ```
   - Target ini menjamin rantai kepemilikan valid: User hanya dapat memegang Role pada lembaga jika Person terkait memiliki Membership di lembaga tersebut.
   - Pada Tahap 1A, kolom `institution_membership_id` belum ditambahkan ke `core_role_user`.

---

### Bagian E: Perubahan Model Eloquent

1. **`Modules\Core\Models\Person`**:
   - Tambahkan relasi:
     ```php
     public function memberships(): HasMany
     {
         return $this->hasMany(InstitutionMembership::class, 'person_id');
     }

     public function institutions(): BelongsToMany
     {
         return $this->belongsToMany(Institution::class, 'core_institution_memberships')
             ->withPivot(['status', 'joined_at', 'left_at'])
             ->withTimestamps();
     }
     ```
2. **`Modules\Core\Models\Institution`**:
   - Tambahkan relasi:
     ```php
     public function memberships(): HasMany
     {
         return $this->hasMany(InstitutionMembership::class, 'institution_id');
     }

     public function persons(): BelongsToMany
     {
         return $this->belongsToMany(Person::class, 'core_institution_memberships')
             ->withPivot(['status', 'joined_at', 'left_at'])
             ->withTimestamps();
     }
     ```
3. **Model Baru: `Modules\Core\Models\InstitutionMembership`**:
   - Menggunakan trait `HasUuids`.
   - Relasi: `belongsTo(Person::class)` dan `belongsTo(Institution::class)`.
   - Cast: `joined_at => date`, `left_at => date`.

---

### Bagian F: Strategi Data Backfill (Aman untuk 0 Maupun N Data)
Meskipun audit membuktikan data saat ini masih kosong di lokal, migration harus menyertakan query backfill idempotensial:
```php
// Jika ada Person yang memiliki institution_id sebelum kolom di-drop:
// DB::table('core_persons')->whereNotNull('institution_id')->each(...)
// Insert ignore ke core_institution_memberships (person_id, institution_id, 'active', now())
```
Proses ini memastikan jika database staging/produksi memiliki data `core_persons`, relasi kelembagannya tidak hilang saat kolom `institution_id` dihapus.

---

### Bagian G: Prosedur Rollback
Setiap file migration wajib memiliki implementasi `down()` simetris:
1. **Rollback `core_institution_memberships`**:
   - Drop tabel `core_institution_memberships`.
2. **Rollback `core_persons`**:
   - Tambahkan kembali kolom `institution_id` (UUID nullable).
   - Buat foreign key `core_persons_institution_id_foreign` ke `core_institutions(id)`.
   - Drop index global `core_persons_nik_unique`.
   - Buat kembali composite unique `person_nik_per_institution (nik, institution_id)`.
   - (Opsional data restore: Kembalikan `institution_id` dari snapshot backup jika diperlukan).

---

### Bagian H: Verifikasi & Acceptance Criteria Tahap 1A

Eksekusi dinyatakan sukses HANYA JIKA seluruh kriteria berikut terpenuhi:
1. **Migration & Rollback Test**:
   - `php artisan migrate` berjalan lancar tanpa error.
   - `php artisan migrate:rollback` berjalan lancar mengembalikan struktur ke kondisi semula.
   - `php artisan migrate` dijalankan ulang.
2. **Integrity Check**:
   - Tabel `core_persons` tidak lagi memiliki kolom `institution_id`.
   - NIK unik secara global (nullable).
   - Tabel `core_institution_memberships` terbentuk dengan foreign keys dan unique composite `(person_id, institution_id)`.
3. **Regression Test (Zero Side-Effects)**:
   - `php artisan route:list` tetap menampilkan 333 rute aktif tanpa error.
   - Alur autentikasi Login & Dashboard admin tetap berfungsi normal (karena `core_role_user` tidak disentuh).
   - Model `Student`, `Employee`, `Classroom` tetap menjalankan `HasInstitutionScope` tanpa terganggu.
4. **Automated Unit Test**:
   - Buat 1 file test: `tests/Unit/IdentityFoundationTest.php` untuk memverifikasi lifecycle `Person` -> `InstitutionMembership` -> `Institution`.
