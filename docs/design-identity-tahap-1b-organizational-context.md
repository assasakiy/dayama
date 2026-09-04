# DAYAMA — Design Document Tahap 1B: Organizational Context & Contextual Permission

**Status:** Final Draft untuk Approval  
**Scope:** Tahap 1B — Organizational Context, Role Assignment SSOT, dan Contextual Permission  
**Di luar scope:** Personal/relationship access seperti Wali→Anak, Santri→own record, PersonRelationshipRule. Seluruhnya masuk Tahap 1C.

**Prasyarat:**

- `docs/rbac/README.md`
- `.agents/references/rbac-module.md`
- Tahap 1A Identity Foundation — CLOSED
- Tahap 1A.1 / 1A.1b / 1A.1c — CLOSED

## 0. Tujuan Tahap 1B

Tahap 1B memisahkan:

```text
ROLE ASSIGNMENT
→ WHICH role applies WHERE?

ORGANIZATIONAL CONTEXT
→ WHERE can actor operate?

CONTEXTUAL PERMISSION
→ WHAT can actor do at that location?

PERSONAL RELATIONSHIP [1C]
→ WHO may personal actor access?
```

Target:

```text
User
├── RoleAssignmentService
├── OrganizationalAccessResolver
└── PermissionContextResolver
```

## 1. Sub-Tahap Wajib

```text
1B.0 Role Assignment SSOT
 ↓
1B.1 Organizational Context
 ↓
1B.2 Contextual Permission
```

Urutan tidak boleh dilompati.

## 2. Masalah Saat Ini

- `ScopeRule`, `InstitutionScope`, `ActiveInstitution`, controllers, dan frontend membaca role/scope sendiri.
- `scope = null` tidak berarti GLOBAL; editor/author/subscriber juga scope null.
- Spatie role assignment tidak menyimpan institution context.
- Union permission Spatie berpotensi membocorkan permission role institutional lintas institution.

## 3. Tahap 1B.0 — Role Assignment SSOT

### 3.1 Writer Resmi

Semua mutasi role wajib melalui `RoleAssignmentService`:

```text
assign()
remove()
sync()
bulkAssign()
assignInstitution()
removeInstitution()
```

Dilarang bagi controller baru memanggil langsung `assignRole`, `removeRole`, `syncRoles`, atau mutasi `RoleUser` untuk organizational assignment.

### 3.2 Kontrak Multi-Institution

```json
{
  "assignments": [
    {"role": "guru", "institution": "MA"},
    {"role": "wakil_kepala", "institution": "MTs"},
    {"role": "editor", "institution": null}
  ]
}
```

### 3.3 Sinkronisasi

```text
Assign Guru @ MA
→ ensure Spatie Guru
→ ensure core_role_user Guru @ MA

Assign Guru @ MTs
→ Spatie Guru tetap satu
→ pivots Guru @ MA dan Guru @ MTs

Remove Guru @ MA
→ hapus pivot MA
→ Spatie Guru tetap karena pivot MTs masih ada

Remove Guru @ MTs terakhir
→ hapus pivot
→ remove Spatie Guru
```

Role non-institution (`editor`, `operator_yayasan`, administrator) memakai assignment Spatie tanpa institution binding.

### 3.4 Resolver Defensive Validation

INSTITUTION assignment valid hanya jika:

```text
core_role_user ada
+ role sama masih assigned melalui Spatie
+ institution exists dan aktif
```

Stale pivot tanpa Spatie role tidak memberi akses. Spatie institutional role tanpa pivot juga tidak memberi institution authority.

### 3.5 Reconciliation

Preflight 1B.0 mengaudit:

```text
orphan role_user
Spatie role missing
institution missing/inactive
duplicate assignments
institutional role tanpa institution binding
```

Perbaikan deterministic dan terdokumentasi; tidak ada delete agresif tanpa aturan.

## 4. Tahap 1B.1 — Organizational Context

### 4.1 OrgLevel

```php
enum OrgLevel: string
{
    case GLOBAL = 'global';
    case FOUNDATION = 'foundation';
    case INSTITUTION = 'institution';
    case PERSONAL = 'personal';
}
```

Precedence:

```text
GLOBAL > FOUNDATION > INSTITUTION > PERSONAL
```

Hanya level mengikuti precedence. Permission tetap union, lalu difilter context pada 1B.2.

### 4.2 OrgContext

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

Immutable, serializable, tidak membawa model User, ID dinormalisasi string unique.

### 4.3 Definisi Level

| Level | Kriteria |
|---|---|
| GLOBAL | PSA atau assigned role dengan `grants_global_context=true` |
| FOUNDATION | assigned Spatie role `scope=yayasan` |
| INSTITUTION | minimal satu valid role-institution assignment |
| PERSONAL | authenticated user tanpa authority organisasi |

Guest/public berada di luar resolver.

### 4.4 grants_global_context

Migration:

```text
core_roles.grants_global_context boolean default false
```

Seed awal:

```text
super-admin, administrator platform → true
lainnya                           → false
```

Runtime resolver hanya membaca flag, tidak nama role.

Hanya Primary Super Admin boleh grant/revoke. Backend/service/policy wajib enforce; UI hiding tidak cukup. Perubahan masuk activity log existing:

```text
role.grants_global_context.changed
actor, role_id, old_value, new_value, timestamp
```

### 4.5 Role Status

`Role.status` tidak berubah semantics pada 1B. PermissionRule dan resolver tidak menambahkan enforcement status baru. Ini known gap terpisah.

### 4.6 Actor Authority vs Person Membership

Authority actor berasal role assignment, bukan `InstitutionMembership` Person actor.

Target Person berbeda: akses memakai intersection:

```text
actor authorized institution IDs
∩ target Person active memberships
```

Inactive target membership tidak memberi akses.

### 4.7 accessibleInstitutionIds

```text
GLOBAL      → null
FOUNDATION  → null
INSTITUTION → [active institution IDs], dapat []
PERSONAL    → []
```

```text
null !== []
null  = unrestricted boundary
[]    = no authorized institution
[IDs] = restricted authority
```

Institution nonaktif dikeluarkan. Jika semua institution nonaktif, level tetap INSTITUTION dengan `[]`.

### 4.8 PERSONAL dan Dashboard

PERSONAL berarti tanpa organizational authority, bukan tanpa dashboard.

```text
PERSONAL + CMS permission → CMS allowed
PERSONAL → Academic/HR/institutional resources denied
```

Dashboard shell tetap permission-based.

## 5. Working Context

### 5.1 activeInstitutionId

Menjawab DI MANA user sedang bekerja, bukan DI MANA boleh bekerja.

- INSTITUTION: wajib anggota accessible IDs.
- GLOBAL/FOUNDATION: boleh institution aktif sebagai display/operational filter, bukan authority.
- PERSONAL: selalu null.

Session tidak memberi authority.

### 5.2 primaryInstitutionId

Disimpan di `core_users.preferences`:

```json
{"primary_institution_id": "uuid"}
```

Update wajib merge JSON. Primary hanya default preference, bukan permission/assignment.

### 5.3 Default Active

```text
INSTITUTION:
primary valid → primary
else first accessible
else null

GLOBAL/FOUNDATION:
primary active valid → optional working context
else null

PERSONAL:
null
```

### 5.4 Switch

Backend:

1. target exists dan aktif;
2. INSTITUTION target harus accessible;
3. GLOBAL/FOUNDATION boleh target aktif;
4. PERSONAL 403;
5. refresh resolver context;
6. write session.

## 6. InstitutionScope Final

```text
GLOBAL      → unrestricted
FOUNDATION  → unrestricted
INSTITUTION → WHERE institution_id = activeInstitutionId
               active null → WHERE 1=0
PERSONAL    → WHERE 1=0
```

GLOBAL/FOUNDATION tidak otomatis terfilter session. Operational view tertentu harus explicit opt-in ke active institution.

INSTITUTION multi-institution report juga explicit opt-in + permission + `whereIn(accessible IDs)`; bukan global scope default.

## 7. Resolver Lifecycle

```php
$this->app->scoped(OrganizationalAccessResolver::class);
```

Cache pada instance per request; bukan static/singleton/cross-request. `refreshActiveInstitution()` membuat immutable context baru tanpa menghitung ulang level/IDs.

`ActiveInstitution` dipertahankan sebagai compatibility facade tipis ke resolver. Tidak boleh membaca role.scope, RoleUser, atau session sebagai authority sendiri.

## 8. ScopeRule Final

Target dengan `institution_id`:

- GLOBAL/FOUNDATION: boundary lewat, lanjut PermissionRule.
- INSTITUTION: target institution harus accessible.
- PERSONAL: deny.

Target Person:

- INSTITUTION: active target memberships beririsan dengan accessible IDs.
- GLOBAL/FOUNDATION: organizational boundary unrestricted; permission tetap wajib.
- PERSONAL: dibahas Tahap 1C, fail closed untuk organizational action pada 1B.

## 9. Tahap 1B.2 — Contextual Permission

### 9.1 Tujuan

Role institutional hanya memberi permission di institution assignment-nya.

```text
Guru @ MA + Wakil Kepala @ MTs
MA  → Guru permissions
MTs → Wakil Kepala permissions
```

### 9.2 Institutional Resource Registry

Registry terpusat, tidak memakai nama `SCOPED_MODULES`:

```text
resource class/name
permission prefix
institutional true/false
```

Contoh:

```text
Student  → academic.students → true
Employee → hr.employees      → true
Post     → posts             → false
```

AbilityResolver dan PermissionContextResolver menggunakan mapping sama.

### 9.3 PermissionContextResolver

Kontrak konsep:

```text
resolve(User actor, permission, institutionId)
```

Sumber institutional permission:

- GLOBAL role: permission berlaku seluruh platform boundary.
- FOUNDATION role: permission berlaku lintas foundation institutions.
- INSTITUTION role: permission hanya pada institution binding role itu.
- Direct user permission: berlaku pada seluruh institution yang sudah authorized OrgContext, tetapi tidak menciptakan WHERE/authority.
- scope-null non-global role: institutional permission diabaikan sebagai configuration anomaly.

Non-institutional permissions tetap memakai existing Spatie semantics.

### 9.4 Mixed Role

```text
Operator Yayasan + Guru @ MA
Target MA  → Foundation + Guru permission
Target MTs → Foundation permission saja
```

Precedence level tidak menghapus lower-level assignment; permission lower-level tetap contextual.

### 9.5 Class-Level Authorization

Instance action mengambil institution ID dari target.

Class action (`viewAny`, `create`) membutuhkan institution authorization context:

```text
INSTITUTION → activeInstitutionId
explicit create institution → validated request context
```

Tidak boleh class-level institutional authorization tanpa institution context. Controller tetap masuk melalui Gate → Policy → AuthorizationService. Policy tetap thin adapter.

### 9.6 Pipeline

Urutan tetap:

```text
PrimarySuperAdminRule
ScopeRule
PermissionRule
OwnershipRule
RankRule
```

ScopeRule menjawab WHERE. PermissionRule menjawab WHAT dan mendelegasikan institutional checks ke PermissionContextResolver.

### 9.7 effectiveRolesByInstitution

Resolver membangun peta per request dari assignment data bersama:

```text
MA  → [guru]
MTs → [wakil_kepala]
```

Tidak query ulang setiap permission check. Cache permission context per `(user,institution,permission)` hanya per request.

### 9.8 Contextual Capabilities

Capabilities institutional dihitung terhadap active/explicit institution. Switch wajib menghasilkan fresh Inertia props/capabilities; tidak cache lintas switch.

## 10. Frontend Contract

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

Frontend tidak boleh infer authority dari role name/scope. Raw role/permissions boleh sementara untuk compatibility granular; organizational decisions memakai OrgContext dan capabilities.

## 11. Platform Modules

Platform modules default diberikan hanya kepada GLOBAL authority melalui registry/seeder, tetapi enforcement tetap Gate/permission-based; bukan `if level GLOBAL allow`.

FOUNDATION tidak otomatis mendapat users/roles/permissions/settings/apps/domains/platform registry.

## 12. Hard-Deleted Institution

Resolver hanya menerima institution existing dan aktif. Stale pivot ke institution hilang tidak menghasilkan ID. Audit FK wajib sebelum implementasi; cascade unrelated tidak diubah diam-diam.

## 13. Consumer Migration

Wajib:

```text
RoleAssignmentService writers
ActiveInstitution
ScopeRule
InstitutionScope
CheckInstitutionScope
Institution switch
HandleInertiaRequests
AuthorizationService capabilities
PersonController
StudentController
EmployeeController
DepartmentController
RombelController
UserController
RoleController
frontend sidebar
usePermissions
semua hasil grep tambahan
```

Pengecualian direct role.scope read hanya untuk resolver/assignment domain dan Role administration metadata UI.

## 14. Test Matrix 1B.0

1. Assign Guru@MA → Spatie+pivot sync.
2. Assign Guru@MTs → no duplicate Spatie, two pivots.
3. Remove Guru@MA → Spatie remains.
4. Remove last Guru assignment → Spatie removed.
5. Bulk assignment sync.
6. RoleController assign users sync.
7. Stale pivot without Spatie gives no authority.
8. Spatie institutional role without pivot gives no authority.
9. Reconciliation deterministic.

## 15. Test Matrix 1B.1

10. PSA → GLOBAL/null.
11. grants global flag → GLOBAL/null.
12. scope-null non-global → PERSONAL/[].
13. Foundation → FOUNDATION/null.
14. MA+MTs assignments → INSTITUTION/[MA,MTs].
15. Foundation+institution → FOUNDATION/null.
16. Inactive institution excluded.
17. All inactive → INSTITUTION/[].
18. PERSONAL active null.
19. Forged session gives no authority.
20. Valid primary defaults active.
21. Stale primary fallback.
22. Resolve once/request.
23. Actor membership inactive does not reduce role authority.
24. Target Person active membership passes scope.
25. Target Person inactive membership denied.

## 16. Test Matrix InstitutionScope/Switch

26. GLOBAL unrestricted.
27. FOUNDATION unrestricted.
28. INSTITUTION active MA → MA only.
29. INSTITUTION active null → no rows.
30. PERSONAL → no rows.
31. null distinct from [].
32. Unauthorized switch 403.
33. Authorized switch allowed.
34. Inactive switch 403.
35. GLOBAL/FOUNDATION active switch allowed.
36. PERSONAL switch 403.

## 17. Test Matrix 1B.2

37. Guru@MA + Wakil Kepala@MTs does not leak permissions.
38. Foundation+Guru: Guru permission only at MA.
39. Direct institutional permission works only inside authorized IDs.
40. PERSONAL direct institutional permission remains denied organizationally.
41. Neutral scope-null role institutional permission ignored.
42. create/viewAny Student requires institution context.
43. Explicit create MA by MTs-only actor denied.
44. Explicit create MA by authorized actor+permission allowed.
45. Contextual capabilities change after switch.

## 18. Regression/Security Tests

46. PERSONAL editor CMS remains allowed.
47. PERSONAL editor Academic/HR denied.
48. Non-PSA global-flag mutation denied.
49. PSA global-flag mutation allowed and logged old/new.
50. Role removal reflected next request.
51. Inertia contract stable.
52. Sidebar does not read role/scope.
53. Existing Gate/Policy tests pass.
54. 1A tests pass.
55. Auth/session tests pass.
56. Route list valid.

## 19. Security Invariants

1. Session/primary never give authority.
2. null and [] remain distinct.
3. PERSONAL fails closed for organizational resources.
4. Actor authority never depends on Person membership.
5. Target Person requires active affiliation for institution scope.
6. Stale pivot without matching Spatie assignment gives no authority.
7. Institutional permission never leaks across role bindings.
8. Direct permission does not create institution authority.
9. Neutral scope-null role cannot source institutional permission.
10. Role scope only read by resolver/assignment domain and metadata admin.
11. UI is never enforcement.
12. Global flag mutation is PSA-only and audited.
13. Guest/public is not PERSONAL.
14. Pipeline separation WHAT/WHERE remains.

## 20. Implementation Order

```text
1B.0 Role Assignment SSOT
├── audit/reconciliation
├── RoleAssignmentService
├── multi-dimensional contract
├── migrate all writers
└── tests

1B.1 Organizational Context
├── grants_global_context migration/governance
├── OrgLevel, OrgContext, scoped resolver
├── primary preference
├── ActiveInstitution facade
├── ScopeRule, InstitutionScope, middleware/switch
├── Inertia orgContext
├── frontend organization rendering
└── tests

1B.2 Contextual Permission
├── Institutional Resource Registry
├── effective roles map
├── PermissionContextResolver
├── PermissionRule integration
├── class-level context
├── direct/neutral role semantics
├── capabilities refresh
└── leakage/security tests
```

## 21. Tahap 1C — Out of Scope

```text
core_person_relationships
guardian/parent/child
relationship verification
portal access grant/revoke
PersonRelationshipRule
santri own Student
own Person
portal authorization
```

## 22. Acceptance Criteria

Tahap 1B CLOSED hanya jika:

- role writers terpusat dan Spatie/core_role_user sinkron;
- stale data tidak memberi authority;
- OrganizationalAccessResolver menjadi SSOT;
- ActiveInstitution hanya facade;
- InstitutionScope mengikuti active context final;
- contextual permissions tidak bocor lintas institution;
- class-level actions punya institution context;
- direct/neutral role semantics teruji;
- capabilities refresh sesuai switch;
- frontend tidak infer authority dari role/scope;
- seluruh test 1A/1B/auth/route lulus;
- tidak ada scope 1C masuk.

## 23. Status Mandor

```text
Tahap 1A      ✅ CLOSED
Tahap 1B      ✅ FINAL DRAFT
Tahap 1B.0    ⛔ HOLD sampai commit dokumen direview
Tahap 1B.1    ⛔ HOLD sampai 1B.0 selesai
Tahap 1B.2    ⛔ HOLD sampai 1B.1 selesai
Tahap 1C      ⛔ HOLD
```

Eksekusi pertama setelah approval hanya Tahap 1B.0 — Role Assignment SSOT.
