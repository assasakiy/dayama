# DAYAMA — Design Document Tahap 1B: Organizational Context

**Status:** Draft untuk review  
**Scope:** Organizational Context sahaja. Personal/relationship access masuk Tahap 1C.  
**Prasyarat:** `docs/rbac/README.md`, terutama bagian Institution Scoping.

## 0. Masalah

Saat ini organizational access membaca `core_roles.scope` secara terpisah di `ScopeRule`, `InstitutionScope`, `ActiveInstitution`, middleware, controller, dan frontend. Ini rawan drift dan salah mengartikan `scope = null` sebagai akses global.

Tahap 1B menetapkan:

```text
Authenticated User
      ↓
OrganizationalAccessResolver [Laravel scoped]
      ↓
OrgContext immutable
      ↓
Semua consumer backend dan frontend contract
```

Permission tetap diputuskan Gate → Policy → AuthorizationService. OrgContext hanya menentukan batas data organisasi.

## 1. OrgContext Contract

```php
final readonly class OrgContext implements JsonSerializable
{
    public function __construct(
        public OrgLevel $level,
        public ?array $accessibleInstitutionIds,
        public ?string $activeInstitutionId,
        public ?string $primaryInstitutionId,
    ) {}
}
```

```php
enum OrgLevel: string
{
    case GLOBAL = 'global';
    case FOUNDATION = 'foundation';
    case INSTITUTION = 'institution';
    case PERSONAL = 'personal';
}
```

Syarat:

- Immutable dan serializable.
- Tidak membawa model `User` atau relasi sensitif.
- ID dinormalisasi sebagai unique array string.

## 2. Level dan GLOBAL Authority

| Level | Kriteria |
|---|---|
| `GLOBAL` | Primary Super Admin atau role dengan `grants_global_context = true` |
| `FOUNDATION` | Minimal satu assignment role aktif dengan `scope = yayasan` |
| `INSTITUTION` | Minimal satu assignment role lembaga, termasuk jika semua institusinya sedang nonaktif |
| `PERSONAL` | User terautentikasi tanpa authority organisasi |

`scope = null` tidak otomatis `GLOBAL`. Editor, author, dan subscriber dapat memakai scope null tanpa platform authority.

Migration 1B menambah:

```text
core_roles.grants_global_context boolean default false
```

Seed:

```text
super-admin / administrator platform → true
editor / author / subscriber          → false
```

Security tidak boleh bergantung nama role. Seeder boleh memilih role awal berdasarkan registry deklaratif, tetapi resolver hanya membaca flag.

## 3. Precedence

```text
GLOBAL > FOUNDATION > INSTITUTION > PERSONAL
```

Precedence hanya memilih organizational level. Permission Spatie tetap union dari seluruh role.

Contoh:

```text
operator_yayasan + guru MA
→ level FOUNDATION
→ permission guru tetap tersedia
```

## 4. Null vs [] vs [IDs]

| Level | accessibleInstitutionIds |
|---|---|
| `GLOBAL` | `null` |
| `FOUNDATION` | `null` |
| `INSTITUTION` | `[A,B,...]`, dapat `[]` bila semua assignment menuju institusi nonaktif |
| `PERSONAL` | `[]` |

```text
null  = unrestricted organizational access
[]    = tidak ada institution aktif yang boleh diakses
[IDs] = akses terbatas
```

Larangan: pengecekan falsy seperti `if (!$ids) unrestricted`. Consumer wajib membedakan `null` dengan `[]` secara eksplisit.

## 5. Active Institution

`activeInstitutionId` adalah working/filter context, bukan bukti authorization.

- `INSTITUTION`: hanya valid bila termasuk `accessibleInstitutionIds`.
- `GLOBAL/FOUNDATION`: boleh menunjuk institution aktif untuk filter tampilan; tidak mengurangi atau menambah authority.
- `PERSONAL`: selalu null.
- Session tidak pernah ditambahkan ke daftar institution authorized.

Default awal sesi untuk `INSTITUTION`:

1. primary institution jika masih accessible;
2. institution aktif pertama;
3. null jika accessible IDs kosong.

## 6. Primary Institution

`primaryInstitutionId` hanya preference/default.

- Tidak memberikan permission.
- Tidak memperluas accessible IDs.
- Nilai stale diabaikan.
- Simpan di preference existing jika cocok; jika tidak ada, migration dapat menambah storage preference paling kecil setelah audit implementasi.

## 7. Resolver Lifecycle

```php
$this->app->scoped(OrganizationalAccessResolver::class);
```

Resolver menyimpan cache pada property instance per request, bukan static/singleton. Aman untuk PHP-FPM dan Octane.

`refreshActiveInstitution()` membuat OrgContext baru dengan active ID baru setelah validasi, tanpa mengulang resolusi role/access IDs.

## 8. Algoritma Resolver

```text
PSA?
├── ya → GLOBAL
└── tidak
    ↓
role grants_global_context?
├── ya → GLOBAL
└── tidak
    ↓
assignment scope yayasan?
├── ya → FOUNDATION
└── tidak
    ↓
ada assignment scope lembaga?
├── ya → INSTITUTION
│        accessible IDs = hanya institution aktif
│        membership aktif Person juga wajib valid jika user memiliki person_id
└── tidak → PERSONAL
```

Institution nonaktif dikeluarkan dari accessible IDs. User tetap `INSTITUTION` dengan `[]` bila role assignment lembaga masih ada tetapi seluruh institusi nonaktif. Ini mempertahankan semantik authority assignment dan tetap fail closed.

## 9. Institution Switch

Backend wajib:

1. cek target institution exists dan aktif;
2. `INSTITUTION`: target harus ada dalam accessible IDs;
3. `GLOBAL/FOUNDATION`: target boleh institution aktif dalam boundary terkait;
4. `PERSONAL`: selalu 403;
5. baru tulis session dan panggil `refreshActiveInstitution()`.

Request palsu yang hanya mengubah session tidak memberi akses.

## 10. Consumer Wajib

| Consumer | Target perubahan |
|---|---|
| `ActiveInstitution` | Facade compatibility tipis ke resolver |
| `ScopeRule` | Baca OrgContext; tidak query role scope/RoleUser langsung |
| `InstitutionScope` | `null`: no filter; `[]`: `1=0`; IDs: `whereIn` |
| `CheckInstitutionScope` | Resolver only |
| Switch endpoint | Validasi lewat resolver |
| `AuthorizationService::capabilities()` | Gunakan OrgContext untuk capability scope |
| `HandleInertiaRequests` | Share OrgContext, capabilities, accessible/active institutions |
| Controllers | Hapus direct `roles()->where('scope', ...)` |
| Frontend/sidebar | Render contract backend; tidak infer dari role/scope |

Consumer hasil sweep saat desain:

- `PersonController`
- `StudentController`
- `EmployeeController`
- `DepartmentController`
- `RombelController`
- `UserController`
- `ScopeRule`
- `InstitutionScope`
- `ActiveInstitution`
- `CheckInstitutionScope`
- `InstitutionController`
- `HandleInertiaRequests`

## 11. Frontend Contract

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

Frontend dilarang memakai `roles.includes(...)` atau `role.scope` untuk keputusan akses. Permission list mentah boleh tetap tersedia selama transisi untuk granular `can()` existing, tetapi organizational UI harus memakai OrgContext.

## 12. GLOBAL vs FOUNDATION Policy Boundary

OrgContext tidak sendirian memberikan akses modul. Semua modul tetap Gate/permission-gated.

Kebijakan produk 1B:

- `GLOBAL`: dapat diberi permission platform-level seperti users, roles, permissions, settings, application registry, domains/sites.
- `FOUNDATION`: tidak otomatis boleh modul platform-level; hanya boleh bila registry/seeder memberikan permission yang relevan.
- `INSTITUTION`: permission berlaku pada data institution yang authorized.
- `PERSONAL`: tidak boleh organizational dashboard meski frontend dipalsukan.

Perbedaan GLOBAL dan FOUNDATION berasal dari organizational authority + permission registry, bukan hardcode role name.

## 13. Hard-Deleted Institution

- `core_role_user` dan domain tables tetap wajib memakai FK yang tepat.
- Resolver selalu inner-join/filter ke `core_institutions` aktif; orphan assignment tidak menghasilkan access ID.
- GLOBAL/FOUNDATION active filter ke institution hard-deleted dianggap stale, dibersihkan/diabaikan.
- Audit FK semua tabel operasional menjadi preflight implementasi; perubahan cascade yang tidak terkait tidak dikerjakan diam-diam dalam 1B.

## 14. Migration Path

1. Tambah `OrgLevel`, `OrgContext`, scoped `OrganizationalAccessResolver`; unit test dahulu.
2. Tambah `core_roles.grants_global_context`; seed authority flags.
3. Refactor `ActiveInstitution` menjadi facade resolver dengan signature lama.
4. Refactor `ScopeRule`, `InstitutionScope`, `CheckInstitutionScope`.
5. Validasi switch institution.
6. Share frontend contract dari `HandleInertiaRequests` dan capabilities.
7. Sweep controller/backend direct role scope reads.
8. Refactor frontend `usePermissions()`/sidebar.
9. Grep gate: direct role scope reads hanya boleh di resolver dan fitur administrasi metadata role.
10. Full regression.

## 15. Test Matrix

### Resolver

1. PSA → GLOBAL/null.
2. grants_global_context → GLOBAL/null.
3. scope null tanpa global flag → PERSONAL/[].
4. yayasan → FOUNDATION/null.
5. lembaga A+B → INSTITUTION/[A,B].
6. yayasan + lembaga → FOUNDATION/null.
7. permission seluruh role tetap union.
8. duplicate assignments menghasilkan IDs unik.
9. membership inactive tidak memberi access ID.
10. institution nonaktif dikeluarkan.
11. seluruh institution nonaktif → INSTITUTION/[].
12. resolve hanya sekali per user/request.

### Active/Primary

13. Active accessible diterima.
14. Active tidak accessible ditolak.
15. Session palsu tidak memberi authority.
16. Primary valid menjadi default.
17. Primary stale diabaikan.
18. PERSONAL active selalu null.
19. GLOBAL/FOUNDATION boleh active null.
20. Switch ke institution nonaktif ditolak.

### Enforcement

21. `InstitutionScope`: null tidak filter.
22. `InstitutionScope`: [] menghasilkan no rows.
23. `InstitutionScope`: IDs memakai whereIn.
24. Operator MTs dengan session MA palsu tetap 403.
25. Mixed Yayasan+Guru tetap FOUNDATION.
26. User PERSONAL gagal organizational dashboard.
27. Person active membership dapat diakses dengan permission.
28. Person inactive membership ditolak.
29. Role dicabut terlihat pada request berikutnya.

### Presentation/Regression

30. Inertia share shape stabil.
31. Sidebar tidak membaca role/scope.
32. Capabilities berasal backend.
33. Gate/Policy tests tetap lulus.
34. Auth/session tetap lulus.
35. Tahap 1A tests tetap lulus.
36. Route list valid.

## 16. Security Invariants

1. Session dan primary institution tidak pernah memberikan akses.
2. `null` dan `[]` tidak pernah tertukar.
3. PERSONAL fail closed untuk organizational data.
4. Institution dan membership inactive tidak memberi akses aktif.
5. UI bukan enforcement layer.
6. Role scope hanya dibaca resolver.
7. Permission dan OrgContext dievaluasi terpisah.
8. Guest/public tidak di-resolve sebagai PERSONAL.

## 17. Batas Tahap 1C

Di luar 1B:

```text
core_person_relationships
PersonRelationshipRule
relationship verification
portal access grant/revoke
guardian → child
santri → own Student
own Person access
```

Relasi identity dan keputusan portal access tetap terpisah. Relationship verified tidak otomatis berarti portal access granted.

## 18. Keputusan Open Questions

1. Institution nonaktif dikeluarkan dari accessible IDs.
2. User dengan assignment lembaga tetapi seluruh institution nonaktif tetap `INSTITUTION` dengan `[]`.
3. Modul platform selalu permission-gated; `FOUNDATION` tidak otomatis mendapat authority GLOBAL.
4. Hard-deleted institution tidak menghasilkan access ID; FK audit wajib sebelum implementasi.

## 19. Hold

```text
🟢 Design 1B siap review
⛔ Migration/code 1B belum boleh dijalankan
⛔ PersonRelationshipRule ditahan untuk 1C
⛔ Relationship migration ditahan untuk 1C
```
