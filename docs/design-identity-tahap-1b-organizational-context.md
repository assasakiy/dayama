# DESIGN TAHAP 1B — ORGANIZATIONAL CONTEXT

Status: design only. Migration dan implementasi masih HOLD.
Tanggal: 2026-09-04

## 1. Tujuan

Tahap 1B menyatukan resolusi kewenangan organisasi ke satu layanan scoped per request:

```text
Authenticated User
      ↓
OrganizationalAccessResolver [Laravel scoped service]
      ↓ resolve sekali per request
OrgContext [immutable value object]
      ↓
Seluruh consumer backend
```

`OrganizationalAccessResolver` menjadi satu-satunya sumber organizational context. Permission tetap ditentukan oleh Spatie Permission melalui Gate, Policy, dan AuthorizationService.

```text
Permission → tindakan apa yang boleh dilakukan
OrgContext → data organisasi mana yang boleh dijangkau
```

Guest/public berada di luar resolver. Level `PERSONAL` hanya untuk user terautentikasi tanpa kewenangan organisasi.

## 2. OrgContext Contract

Kontrak konseptual immutable:

```php
final readonly class OrgContext
{
    public function __construct(
        public OrgLevel $level,
        public ?array $accessibleInstitutionIds,
        public ?string $activeInstitutionId,
        public ?string $primaryInstitutionId,
    ) {}
}
```

Enum:

```php
enum OrgLevel: string
{
    case GLOBAL = 'global';
    case FOUNDATION = 'foundation';
    case INSTITUTION = 'institution';
    case PERSONAL = 'personal';
}
```

Invariants:

| Level | accessibleInstitutionIds | activeInstitutionId | Makna |
|---|---|---|---|
| `GLOBAL` | `null` | nullable | Organizational access tanpa batas institusi |
| `FOUNDATION` | `null` | nullable | Lintas institusi dalam foundation aktif; foundation boundary ditambahkan saat multi-foundation aktif |
| `INSTITUTION` | `[A,B,...]` | nullable atau anggota array | Hanya institusi yang diberi lewat role assignment |
| `PERSONAL` | `[]` | `null` | Tidak punya organizational access |

`null`, `[]`, dan `[IDs]` tidak boleh disamakan:

```text
null  = unrestricted pada organizational boundary level tersebut
[]    = tidak punya organizational access
[IDs] = akses terbatas ke daftar institusi
```

## 3. Precedence

Precedence tunggal:

```text
GLOBAL > FOUNDATION > INSTITUTION > PERSONAL
```

Resolver mengevaluasi seluruh assignment role, tetapi memilih level tertinggi untuk organizational context.

Contoh mixed-role:

| Assignment | Hasil level | Institution IDs |
|---|---|---|
| Primary Super Admin + role apa pun | `GLOBAL` | `null` |
| Operator Yayasan + Guru MA | `FOUNDATION` | `null` |
| Operator MTs + Guru MA | `INSTITUTION` | `[MTs, MA]` |
| Portal user tanpa role organisasi | `PERSONAL` | `[]` |
| Role Spatie ada tetapi semua assignment lembaga inactive/invalid | `PERSONAL` | `[]` |

Permission tidak mengikuti precedence. Semua permission Spatie dari seluruh role tetap digabung sesuai perilaku package.

## 4. Data Sources

Tahap 1B membaca:

1. `core_users.is_primary_super_admin` untuk `GLOBAL`.
2. `core_roles.scope` untuk klasifikasi assignment (`yayasan` atau `lembaga`) hanya di dalam resolver.
3. `core_role_user` sebagai source of truth authorization organisasi selama bridge 1A masih berlaku.
4. `core_institution_memberships` untuk validasi bahwa Person user mempunyai active membership pada institution assignment.
5. Session `active_institution_id` sebagai pilihan context, bukan bukti authorization.
6. User preference/profile untuk `primary_institution_id`, bukan permission.

Tidak boleh ada consumer lain yang membaca `role.scope` langsung setelah migrasi consumer selesai.

## 5. Resolver Lifecycle dan Per-Request Cache

Binding di service provider:

```php
$this->app->scoped(OrganizationalAccessResolver::class);
```

Resolver menyimpan hasil pada property instance:

```text
resolve(User)
├── sudah resolved untuk user ID yang sama → return OrgContext cache
└── belum → query assignment, membership, session; build immutable OrgContext
```

Larangan:

- Tidak memakai static property/cache.
- Tidak menyimpan OrgContext user ke singleton.
- Tidak memakai cache lintas request untuk session aktif.
- Aman untuk PHP-FPM, queue worker, dan Octane.

## 6. Algoritma Resolver

```text
Input authenticated User
│
├── is_primary_super_admin = true
│   └── GLOBAL, accessible=null
│
├── cari valid role assignments dari core_role_user
│   ├── ada assignment scope yayasan
│   │   └── FOUNDATION, accessible=null
│   │
│   ├── ada assignment scope lembaga
│   │   ├── ambil distinct institution IDs
│   │   ├── validasi institution aktif
│   │   ├── validasi active Person membership bila User punya person_id
│   │   └── INSTITUTION, accessible=[IDs]
│   │
│   └── tidak ada assignment organisasi valid
│       └── PERSONAL, accessible=[]
│
├── resolve primaryInstitutionId sebagai preference/default
│
└── validate activeInstitutionId
    ├── level INSTITUTION + active termasuk accessible → pakai
    ├── level INSTITUTION + active invalid → null atau primary valid
    ├── GLOBAL/FOUNDATION → active opsional untuk data-entry context
    └── PERSONAL → null
```

Keputusan invalid session:

- Resolver tidak menganggap session sebagai permission.
- Nilai invalid tidak masuk ke OrgContext.
- Switch endpoint menolak request invalid dengan 403 dan tidak menyimpan session.
- Session stale boleh dibersihkan setelah resolusi.

## 7. Active Institution Semantics

`activeInstitutionId` adalah working context, bukan authorization grant.

### INSTITUTION

```text
activeInstitutionId harus ada di accessibleInstitutionIds
```

Jika null:

1. gunakan `primaryInstitutionId` jika accessible;
2. jika hanya satu accessible institution, gunakan ID itu;
3. selain itu tetap null dan UI meminta pemilihan.

### FOUNDATION / GLOBAL

Active institution boleh null untuk overview lintas lembaga. Saat dipilih, ID harus institution valid di boundary foundation/platform yang relevan. Nilai ini hanya mempersempit context kerja.

### PERSONAL

Selalu null. Portal personal tidak memakai active institution untuk subject access.

## 8. Primary Institution Semantics

`primaryInstitutionId`:

- Preference/default navigasi.
- Bukan authorization source.
- Tidak memperluas `accessibleInstitutionIds`.
- Untuk `INSTITUTION`, harus anggota accessible IDs sebelum dipakai.
- Jika tidak valid/stale, diabaikan.
- Penyimpanan target dapat memakai profile/preferences existing; schema baru hanya dibuat jika audit implementasi membuktikan belum ada tempat yang tepat.

## 9. Consumers yang Wajib Dimigrasi

Daftar consumer berdasarkan sweep kode saat desain:

1. `App\Support\ActiveInstitution`
   - Menjadi compatibility facade tipis di atas resolver selama migrasi.
   - Tidak query role/scope langsung.
2. `App\Authorization\Rules\ScopeRule`
   - Membaca OrgContext.
   - Person access: active membership harus beririsan dengan accessible IDs untuk level institution.
3. `App\Authorization\Scopes\InstitutionScope`
   - `GLOBAL/FOUNDATION`: tidak memfilter kecuali active context sengaja diterapkan.
   - `INSTITUTION`: filter active institution; fail closed jika context dibutuhkan tetapi null.
   - `PERSONAL`: fail closed untuk organizational models.
4. `App\Http\Middleware\CheckInstitutionScope`
   - Menggunakan resolver, tanpa `roles()->where('scope', ...)`.
5. `InstitutionController` switch endpoint
   - Validasi candidate ID terhadap OrgContext sebelum menulis session.
6. `HandleInertiaRequests`
   - Share `auth.orgContext`, `accessibleInstitutions`, `activeInstitution`, dan capabilities hasil backend.
7. `AuthorizationService` / Capabilities Presenter
   - Tetap sumber capabilities UI; menerima OrgContext bila scope diperlukan.
8. Controller user-facing yang saat ini membaca `ActiveInstitution` atau `role.scope`:
   - `PersonController`
   - `StudentController`
   - `EmployeeController`
   - `DepartmentController`
   - `RombelController`
   - `UserController`
9. Sidebar/frontend
   - Hanya render data share backend.
   - Dilarang menyimpulkan akses dari role names atau `role.scope`.

Contract share frontend:

```json
{
  "auth": {
    "orgContext": {
      "level": "institution",
      "accessibleInstitutionIds": ["A", "B"],
      "activeInstitutionId": "A",
      "primaryInstitutionId": "A"
    },
    "capabilities": {}
  },
  "accessibleInstitutions": [],
  "activeInstitution": null
}
```

## 10. Migration Path dari ActiveInstitution Saat Ini

### Phase 1 — Introduce tanpa behavior switch

- Tambah `OrgLevel`, `OrgContext`, `OrganizationalAccessResolver`.
- Bind resolver sebagai scoped service.
- Tambah unit tests resolver.
- Belum ubah consumer.

### Phase 2 — Compatibility Facade

- Ubah `ActiveInstitution` menjadi facade resolver:
  - `id()` → `OrgContext.activeInstitutionId`
  - `accessibleIds()` → `OrgContext.accessibleInstitutionIds`
  - `shouldScope()` → berdasarkan `OrgLevel`, bukan query role
  - `authorizeAccess()` → resolver membership check
- Pertahankan public API sementara untuk mengurangi blast radius.

### Phase 3 — Core Enforcement Consumers

- Migrasi `ScopeRule`, `InstitutionScope`, `CheckInstitutionScope`, switch endpoint.
- Fail closed pada context invalid.

### Phase 4 — Presentation Consumers

- Migrasi `HandleInertiaRequests`, Capabilities Presenter, sidebar shared data.
- Hapus frontend inference dari role names/scope.

### Phase 5 — Controller Sweep

- Ganti semua direct role scope reads dengan OrgContext.
- Controller tetap memakai Gate untuk permission.
- Query visibility memakai resolver/query helper terpusat.

### Phase 6 — Cleanup

- Grep memastikan direct reads hanya tersisa di resolver/seeder/admin role-management yang memang mengelola metadata.
- Hapus compatibility methods yang tidak lagi dipakai pada tahap terpisah.

Tidak ada perubahan skema wajib pada Phase 1–5 kecuali audit `primaryInstitutionId` membutuhkan storage tambahan.

## 11. Test Matrix

### Resolver Unit Tests

1. PSA menghasilkan `GLOBAL`, IDs `null`.
2. Role yayasan menghasilkan `FOUNDATION`, IDs `null`.
3. Role lembaga A+B menghasilkan `INSTITUTION`, IDs `[A,B]`.
4. Tanpa assignment menghasilkan `PERSONAL`, IDs `[]`.
5. Mixed yayasan+lembaga menghasilkan `FOUNDATION`.
6. Permission seluruh role tetap tersedia walau context memakai precedence tertinggi.
7. Membership inactive tidak memberikan institution access.
8. Duplicate role assignments menghasilkan unique institution IDs.
9. Resolver query hanya sekali per request/user.

### Active Context Tests

10. Active ID anggota accessible IDs diterima.
11. Active ID bukan anggota ditolak/diabaikan dan tidak menjadi permission.
12. Primary ID valid menjadi default.
13. Primary ID stale diabaikan.
14. PERSONAL selalu active null.
15. FOUNDATION/GLOBAL boleh overview dengan active null.

### Enforcement Integration Tests

16. Operator MTs hanya melihat data operasional MTs.
17. Operator MTs dengan session MA palsu tetap 403.
18. Operator Yayasan + Guru MA tetap melihat overview foundation.
19. Person dengan active membership MTs dapat diakses operator MTs dengan permission.
20. Person membership inactive MTs tidak dapat diakses operator MTs.
21. User PERSONAL gagal mengakses organizational dashboard data.
22. Switch endpoint hanya menerima accessible institution.

### Presentation Tests

23. Inertia share memuat OrgContext konsisten.
24. Accessible institutions cocok dengan context.
25. Sidebar tidak mengandalkan role name/scope.
26. Capabilities berasal dari backend presenter.

### Regression

27. Gate/Policy permission tests tetap lulus.
28. Route list tetap valid.
29. Auth/login/session tests tetap lulus.
30. 1A identity tests tetap lulus.

## 12. Security Invariants

1. Session tidak pernah memberikan akses baru.
2. `primaryInstitutionId` tidak pernah memberikan akses baru.
3. UI tidak menjadi enforcement layer.
4. `PERSONAL` fail closed untuk model organizational.
5. Membership inactive tidak dihitung sebagai akses aktif.
6. Role scope hanya dibaca resolver.
7. Permission dan organizational context selalu dievaluasi terpisah.
8. Resolver tidak mengambil guest/public context.

## 13. Batas Tegas Tahap 1C

Tahap 1B tidak membuat atau mengimplementasikan:

```text
❌ core_person_relationships
❌ PersonRelationshipRule
❌ guardian → child authorization
❌ own Person / own Student personal authorization
❌ relationship verification workflow
❌ portal access grant/revoke
❌ relationship migration
```

Tahap 1C khusus Personal Access dan memakai model terpisah:

```text
Identity fact:
core_person_relationships
- relationship_type
- status pending|verified|rejected|revoked
- verified_at
- verified_by

Authorization decision:
- portal_access_status none|pending|granted|revoked
- access_granted_at/by
- access_revoked_at/by
```

Relasi terverifikasi tidak otomatis memberi portal access. Akses personal harus memerlukan authorization decision eksplisit.

## 14. Acceptance Criteria Desain 1B

Design siap diimplementasikan hanya setelah review menyetujui:

- OrgContext contract dan invariants.
- Precedence mixed-role.
- Scoped resolver lifecycle.
- Source data dan active session validation.
- Consumer migration path.
- Test matrix.
- Batas 1C.

Sampai approval implementasi diberikan:

```text
⛔ Jangan membuat migration 1B
⛔ Jangan menulis resolver/code 1B
⛔ Jangan membuat PersonRelationshipRule
⛔ Jangan membuat relationship schema
```
