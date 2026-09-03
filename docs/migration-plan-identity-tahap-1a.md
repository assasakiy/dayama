# MIGRATION PLAN TAHAP 1A — IDENTITY FOUNDATION (REVISI LENGKAP)
**Platform DAYAMA — Decoupling Person, Introducing InstitutionMembership, and Application Compatibility**
*Dokumen Rencana Teknis Terpadu (Tanpa Eksekusi Skema)*
*Tanggal: 2026-09-04*

---

## 1. Ringkasan & Tujuan Tahap 1A

Tahap 1A meletakkan fondasi identitas yang bersih tanpa merusak (*zero breaking changes*) otorisasi eksisting:
1. **Membebaskan `core_persons`** dari kepemilikan satu lembaga (`institution_id`), menjadikannya representasi manusia fisik global.
2. **Memperkenalkan `core_institution_memberships`** sebagai representasi hubungan kelembagaan yang bersifat *Person-centric*.
3. **Menjamin Kompatibilitas Kode Aplikasi (Application Compatibility)**: Menyelaraskan seluruh *write-paths* (`PersonController`, `StudentController`, `EmployeeController`) dan *read-paths* (filter index) agar tidak ada SQL crash akibat kolom `institution_id` yang hilang.
4. **Mengubah Paradigma Duplikasi**: Menghentikan pola kloning/replicate Person di `YayasanPersonIndexService` menjadi pola penggunaan Person global yang sama + pembuatan Membership baru.
5. **Mempertahankan `core_role_user`** sebagai jembatan kompatibilitas (*compatibility bridge*) agar RBAC dan seluruh pipeline otorisasi saat ini tetap berjalan normal 100%.

---

## 2. Batasan Ruang Lingkup (Scope Boundaries)

### A. Di dalam Scope Tahap 1A
- **DDL Migration**: Modifikasi tabel `core_persons` (drop constraint & kolom `institution_id`, set global nullable unique NIK).
- **DDL Migration**: Pembuatan tabel baru `core_institution_memberships`.
- **Model Updates**: `Person`, `Institution`, dan pembuatan model baru `InstitutionMembership`.
- **Hapus trait** `HasInstitutionScope` dari model `Person`.
- **Pembaruan Application Write-Paths**:
  - `PersonController` (store, update, index filtering via membership).
  - `StudentController` (penciptaan/pencarian Person + pembuatan Membership).
  - `EmployeeController` (penciptaan/pencarian Person + pembuatan Membership).
- **Penyesuaian `YayasanPersonIndexService`**: Menghapus `copyFromInstitution()` yang mereplikasi Person; menggantinya dengan penautan `InstitutionMembership`.
- **Script / Prosedur Backfill & Dual-Level Rollback**.
- **Automated Regression & Acceptance Tests**.

### B. EKSPLISIT DI LUAR SCOPE (OUT OF SCOPE TAHAP 1A)
```text
❌ DILARANG refactor ActiveInstitution secara fundamental (tetap session & role_user bridge)
❌ DILARANG refactor ScopeRule (tetap gunakan core_role_user bridge)
❌ DILARANG menghapus atau mengubah kolom tabel core_role_user
❌ DILARANG implementasi Personal/Relationship authorization UI (Portal Santri/Wali)
❌ DILARANG implementasi Portal, PSB, atau Data Center UI
❌ DILARANG implementasi Dynamic Sites Resolution (DB-driven hosts)
❌ DILARANG multi-runtime isolation (tetap 1 runtime Laravel)
```

---

## 3. Rencana Langkah Rinci

### Bagian A: Preflight Checks & Target Database
1. **Target Database Verification**:
   - Database aktif diverifikasi via config/runtime (di lokal aktif: `modern_blog` atau `dayama`).
   - Wajib konfirmasi string `DB_DATABASE` sebelum eksekusi.
2. **Snapshot / Backup**:
   - Eksekusi backup fisik sebelum DDL: `mysqldump -u root -p [active_db] > backup_pre_tahap1a.sql`.
3. **Pemeriksaan Integritas Data**:
   - Query verifikasi count: `SELECT COUNT(*) FROM core_persons;`.
   - Deteksi potensi bentrok NIK sebelum constraint diubah:
     ```sql
     SELECT nik, COUNT(*) as c FROM core_persons WHERE nik IS NOT NULL GROUP BY nik HAVING c > 1;
     ```
   - Catat index & FK existing:
     - Index: `person_nik_per_institution` pada `(nik, institution_id)`
     - FK: `core_persons_institution_id_foreign`

---

### Bagian B: Person Decoupling (DDL Skema)
1. **Transformasi Index NIK**:
   - Drop composite unique index: `person_nik_per_institution` (`nik`, `institution_id`).
   - Buat index baru: `UNIQUE KEY core_persons_nik_unique (nik)` bersifat nullable (hanya mengunci nilai non-null).
2. **Pelepasan Kolom Lembaga**:
   - Drop foreign key constraint: `core_persons_institution_id_foreign`.
   - Drop kolom: `institution_id` dari tabel `core_persons`.
3. **Pembersihan Model `Person`**:
   - Hapus `use \App\Authorization\Concerns\HasInstitutionScope;`.
   - Hapus `'institution_id'` dari array `$fillable`.
   - Ganti relasi `institution()` menjadi relasi `memberships()` dan `institutions()`.

---

### Bagian C: Skema Final `core_institution_memberships`
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

**Kaidah Desain:**
- `status`: Murni status hubungan lembaga (`active` | `inactive`). Tidak memuat status akademik (`alumni`, `student`) maupun HR (`employee`, `teacher`).
- `uq_person_institution`: Menjamin 1 Person hanya memiliki 1 baris status keanggotaan pada satu lembaga (dapat diaktifkan/nonaktifkan kembali via `joined_at` / `left_at`).

---

### Bagian D: Application Compatibility & Write-Paths Refactoring

Pembaruan mutlak pada controller & service agar tidak crash setelah `institution_id` lepas dari `core_persons`:

1. **`PersonController`**:
   - **`index()`**:
     - Hapus `ActiveInstitution::applyToQuery($q)` pada model `Person` (karena kolom `institution_id` sudah tidak ada di tabel `core_persons`).
     - Ganti dengan scoping berbasis membership:
       ```php
       if (ActiveInstitution::shouldScope()) {
           $instId = ActiveInstitution::id();
           $query->whereHas('memberships', fn ($m) => $m->where('institution_id', $instId)->where('status', 'active'));
       }
       // Pengguna dengan Yayasan scope / Super Admin otomatis melihat Person global tanpa filter.
       ```
   - **`store()`**:
     - Buat `Person` global (tanpa `institution_id`).
     - Jika dibuat dalam konteks lembaga (`$instId = ActiveInstitution::id()`), otomatis buatkan membership:
       ```php
       InstitutionMembership::firstOrCreate([
           'person_id' => $person->id,
           'institution_id' => $instId,
       ], [
           'status' => 'active',
           'joined_at' => now(),
       ]);
       ```
   - **`update()`**:
     - Ubah validasi NIK: bukan lagi `unique:core_persons,nik,...,institution_id,...`, melainkan `Rule::unique('core_persons', 'nik')->ignore($person->id)` (global nullable unique).

2. **`StudentController` (`store`)**:
   - Menghapus `'institution_id' => $institutionId` dari array `Person::create()`.
   - Setelah `Person` didapat/dibuat, pastikan relasi membership terbentuk:
     ```php
     InstitutionMembership::firstOrCreate([
         'person_id' => $person->id,
         'institution_id' => $institutionId,
     ], [
         'status' => 'active',
         'joined_at' => now(),
     ]);
     ```
   - Lanjutkan pembuatan record domain `Student` (`student` tetap institution-scoped).

3. **`EmployeeController` (`store`)**:
   - Menghapus `'institution_id' => $institutionId` dari array `Person::create()`.
   - Buat/pastikan `InstitutionMembership` aktif di lembaga terkait.
   - Lanjutkan pembuatan record domain `Employee` (`employee` tetap institution-scoped).

4. **`YayasanPersonIndexService`**:
   - **Hentikan Replikasi Person (`copyFromInstitution`)**:
     - Alur lama mereplikasi record `core_persons` ke ID baru dengan `institution_id` target.
     - Alur baru: Jika Person dengan NIK tersebut sudah ada, gunakan `Person` ID yang sama dan buatkan `InstitutionMembership` baru untuk `targetInstitutionId`:
       ```php
       public function linkToInstitution(string $nik, string $targetInstitutionId): ?Person
       {
           $person = Person::where('nik', $nik)->first();
           if (! $person) return null;

           InstitutionMembership::firstOrCreate([
               'person_id' => $person->id,
               'institution_id' => $targetInstitutionId,
           ], [
               'status' => 'active',
               'joined_at' => now(),
           ]);

           return $person;
       }
       ```
   - Hapus ketergantungan kolom `$person->institution_id` pada metode `syncPerson` dan `removePerson`.

---

### Bagian E: Compatibility Bridge & Role Assignment Target

1. **Aturan Selama Tahap 1A**:
   - `core_role_user` **tetap dipertahankan 100%** sebagai Single Source of Truth Otorisasi bagi `ActiveInstitution`, `CheckInstitutionScope`, dan `ScopeRule`.
   - Tidak ada modifikasi skema tabel `core_role_user` pada Tahap 1A.
2. **Target Masa Depan (Phase 4)**:
   - Target akhir adalah menghubungkan penugasan peran akun ke keanggotaan institusi:
     `core_role_user (user_id, role_id, institution_membership_id)`.
   - Mencegah akun memegang role lembaga tanpa adanya Person Membership di lembaga tersebut.

---

### Bagian F: Dual-Level Rollback Semantics

Kebijakan rollback dibagi secara tegas menjadi dua level:

1. **Level 1 — Early Rollback (Sebelum Multi-Membership Terbentuk)**:
   - Berlaku jika rollback dilakukan sesaat setelah migrasi dijalankan dan belum ada Person yang terhubung ke >1 lembaga.
   - Prosedur:
     - Tambahkan kembali kolom `institution_id` nullable ke `core_persons`.
     - Kembalikan `institution_id` dari baris `core_institution_memberships` (jika ada data).
     - Buat foreign key `core_persons_institution_id_foreign`.
     - Drop index `core_persons_nik_unique`.
     - Buat kembali `person_nik_per_institution (nik, institution_id)`.
     - Drop tabel `core_institution_memberships`.
2. **Level 2 — Post-Adoption Recovery (Setelah Multi-Membership Aktif Digunakan)**:
   - Jika sistem sudah berjalan dan terdapat Person yang memiliki banyak membership (misal: Ahmad memiliki membership MTs dan MA), **rollback skema ke single `institution_id` secara lossless adalah mustahil secara matematis**.
   - Prosedur recovery: Gunakan snapshot database backup yang diambil pada preflight, atau lakukan forward migration fix. Jangan mengandalkan perintah rollback bawaan.

---

### Bagian G: Rencana Verifikasi & Acceptance Test

1. **Migration Integrity**:
   - `php artisan migrate` berjalan bersih.
   - Skema `core_persons` bebas dari `institution_id`.
   - Skema `core_institution_memberships` terbentuk dengan constraint yang tepat.
2. **Regression Test (Zero Side-Effects)**:
   - `php artisan route:list` tetap menampilkan 333 rute aktif.
   - Login, dashboard admin, dan otorisasi `core_role_user` tetap berfungsi normal.
   - Model operasional (`Student`, `Employee`, `Classroom`) tetap berjalan dengan `HasInstitutionScope`.
3. **Automated Feature Test (`tests/Feature/IdentityTahap1ATest.php`)**:
   - **Test 1**: Membuat 1 `Person` (Ahmad) dan mendaftarkannya ke 2 lembaga berbeda (`MTs` dan `MA`) menghasilkan 2 baris di `core_institution_memberships` dan tetap HANYA 1 baris di `core_persons`.
   - **Test 2**: Pendaftaran Siswa (`StudentController`) dan Pegawai (`EmployeeController`) otomatis membuat/mengaitkan `InstitutionMembership` tanpa memicu error SQL missing column.
   - **Test 3**: Query `PersonController@index` pada operator lembaga memfilter data via relasi membership, sedangkan admin yayasan melihat seluruh Person global.
   - **Test 4**: Validasi NIK menolak NIK yang sama untuk Person berbeda secara global.
