# Arsitektur Dayama — Modular Monolith

```
Dayama
├── Core
├── Academic
├── HR
├── CRM
├── CMS
├── Landing
├── Finance
├── Library
├── Inventory
├── AI
└── System
```

---

## Prinsip Arsitektur

1. **Modular Monolith** — Kode dipisah per domain dalam `app/Modules/{Domain}/`, satu database, satu deployment.
2. **Domain Prefix** — Semua tabel menggunakan prefix domain (`core_`, `academic_`, `hr_`, `crm_`, `cms_`, `landing_`, `finance_`, `library_`, `inventory_`, `ai_`, `system_`).
3. **Shared Masters di Core** — Master data lintas domain (`religions`, `genders`, `education_levels`, `skills`, dll) ditempatkan di Core.
4. **Person-Centric** — `persons` adalah identitas pusat. `users` hanyalah akses login yang melekat pada Person.
5. **UUID Primary Key** — Semua tabel menggunakan UUID, bukan auto-increment.
6. **Spatie Permission** — RBAC dengan tabel `core_roles`, `core_permissions`, `core_role_user`. Institution-scoped via `role_user.institution_id`.

---

---

# Struktur Direktori

```
app/Modules/
├── Core/                         # Fondasi sistem — dipakai semua modul
│   └── Models/
│       ├── Person.php
│       ├── User.php
│       ├── Role.php
│       ├── Permission.php
│       ├── PermissionGroup.php
│       ├── RoleUser.php
│       ├── Institution.php
│       ├── InstitutionType.php
│       ├── InstitutionAddress.php
│       ├── InstitutionContact.php
│       ├── InstitutionLegality.php
│       ├── Contact.php
│       ├── ContactType.php
│       ├── Address.php
│       ├── AddressType.php
│       ├── Skill.php
│       ├── Language.php
│       ├── Profession.php
│       ├── Certificate.php
│       ├── Religion.php
│       ├── Gender.php
│       ├── MaritalStatus.php
│       ├── EducationLevel.php
│       ├── RelationshipType.php
│       ├── PersonEducation.php
│       ├── PersonSkill.php
│       ├── PersonLanguage.php
│       ├── PersonProfession.php
│       ├── Media.php
│       ├── MediaFolder.php
│       ├── MediaItem.php
│       ├── UserProfile.php
│       ├── UserEmail.php
│       ├── ConnectedAccount.php
│       └── LoginHistory.php
│
├── Academic/                     # Proses belajar-mengajar
│   └── Models/
│       ├── Student.php
│       ├── StudentStatus.php
│       ├── StudentEnrollment.php
│       ├── Semester.php
│       ├── AClass.php
│       ├── AcademicYear.php
│       ├── Subject.php
│       ├── SubjectGroup.php
│       ├── Grade.php
│       ├── Attendance.php
│       ├── Graduation.php
│       ├── Classroom.php
│       └── TeachingAssignment.php
│
├── HR/                           # Kepegawaian & jabatan
│   └── Models/
│       ├── Employee.php
│       ├── Position.php
│       ├── EmploymentStatus.php
│       ├── EmploymentHistory.php
│       ├── EmployeeProfile.php
│       ├── Department.php
│       ├── Division.php
│       ├── LeaveRequest.php
│       └── Attendance.php
│
├── CRM/                          # Hubungan dengan pihak luar
│   └── Models/
│       ├── Guardian.php
│       ├── Donor.php
│       ├── Partner.php
│       └── Subscriber.php
│
├── CMS/                          # Manajemen konten publikasi
│   └── Models/
│       ├── Post.php
│       ├── Category.php
│       ├── Tag.php
│       ├── Comment.php
│       ├── CommentReaction.php
│       ├── Reaction.php
│       ├── PostRevision.php
│       ├── PostView.php
│       ├── Bookmark.php
│       ├── ReadingHistory.php
│       ├── Menu.php
│       ├── MenuItem.php
│       └── Announcement.php
│
├── Landing/                      # Halaman publik statis
│   └── Models/
│       ├── Page.php
│       ├── HeroSection.php
│       ├── Testimonial.php
│       ├── Partner.php
│       ├── Gallery.php
│       ├── Faq.php
│       ├── Cta.php
│       └── StatGroup.php
│
├── Finance/                      # Pembayaran, invoice, donasi
│   └── Models/
│       ├── PaymentType.php
│       ├── Payment.php
│       ├── Invoice.php
│       ├── Transaction.php
│       └── Donation.php
│
├── Library/                      # Manajemen perpustakaan
│   └── Models/
│       ├── Book.php
│       ├── BookCategory.php
│       ├── BookAuthor.php
│       └── Borrowing.php
│
├── Inventory/                    # Aset, barang, ruangan
│   └── Models/
│       ├── Item.php
│       ├── AssetCategory.php
│       ├── Room.php
│       ├── Stock.php
│       └── AssetMovement.php
│
├── AI/                           # AI, agen, knowledge base
│   └── Models/
│       ├── Agent.php
│       ├── Prompt.php
│       ├── Knowledge.php
│       ├── Embedding.php
│       ├── Conversation.php
│       └── Message.php
│
└── System/                       # Log, notifikasi, konfigurasi
    └── Models/
        ├── Setting.php
        ├── SettingGroup.php
        ├── ActivityLog.php
        ├── Backup.php
        ├── Notification.php
        ├── EmailTemplate.php
        └── SystemAsset.php
```

<details>
<summary><strong>Direktori Lainnya</strong></summary>

```
app/
├── Console/Commands/       # CLI commands (PublishScheduledPosts, CmsDoctor, dll)
├── Events/                 # Event classes (CommentPublished, dll)
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/         # REST API controllers
│   │   ├── Dashboard/      # Admin dashboard controllers
│   │   │   ├── Account/
│   │   │   ├── Landing/
│   │   │   └── User/
│   │   ├── Projects/Landing/  # Landing page controllers
│   │   └── Web/            # Frontend web controllers
│   ├── Middleware/          # Custom middleware
│   └── Requests/           # Form request validation
├── Listeners/              # Event listeners
├── Livewire/               # Livewire components
├── Mail/                   # Mailables
├── Modules/                # Domain modules (di atas)
├── Notifications/          # Notification classes
├── Observers/              # Model observers
├── Policies/               # Authorization policies
├── Providers/              # Service providers
├── Services/               # Business logic services
├── Support/                # Helpers, utilities
└── View/Components/        # Blade components
```

```
config/
├── permission.php          # Spatie permission config (table names: core_*)
├── projects.php            # Multi-domain project routing
├── media-library.php       # Spatie media library config
├── activitylog.php         # Spatie activity log config
├── authorization.php       # Authorization pipeline
└── ownership.php           # Model ownership mapping

routes/
├── dashboard.php           # Admin dashboard routes
├── web.php                 # Frontend web routes
├── api.php                 # REST API routes
├── projects/               # Project-specific routes
└── ...

database/
├── migrations/             # All migrations (by domain groups)
├── seeders/                # Database seeders
└── factories/              # Model factories
```
</details>

---

---

# Struktur Database

## Core (`core_*`) — Fondasi Sistem

Tabel yang dipakai oleh hampir semua modul.

### Master Data

```sql
CREATE TABLE core_religions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
);

CREATE TABLE core_genders (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_marital_statuses (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_education_levels (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    level INT NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_relationship_types (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    opposite_id CHAR(36) NULL,
    description TEXT NULL,
    is_family BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (opposite_id) REFERENCES core_relationship_types(id)
);

CREATE TABLE core_contact_types (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    icon VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_address_types (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_skills (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(100) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_languages (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    native_name VARCHAR(255) NULL,
    code VARCHAR(10) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_professions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

### Identitas & Akses

```sql
CREATE TABLE core_persons (
    id CHAR(36) PRIMARY KEY,
    nik VARCHAR(50) NULL,
    passport VARCHAR(50) NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    gelar_depan VARCHAR(100) NULL,
    gelar_belakang VARCHAR(100) NULL,
    gender VARCHAR(20) NULL,
    tempat_lahir VARCHAR(255) NULL,
    tanggal_lahir DATE NULL,
    agama VARCHAR(50) NULL,
    status_hidup BOOLEAN DEFAULT TRUE,
    photo VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_nik (nik),
    INDEX idx_nama (nama_lengkap)
);

CREATE TABLE core_users (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    username VARCHAR(255) NULL UNIQUE,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    preferences JSON NULL,
    two_factor_secret TEXT NULL,
    two_factor_recovery_codes TEXT NULL,
    two_factor_confirmed_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    status VARCHAR(50) DEFAULT 'active',
    is_primary_super_admin BOOLEAN DEFAULT FALSE,
    is_protected BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(100) NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    INDEX idx_email (email),
    INDEX idx_username (username)
);

CREATE TABLE core_user_profiles (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    full_name VARCHAR(255) NULL,
    nickname VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    banner VARCHAR(255) NULL,
    biography TEXT NULL,
    website VARCHAR(255) NULL,
    social_links JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES core_users(id) ON DELETE CASCADE
);

CREATE TABLE core_user_emails (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES core_users(id) ON DELETE CASCADE
);

CREATE TABLE core_connected_accounts (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    provider_id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    token TEXT NULL,
    refresh_token TEXT NULL,
    expires_at INT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES core_users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_provider (provider, provider_id)
);

CREATE TABLE core_login_histories (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    device_type VARCHAR(50) NULL,
    browser VARCHAR(255) NULL,
    platform VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    country VARCHAR(255) NULL,
    is_successful BOOLEAN DEFAULT TRUE,
    login_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES core_users(id) ON DELETE CASCADE,
    INDEX idx_login_user (user_id, login_at)
);
```

### RBAC

```sql
CREATE TABLE core_roles (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL, -- Diisi dengan slug (machine-readable)
    guard_name VARCHAR(255) DEFAULT 'web',
    display_name VARCHAR(255) NULL, -- Diisi dengan nama yang ramah pengguna
    description TEXT NULL,
    slug VARCHAR(255) NULL,
    scope VARCHAR(50) NULL, -- NULL=global, 'yayasan'=semua institusi, 'lembaga'=spesifik via pivot
    color VARCHAR(50) NULL,
    icon VARCHAR(100) NULL,
    is_system BOOLEAN DEFAULT FALSE,
    status VARCHAR(50) DEFAULT 'active',
    sort_order INT DEFAULT 0,
    rank INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_role_name (name)
);

CREATE TABLE core_permissions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) DEFAULT 'web',
    module VARCHAR(100) NULL,
    action VARCHAR(100) NULL,
    scope VARCHAR(100) NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_perm_module (module)
);

CREATE TABLE core_role_user (
    id CHAR(36) PRIMARY KEY,
    role_id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    institution_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES core_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES core_users(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_user (role_id, user_id, institution_id)
);

CREATE TABLE core_role_has_permissions (
    permission_id CHAR(36) NOT NULL,
    role_id CHAR(36) NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES core_permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES core_roles(id) ON DELETE CASCADE
);

CREATE TABLE core_model_has_roles (
    role_id CHAR(36) NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id CHAR(36) NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES core_roles(id) ON DELETE CASCADE,
    INDEX idx_model (model_type, model_id)
);

CREATE TABLE core_model_has_permissions (
    permission_id CHAR(36) NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id CHAR(36) NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES core_permissions(id) ON DELETE CASCADE,
    INDEX idx_model (model_type, model_id)
);
```

### Institution

```sql
CREATE TABLE core_institutions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    institution_type_id CHAR(36) NULL,
    npsn VARCHAR(20) NULL,
    nsm VARCHAR(20) NULL,
    nss VARCHAR(30) NULL,
    website VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    logo VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    status VARCHAR(50) DEFAULT 'active',
    founded_date DATE NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (institution_type_id) REFERENCES core_institution_types(id),
    INDEX idx_slug (slug)
);

CREATE TABLE core_institution_types (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category VARCHAR(100) NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE core_institution_addresses (
    id CHAR(36) PRIMARY KEY,
    institution_id CHAR(36) NOT NULL,
    address_type VARCHAR(50) DEFAULT 'utama',
    street TEXT NULL,
    village VARCHAR(255) NULL,
    district VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    province VARCHAR(255) NULL,
    postal_code VARCHAR(20) NULL,
    country VARCHAR(255) DEFAULT 'Indonesia',
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE
);

CREATE TABLE core_institution_contacts (
    id CHAR(36) PRIMARY KEY,
    institution_id CHAR(36) NOT NULL,
    contact_type_id CHAR(36) NULL,
    label VARCHAR(255) NULL,
    value VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_type_id) REFERENCES core_contact_types(id)
);

CREATE TABLE core_institution_legalities (
    id CHAR(36) PRIMARY KEY,
    institution_id CHAR(36) NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    document_number VARCHAR(100) NULL,
    document_name VARCHAR(255) NULL,
    issued_by VARCHAR(255) NULL,
    issued_date DATE NULL,
    expiry_date DATE NULL,
    file VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES core_institutions(id) ON DELETE CASCADE
);
```

### Kontak & Alamat

```sql
CREATE TABLE core_contacts (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    contact_type_id CHAR(36) NULL,
    label VARCHAR(255) NULL,
    value VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_type_id) REFERENCES core_contact_types(id)
);

CREATE TABLE core_addresses (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    address_type_id CHAR(36) NULL,
    label VARCHAR(255) NULL,
    street TEXT NULL,
    village VARCHAR(255) NULL,
    district VARCHAR(255) NULL,
    city VARCHAR(255) NULL,
    province VARCHAR(255) NULL,
    postal_code VARCHAR(20) NULL,
    country VARCHAR(255) DEFAULT 'Indonesia',
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (address_type_id) REFERENCES core_address_types(id)
);
```

### Person Attributes (Relasi Many-to-Many dengan Core Masters)

```sql
CREATE TABLE core_person_skills (
    person_id CHAR(36) NOT NULL,
    skill_id CHAR(36) NOT NULL,
    level VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (person_id, skill_id),
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES core_skills(id) ON DELETE CASCADE
);

CREATE TABLE core_person_languages (
    person_id CHAR(36) NOT NULL,
    language_id CHAR(36) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (person_id, language_id),
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES core_languages(id) ON DELETE CASCADE
);

CREATE TABLE core_person_professions (
    person_id CHAR(36) NOT NULL,
    profession_id CHAR(36) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    mulai DATE NULL,
    selesai DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (person_id, profession_id),
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (profession_id) REFERENCES core_professions(id) ON DELETE CASCADE
);

CREATE TABLE core_person_educations (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    education_level_id CHAR(36) NULL,
    institution_name VARCHAR(255) NULL,
    major VARCHAR(255) NULL,
    graduation_year YEAR NULL,
    is_completed BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (education_level_id) REFERENCES core_education_levels(id)
);

CREATE TABLE core_certificates (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    issuer VARCHAR(255) NULL,
    certificate_number VARCHAR(100) NULL,
    issue_date DATE NULL,
    expiry_date DATE NULL,
    file VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE
);
```

### Media

```sql
CREATE TABLE core_media (
    id CHAR(36) PRIMARY KEY,
    model_type VARCHAR(255) NOT NULL,
    model_id CHAR(36) NOT NULL,
    uuid CHAR(36) NULL,
    collection_name VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(255) NULL,
    disk VARCHAR(255) NOT NULL,
    conversions_disk VARCHAR(255) NULL,
    size BIGINT UNSIGNED NOT NULL,
    manipulations JSON NULL,
    custom_properties JSON NULL,
    generated_conversions JSON NULL,
    responsive_images JSON NULL,
    order_column INT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_model (model_type, model_id)
);

CREATE TABLE core_media_folders (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (parent_id) REFERENCES core_media_folders(id) ON DELETE CASCADE
);
```

---

## Academic (`academic_*`) — Proses Belajar-Mengajar

```sql
CREATE TABLE academic_students (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    nis VARCHAR(50) NULL,
    nisn VARCHAR(50) NULL,
    institution_id CHAR(36) NULL,
    entry_year YEAR NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    INDEX idx_nisn (nisn)
);

CREATE TABLE academic_student_statuses (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    color VARCHAR(50) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE academic_student_enrollments (
    id CHAR(36) PRIMARY KEY,
    student_id CHAR(36) NOT NULL,
    academic_year_id CHAR(36) NOT NULL,
    semester_id CHAR(36) NULL,
    class_id CHAR(36) NULL,
    status_id CHAR(36) NULL,
    entry_date DATE NOT NULL,
    exit_date DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES academic_students(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_academic_years(id),
    FOREIGN KEY (semester_id) REFERENCES academic_semesters(id),
    FOREIGN KEY (class_id) REFERENCES academic_classes(id),
    FOREIGN KEY (status_id) REFERENCES academic_student_statuses(id),
    INDEX idx_enrollment_student (student_id),
    INDEX idx_enrollment_year (academic_year_id),
    INDEX idx_enrollment_class (class_id)
);

CREATE TABLE academic_academic_years (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    year_start INT NOT NULL,
    year_end INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE academic_semesters (
    id CHAR(36) PRIMARY KEY,
    academic_year_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_academic_years(id) ON DELETE CASCADE,
    INDEX idx_semester_year (academic_year_id)
);

CREATE TABLE academic_classes (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    academic_year_id CHAR(36) NOT NULL,
    education_level_id CHAR(36) NULL,
    homeroom_teacher_id CHAR(36) NULL,
    capacity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_academic_years(id),
    FOREIGN KEY (education_level_id) REFERENCES core_education_levels(id),
    INDEX idx_class_year (academic_year_id)
);

CREATE TABLE academic_subjects (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    subject_group_id CHAR(36) NULL,
    code VARCHAR(50) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (subject_group_id) REFERENCES academic_subject_groups(id)
);

CREATE TABLE academic_subject_groups (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE academic_classrooms (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    capacity INT DEFAULT 0,
    location VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE academic_classroom_student (
    classroom_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    academic_year_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (classroom_id, student_id),
    FOREIGN KEY (classroom_id) REFERENCES academic_classrooms(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES academic_students(id) ON DELETE CASCADE
);

CREATE TABLE academic_teaching_assignments (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    subject_id CHAR(36) NOT NULL,
    classroom_id CHAR(36) NULL,
    academic_year_id CHAR(36) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES academic_subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES academic_classrooms(id) ON DELETE CASCADE
);

CREATE TABLE academic_grades (
    id CHAR(36) PRIMARY KEY,
    student_enrollment_id CHAR(36) NOT NULL,
    subject_id CHAR(36) NOT NULL,
    semester_id CHAR(36) NULL,
    score DECIMAL(5,2) NULL,
    grade_letter VARCHAR(10) NULL,
    notes TEXT NULL,
    graded_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (student_enrollment_id) REFERENCES academic_student_enrollments(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES academic_subjects(id),
    FOREIGN KEY (semester_id) REFERENCES academic_semesters(id),
    FOREIGN KEY (graded_by) REFERENCES core_users(id),
    INDEX idx_grade_enrollment (student_enrollment_id),
    INDEX idx_grade_subject (subject_id)
);

CREATE TABLE academic_attendance (
    id CHAR(36) PRIMARY KEY,
    student_enrollment_id CHAR(36) NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    recorded_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (student_enrollment_id) REFERENCES academic_student_enrollments(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES core_users(id),
    INDEX idx_attendance_enrollment (student_enrollment_id),
    INDEX idx_attendance_date (date)
);

CREATE TABLE academic_graduations (
    id CHAR(36) PRIMARY KEY,
    student_enrollment_id CHAR(36) UNIQUE NOT NULL,
    graduation_date DATE NOT NULL,
    certificate_number VARCHAR(100) NULL,
    final_score DECIMAL(5,2) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (student_enrollment_id) REFERENCES academic_student_enrollments(id) ON DELETE CASCADE
);
```

---

## HR (`hr_*`) — Kepegawaian & Jabatan

```sql
CREATE TABLE hr_employees (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    employee_number VARCHAR(100) UNIQUE NOT NULL,
    employment_status_id CHAR(36) NULL,
    position_id CHAR(36) NULL,
    department_id CHAR(36) NULL,
    division_id CHAR(36) NULL,
    hire_date DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (employment_status_id) REFERENCES hr_employment_statuses(id),
    FOREIGN KEY (position_id) REFERENCES hr_positions(id),
    FOREIGN KEY (department_id) REFERENCES hr_departments(id),
    FOREIGN KEY (division_id) REFERENCES hr_divisions(id)
);

CREATE TABLE hr_positions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    category VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE hr_departments (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    code VARCHAR(50) NULL,
    description TEXT NULL,
    head_employee_id CHAR(36) NULL,
    parent_id CHAR(36) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (head_employee_id) REFERENCES hr_employees(id),
    FOREIGN KEY (parent_id) REFERENCES hr_departments(id),
    INDEX idx_dept_parent (parent_id)
);

CREATE TABLE hr_divisions (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    code VARCHAR(50) NULL,
    department_id CHAR(36) NOT NULL,
    description TEXT NULL,
    head_employee_id CHAR(36) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (department_id) REFERENCES hr_departments(id) ON DELETE CASCADE,
    FOREIGN KEY (head_employee_id) REFERENCES hr_employees(id),
    INDEX idx_div_dept (department_id)
);

CREATE TABLE hr_employment_statuses (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE hr_employment_histories (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    company VARCHAR(255) NOT NULL,
    position_held VARCHAR(255) NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE
);

CREATE TABLE hr_employee_profiles (
    id CHAR(36) PRIMARY KEY,
    employee_id CHAR(36) NOT NULL,
    -- (extended employee data)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE
);

CREATE TABLE hr_employee_positions (
    person_id CHAR(36) NOT NULL,
    position_id CHAR(36) NOT NULL,
    institution_id CHAR(36) NULL,
    nomor_induk VARCHAR(100) NULL,
    tanggal_mulai DATE NULL,
    tanggal_selesai DATE NULL,
    status VARCHAR(50) DEFAULT 'aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (person_id, position_id),
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES hr_positions(id) ON DELETE CASCADE
);

CREATE TABLE hr_leave_requests (
    id CHAR(36) PRIMARY KEY,
    employee_id CHAR(36) NOT NULL,
    leave_type VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    approved_by CHAR(36) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES hr_employees(id),
    INDEX idx_leave_employee (employee_id),
    INDEX idx_leave_status (status)
);

CREATE TABLE hr_attendances (
    id CHAR(36) PRIMARY KEY,
    employee_id CHAR(36) NOT NULL,
    date DATE NOT NULL,
    check_in DATETIME NULL,
    check_out DATETIME NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    recorded_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES core_users(id),
    INDEX idx_attendance_employee (employee_id),
    INDEX idx_attendance_date (date)
);
```

---

## CRM (`crm_*`) — Hubungan Pihak Luar

```sql
CREATE TABLE crm_guardians (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    student_id CHAR(36) NULL,
    relationship_type_id CHAR(36) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_emergency_contact BOOLEAN DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES academic_students(id) ON DELETE CASCADE,
    FOREIGN KEY (relationship_type_id) REFERENCES core_relationship_types(id),
    INDEX idx_guardian_person (person_id),
    INDEX idx_guardian_student (student_id)
);

CREATE TABLE crm_donors (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    donor_type VARCHAR(100) NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    INDEX idx_donor_person (person_id)
);

CREATE TABLE crm_partners (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    type VARCHAR(100) NULL,
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    website VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_partner_slug (slug)
);

CREATE TABLE crm_subscribers (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP NULL,
    unsubscribed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_subscriber_email (email)
);

CREATE TABLE crm_family_relations (
    id CHAR(36) PRIMARY KEY,
    person_id CHAR(36) NOT NULL,
    related_person_id CHAR(36) NOT NULL,
    relationship_type_id CHAR(36) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (related_person_id) REFERENCES core_persons(id) ON DELETE CASCADE,
    FOREIGN KEY (relationship_type_id) REFERENCES core_relationship_types(id),
    UNIQUE KEY unique_relation (person_id, related_person_id, relationship_type_id)
);
```

---

## CMS (`cms_*`) — Manajemen Konten

```sql
CREATE TABLE cms_posts (
    id CHAR(36) PRIMARY KEY,
    author_id CHAR(36) NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NULL,
    excerpt TEXT NULL,
    featured_image VARCHAR(255) NULL,
    status VARCHAR(50) DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (author_id) REFERENCES core_users(id) ON DELETE SET NULL,
    INDEX idx_post_status (status),
    INDEX idx_post_published (published_at),
    FULLTEXT idx_post_search (title, content)
);

CREATE TABLE cms_categories (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    parent_id CHAR(36) NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (parent_id) REFERENCES cms_categories(id) ON DELETE SET NULL
);

CREATE TABLE cms_tags (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE cms_comments (
    id CHAR(36) PRIMARY KEY,
    post_id CHAR(36) NOT NULL,
    author_id CHAR(36) NULL,
    parent_id CHAR(36) NULL,
    body TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES core_users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES cms_comments(id) ON DELETE CASCADE,
    INDEX idx_comment_post (post_id)
);

CREATE TABLE cms_category_post (
    post_id CHAR(36) NOT NULL,
    category_id CHAR(36) NOT NULL,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES cms_categories(id) ON DELETE CASCADE
);

CREATE TABLE cms_post_tag (
    post_id CHAR(36) NOT NULL,
    tag_id CHAR(36) NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES cms_tags(id) ON DELETE CASCADE
);

CREATE TABLE cms_post_revisions (
    id CHAR(36) PRIMARY KEY,
    post_id CHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NULL,
    slug VARCHAR(255) NULL,
    user_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    INDEX idx_revision_post (post_id)
);

CREATE TABLE cms_post_views (
    id CHAR(36) PRIMARY KEY,
    post_id CHAR(36) NOT NULL,
    identity_key VARCHAR(255) NOT NULL,
    user_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    INDEX idx_view_post (post_id),
    INDEX idx_view_identity (identity_key)
);

CREATE TABLE cms_reactions (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    label VARCHAR(100) NULL,
    icon VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE cms_comment_reactions (
    id CHAR(36) PRIMARY KEY,
    comment_id CHAR(36) NOT NULL,
    reaction_id CHAR(36) NULL,
    identity_key VARCHAR(255) NOT NULL,
    user_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (comment_id) REFERENCES cms_comments(id) ON DELETE CASCADE,
    FOREIGN KEY (reaction_id) REFERENCES cms_reactions(id) ON DELETE SET NULL,
    UNIQUE KEY unique_comment_reaction (comment_id, identity_key)
);

CREATE TABLE cms_bookmarks (
    id CHAR(36) PRIMARY KEY,
    post_id CHAR(36) NOT NULL,
    identity_key VARCHAR(255) NOT NULL,
    user_id CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_bookmark (post_id, identity_key),
    INDEX idx_bookmark_identity (identity_key)
);

CREATE TABLE cms_reading_histories (
    id CHAR(36) PRIMARY KEY,
    post_id CHAR(36) NOT NULL,
    identity_key VARCHAR(255) NOT NULL,
    user_id CHAR(36) NULL,
    first_read_at TIMESTAMP NULL,
    last_read_at TIMESTAMP NULL,
    read_count INT DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (post_id) REFERENCES cms_posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_history (post_id, identity_key),
    INDEX idx_history_identity (identity_key, last_read_at)
);

CREATE TABLE cms_menus (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    location VARCHAR(100) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_menu_location (location)
);

CREATE TABLE cms_menu_items (
    id CHAR(36) PRIMARY KEY,
    menu_id CHAR(36) NOT NULL,
    parent_id CHAR(36) NULL,
    label VARCHAR(255) NOT NULL,
    url VARCHAR(255) NULL,
    page_id CHAR(36) NULL,
    target VARCHAR(20) DEFAULT '_self',
    icon VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (menu_id) REFERENCES cms_menus(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES cms_menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (page_id) REFERENCES cms_posts(id) ON DELETE SET NULL,
    INDEX idx_menu_item_menu (menu_id),
    INDEX idx_menu_item_parent (parent_id)
);

CREATE TABLE cms_announcements (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    published_at TIMESTAMP NULL,
    author_id CHAR(36) NULL,
    is_published BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (author_id) REFERENCES core_users(id) ON DELETE SET NULL,
    INDEX idx_announcement_published (published_at),
    INDEX idx_announcement_status (is_published)
);
```

---

## Landing (`landing_*`) — Halaman Publik Statis

```sql
CREATE TABLE landing_pages (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NULL,
    subtitle TEXT NULL,
    content TEXT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE landing_hero_sections (
    id CHAR(36) PRIMARY KEY,
    page_id CHAR(36) NULL,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NULL,
    description TEXT NULL,
    background_image VARCHAR(255) NULL,
    background_color VARCHAR(50) NULL,
    cta_text VARCHAR(255) NULL,
    cta_url VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);

CREATE TABLE landing_testimonials (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    content TEXT NOT NULL,
    rating INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE landing_partners (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE landing_galleries (
    id CHAR(36) PRIMARY KEY,
    page_id CHAR(36) NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);

CREATE TABLE landing_faqs (
    id CHAR(36) PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE landing_ctas (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NULL,
    description TEXT NULL,
    button_text VARCHAR(255) NULL,
    button_url VARCHAR(255) NULL,
    background_image VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE landing_stat_groups (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NULL,
    subtitle VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

---

## Finance (`finance_*`) — Pembayaran & Keuangan

```sql
CREATE TABLE finance_payment_types (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE finance_invoices (
    id CHAR(36) PRIMARY KEY,
    invoice_number VARCHAR(255) UNIQUE NOT NULL,
    invoiceable_type VARCHAR(255) NOT NULL,
    invoiceable_id CHAR(36) NOT NULL,
    student_id CHAR(36) NULL,
    amount DECIMAL(15,2) NOT NULL,
    due_date DATE NULL,
    status VARCHAR(50) DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES academic_students(id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_invoice_status (status),
    INDEX idx_invoice_model (invoiceable_type, invoiceable_id)
);

CREATE TABLE finance_payments (
    id CHAR(36) PRIMARY KEY,
    payment_type_id CHAR(36) NOT NULL,
    payable_type VARCHAR(255) NOT NULL,
    payable_id CHAR(36) NOT NULL,
    invoice_id CHAR(36) NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    payment_method VARCHAR(100) NULL,
    reference_number VARCHAR(255) NULL,
    notes TEXT NULL,
    paid_by CHAR(36) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (payment_type_id) REFERENCES finance_payment_types(id),
    FOREIGN KEY (invoice_id) REFERENCES finance_invoices(id),
    FOREIGN KEY (paid_by) REFERENCES core_users(id),
    FOREIGN KEY (verified_by) REFERENCES core_users(id),
    INDEX idx_payment_type (payment_type_id),
    INDEX idx_payment_invoice (invoice_id),
    INDEX idx_payment_model (payable_type, payable_id)
);

CREATE TABLE finance_transactions (
    id CHAR(36) PRIMARY KEY,
    from_account VARCHAR(255) NULL,
    to_account VARCHAR(255) NULL,
    amount DECIMAL(15,2) NOT NULL,
    type VARCHAR(50) NOT NULL,
    category VARCHAR(100) NULL,
    description TEXT NULL,
    reference_id CHAR(36) NULL,
    reference_type VARCHAR(255) NULL,
    transaction_date DATETIME NOT NULL,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES core_users(id),
    INDEX idx_transaction_type (type),
    INDEX idx_transaction_date (transaction_date)
);

CREATE TABLE finance_donations (
    id CHAR(36) PRIMARY KEY,
    donor_id CHAR(36) NULL,
    amount DECIMAL(15,2) NOT NULL,
    donation_date DATE NOT NULL,
    payment_type_id CHAR(36) NULL,
    campaign VARCHAR(255) NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (donor_id) REFERENCES crm_donors(id),
    FOREIGN KEY (payment_type_id) REFERENCES finance_payment_types(id),
    INDEX idx_donation_donor (donor_id),
    INDEX idx_donation_date (donation_date)
);
```

---

## Library (`library_*`) — Perpustakaan

```sql
CREATE TABLE library_book_categories (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    parent_id CHAR(36) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (parent_id) REFERENCES library_book_categories(id) ON DELETE SET NULL
);

CREATE TABLE library_book_authors (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    biography TEXT NULL,
    photo VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE library_books (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    isbn VARCHAR(50) UNIQUE NULL,
    author_id CHAR(36) NULL,
    category_id CHAR(36) NULL,
    publisher VARCHAR(255) NULL,
    published_year INT NULL,
    pages INT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    quantity INT DEFAULT 1,
    available_quantity INT DEFAULT 1,
    location VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (author_id) REFERENCES library_book_authors(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES library_book_categories(id) ON DELETE SET NULL,
    INDEX idx_book_author (author_id),
    INDEX idx_book_category (category_id),
    INDEX idx_book_isbn (isbn)
);

CREATE TABLE library_borrowings (
    id CHAR(36) PRIMARY KEY,
    book_id CHAR(36) NOT NULL,
    borrower_type VARCHAR(255) NOT NULL,
    borrower_id CHAR(36) NOT NULL,
    borrowed_at DATETIME NOT NULL,
    due_at DATETIME NOT NULL,
    returned_at DATETIME NULL,
    status VARCHAR(50) DEFAULT 'borrowed',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE,
    INDEX idx_borrowing_book (book_id),
    INDEX idx_borrowing_borrower (borrower_type, borrower_id),
    INDEX idx_borrowing_status (status)
);
```

---

## Inventory (`inventory_*`) — Aset & Inventaris

```sql
CREATE TABLE inventory_asset_categories (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    parent_id CHAR(36) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (parent_id) REFERENCES inventory_asset_categories(id) ON DELETE SET NULL
);

CREATE TABLE inventory_rooms (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    location TEXT NULL,
    capacity INT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE inventory_items (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE NULL,
    category_id CHAR(36) NULL,
    room_id CHAR(36) NULL,
    description TEXT NULL,
    quantity INT DEFAULT 0,
    minimum_stock INT DEFAULT 0,
    unit VARCHAR(50) NULL,
    condition VARCHAR(50) DEFAULT 'baik',
    purchase_date DATE NULL,
    purchase_price DECIMAL(15,2) NULL,
    supplier VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES inventory_asset_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES inventory_rooms(id) ON DELETE SET NULL,
    INDEX idx_item_category (category_id),
    INDEX idx_item_room (room_id),
    INDEX idx_item_sku (sku)
);

CREATE TABLE inventory_stocks (
    id CHAR(36) PRIMARY KEY,
    item_id CHAR(36) NOT NULL,
    type VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(255) NULL,
    reference_id CHAR(36) NULL,
    notes TEXT NULL,
    recorded_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES core_users(id),
    INDEX idx_stock_item (item_id),
    INDEX idx_stock_type (type)
);

CREATE TABLE inventory_asset_movements (
    id CHAR(36) PRIMARY KEY,
    item_id CHAR(36) NOT NULL,
    from_room_id CHAR(36) NULL,
    to_room_id CHAR(36) NULL,
    quantity INT NOT NULL,
    movement_date DATETIME NOT NULL,
    reason TEXT NULL,
    notes TEXT NULL,
    recorded_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    FOREIGN KEY (from_room_id) REFERENCES inventory_rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (to_room_id) REFERENCES inventory_rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES core_users(id),
    INDEX idx_movement_item (item_id),
    INDEX idx_movement_from (from_room_id),
    INDEX idx_movement_to (to_room_id)
);
```

---

## AI (`ai_*`) — Kecerdasan Buatan

```sql
CREATE TABLE ai_agents (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    model VARCHAR(255) NULL,
    system_prompt TEXT NULL,
    temperature DECIMAL(3,2) DEFAULT 0.70,
    max_tokens INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE ai_prompts (
    id CHAR(36) PRIMARY KEY,
    agent_id CHAR(36) NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100) NULL,
    variables JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (agent_id) REFERENCES ai_agents(id) ON DELETE CASCADE,
    INDEX idx_prompt_agent (agent_id),
    INDEX idx_prompt_category (category)
);

CREATE TABLE ai_knowledge (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    source_type VARCHAR(255) NULL,
    source_id CHAR(36) NULL,
    tags JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE ai_embeddings (
    id CHAR(36) PRIMARY KEY,
    embeddable_type VARCHAR(255) NOT NULL,
    embeddable_id CHAR(36) NOT NULL,
    content TEXT NOT NULL,
    embedding JSON NULL,
    model VARCHAR(255) NULL,
    chunk_index INT DEFAULT 0,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_embeddable (embeddable_type, embeddable_id)
);

CREATE TABLE ai_conversations (
    id CHAR(36) PRIMARY KEY,
    agent_id CHAR(36) NULL,
    user_id CHAR(36) NULL,
    session_id VARCHAR(255) NULL,
    title VARCHAR(255) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (agent_id) REFERENCES ai_agents(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES core_users(id) ON DELETE SET NULL,
    INDEX idx_conversation_agent (agent_id),
    INDEX idx_conversation_user (user_id),
    INDEX idx_conversation_session (session_id)
);

CREATE TABLE ai_messages (
    id CHAR(36) PRIMARY KEY,
    conversation_id CHAR(36) NOT NULL,
    role VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    tokens_used INT NULL,
    model VARCHAR(255) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (conversation_id) REFERENCES ai_conversations(id) ON DELETE CASCADE,
    INDEX idx_message_conversation (conversation_id)
);
```

---

## System (`system_*`) — Log, Notifikasi, Konfigurasi

```sql
CREATE TABLE system_settings (
    id CHAR(36) PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NULL,
    type VARCHAR(50) DEFAULT 'string',
    group_id CHAR(36) NULL,
    is_locked BOOLEAN DEFAULT FALSE,
    is_env BOOLEAN DEFAULT FALSE,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (group_id) REFERENCES system_setting_groups(id) ON DELETE SET NULL,
    INDEX idx_setting_key (`key`)
);

CREATE TABLE system_setting_groups (
    id CHAR(36) PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(100) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE system_activity_logs (
    id CHAR(36) PRIMARY KEY,
    log_name VARCHAR(255) NULL,
    description TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id CHAR(36) NULL,
    event VARCHAR(255) NULL,
    causer_type VARCHAR(255) NULL,
    causer_id CHAR(36) NULL,
    batch_uuid CHAR(36) NULL,
    properties JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_activity_log_name (log_name),
    INDEX idx_activity_subject (subject_type, subject_id),
    INDEX idx_activity_causer (causer_type, causer_id)
);

CREATE TABLE system_backups (
    id CHAR(36) PRIMARY KEY,
    backupable_type VARCHAR(255) NOT NULL,
    backupable_id CHAR(36) NOT NULL,
    status VARCHAR(50) NOT NULL,
    filename VARCHAR(255) NULL,
    size BIGINT NULL,
    files JSON NULL,
    metadata JSON NULL,
    logs TEXT NULL,
    created_by CHAR(36) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES core_users(id) ON DELETE SET NULL,
    INDEX idx_backup_model (backupable_type, backupable_id)
);

CREATE TABLE system_notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id CHAR(36) NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_notification_notifiable (notifiable_type, notifiable_id)
);

CREATE TABLE system_email_templates (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE system_assets (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    -- managed by Spatie Media Library
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE system_personal_access_tokens (
    id CHAR(36) PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_token_tokenable (tokenable_type, tokenable_id)
);
```

---

## Filosofi Domain

```
Core     → "Apakah modul lain juga membutuhkan tabel ini?"
Academic → "Apakah ini hanya berkaitan dengan proses belajar-mengajar?"
HR       → "Apakah ini hanya berkaitan dengan pegawai & jabatan?"
CRM      → "Apakah ini mengelola hubungan dengan pihak luar?"
CMS      → "Apakah ini mengelola konten publikasi?"
Landing  → "Apakah ini mengelola tampilan halaman publik statis?"
Finance  → "Apakah ini berkaitan dengan pembayaran & keuangan?"
Library  → "Apakah ini berkaitan dengan manajemen perpustakaan?"
Inventory→ "Apakah ini berkaitan dengan aset, barang, & ruangan?"
AI       → "Apakah ini berkaitan dengan AI, agen, & knowledge base?"
System   → "Apakah ini bersifat infrastruktur (log, notifikasi, konfigurasi)?"
```

## Pemisahan Guru

| Domain | Tanggung Jawab |
|--------|----------------|
| **HR** | Data pegawai, jabatan, riwayat pekerjaan, cuti, absensi |
| **Academic** | Aktivitas mengajar (mata pelajaran, kelas, jadwal) |

Seorang guru adalah `hr_employees` dengan `position_id` → "Guru". Beban mengajarnya dikelola di `academic_teaching_assignments`.

---

## Catatan Penting

1. **UUID** → Semua primary key menggunakan UUID (CHAR(36)), bukan auto-increment.
2. **Soft Deletes** → Hampir semua tabel menggunakan `deleted_at` untuk soft delete, kecuali tabel log/transaksional tertentu.
3. **Timestamps** → Semua tabel memiliki `created_at` dan `updated_at`.
4. **Indexes** → Foreign key columns dan kolom yang sering di-query diberi index.
5. **`core_persons` adalah pusat identitas** — `core_users` hanyalah akses login. `user.name` adalah accessor yang membaca `profile.nickname`, fallback ke `profile.full_name`, dan terakhir `person.nama_lengkap`.
