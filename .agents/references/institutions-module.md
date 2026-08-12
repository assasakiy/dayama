# Institusi Management Module

## Overview
Module terpisah untuk mengelola lembaga pendidikan beserta entitas terkait (guru, siswa, kelas).
Institusi adalah **entitas inti** yang bisa direferensikan dari berbagai tempat: landing page, blog post, dll.

> **Status**: Lembaga sudah ada (migrasi dari Landing). Guru, Siswa, Kelas belum diimplementasi.

## Sidebar Group
```
Institusi Management
├── Lembaga       (/institutions)         ← ✅ Sudah ada
├── Guru / Ustadz (/institutions/teachers) ← 🔜 Belum
├── Siswa         (/institutions/students) ← 🔜 Belum
└── Kelas         (/institutions/classes)  ← 🔜 Belum
```

## Entity Relationship
```
Institution (1) ──┬── (N) Teachers
                   ├── (N) Classes ── (N) Students
                   └── (N) Students
```

---

## Database Schema

### `institutions` table (rename dari `landing_institutions`)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| name | string | Nama lembaga (e.g., "MA", "MTs", "MI") |
| slug | string | URL slug |
| description | text | Deskripsi lembaga |
| cover_url | string | Cover image |
| logo_url | string | Logo image |
| accreditation | string | Akreditasi |
| head_name | string | Nama kepala lembaga |
| student_count | int | Jumlah siswa |
| established_year | int | Tahun berdiri |
| facilities | json | Array of facility objects (uses `<IconPicker>` for `icon`) |
| extracurriculars | json | Array of strings |
| is_active | boolean | Status aktif |
| created_at / updated_at | timestamps | |

### `teachers` table (🔜 belum ada)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| institution_id | bigint | FK to institutions |
| name | string | Nama guru/ustadz |
| nip | string | NIP (nullable) |
| position | string | Jabatan |
| subject | string | Mata pelajaran |
| photo_url | string | Foto |
| is_active | boolean | |

### `classes` table (🔜 belum ada)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| institution_id | bigint | FK to institutions |
| name | string | Nama kelas (e.g., "7A", "8B") |
| grade | int | Tingkat (7, 8, 9, 10, 11, 12) |
| academic_year | string | Tahun ajaran |
| homeroom_teacher_id | bigint | FK to teachers (nullable) |

### `students` table (🔜 belum ada)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| institution_id | bigint | FK to institutions |
| class_id | bigint | FK to classes (nullable) |
| name | string | Nama siswa |
| nis | string | NIS |
| gender | enum | L/P |
| photo_url | string | Foto |
| is_active | boolean | |

---

## Controller Routing

### Saat ini (masih di route landing — perlu migrasi)
| Method | Route | Description |
|--------|-------|-------------|
| GET | `/landing/institutions` | Index lembaga |
| POST | `/landing/institutions` | Create lembaga |
| GET | `/landing/institutions/{id}/edit` | Edit lembaga |
| PUT | `/landing/institutions/{id}` | Update lembaga |
| DELETE | `/landing/institutions/{id}` | Delete lembaga |

### Target (setelah migrasi)
| Method | Route | Description |
|--------|-------|-------------|
| GET | `/institutions` | Index lembaga |
| POST | `/institutions` | Create lembaga |
| GET | `/institutions/{id}/edit` | Edit lembaga |
| PUT | `/institutions/{id}` | Update lembaga |
| DELETE | `/institutions/{id}` | Delete lembaga |
| — | `/institutions/teachers` | 🔜 CRUD guru |
| — | `/institutions/students` | 🔜 CRUD siswa |
| — | `/institutions/classes` | 🔜 CRUD kelas |

---

## Frontend File Map

### Saat ini (perlu dipindah)
```
Pages/Landing/Institutions/
├── Index.tsx     → Card grid + create modal
└── Edit.tsx      → Full form editor (cover, logo, facilities, extracurriculars)
```

### Target (setelah migrasi)
```
Pages/Institutions/
├── Index.tsx         → Daftar lembaga
├── Edit.tsx          → Edit profil lembaga
├── Teachers/
│   └── Index.tsx     → 🔜 Daftar guru per-lembaga
├── Students/
│   └── Index.tsx     → 🔜 Daftar siswa
└── Classes/
    └── Index.tsx     → 🔜 Daftar kelas
```

---

## Migrasi Checklist
- [ ] Rename tabel `landing_institutions` → `institutions`
- [ ] Pindahkan `Pages/Landing/Institutions/` → `Pages/Institutions/`
- [ ] Pindahkan route dari `/landing/institutions` → `/institutions`
- [ ] Update sidebar: hapus dari group `landing`, buat group `institusi` baru
- [ ] Update semua import path dan controller reference
- [ ] Buat `InstitutionController` terpisah (jika masih di `LandingController`)
