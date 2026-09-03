# DRAFT ARSITEKTUR PLATFORM DAYAMA

## 1. Latar Belakang

DAYAMA pada awalnya berkembang sebagai aplikasi Laravel berbasis CMS, kemudian berevolusi menjadi sistem manajemen yayasan dan lembaga pendidikan dengan pendekatan **Modular Monolith**, multi-domain, multi-institusi, serta Role-Based Access Control berbasis scope.

Pada kondisi repository saat ini, backend telah dibagi menjadi 12 domain utama, yaitu Core, CMS, Academic, HR, CRM, Finance, Library, Inventory, AI, Landing, Yayasan, dan System. Aplikasi juga telah mempunyai konsep domain terpisah untuk Account, Dashboard, API, Blog, dan Landing.

Konfigurasi multi-domain saat ini masih dikelola melalui `config/projects.php`, dengan domain sistem inti dan project frontend didefinisikan melalui environment configuration.

Arsitektur tersebut akan dikembangkan lebih lanjut menjadi **DAYAMA Platform**, yaitu satu ekosistem aplikasi yang:

- menggunakan satu identitas pengguna;
- mendukung banyak yayasan dan lembaga;
- memungkinkan satu pengguna memiliki banyak peran;
- memungkinkan satu pengguna terhubung ke lebih dari satu lembaga;
- memungkinkan aplikasi berjalan secara independen;
- memungkinkan setiap lembaga memiliki website dan branding sendiri;
- mempunyai pusat data yayasan tanpa mengambil alih kepemilikan data lembaga;
- serta menghindari kegagalan satu aplikasi menjatuhkan seluruh sistem.

---

# 2. Prinsip Dasar Arsitektur

Arsitektur DAYAMA akan mengikuti beberapa prinsip utama.

## 2.1 Satu platform, beberapa aplikasi

DAYAMA bukan satu dashboard besar yang menampung seluruh kebutuhan.

Platform terdiri atas beberapa aplikasi dengan fungsi yang berbeda:

```text
DAYAMA Platform
│
├── Account
├── Dashboard
├── Portal
├── Data Center
├── PSB
├── Sites
├── Blog/CMS
├── API Gateway
└── Core Platform
```

Masing-masing dapat mempunyai frontend dan backend sendiri.

---

## 2.2 Satu repository masih diperbolehkan

Pemisahan aplikasi tidak harus langsung berarti microservice dan repository terpisah.

Model awal yang direkomendasikan:

```text
MONOREPO
+
MULTI APPLICATION
+
MULTI RUNTIME
```

Contoh:

```text
dayama/
├── apps/
│   ├── core/
│   ├── account/
│   ├── dashboard/
│   ├── portal/
│   ├── datacenter/
│   ├── psb/
│   ├── sites/
│   └── blog/
│
├── modules/
│   ├── Academic/
│   ├── HR/
│   ├── Finance/
│   ├── CRM/
│   ├── Library/
│   └── Inventory/
│
└── packages/
    ├── contracts/
    ├── shared-types/
    └── common/
```

Satu repository mempermudah pengembangan, sedangkan pemisahan runtime memberikan isolation.

---

# 3. Tujuan Failure Isolation

Salah satu tujuan utama arsitektur baru adalah:

> Kegagalan satu aplikasi tidak boleh otomatis menjatuhkan aplikasi lain.

Contohnya:

```text
Blog crash
│
├── Account     ✓
├── Portal      ✓
├── Dashboard   ✓
├── PSB         ✓
└── Data Center ✓
```

Begitu juga:

```text
Dashboard maintenance
│
├── Account ✓
├── Portal  ✓
├── Blog    ✓
└── PSB     ✓
```

Karena itu pemisahan route saja tidak cukup.

Aplikasi penting harus dapat dijalankan sebagai runtime/process/container terpisah.

Contoh:

```text
core
account
dashboard
portal
datacenter
psb
blog
```

masing-masing dapat direstart dan dideploy secara terpisah.

---

# 4. Core Platform

Core tetap dipertahankan, tetapi perannya berubah.

Core tidak lagi menjadi tempat seluruh business logic.

Core menjadi **platform registry dan control plane**.

Tanggung jawab Core meliputi:

```text
Core
├── Application registry
├── Institution registry
├── Foundation registry
├── Domain registry
├── Site registry
├── Branding configuration
├── Feature flags
├── Service discovery
├── Routing metadata
├── Shared platform configuration
└── Health metadata
```

Core harus dibuat:

- kecil;
- stabil;
- minim dependency;
- minim business logic;
- jarang berubah.

Prinsipnya:

> Core mengetahui aplikasi dan lembaga apa yang tersedia, tetapi tidak harus mengetahui bagaimana seluruh bisnis masing-masing aplikasi bekerja.

---

# 5. Domain Sistem dan Domain Dinamis

Domain akan dibagi menjadi dua kategori.

## 5.1 Domain sistem

Domain sistem dikonfigurasi secara manual melalui `.env` atau konfigurasi deployment.

Contoh:

```env
MAIN_DOMAIN=dayama.id

ACCOUNT_DOMAIN=account.dayama.id
DASHBOARD_DOMAIN=dashboard.dayama.id
PORTAL_DOMAIN=portal.dayama.id
DATACENTER_DOMAIN=data.dayama.id
PSB_DOMAIN=psb.dayama.id
API_DOMAIN=api.dayama.id
```

Domain sistem harus tetap dapat dikenali walaupun database atau cache bermasalah.

---

# 6. Domain Lembaga dan Site

Domain lembaga tidak perlu dimasukkan satu per satu ke `.env`.

Domain tersebut disimpan pada database Core.

Contoh:

```text
sites

id
institution_id
name
domain
type
status
is_primary
```

Contoh data:

```text
dayama.id
→ Yayasan DAYAMA

mts.dayama.id
→ MTs DAYAMA

ma.dayama.id
→ MA DAYAMA

mi.dayama.id
→ MI DAYAMA
```

Bahkan satu lembaga dapat mempunyai beberapa site:

```text
MA DAYAMA
│
├── ma.dayama.id
├── alumni.ma.dayama.id
└── informasi.ma.dayama.id
```

Karena itu:

```text
Institution ≠ Site
```

Relasinya:

```text
Institution
   │
   └── Sites
       ├── Main Site
       ├── Alumni Site
       └── Microsite
```

---

# 7. Domain Resolution dan Cache

Ketika request datang:

```text
Host: mts.dayama.id
```

sistem melakukan:

```text
Request
   ↓
Redis
   ↓
Domain Registry
   ↓
Site
   ↓
Institution
   ↓
Branding + Configuration
```

Contoh cache:

```text
site:domain:mts.dayama.id
```

berisi:

```text
site_id
institution_id
site_type
active
branding_version
```

Jika cache tidak ditemukan:

```text
Redis miss
    ↓
Core DB
    ↓
resolve site
    ↓
cache kembali
```

Jika domain atau konfigurasi site berubah:

```text
Update DB
→ invalidate cache
→ request berikutnya rebuild cache
```

---

# 8. Branding Setiap Lembaga

Setiap site dapat mempunyai branding masing-masing.

Contohnya:

```text
institution/site branding
├── logo
├── favicon
├── nama resmi
├── nama pendek
├── primary color
├── secondary color
├── typography
├── address
├── phone
├── email
├── social links
└── theme configuration
```

Dengan demikian seluruh lembaga tetap menggunakan DAYAMA Platform tetapi tampil sebagai identitas lembaganya masing-masing.

---

# 9. Account sebagai Identity Provider

`account.dayama.id` menjadi aplikasi khusus identitas.

Account tidak menyimpan data akademik maupun operasional.

Tanggung jawab Account:

```text
Account
├── Login
├── Register
├── Password
├── Password recovery
├── Email verification
├── Phone verification
├── 2FA
├── OAuth/OIDC
├── Sessions
├── Security
├── Connected identity
└── Basic account profile
```

Account menjadi Identity Provider untuk seluruh aplikasi.

Contohnya:

```text
dashboard.dayama.id
        ↓
belum login
        ↓
account.dayama.id
        ↓
authenticate
        ↓
dashboard.dayama.id
```

Begitu pula:

```text
portal
PSB
Data Center
Blog
Library
```

dapat memakai identitas yang sama.

---

# 10. User, Person, dan Role Tidak Boleh Disamakan

DAYAMA harus membedakan dengan tegas:

## Account/User

Identitas digital untuk login.

```text
User
id
email
phone
status
```

## Person

Manusia sebenarnya.

```text
Person
id
nik
name
birth
gender
```

## Membership / Assignment

Hubungan Person/User dengan organisasi.

Misalnya:

```text
Ahmad
│
├── Operator → MTs
├── Operator → MA
├── Guru → MA
└── Wali → Santri MI
```

Artinya:

```text
1 Account
1 Person
banyak hubungan
```

---

# 11. Multi-Role dan Multi-Institution

DAYAMA tidak boleh menggunakan model:

```text
users.institution_id
```

sebagai satu-satunya hubungan user dan lembaga.

Hubungannya harus many-to-many.

Contoh konsep:

```text
institution_memberships

id
person_id
institution_id
role_id
status
valid_from
valid_until
```

Contohnya:

```text
Ahmad
├── MTs → Operator
└── MA  → Operator
```

Ahmad tidak perlu mempunyai dua akun.

---

# 12. Scope Akses

Scope dikembangkan dari model sekarang yang membedakan `yayasan` dan `lembaga`. Repository saat ini memang telah menerapkan scope yayasan/lembaga melalui pipeline authorization, Global Scope, serta middleware.

Target baru mempunyai tiga kategori utama:

```text
Foundation Scope
Institution Scope
Personal Scope
```

## Foundation Scope

Contoh:

```text
Ketua Yayasan
Admin Yayasan
Auditor
```

Dapat melihat lintas lembaga sesuai permission.

## Institution Scope

Contoh:

```text
Kepala Madrasah
Operator
Guru
Bendahara
TU
```

Terikat pada satu atau beberapa lembaga.

## Personal Scope

Contoh:

```text
Santri
Wali
Alumni
```

Hanya dapat mengakses data yang secara langsung berhubungan dengan dirinya.

---

# 13. Dashboard

`dashboard.dayama.id` merupakan **workspace operasional**.

Pengguna utamanya:

```text
Admin Yayasan
Operator
Kepala Lembaga
Bendahara
TU
Guru/Ustadz
Pegawai tertentu
```

Tidak semua role harus diarahkan ke Dashboard.

Struktur:

```text
dashboard.dayama.id/
│
├── datacenter
├── academic
├── hr
├── finance
├── library
├── inventory
├── cms
└── system
```

Tidak perlu:

```text
dashboard.dayama.id/dashboard/...
```

karena domain tersebut sendiri sudah merupakan dashboard.

---

# 14. Data Center

Data Center menjadi pusat master data manusia dan hubungan organisasi.

Contoh:

```text
Data Center
├── Persons
├── Students
├── Employees
├── Teachers/Ustadz
├── Guardians
├── Alumni
├── Donors
└── Relationships
```

Namun Data Center tidak berarti semua data dimasukkan ke satu tabel.

Konsepnya:

```text
Person
│
├── Student identity
├── Employee identity
├── Guardian relationships
├── Alumni relationships
└── Institution relationships
```

Data Center menjawab:

> Siapa orang ini?

Academic menjawab:

> Bagaimana kegiatan akademiknya?

HR menjawab:

> Bagaimana status kepegawaiannya?

Finance menjawab:

> Bagaimana transaksi dan kewajiban keuangannya?

---

# 15. Dashboard Context Switcher

Operator dapat mempunyai akses lebih dari satu lembaga.

Karena itu Dashboard mempunyai:

```text
Context Switcher
```

Contoh:

```text
Konteks Aktif

✓ Yayasan DAYAMA
  MTs DAYAMA
  MA DAYAMA
```

Saat berubah dari:

```text
MTs
```

ke:

```text
MA
```

tidak perlu login ulang.

Menu dan data akan mengikuti context aktif.

Server tetap wajib memvalidasi bahwa user memang mempunyai membership pada lembaga tersebut.

Frontend tidak boleh menentukan akses sendirian.

---

# 16. Portal

`portal.dayama.id` adalah aplikasi personal.

Target pengguna:

```text
Santri
Wali
Alumni
Guru
Pegawai
```

Portal bersifat **user-centric**, bukan institution-centric.

Contohnya seseorang dapat sekaligus:

```text
Guru
Pegawai
Wali
Alumni
```

semuanya tetap menggunakan satu Portal.

Tidak perlu membuat:

```text
santri.dayama.id
wali.dayama.id
guru.dayama.id
alumni.dayama.id
```

hanya berdasarkan role.

---

# 17. Portal Wali Multi-Lembaga

Contoh:

Fatimah mempunyai dua anak:

```text
Ahmad
→ MTs DAYAMA

Aisyah
→ MA DAYAMA
```

Portal menampilkan:

```text
Anak Saya

Ahmad
MTs DAYAMA
Kehadiran 96%
Nilai rata-rata 84

Aisyah
MA DAYAMA
Kehadiran 98%
Nilai rata-rata 88
```

Fatimah tidak perlu login ke dua portal berbeda.

---

# 18. Portal sebagai Unified Overview

Portal pusat menampilkan informasi lintas lembaga secara ringkas.

Contohnya:

```text
Nilai
Kehadiran
Tagihan
Pengumuman
Status akademik
```

Tetapi data lengkap tetap dapat dilihat pada konteks lembaga terkait.

Prinsip:

> Yayasan/Portal = unified overview.

> Lembaga = detailed operational experience.

Contohnya:

```text
Portal
  ↓
Ahmad - MTs
  ↓
Lihat Detail
  ↓
MTs Site / Academic Experience
```

Di sisi lembaga dapat ditampilkan:

```text
nilai per mapel
tugas
UTS
UAS
absensi rinci
catatan guru
rapor
jadwal
```

---

# 19. App Switcher

Header platform menyediakan **Application Switcher**.

Contoh:

```text
Aplikasi

Portal Saya
Dashboard
PSB
Perpustakaan
Data Center
Blog
Website MTs
Website MA
```

App Switcher berbeda dengan Context Switcher.

### App Switcher

Mengubah aplikasi.

```text
Portal → Dashboard → PSB
```

### Context Switcher

Mengubah lembaga/context.

```text
Yayasan → MTs → MA
```

Keduanya jangan digabung menjadi satu dropdown.

---

# 20. Guru dan Pegawai dapat Memiliki Dua Pengalaman

Seorang guru dapat menggunakan:

```text
dashboard.dayama.id
```

untuk pekerjaan.

Dan:

```text
portal.dayama.id
```

untuk keperluan personal.

Contoh:

```text
Ahmad

Dashboard
├── Guru MA
└── Operator MTs

Portal
├── Profil pribadi
└── Wali Santri MI
```

Satu akun tetap cukup.

---

# 21. PSB sebagai Aplikasi Terpusat

PSB dibuat sebagai aplikasi khusus:

```text
psb.dayama.id
```

PSB menangani penerimaan seluruh lembaga.

Jika user datang langsung:

```text
psb.dayama.id
```

maka tampil:

```text
Pilih tujuan pendaftaran:

MTs DAYAMA
MA DAYAMA
MI DAYAMA
RA DAYAMA
Pondok
```

Jika datang dari:

```text
mts.dayama.id
```

kemudian klik:

```text
Daftar
```

langsung diarahkan:

```text
psb.dayama.id/mts/register
```

tanpa memilih MTs lagi.

---

# 22. PSB Dashboard Pendaftar

Setelah login, pendaftar mempunyai:

```text
psb.dayama.id/dashboard
```

yang menampilkan seluruh pendaftarannya.

Contoh:

```text
Pendaftaran Saya

MTs DAYAMA
Status: Verifikasi

MA DAYAMA
Status: Jadwal Tes

Pondok
Status: Berkas Belum Lengkap
```

Pendaftar tidak perlu bolak-balik masuk website lembaga.

---

# 23. Applicant Bukan Student

DAYAMA harus membedakan:

```text
Applicant
```

dengan:

```text
Student
```

Alur:

```text
Applicant
↓
Submitted
↓
Verified
↓
Selection
↓
Accepted
↓
Re-registration
↓
Enrolled
↓
Student
```

Data akademik baru dibuat ketika peserta benar-benar menjadi siswa/santri.

---

# 24. Pengelolaan PSB dari Dashboard

Operator tidak menggunakan dashboard PSB publik.

Operator mengelola:

```text
dashboard.dayama.id/psb
```

Scope menentukan data.

Operator MTs:

```text
hanya pendaftar MTs
```

Operator MA:

```text
hanya pendaftar MA
```

Admin Yayasan:

```text
seluruh lembaga
```

---

# 25. Database Strategy

Database tidak harus menjadi satu database besar.

Target pembagian:

```text
core_db
identity_db
foundation_db / datacenter_db

ma_db
mts_db
mi_db
ra_db
pondok_db

psb_db
cms_db
```

Tidak seluruhnya harus dipisahkan fisik sejak awal.

Pemisahan dapat dilakukan bertahap.

---

# 26. Core Database

`core_db` menyimpan metadata platform.

Contoh:

```text
foundations
institutions
sites
domains
applications
service_registry
branding
feature_flags
platform_settings
```

Core DB tidak menyimpan nilai, absensi, tagihan, dan transaksi akademik lembaga.

---

# 27. Identity Database

`identity_db` dikelola Account.

Contoh:

```text
users
credentials
sessions
two_factor
oauth_clients
account_security
connected_identities
```

Identity tetap global.

Satu user dapat masuk ke banyak aplikasi dan banyak lembaga.

---

# 28. Database Lembaga

Setiap lembaga dapat mempunyai database operasional sendiri.

Contoh:

```text
mts_db
├── persons_local
├── students
├── teachers
├── employees
├── classes
├── attendance
├── grades
├── finance
└── operational data
```

Begitu pula:

```text
ma_db
mi_db
ra_db
```

---

# 29. Lembaga sebagai Source of Truth Operasional

Prinsip utama:

> Data operasional lembaga dimiliki oleh lembaga.

Contohnya:

```text
Nilai MTs
→ MTs authoritative

Absensi MA
→ MA authoritative

Pegawai MI
→ MI authoritative
```

Jika operator menemukan kesalahan:

```text
operator edit
→ langsung tersimpan
```

tidak perlu:

```text
edit
→ pengajuan
→ approval yayasan
→ baru berubah
```

Ini mempertahankan otonomi operasional lembaga.

---

# 30. Yayasan sebagai Aggregator

Database Yayasan/Data Center tidak menjadi transaksi utama lembaga.

Fungsinya:

```text
cross institution index
reporting
aggregate
global search
foundation statistics
global person matching
```

Flow:

```text
Lembaga
   ↓
sync/event
   ↓
Yayasan
```

Bukan:

```text
Yayasan
   ↓
menjadi pusat semua transaksi
```

---

# 31. Global Person Index

Karena database lembaga dapat independen, orang yang sama dapat mempunyai local record berbeda.

Contohnya:

```text
Ahmad

MTs → local person A1
MA  → local person B8
MI  → guardian record C4
```

Data Center mempunyai:

```text
Global Person GP-001
│
├── MTs → A1
├── MA  → B8
└── MI  → C4
```

Global Person tidak harus memaksa semua lembaga menggunakan record fisik yang sama.

Ini memberikan:

```text
independensi lembaga
+
cross-institution identity
```

---

# 32. Sinkronisasi

Sinkronisasi sebaiknya menggunakan event.

Contoh:

```text
PersonUpdated
StudentEnrolled
StudentGraduated
EmployeeAssigned
PaymentRecorded
```

Alurnya:

```text
Operator MTs
    ↓
update
    ↓
MTs DB commit
    ↓
event
    ↓
queue
    ↓
Foundation/Data Center
```

Jika Data Center sedang down:

```text
MTs tetap bekerja
```

Event dapat diproses setelah sistem kembali tersedia.

---

# 33. Hindari Sinkronisasi Dua Arah Tanpa Ownership

Setiap kategori data harus mempunyai owner.

Contoh:

```text
Data akademik
owner = lembaga

Institution registry
owner = Core/Yayasan

Account identity
owner = Account
```

Hindari keadaan:

```text
MTs mengubah A
Yayasan mengubah B
```

tanpa aturan siapa yang berhak menang.

---

# 34. Backend Setiap Aplikasi

Setiap aplikasi penting boleh membawa backend-nya sendiri.

Contoh:

```text
Account
├── frontend
├── backend
└── identity_db
```

```text
Dashboard
├── frontend
└── dashboard backend/BFF
```

```text
Portal
├── frontend
└── portal backend/BFF
```

```text
PSB
├── frontend
├── backend
└── psb_db
```

```text
Blog
├── frontend
├── CMS backend
└── cms_db
```

Konsep ini mirip **Backend for Frontend**.

---

# 35. API Gateway

`api.dayama.id` tetap tersedia.

Namun API Gateway bukan seluruh backend DAYAMA.

Fungsi utamanya:

```text
Public API
Mobile application
External integration
Partner integration
Webhook
Third-party services
```

Contohnya:

```text
api.dayama.id/v1/accounts
api.dayama.id/v1/institutions
api.dayama.id/v1/academic
api.dayama.id/v1/finance
```

Gateway meneruskan request ke service terkait.

---

# 36. Komunikasi Internal Tidak Wajib Lewat Gateway

Jangan membuat:

```text
Dashboard
    ↓
api.dayama.id
    ↓
Account
```

sebagai satu-satunya jalur.

Lebih baik:

```text
Dashboard Backend
    ↓
Account Service
```

langsung melalui jaringan private.

Sedangkan:

```text
External Client
    ↓
api.dayama.id
    ↓
Account Service
```

Dengan begitu jika API Gateway maintenance:

```text
Dashboard ✓
Account   ✓
Portal    ✓
Blog      ✓
```

yang terganggu terutama konsumen API eksternal.

---

# 37. Internal Network

Contoh service internal:

```text
account-service
dashboard-service
portal-service
datacenter-service
psb-service
academic-service
finance-service
cms-service
```

Mereka dapat berkomunikasi melalui private network.

Contoh:

```text
http://account-service:8000
```

tanpa harus membuka seluruh backend ke internet.

---

# 38. Service Boundary Tidak Perlu Terlalu Kecil

DAYAMA tidak perlu langsung membuat:

```text
student-service
teacher-service
person-service
grade-service
attendance-service
class-service
```

secara terpisah.

Tahap awal lebih aman menggunakan coarse-grained service.

Contohnya:

```text
Account
Core
Data Center
Platform Business API
PSB
CMS
Portal
Dashboard
```

Sedangkan Academic, HR, Finance, Library, Inventory tetap dapat menjadi modul di dalam business backend.

Microservice baru diekstrak jika memang dibutuhkan.

---

# 39. Target Logical Architecture

```text
                        DAYAMA PLATFORM

                     ┌─────────────────┐
                     │      CORE       │
                     │ Registry/Config │
                     └────────┬────────┘
                              │

        ┌─────────────────────┼──────────────────────┐
        │                     │                      │
        ▼                     ▼                      ▼

 ┌──────────────┐      ┌──────────────┐      ┌─────────────┐
 │   ACCOUNT    │      │  DASHBOARD   │      │   PORTAL    │
 │ Identity     │      │ Staff/Admin  │      │ Personal    │
 └──────────────┘      └──────┬───────┘      └──────┬──────┘
                              │                     │
                              │                     │
                      ┌───────▼────────┐    ┌───────▼────────┐
                      │  DATA CENTER   │    │ Personal Data  │
                      │ Foundation     │    │ Aggregation    │
                      └───────┬────────┘    └────────────────┘
                              │
              ┌───────────────┼─────────────────┐
              │               │                 │
              ▼               ▼                 ▼

            MTs DB          MA DB             MI DB
              │               │                 │
            Local           Local             Local
          Operations      Operations        Operations
```

Aplikasi lain:

```text
PSB
CMS/Blog
Library
Sites
```

dapat berjalan sebagai bagian ekosistem yang sama.

---

# 40. Website Yayasan dan Website Lembaga

Website publik menggunakan Core Site Registry.

Contoh:

```text
dayama.id
→ site yayasan

mts.dayama.id
→ site MTs

ma.dayama.id
→ site MA
```

Tombol layanan dapat membawa context.

Contoh:

```text
mts.dayama.id
   ↓
Daftar Sekarang
   ↓
psb.dayama.id/mts/register
```

---

# 41. Navigasi Ekosistem

Header aplikasi dapat menyediakan:

```text
[Logo DAYAMA]
[Aplikasi ▾]
[Konteks ▾]
[Notification]
[Account]
```

## Aplikasi

```text
Portal
Dashboard
PSB
Data Center
Library
Website Lembaga
```

## Context

Contoh operator:

```text
Yayasan
MTs
MA
```

Contoh wali:

context dapat lebih personal:

```text
Ahmad - MTs
Aisyah - MA
```

---

# 42. Prioritas Migrasi dari Repository Sekarang

DAYAMA tidak perlu di-rewrite.

Migrasi dilakukan bertahap.

## Tahap 1 — Stabilkan Model Identity

Prioritas:

```text
User
Person
Account
Membership
Institution
Role
Relationship
```

Pastikan seluruh konsep tersebut terpisah dengan jelas.

---

## Tahap 2 — Core Registry

Pindahkan tanggung jawab Core ke:

```text
Foundation
Institution
Site
Domain
Branding
Feature
Application
```

Domain lembaga mulai disimpan di database + Redis.

---

## Tahap 3 — Account

Pisahkan Account sebagai aplikasi/runtime sendiri.

Implementasikan:

```text
OIDC/OAuth2
SSO
session management
2FA
```

Seluruh aplikasi mulai menggunakan Account yang sama.

---

## Tahap 4 — Dashboard Context

Implementasikan:

```text
Membership multi-institution
Context Switcher
Role per institution
Permission per context
```

Ini menyelesaikan kasus operator dua lembaga.

---

## Tahap 5 — Data Center

Bangun Data Center sebagai:

```text
Person Index
Cross Institution Search
Relationship
Foundation Aggregation
```

Jangan jadikan Data Center transaksi utama seluruh lembaga.

---

## Tahap 6 — Distributed Institution Data

Mulai memisahkan database lembaga jika diperlukan.

Tidak harus sekaligus.

Misalnya mulai dari:

```text
MTs
MA
```

kemudian lembaga lain.

---

## Tahap 7 — Event Synchronization

Tambahkan:

```text
queue
events
sync log
retry
dead letter
```

untuk sinkronisasi lembaga → Data Center/Yayasan.

---

## Tahap 8 — Portal

Bangun:

```text
portal.dayama.id
```

dengan model unified personal experience.

Portal menggabungkan:

```text
Santri
Wali
Guru
Pegawai
Alumni
```

berdasarkan relationship, bukan berdasarkan satu role.

---

## Tahap 9 — PSB

Bangun:

```text
psb.dayama.id
```

sebagai centralized admission system dengan context lembaga.

---

## Tahap 10 — Runtime Isolation

Setelah logical boundary stabil:

```text
Core
Account
Dashboard
Portal
Data Center
PSB
Blog
```

mulai dijalankan secara independen.

---

# 43. Hal yang Tidak Perlu Dilakukan Sekarang

Untuk menghindari kompleksitas berlebihan, jangan langsung:

```text
memecah setiap module menjadi microservice
membuat database berbeda untuk setiap tabel
membuat domain berdasarkan role
membuat login terpisah untuk setiap aplikasi
membuat gateway sebagai mandatory dependency
memindahkan seluruh data ke Yayasan
```

---

# 44. Keputusan Arsitektur Utama

Setelah seluruh pembahasan, arah DAYAMA dapat diringkas menjadi:

### Repository

```text
Monorepo
```

### Deployment

```text
Multi-app / multi-runtime
```

### Authentication

```text
Central Account / Identity Provider
```

### User

```text
1 Account
1 Person
banyak relationship
```

### Institution

```text
many-to-many membership
```

### Dashboard

```text
workspace operasional staf
```

### Portal

```text
workspace personal lintas peran/lembaga
```

### Data Center

```text
global index + aggregation
```

### Lembaga

```text
source of truth data operasional
```

### Core

```text
registry + configuration + routing metadata
```

### Site Lembaga

```text
dynamic domain + branding
```

### PSB

```text
centralized admission application
```

### API

```text
external gateway / facade
```

### Internal Communication

```text
direct service-to-service private network
```

### Synchronization

```text
event-driven
```

### Failure Model

```text
satu aplikasi gagal
≠
seluruh DAYAMA gagal
```

---

# 45. Bentuk Akhir Ekosistem DAYAMA

```text
                         account.dayama.id
                              ACCOUNT
                                 │
                         Single Identity
                                 │
        ┌────────────────────────┼────────────────────────┐
        │                        │                        │
        ▼                        ▼                        ▼

dashboard.dayama.id      portal.dayama.id         psb.dayama.id
     STAFF                    PERSONAL                ADMISSION
        │                        │                        │
        │                        │                        │
        └─────────────┬──────────┴───────────┬────────────┘
                      │                      │
                      ▼                      ▼

                Data Center            Business Modules
                     │                Academic / HR /
                     │                Finance / Library
                     │
              Foundation Index
                     │
        ┌────────────┼─────────────┐
        │            │             │
        ▼            ▼             ▼
      MTs DB        MA DB         MI DB
        │            │             │
        ▼            ▼             ▼
 mts.dayama.id  ma.dayama.id  mi.dayama.id
```

Sedangkan:

```text
api.dayama.id
```

menjadi pintu integrasi eksternal ke ekosistem tersebut.

---

# 46. Filosofi Akhir DAYAMA

DAYAMA sebaiknya tidak dianggap sebagai satu aplikasi sekolah.

DAYAMA adalah:

> **platform digital yayasan yang menyediakan identitas tunggal, layanan bersama, aplikasi terpisah, data lembaga yang tetap otonom, serta akses lintas lembaga berdasarkan hubungan nyata pengguna.**

Yayasan memiliki pandangan menyeluruh.

Lembaga tetap memiliki kendali atas operasionalnya.

Pengguna hanya membutuhkan satu akun.

Operator dapat bekerja di beberapa lembaga.

Wali dapat melihat beberapa anak dari beberapa lembaga.

Guru dapat menjadi pegawai sekaligus wali.

Santri dapat menggunakan portal pusat sambil tetap mendapatkan layanan lengkap dari lembaganya.

Dan kegagalan satu aplikasi tidak harus membuat seluruh ekosistem DAYAMA berhenti.
