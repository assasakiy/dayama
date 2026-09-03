# MIGRATION PLAN TAHAP 1A — IDENTITY FOUNDATION (FINAL GOLD STANDARD)
**Platform DAYAMA — Decoupling Person, Introducing InstitutionMembership, and Application Compatibility**
*Dokumen Rencana Teknis Terpadu*
*Tanggal: 2026-09-04*

---

## 1. Ringkasan & Tujuan Tahap 1A

Tahap 1A meletakkan fondasi identitas yang bersih tanpa merusak (*zero breaking changes*) otorisasi eksisting:
1. **Membebaskan `core_persons`** dari kepemilikan satu lembaga (`institution_id`), menjadikannya representasi manusia fisik global.
2. **Memperkenalkan `core_institution_memberships`** sebagai representasi hubungan kelembagaan yang bersifat *Person-centric*.
3. **Menjamin Kompatibilitas Kode Aplikasi (Application Compatibility)**: Menyelaraskan seluruh *write-paths* (`PersonController`, `StudentController`, `EmployeeController`) dan *read-paths* (filter index) agar tidak ada SQL crash akibat kolom `institution_id` yang hilang.
4. **Global Person Resolver**: Menyediakan pola resolusi person yang seragam di pendaftaran Siswa & Pegawai (reuse person global via `person_id` atau `nik`, create hanya jika manusia baru).
5. **Membership Ensure & Reaktivasi**: Mengaktifkan kembali membership lama bila person kembali aktif di lembaga tersebut, tanpa menimpa `joined_at` pertama kali.
6. **Desentralisasi YayasanPersonIndex**: Menghilangkan duplikasi relasi di JSON `refs`. `yayasan_person_index` murni mengindeks identitas global, sedangkan afiliasi lembaga dibaca langsung dari `core_institution_memberships`.
7. **Mempertahankan `core_role_user`** sebagai jembatan kompatibilitas (*compatibility bridge*) agar RBAC dan seluruh pipeline otorisasi saat ini tetap berjalan normal 100%.

---

## 2. Batasan Ruang Lingkup (Scope Boundaries)

### A. Di dalam Scope Tahap 1A
- **DDL Migration**: Modifikasi tabel `core_persons` (drop constraint & kolom `institution_id`, set global nullable unique NIK).
- **DDL Migration**: Pembuatan tabel baru `core_institution_memberships`.
- **Model Updates**: `Person`, `Institution`, dan pembuatan model baru `InstitutionMembership`.
- **Hapus trait** `HasInstitutionScope` dari model `Person`.
- **Pembaruan Application Write-Paths**:
  - `PersonController` (store, update, index filtering dengan prioritas Yayasan/SuperAdmin).
  - `StudentController` (validasi `person_id`, Global Person Resolver, ensure membership).
  - `EmployeeController` (validasi `person_id`, Global Person Resolver, ensure membership).
- **Penyesuaian `YayasanPersonIndexService` & `PersonObserver`**: Sinkronisasi murni identitas global (nama, NIK, TTL) tanpa menyimpan JSON duplikat relasi lembaga.
- **Dual-Level Rollback Semantics** (Early Rollback vs Post-Adoption Recovery).
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
   - Database aktif diverifikasi via runtime (di lokal aktif: `modern_blog` atau `dayama`).
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
Tabel dirancang *Person-centric* dan murni mencatat status hubungan organisasi:

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

---

### Bagian D: Aturan Teknis Utama & Application Compatibility

#### 1. Foundation Precedence pada Index Filtering (`PersonController@index`)
Tidak boleh hanya bergantung pada `ActiveInstitution::shouldScope()`. Otorisasi diselesaikan dengan urutan hierarki yang tegas:
```php
$user = $request->user();

// 1. Super Admin atau memiliki Role Scope Yayasan -> Akses Global Unrestricted
$isYayasanOrAdmin = $user->is_primary_super_admin || $user->roles()->where('scope', 'yayasan')->exists();

if (! $isYayasanOrAdmin) {
    // 2. Operator / User dengan Institution Scope -> Terikat Active Institution
    $instId = ActiveInstitution::id();
    if ($instId) {
        $query->whereHas('memberships', fn ($m) => 
            $m->where('institution_id', $instId)->where('status', 'active')
        );
    } else {
        $query->whereRaw('1 = 0');
    }
}
```

#### 2. Pola Membership Ensure & Reaktivasi (`ensureMembership`)
Bukan sekadar `firstOrCreate()`. Menyediakan metode helper/service yang menangani reaktivasi:
```php
public static function ensureMembership(string $personId, string $institutionId): InstitutionMembership
{
    $membership = InstitutionMembership::where('person_id', $personId)
        ->where('institution_id', $institutionId)
        ->first();

    if (! $membership) {
        return InstitutionMembership::create([
            'id'             => (string) Str::orderedUuid(),
            'person_id'      => $personId,
            'institution_id' => $institutionId,
            'status'         => 'active',
            'joined_at'      => now(),
            'left_at'        => null,
        ]);
    }

    if ($membership->status !== 'active') {
        $membership->update([
            'status'  => 'active',
            'left_at' => null,
            // joined_at awal dipertahankan
        ]);
    }

    return $membership;
}
```

#### 3. Global Person Resolver pada Write-Paths (`StudentController` & `EmployeeController`)
Kedua controller dilengkapi validasi `person_id` dan resolver global person:
```text
Langkah Resolusi Person:
1. Validasi Input:
   - 'person_id' => 'nullable|uuid|exists:core_persons,id'
   - 'nik' => ['nullable', 'string', 'max:20', Rule::unique('core_persons', 'nik')->ignore($personId)]
2. Eksekusi Resolver:
   - Jika 'person_id' diberikan -> $person = Person::findOrFail($personId)
   - Jika 'person_id' kosong tapi 'nik' diberikan dan ditemukan di DB -> $person = Person::where('nik', $nik)->first()
   - Selain itu -> $person = Person::create([...]) (Manusia baru)
3. Pastikan Keanggotaan Lembaga:
   - ensureMembership($person->id, $institutionId)
4. Buat Record Domain Operasional:
   - Student::create([...]) atau Employee::create([...])
```

#### 4. YayasanPersonIndexService & PersonObserver Semantics
- **Tanggung Jawab Tunggal**:
  - `core_persons`: Sumber kebenaran manusia fisik global.
  - `core_institution_memberships`: Sumber kebenaran afiliasi kelembagaan.
  - `yayasan_person_index`: Indeks pencarian terpusat identitas manusia (NIK, nama, tanggal lahir).
- **Penyesuaian**:
  - Hapus array `refs` duplikat dari `yayasan_person_index`. Pencarian lembaga mana saja yang terafiliasi dengan NIK tersebut langsung melakukan query relasi `Person->institutions`.
  - `PersonObserver`: Menyinkronkan identitas global ke index tanpa terikat event membership.
  - Hapus metode replikasi `copyFromInstitution()`. Gantikan dengan `linkToInstitution(string $nik, string $targetInstitutionId)` yang memanggil `ensureMembership()`.

---

### Bagian E: Compatibility Bridge & Role Assignment Target

1. **Aturan Selama Tahap 1A**:
   - `core_role_user` **tetap dipertahankan 100%** sebagai Single Source of Truth Otorisasi bagi `ActiveInstitution`, `CheckInstitutionScope`, dan `ScopeRule`.
   - Tidak ada modifikasi skema tabel `core_role_user` pada Tahap 1A.
2. **Target Masa Depan (Phase 4)**:
   - Target akhir: `core_role_user (user_id, role_id, institution_membership_id)`.

---

### Bagian F: Dual-Level Rollback Semantics

1. **Level 1 — Early Rollback (Sebelum Multi-Membership Terbentuk)**:
   - Berlaku sesaat setelah migrasi dijalankan jika ada kegagalan teknis awal.
   - Prosedur:
     - Tambahkan kembali kolom `institution_id` nullable ke `core_persons`.
     - Kembalikan `institution_id` dari baris `core_institution_memberships` (jika ada data 1-to-1).
     - Buat foreign key `core_persons_institution_id_foreign`.
     - Drop index `core_persons_nik_unique`.
     - Buat kembali `person_nik_per_institution (nik, institution_id)`.
     - Drop tabel `core_institution_memberships`.
2. **Level 2 — Post-Adoption Recovery (Setelah Multi-Membership Aktif Digunakan)**:
   - Jika Person sudah memiliki banyak membership (Ahmad di MTs dan MA), rollback ke single `institution_id` secara lossless tidak mungkin dilakukan.
   - Prosedur recovery: Gunakan snapshot database backup preflight atau forward migration.

---

### Bagian G: Acceptance Criteria & Automated Tests

Pengujian dilakukan di `tests/Feature/IdentityTahap1ATest.php`:
1. **Test 1 (Multi-Membership Integritas)**:
   - Mendaftarkan 1 `Person` ke MTs dan MA menghasilkan 2 baris di `core_institution_memberships` dan tetap HANYA 1 baris di `core_persons`.
2. **Test 2 (Reaktivasi Membership)**:
   - Menonaktifkan membership (`status = inactive`, `left_at = now()`), lalu memanggil `ensureMembership()` memastikan status kembali `active` dan `left_at = null` tanpa mengubah `joined_at` lama.
3. **Test 3 (Global Person Resolver pada Student & Employee)**:
   - Input pendaftaran dengan NIK yang sama di lembaga baru berhasil menggunakan Person yang sama tanpa melempar SQL duplicate exception, dan menghasilkan membership baru di lembaga kedua.
4. **Test 4 (Foundation Precedence pada Index)**:
   - User dengan role Yayasan/SuperAdmin melihat seluruh Person global.
   - User dengan role Lembaga hanya melihat Person yang memiliki active membership di lembaga tersebut.
5. **Test 5 (Regression Rute & RBAC)**:
   - Seluruh 333 rute aktif dan login/dashboard staf tetap berjalan lancar.
