# 🏗️ DOMAIN-TIK COMPREHENSIVE SYSTEM ANALYSIS

**Last Updated:** May 4, 2026  
**Framework:** Laravel 12.x | **Database:** PostgreSQL | **PHP:** 8.2+

---

## 📑 TABLE OF CONTENTS

1. [System Overview](#1-system-overview)
2. [Architecture & Design Patterns](#2-architecture--design-patterns)
3. [Database Schema](#3-database-schema)
4. [Core Features](#4-core-features)
5. [Code Organization](#5-code-organization)
6. [Business Logic Flow](#6-business-logic-flow)
7. [Frontend Architecture](#7-frontend-architecture)
8. [Configuration & Environment](#8-configuration--environment)

---

## 1. SYSTEM OVERVIEW

### 1.1 Project Overview
- **Name:** Domain-TIK (Domain Teknologi Informasi Unila)
- **Purpose:** Service request management system for IT UPT (Universitas Lampung)
- **Scope:** Manage requests for Sub Domain, Hosting, and VPS services
- **Users:** Administrators, Verifiers, Executors, Leaders, and General Users

### 1.2 Key Features
- ✅ Multi-service request forms (Domain, Hosting, VPS)
- ✅ Dual output: Paperless (digital) & Hardcopy (PDF for leadership)
- ✅ Ticket-based request tracking
- ✅ Dual authentication: Local credentials & SSO (akses.unila.ac.id)
- ✅ Complete audit trail and activity logging
- ✅ Role-based access control (RBAC)
- ✅ Admin notification system

### 1.3 Tech Stack
```
Backend:       Laravel 12.x
Database:      PostgreSQL (with schemas)
Frontend:      Blade + Alpine.js + Tailwind CSS
PDF Generation: dompdf
Authentication: Session-based + SSO (JWT)
File Storage:  Local storage
Build Tool:    Vite
```

---

## 2. ARCHITECTURE & DESIGN PATTERNS

### 2.1 MVC Structure

#### Controllers Hierarchy
```
app/Http/Controllers/
├── AuthController.php                 # Local login
├── DashboardController.php            # Role-based dashboard routing
├── SubmissionController.php           # User submission CRUD
├── VerificationController.php         # Verifier workflows
├── ExecutionController.php            # Executor/Task workflows
├── FormGeneratorController.php        # Form generation & PDF export
├── Auth/
│   ├── SSOController.php             # SSO authentication flow
│   └── RegisterController.php        # User self-registration
├── Admin/
│   ├── AdminController.php           # User verification & management
│   ├── NotificationController.php    # Admin notification system
│   ├── AuditLogController.php        # Audit log viewing
│   └── UnitController.php            # Unit management
└── Pimpinan/
    └── PimpinanController.php        # Leadership dashboards
```

#### Key Characteristics
- **Thin Controllers:** Business logic delegated to Services
- **Method Responsibility:** Single, clear purpose per method
- **Dependency Injection:** Services injected in constructors
- **No Business Logic:** Controllers only handle HTTP concerns

### 2.2 Service Layer Pattern

#### Services
```
app/Services/
├── AuthService.php                   # Authentication logic
├── UserService.php                   # User registration & management
├── AdminService.php                  # Admin operations (verification, status)
├── AuditLogService.php               # Login & submission logging
├── NotificationService.php           # User & admin notifications
├── UnitSyncService.php               # Unit data synchronization
└── PimpinanService.php               # Leadership-level operations
```

#### Service Pattern Benefits
- Reusable business logic across controllers
- Clean separation of concerns
- Easy unit testing
- Single source of truth for domain logic

### 2.3 Repository Pattern (Implicit)
- Not explicitly implemented but models act as data access layer
- Models handle direct DB queries and relationships
- Services consume models for business logic

### 2.4 Request Validation Pattern

#### Validation Approach
- Form Request classes not extensively used
- Validation in controllers via `$request->validate()`
- Custom validation rules where needed
- Example: [SubmissionController.php](app/Http/Controllers/SubmissionController.php#L52) has comprehensive rules

#### Validation Categories
- **User Registration:** Email uniqueness, password strength, file uploads
- **Submission Creation:** Required fields, type validation, file uploads
- **Admin Operations:** UUID validation, role assignment

### 2.5 Middleware Architecture

#### Middleware Stack
```
app/Http/Middleware/
├── EnsureUserIsActive.php   # Check a_aktif flag (account activation)
└── RoleMiddleware.php        # Role-based route protection
```

#### Middleware Flow
1. **Authentication:** `auth` middleware (Laravel default)
2. **Activation Check:** `EnsureUserIsActive` - blocks inactive users
3. **Role Verification:** `RoleMiddleware` - role:admin,verifikator,etc.

#### Middleware Logic Details

**EnsureUserIsActive.php**
```php
// Flow:
1. Checks user.a_aktif flag
2. If inactive:
   - Route in whitelist (dashboard, logout) → Allow with flag
   - Other routes → Redirect with error message
3. If active → Allow all access
```

**RoleMiddleware.php**
```php
// Flow:
1. Get user role from user.peran relationship
2. Pimpinan (Super Admin) → Access all
3. Admin → Access all except pimpinan-only routes
4. Other roles → Check against required roles
5. No match → 403 Forbidden
```

### 2.6 Design Principles Applied
- **Separation of Concerns:** Controllers, Services, Models have distinct responsibilities
- **DRY (Don't Repeat Yourself):** Shared logic in Services
- **SOLID Principles:**
  - Single Responsibility: Services, Controllers, Models
  - Open/Closed: Service-based extension points
  - Dependency Injection: Constructor injection in Controllers

---

## 3. DATABASE SCHEMA

### 3.1 PostgreSQL Schema Organization

```
Database Structure:
├── akun (Account Management)
├── referensi (Master Data)
├── transaksi (Transaction Data)
└── audit (Audit & History)
```

### 3.2 Complete Schema Definition

#### SCHEMA: akun (Account Management)

##### Table: akun.peran (Roles)
```sql
CREATE TABLE akun.peran (
    UUID UUID PRIMARY KEY,
    nm_peran VARCHAR(50) UNIQUE,           -- Role name: 'Pengguna', 'Verifikator', 'Eksekutor', 'Admin', 'Pimpinan'
    a_aktif BOOLEAN DEFAULT TRUE,          -- Active flag
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    last_sync TIMESTAMP,
    delete_at TIMESTAMP,
    expired_date TIMESTAMP,
    id_creator UUID,
    id_updater UUID
);
```

**Role Types:**
- `Pengguna` - Regular users who submit requests
- `Verifikator` - Verify submitted requests
- `Eksekutor` - Execute/process approved requests
- `Admin` - Manage users and system settings
- `Pimpinan` - Leadership/super admin level

##### Table: akun.pengguna (Users)
```sql
CREATE TABLE akun.pengguna (
    UUID UUID PRIMARY KEY,
    nm VARCHAR(125),                       -- Full name
    usn VARCHAR(100) UNIQUE,               -- Username
    email VARCHAR(125) UNIQUE,
    ktp VARCHAR(20),                       -- ID number
    tgl_lahir DATE,
    kata_sandi VARCHAR(255),               -- Hashed password
    peran_uuid UUID FK,                    -- Foreign key to peran
    a_aktif BOOLEAN DEFAULT TRUE,          -- Account activation flag
    sso_id VARCHAR(255),                   -- SSO user ID (if SSO login)
    last_login_at TIMESTAMP,
    last_login_ip VARCHAR(45),
    file_ktp_ktm_path VARCHAR(255),        -- Path to KTP/KTM file (storage)
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    delete_at TIMESTAMP,
    id_creator UUID FK (self-reference),
    id_updater UUID FK (self-reference)
);
```

**Key Fields:**
- `peran_uuid` → Relationship to roles
- `a_aktif` → Account activation status (admin approval needed)
- `sso_id` → SSO integration (from akses.unila.ac.id)
- `file_ktp_ktm_path` → Document verification storage

##### Table: akun.pemetaan_peran_sso (SSO Role Mapping)
```sql
CREATE TABLE akun.pemetaan_peran_sso (
    UUID UUID PRIMARY KEY,
    atribut_sso VARCHAR(100),              -- SSO attribute value
    peran_uuid UUID FK,                    -- Role to assign
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    id_creator UUID,
    id_updater UUID
);
```

**Purpose:** Map SSO attributes to internal roles

---

#### SCHEMA: referensi (Master Data)

##### Table: referensi.kategori_unit (Unit Categories)
```sql
CREATE TABLE referensi.kategori_unit (
    UUID UUID PRIMARY KEY,
    nm_kategori VARCHAR(100) UNIQUE,       -- Examples: 'Fakultas', 'UPT', 'Direktorat'
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    id_creator UUID,
    id_updater UUID
);
```

##### Table: referensi.unit_kerja (Work Units/Departments)
```sql
CREATE TABLE referensi.unit_kerja (
    UUID UUID PRIMARY KEY,
    nm_lmbg VARCHAR(125),                  -- Unit name
    kode_unit VARCHAR(50),                 -- Unit code (for subdomains)
    kategori_uuid UUID FK,                 -- Category reference
    a_aktif BOOLEAN DEFAULT TRUE,
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    id_creator UUID,
    id_updater UUID
);
```

**Purpose:** Organizational hierarchy for subdomain assignment

##### Table: referensi.jenis_layanan (Service Types)
```sql
CREATE TABLE referensi.jenis_layanan (
    UUID UUID PRIMARY KEY,
    nm_layanan VARCHAR(100),               -- Examples: 'domain', 'hosting', 'vps'
    deskripsi TEXT,
    a_aktif BOOLEAN DEFAULT TRUE,
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    id_creator UUID,
    id_updater UUID
);
```

##### Table: referensi.status_pengajuan (Submission Status)
```sql
CREATE TABLE referensi.status_pengajuan (
    UUID UUID PRIMARY KEY,
    nm_status VARCHAR(50),                 -- Status values (see below)
    create_at TIMESTAMP DEFAULT NOW()
);
```

**Status Workflow:**
```
Draft
  ↓
Diajukan (Submitted)
  ↓
Menunggu Verifikasi (Awaiting Verification)
  ├→ Disetujui Verifikator (Approved by Verifier)
  │   ↓
  │   Menunggu Eksekusi (Awaiting Execution)
  │   ├→ Sedang Dikerjakan (In Progress)
  │   │   ├→ Selesai (Completed)
  │   │   └→ Ditolak Eksekutor (Rejected by Executor)
  │   └→ (Error handling)
  │
  └→ Ditolak Verifikator (Rejected by Verifier)
```

---

#### SCHEMA: transaksi (Transaction Data)

##### Table: transaksi.pengajuan (Submission Header)
```sql
CREATE TABLE transaksi.pengajuan (
    UUID UUID PRIMARY KEY,
    no_tiket VARCHAR(50) UNIQUE,           -- Ticket number: TIK-YYYYMMDD-XXXX
    pengguna_uuid UUID FK,                 -- Submitter reference
    unit_kerja_uuid UUID FK,               -- Organization unit
    jenis_layanan_uuid UUID FK,            -- Service type
    status_uuid UUID FK,                   -- Current status
    tgl_pengajuan DATE,                    -- Submission date
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    last_sync TIMESTAMP,
    delete_at TIMESTAMP (soft delete),
    expired_date TIMESTAMP,
    id_creator UUID FK,
    id_updater UUID FK
);
```

**Key Logic:**
- Ticket number generated: `TIK-{YYYYMMDD}-{4-RANDOM-HEX}`
- Status transitions tracked in audit log
- Soft delete support via `delete_at`

##### Table: transaksi.rincian_pengajuan (Submission Details)
```sql
CREATE TABLE transaksi.rincian_pengajuan (
    UUID UUID PRIMARY KEY,
    pengajuan_uuid UUID FK UNIQUE,         -- One-to-one with pengajuan
    nm_domain VARCHAR(150),                -- Domain name requested
    alamat_ip VARCHAR(45),                 -- IP address (IPv4/IPv6)
    kapasitas_penyimpanan VARCHAR(50),     -- Storage capacity
    lokasi_server VARCHAR(100),            -- Server location
    vps_os VARCHAR(50),                    -- VPS OS type
    vps_cpu VARCHAR(20),                   -- VPS CPU specs
    vps_ram VARCHAR(20),                   -- VPS RAM specs
    vps_storage VARCHAR(20),               -- VPS storage specs
    keterangan_keperluan TEXT,             -- Purpose/requirements (JSON)
    file_lampiran VARCHAR(255),            -- Attachment file path
    create_at TIMESTAMP DEFAULT NOW(),
    last_update TIMESTAMP DEFAULT NOW(),
    id_creator UUID,
    id_updater UUID
);
```

**Notes:**
- `keterangan_keperluan` stored as JSON string with form data
- One detail record per submission (1:1 relationship)

---

#### SCHEMA: audit (Audit & History)

##### Table: audit.riwayat_pengajuan (Submission Status History)
```sql
CREATE TABLE audit.riwayat_pengajuan (
    UUID UUID PRIMARY KEY,
    pengajuan_uuid UUID FK,                -- Submission reference
    status_lama_uuid UUID FK,              -- Old status
    status_baru_uuid UUID FK,              -- New status
    catatan_log TEXT,                      -- Notes on status change
    create_at TIMESTAMP DEFAULT NOW(),
    id_creator UUID FK                     -- Who made the change
);
```

**Purpose:** Immutable log of all status transitions

##### Table: audit.riwayat_login (Login History)
```sql
CREATE TABLE audit.riwayat_login (
    UUID UUID PRIMARY KEY,
    pengguna_uuid UUID FK,                 -- User who logged in (null if failed)
    alamat_ip VARCHAR(45),                 -- Source IP address
    status_akses VARCHAR(50),              -- BERHASIL, GAGAL_PASSWORD, GAGAL_SSO, etc.
    keterangan TEXT,                       -- Additional details/error message
    create_at TIMESTAMP DEFAULT NOW()
);
```

**Status Values:**
- `BERHASIL` - Successful login
- `GAGAL_PASSWORD` - Wrong password
- `GAGAL_NOT_FOUND` - User not found
- `GAGAL_SUSPEND` - Account suspended/inactive
- `GAGAL_SSO` - SSO authentication failed

---

#### Table: admin_notifications (Admin Notification System)
```sql
CREATE TABLE admin_notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255),                     -- Notification type
    title VARCHAR(255),
    message TEXT,
    related_user_uuid UUID FK,             -- Associated user (if any)
    related_submission_uuid UUID FK,       -- Associated submission (if any)
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

### 3.3 Database Conventions

| Convention | Details |
|---|---|
| **Primary Key** | UUID (not auto-increment integer) |
| **Timestamps** | `create_at`, `last_update` (not Laravel default) |
| **Soft Delete** | `delete_at` (nullable timestamp) |
| **Naming** | Indonesian language (nm_lmbg, no_tiket, kata_sandi) |
| **Schema Prefix** | PostgreSQL schemas (akun., referensi., transaksi., audit.) |
| **Foreign Keys** | Explicit FK constraints with cascade options |
| **Audit Columns** | create_at, last_update, last_sync, delete_at, expired_date, id_creator, id_updater |

### 3.4 Key Relationships

```
User (pengguna)
├── 1:1 → Peran (role)
├── 1:M → Submission (submissions)
├── 1:M → LoginLog (loginLogs)
└── 1:M → SubmissionLog (as creator/updater)

Submission (pengajuan)
├── M:1 → User (pengguna/applicant)
├── M:1 → Unit (unitKerja)
├── M:1 → JenisLayanan (service type)
├── M:1 → StatusPengajuan (current status)
├── 1:1 → SubmissionDetail (rincian)
├── 1:M → SubmissionLog (riwayat)
└── M:1 → User (creator/updater)

Unit (unit_kerja)
├── M:1 → UnitCategory (kategori)
└── 1:M → Submission (submissions)

StatusPengajuan
└── 1:M → Submission (submissions)
        └── 1:M → SubmissionLog (status changes)

SubmissionLog
├── M:1 → Submission (pengajuan)
├── M:1 → StatusPengajuan (statusLama/statusBaru)
└── M:1 → User (creator)
```

---

## 4. CORE FEATURES

### 4.1 User Authentication & Authorization

#### Authentication Methods

**1. Local Credentials**
```
Flow:
1. User enters email & password
2. AuthController::store() validates
3. AuthService::login() checks credentials
4. Calls Auth::login($user, $remember)
5. AuditLogService::recordLoginLog() records attempt
6. Session created, user redirected to dashboard
```

**Key Files:**
- [AuthController.php](app/Http/Controllers/AuthController.php)
- [AuthService.php](app/Services/AuthService.php)
- [config/auth.php](config/auth.php) - Uses 'web' guard with session driver

**2. SSO Integration (akses.unila.ac.id)**
```
Flow:
1. User clicks "Login SSO"
2. SSOController::redirectToSSO()
   - Builds SSO URL with app_key & redirect_uri
   - Redirects to https://akses.unila.ac.id/api/live/v1/auth/login/sso
3. User authenticates at SSO provider
4. SSO redirects back to sso.callback with token
5. SSOController::handleCallback()
   - Validates token via TokenSSO() helper
   - Checks/creates user from SSO payload
   - Maps SSO role to local role
   - AuditLogService records login
6. Session created, user logged in
```

**Key Files:**
- [SSOController.php](app/Http/Controllers/Auth/SSOController.php)
- [TokenSSO.php](app/Helpers/TokenSSO.php) - JWT token validation
- [config/services.php] - SSO configuration

**SSO Payload Requirements:**
```php
{
    "id_aplikasi",
    "url_aplikasi",
    "id_pengguna",
    "username",
    "nm_pengguna",
    "peran_pengguna",           // Maps to local role
    "id_sdm_pengguna",
    "id_pd_pengguna",
    "email",
    "token_dibuat",
    "token_kadarluwasa",        // Expiration timestamp
    "asal_domain",
    "ip_address",
    "sso": true
}
```

#### Authorization (Role-Based Access Control)

**User Roles:**
| Role | Permissions | Dashboard |
|---|---|---|
| **Pengguna** (User) | Create submissions, view own submissions | Personal dashboard |
| **Verifikator** | View pending submissions, approve/reject | Verifikator dashboard |
| **Eksekutor** | Execute approved tasks, mark complete | Executor dashboard |
| **Admin** | Manage users, verify accounts, view audit logs | Admin dashboard |
| **Pimpinan** | Leadership reports, override decisions | Leadership dashboard |

**Route Protection:**
```php
// Require authentication
Route::middleware('auth')->group(...)

// Require active account
Route::middleware('active')->group(...)

// Require specific role(s)
Route::middleware('role:admin')->group(...)
Route::middleware('role:verifikator,eksekutor')->group(...)

// Role hierarchy
// Pimpinan > Admin > [Verifikator, Eksekutor, Pengguna]
```

**Middleware Details:**
- **EnsureUserIsActive:** Checks `user.a_aktif` flag
  - Allows: dashboard, logout (whitelist)
  - Blocks: All other routes if inactive
- **RoleMiddleware:** Checks `user.peran.nm_peran`
  - Pimpinan can access everything
  - Admin can access everything except pimpinan-only
  - Others must match required roles

---

### 4.2 User Registration & Activation Flow

#### Registration Flow

```
1. User Self-Registration
   ├─ /register page: Form collection
   ├─ POST /register: Input validation
   ├─ UserService::register():
   │  ├─ Get 'Pengguna' role UUID
   │  ├─ Generate UUID for new user
   │  ├─ Handle KTP/KTM file upload (if provided)
   │  ├─ Create user record with a_aktif = FALSE
   │  ├─ Set id_creator = self UUID
   │  ├─ Audit log: User registration
   │  └─ Return new user
   ├─ NotificationService::notifyUserRegistration():
   │  ├─ Create AdminNotification in database
   │  ├─ Send email to admin (configured)
   │  └─ Notify admin of new registration
   └─ Redirect to login with success message

2. Admin Verification
   ├─ Admin views /admin/users/verification
   ├─ AdminService::getUsersForVerification():
   │  ├─ Filter by account type (SSO/Local)
   │  ├─ Filter by status (Active/Inactive)
   │  ├─ Search by name/username/email
   │  ├─ Sort results
   │  └─ Paginate results
   ├─ Admin toggles user status
   ├─ POST /admin/users/{uuid}/toggle-status
   ├─ AdminService::toggleUserStatus():
   │  ├─ Toggle a_aktif boolean
   │  ├─ Log change with admin UUID
   │  └─ Return success
   ├─ NotificationService::notifyUserActivation():
   │  ├─ Create AdminNotification
   │  ├─ Send email to user
   │  └─ Notify user of activation
   └─ User can now login
```

**Key Files:**
- [RegisterController.php](app/Http/Controllers/Auth/RegisterController.php)
- [UserService.php](app/Services/UserService.php)
- [NotificationService.php](app/Services/NotificationService.php)

---

### 4.3 Submission Workflow

#### Complete Submission Lifecycle

```
DRAFT STAGE
│
├─ User: POST /pengajuan/buat
│  ├─ Create submission record (status: Draft)
│  ├─ Create submission detail record
│  └─ Store form data in keterangan_keperluan (JSON)
│
├─ User: GET /pengajuan/{submission}/upload
│  └─ Upload supporting files
│
└─ User: POST /pengajuan/{submission}/quick-submit
   └─ Mark submission as "Diajukan"
      └─ SubmissionLog created (Draft → Diajukan)
         └─ NotificationService notifies
            └─ AdminNotification created

VERIFICATION STAGE
│
├─ Verifikator: GET /verifikator/daftar-pengajuan
│  └─ View all "Diajukan" submissions
│
├─ Verifikator: GET /verifikator/{submission}
│  ├─ Review submission details
│  ├─ View history (riwayat)
│  └─ View file attachments
│
├─ Verifikator: POST /verifikator/{submission}/approve
│  ├─ Update status → "Disetujui Verifikator"
│  ├─ Create SubmissionLog
│  ├─ Record id_creator (verifikator UUID)
│  └─ NotificationService notifies user
│
└─ Verifikator: POST /verifikator/{submission}/reject
   ├─ Update status → "Ditolak Verifikator"
   ├─ Create SubmissionLog with notes
   ├─ Record rejection reason
   └─ NotificationService notifies user

EXECUTION STAGE
│
├─ Executor: GET /eksekutor/daftar-tugas
│  └─ View all "Disetujui Verifikator" submissions
│
├─ Executor: GET /eksekutor/{submission}
│  └─ View submission details & logs
│
├─ Executor: POST /eksekutor/{submission}/start-work
│  ├─ Update status → "Sedang Dikerjakan"
│  ├─ Create SubmissionLog
│  └─ Lock submission from other editors
│
├─ Executor: POST /eksekutor/{submission}/complete
│  ├─ Update status → "Selesai"
│  ├─ Create SubmissionLog
│  ├─ Generate completion report
│  └─ NotificationService notifies all stakeholders
│
└─ Executor: POST /eksekutor/{submission}/reject
   ├─ Update status → "Ditolak Eksekutor"
   ├─ Create SubmissionLog with rejection reason
   └─ NotificationService notifies user & admin

COMPLETION
│
├─ Selesai: Submission marked complete
├─ Ditolak: Submission marked rejected (final)
└─ User can view completion history & download results
```

#### Form Generation (Dual Output)

```
User: POST /pengajuan/{submission}/generate-form
  ├─ Determine service type (domain/hosting/vps)
  ├─ Generate unique filename
  │
  ├─ Paperless Output
  │  └─ GET /form/{ticketNumber}/paperless
  │     └─ Display form-paperless.blade.php view
  │        └─ Render on screen (for TIK digital archive)
  │
  └─ Hardcopy Output
     └─ GET /form/{ticketNumber}/hardcopy/download
        ├─ Load form-hardcopy.blade.php
        ├─ Use dompdf to generate PDF
        ├─ Set filename: Form_{Type}_{Service}_{Ticket}.pdf
        └─ Download to user device (for leadership signatures)
```

**Key Files:**
- [SubmissionController.php](app/Http/Controllers/SubmissionController.php) - Create, store, upload
- [VerificationController.php](app/Http/Controllers/VerificationController.php) - Verification actions
- [ExecutionController.php](app/Http/Controllers/ExecutionController.php) - Execution tasks
- [FormGeneratorController.php](app/Http/Controllers/FormGeneratorController.php) - Form generation

---

### 4.4 Audit & Logging System

#### Login History Tracking

```
AuditLogService::recordLoginLog():
├─ Inputs:
│  ├─ userUuid (null if user not found)
│  ├─ status (BERHASIL, GAGAL_PASSWORD, GAGAL_SSO, etc.)
│  ├─ request (HTTP request for IP extraction)
│  ├─ keterangan (error details/notes)
│  └─ customIp (optional override)
│
├─ IP Detection (priority order):
│  ├─ Custom IP (if provided)
│  ├─ X-Forwarded-For header (proxy)
│  ├─ X-Real-IP header (nginx)
│  └─ $_SERVER['REMOTE_ADDR']
│
├─ User Agent Sanitization:
│  ├─ Truncate to 500 chars
│  └─ Remove sensitive info
│
├─ Database Recording:
│  └─ INSERT audit.riwayat_login (
│        UUID, pengguna_uuid, alamat_ip,
│        status_akses, keterangan, create_at
│      )
│
└─ Called from:
   ├─ AuthController::store() - Local login
   ├─ SSOController::handleCallback() - SSO login
   └─ Custom login attempts
```

#### Submission Status History Tracking

```
When submission status changes:
1. SubmissionLog::create({
     pengajuan_uuid,
     status_lama_uuid,
     status_baru_uuid,
     catatan_log,           // Optional notes
     id_creator              // Who made change
   })
2. Stored in audit.riwayat_pengajuan (immutable)
3. No updates/deletes allowed (audit trail integrity)
```

#### Activity/Audit Log Views

**Admin Access:**
```
/admin/audit/aktivitas
├─ All login attempts (success/failure)
├─ IP addresses & user agents
├─ Timestamps & status codes
└─ Searchable & filterable

/admin/audit/submissions
├─ All submission status changes
├─ Who made changes & when
├─ Change notes/reasons
└─ Exportable reports

/admin/audit/user/{uuid}
├─ Specific user's complete history
├─ All activities (login, submissions, changes)
└─ Detailed activity timeline
```

**Key Files:**
- [AuditLogService.php](app/Services/AuditLogService.php)
- [LoginLog.php](app/Models/LoginLog.php)
- [SubmissionLog.php](app/Models/SubmissionLog.php)
- [AuditLogController.php](app/Http/Controllers/Admin/AuditLogController.php)

---

### 4.5 Admin Notification System (New: May 4, 2026)

#### Notification Types

| Type | Trigger | Related Model | Audience |
|---|---|---|---|
| `user_registered` | New user self-registers | User | Admin |
| `user_activated` | Admin activates user | User | User |
| `submission_status_changed` | Status transitions | Submission | User + Admin |
| `verification_approved` | Verifier approves | Submission | User + Admin |
| `verification_rejected` | Verifier rejects | Submission | User + Admin |
| `execution_completed` | Executor completes | Submission | User + Admin |
| `execution_rejected` | Executor rejects | Submission | User + Admin |

#### Notification Storage & Display

```
AdminNotification Model:
├─ id: UUID
├─ type: notification type
├─ title: short title
├─ message: full message
├─ related_user_uuid: associated user
├─ related_submission_uuid: associated submission
├─ is_read: boolean
├─ read_at: timestamp
├─ created_at / updated_at: timestamps

Admin Dashboard (/admin/notifications):
├─ Display all notifications (newest first)
├─ Mark individual notification as read
├─ Mark all as read
├─ Delete notification
├─ Show unread count in header

Notification Details (/admin/notifications/{id}):
├─ View full notification details
├─ Link to related user/submission
├─ Auto-mark as read on view
└─ Back link to list
```

**Key Files:**
- [AdminNotification.php](app/Models/AdminNotification.php)
- [NotificationController.php](app/Http/Controllers/Admin/NotificationController.php)
- [NotificationService.php](app/Services/NotificationService.php)
- Migration: [2026_05_04_192338_create_notifications_table.php](database/migrations/2026_05_04_192338_create_notifications_table.php)

---

## 5. CODE ORGANIZATION

### 5.1 Directory Structure & File Responsibilities

```
app/
├── Console/
│   └── Commands/                    # Artisan commands
│
├── Enums/
│   └── UserRole.php                 # User role enum (USER, VERIFIKATOR, EKSEKUTOR, ADMIN, PIMPINAN)
│
├── Helpers/
│   └── TokenSSO.php                 # SSO JWT token validation function
│
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       # Local authentication
│   │   ├── DashboardController.php  # Role-based dashboard routing
│   │   ├── SubmissionController.php # Submission CRUD & workflows
│   │   ├── VerificationController.php
│   │   ├── ExecutionController.php
│   │   ├── FormGeneratorController.php
│   │   ├── Auth/
│   │   │   ├── SSOController.php    # SSO auth flow
│   │   │   └── RegisterController.php
│   │   ├── Admin/
│   │   │   ├── AdminController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── AuditLogController.php
│   │   │   └── UnitController.php
│   │   ├── Pimpinan/
│   │   │   └── PimpinanController.php
│   │   └── Controller.php           # Base controller
│   │
│   ├── Middleware/
│   │   ├── EnsureUserIsActive.php   # Check account activation
│   │   └── RoleMiddleware.php       # Check user role/permissions
│   │
│   └── Requests/
│       └── (Validation form requests - minimal usage)
│
├── Models/
│   ├── User.php                     # akun.pengguna
│   ├── Peran.php                    # akun.peran
│   ├── Unit.php                     # referensi.unit_kerja
│   ├── UnitCategory.php             # referensi.kategori_unit
│   ├── JenisLayanan.php             # referensi.jenis_layanan
│   ├── StatusPengajuan.php          # referensi.status_pengajuan
│   ├── Submission.php               # transaksi.pengajuan
│   ├── SubmissionDetail.php         # transaksi.rincian_pengajuan
│   ├── SubmissionLog.php            # audit.riwayat_pengajuan
│   ├── LoginLog.php                 # audit.riwayat_login
│   └── AdminNotification.php        # admin_notifications
│
├── Providers/
│   ├── AppServiceProvider.php       # Global app config (HTTPS, DB timeouts)
│   └── SessionServiceProvider.php
│
├── Services/
│   ├── AuthService.php              # Auth logic (login/logout)
│   ├── UserService.php              # User registration & management
│   ├── AdminService.php             # Admin operations
│   ├── AuditLogService.php          # Login & submission logging
│   ├── NotificationService.php      # User & admin notifications
│   ├── PimpinanService.php          # Leadership operations
│   └── UnitSyncService.php          # Unit data sync
│
└── Session/
    └── (Session middleware/handlers)
```

### 5.2 Model Organization

#### Model Naming Convention
```
Database Table → Model Name
akun.pengguna → User
akun.peran → Peran
referensi.unit_kerja → Unit
referensi.jenis_layanan → JenisLayanan
transaksi.pengajuan → Submission
transaksi.rincian_pengajuan → SubmissionDetail
audit.riwayat_pengajuan → SubmissionLog
audit.riwayat_login → LoginLog
```

#### Model Configuration Template
```php
<?php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExampleModel extends Model {
    use HasUuids;
    
    protected $table = 'schema.table_name';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;  // If using custom timestamp columns
    
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';
    
    protected $fillable = [...];
    protected $casts = [...];
}
```

### 5.3 Service Usage Patterns

#### Dependency Injection Pattern
```php
// In Controller constructor
public function __construct(
    protected AuthService $authService,
    protected NotificationService $notificationService
) {}

// Usage in method
public function store(Request $request)
{
    $user = $this->authService->login($email, $password);
    $this->notificationService->notifyLogin($user);
}
```

#### Static Service Method Pattern
```php
// Direct static call (no DI needed for simple operations)
NotificationService::notifyUserRegistration($user);
AuditLogService::recordLoginLog($userUuid, 'BERHASIL', $request);
```

### 5.4 Helper Functions

```php
app/Helpers/TokenSSO.php:

TokenSSO($jwt)
├─ Decodes JWT token from SSO
├─ Validates signature using secret
├─ Checks token expiration
├─ Returns payload object or false

generateJWT($payload, $expiry)
├─ Generates JWT token for authentication
├─ Encodes with HS256 algorithm
└─ Returns token string
```

---

## 6. BUSINESS LOGIC FLOW

### 6.1 User Registration to Submission Workflow

```
┌─ USER LIFECYCLE ─────────────────────────────────────────┐
│                                                           │
│  1. REGISTRATION (Self-Service)                          │
│     └─ User fills registration form                      │
│     └─ UserService::register() creates user              │
│     └─ Status: INACTIVE (a_aktif = false)               │
│     └─ Admin notified                                    │
│                                                           │
│  2. ADMIN VERIFICATION                                   │
│     └─ Admin reviews user in /admin/users/verification   │
│     └─ Admin toggles status (activate)                   │
│     └─ AdminService::toggleUserStatus()                  │
│     └─ Status: ACTIVE (a_aktif = true)                  │
│     └─ User notified via email                           │
│                                                           │
│  3. LOGIN                                                │
│     └─ User logs in (local or SSO)                       │
│     └─ EnsureUserIsActive middleware checks a_aktif      │
│     └─ AuditLogService records login attempt             │
│     └─ Session created, user enters dashboard            │
│                                                           │
│  4. SUBMIT REQUEST (Pengajuan)                           │
│     └─ User selects service type (domain/hosting/vps)    │
│     └─ User fills request form with details              │
│     └─ SubmissionController::store()                     │
│     └─ Creates pengajuan record (status: Draft initially) │
│     └─ Creates rincian_pengajuan detail record           │
│     └─ User can upload supporting files                  │
│     └─ User submits form (status → Diajukan)             │
│     └─ SubmissionLog records status change               │
│     └─ Admin notified                                    │
│                                                           │
└───────────────────────────────────────────────────────────┘

┌─ VERIFICATION LIFECYCLE ─────────────────────────────────┐
│                                                           │
│  1. VERIFIKATOR REVIEW                                   │
│     └─ Verifikator logs into /verifikator/daftar-pengajuan
│     └─ Views list of "Diajukan" submissions              │
│     └─ VerificationController::show() loads submission   │
│     └─ Reviews form data, files, attachments             │
│     └─ Reviews submission history (riwayat)              │
│                                                           │
│  2. VERIFICATION DECISION                                │
│     ├─ APPROVAL: VerificationController::approve()       │
│     │  ├─ Update status → "Disetujui Verifikator"       │
│     │  ├─ Create SubmissionLog                          │
│     │  ├─ Record verifikator UUID                        │
│     │  └─ Notify user & admin                            │
│     │                                                    │
│     └─ REJECTION: VerificationController::reject()       │
│        ├─ Update status → "Ditolak Verifikator"         │
│        ├─ Create SubmissionLog with reason               │
│        └─ Notify user & admin                            │
│                                                           │
└───────────────────────────────────────────────────────────┘

┌─ EXECUTION LIFECYCLE ────────────────────────────────────┐
│                                                           │
│  1. EXECUTOR VIEWS TASKS                                 │
│     └─ Executor logs into /eksekutor/daftar-tugas       │
│     └─ Views list of "Disetujui Verifikator" submissions │
│     └─ ExecutionController::index() with filters         │
│                                                           │
│  2. START EXECUTION                                      │
│     └─ ExecutionController::startWork()                  │
│     └─ Update status → "Sedang Dikerjakan"              │
│     └─ Lock submission for other editors                │
│     └─ Create SubmissionLog                              │
│                                                           │
│  3. COMPLETE EXECUTION                                   │
│     ├─ COMPLETION: ExecutionController::complete()      │
│     │  ├─ Update status → "Selesai"                     │
│     │  ├─ Create SubmissionLog with completion notes    │
│     │  ├─ Unlock submission                             │
│     │  └─ Notify user & admin                            │
│     │                                                    │
│     └─ REJECTION: ExecutionController::reject()          │
│        ├─ Update status → "Ditolak Eksekutor"           │
│        ├─ Create SubmissionLog with reason               │
│        └─ Notify user & admin                            │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

### 6.2 Permission & Authorization Checks

#### Route-Level Authorization

```php
// Public routes (no auth)
Route::get('/', ...)

// Authenticated routes only
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...)
    
    // Active users only
    Route::middleware('active')->group(function () {
        Route::prefix('pengajuan')->group(...)
    })
})

// Admin only
Route::middleware(['auth', 'active', 'role:admin'])->group(...)

// Verifier or Executor
Route::middleware(['auth', 'active', 'role:verifikator,eksekutor'])->group(...)

// Leadership/Pimpinan
Route::middleware(['auth', 'role:pimpinan'])->group(...)
```

#### Permission Checks in Controllers

```php
// Example: Verify ownership
if ($submission->pengguna_uuid !== Auth::user()->UUID) {
    return abort(403, 'Unauthorized');
}

// Example: Check submission status
if ($submission->status->nm_status !== 'Diajukan') {
    return abort(422, 'Cannot verify submission with current status');
}

// Example: Role-based behavior
if (Auth::user()->role === 'admin') {
    // Admin-specific logic
} elseif (Auth::user()->role === 'verifikator') {
    // Verifier-specific logic
}
```

### 6.3 Notification Trigger Points

```
1. USER REGISTRATION
   └─ NotificationService::notifyUserRegistration()
      └─ Create AdminNotification (type: user_registered)
      └─ Send email to configured admin email

2. ACCOUNT ACTIVATION
   └─ NotificationService::notifyUserActivation()
      └─ Create AdminNotification (type: user_activated)
      └─ Send email to user

3. SUBMISSION STATUS CHANGE
   └─ NotificationService::notifySubmissionStatusChange()
      └─ Create AdminNotification
      └─ Send email to user & stakeholders
      └─ Includes status transition info & notes

4. VERIFICATION APPROVED
   └─ Automatic via notifySubmissionStatusChange()
      └─ Trigger: status update to "Disetujui Verifikator"

5. EXECUTION COMPLETED
   └─ Automatic via notifySubmissionStatusChange()
      └─ Trigger: status update to "Selesai"
```

---

## 7. FRONTEND ARCHITECTURE

### 7.1 View Structure

```
resources/views/
├── home.blade.php                  # Home/landing page
│
├── dashboard.blade.php             # User dashboard
│
├── admin/
│   ├── user-verification.blade.php # User account management
│   ├── audit-logs.blade.php        # Audit log display
│   └── notifications/
│       ├── index.blade.php         # Notification list
│       └── show.blade.php          # Notification detail
│
├── verifikator/
│   ├── index.blade.php             # Pending submissions list
│   └── show.blade.php              # Submission detail & actions
│
├── eksekutor/
│   ├── index.blade.php             # Task list
│   └── show.blade.php              # Task detail & execution
│
├── submissions/
│   ├── index.blade.php             # User's submissions list
│   ├── create.blade.php            # Submission form
│   ├── show.blade.php              # Submission detail
│   ├── select-service.blade.php    # Service type selection
│   └── upload.blade.php            # File upload
│
├── forms/
│   ├── select-form.blade.php       # Choose form type (paperless/hardcopy)
│   ├── form-paperless.blade.php    # Digital form for TIK
│   └── form-hardcopy.blade.php     # PDF form for leadership
│
├── auth/
│   ├── login.blade.php             # Login page (local & SSO options)
│   └── register.blade.php          # Registration form
│
├── layouts/
│   ├── app-layout.blade.php        # Main app layout
│   ├── guest-layout.blade.php      # Guest/public layout
│   └── register-layout.blade.php   # Registration layout
│
├── components/
│   ├── navbar.blade.php            # Top navigation
│   ├── sidebar.blade.php           # Side menu
│   ├── status-card.blade.php       # Status display
│   ├── alert.blade.php             # Alert/message display
│   └── ...more components
│
└── emails/
    ├── user-activated.blade.php    # User activation email
    ├── submission-status-changed.blade.php
    └── ...more email templates
```

### 7.2 Blade Template Patterns

#### Common Patterns Used

**1. Component Inclusion**
```blade
<x-navbar />
<x-sidebar />
<x-alert type="success" message="Operation successful" />
<x-status-card :submission="$submission" />
```

**2. Conditional Display**
```blade
@if(Auth::user()->role === 'admin')
    {{-- Admin-only content --}}
@endif

@unless($submission->a_aktif)
    <span class="badge">Inactive</span>
@endunless
```

**3. Loop & Iteration**
```blade
@forelse($submissions as $submission)
    <tr>
        <td>{{ $submission->no_tiket }}</td>
        <td>{{ $submission->status->nm_status }}</td>
    </tr>
@empty
    <tr><td colspan="2">No submissions found</td></tr>
@endforelse
```

**4. Form Display**
```blade
<form action="{{ route('submissions.store') }}" method="POST">
    @csrf
    <input name="no_tiket" value="{{ old('no_tiket') }}" />
    @error('no_tiket')
        <span class="error">{{ $message }}</span>
    @enderror
</form>
```

### 7.3 Frontend Technologies

#### Tailwind CSS
- **Usage:** All styling via utility classes
- **Configuration:** `tailwind.config.js` in root
- **Build:** Vite (via `@tailwindcss/vite`)
- **Files:** `resources/css/app.css`

#### Alpine.js
- **Purpose:** Light interactive components without framework
- **Common Uses:**
  - Modal/dropdown toggles
  - Form validation feedback
  - Dynamic list filtering
  - Notification display
- **Pattern:** `x-data`, `x-show`, `x-on:click`, etc.

#### Vite Build Tool
- **Config:** [vite.config.js](vite.config.js)
- **Entry Points:**
  - `resources/css/app.css`
  - `resources/js/app.js`
- **Dev Server:** Port 5173 (HMR enabled)
- **Build Command:** `npm run build`
- **Dev Command:** `npm run dev`

#### dompdf
- **Purpose:** Generate PDF forms
- **Usage:** [FormGeneratorController.php](app/Http/Controllers/FormGeneratorController.php)
- **Views:** `resources/views/forms/form-hardcopy.blade.php`
- **Features:**
  - HTML → PDF conversion
  - Custom fonts & styling
  - Image support
  - Filename generation

### 7.4 Form Patterns

#### Service Request Form Structure
```
1. REQUEST TYPE (tipe_pengajuan)
   - Pengajuan Baru (New Request)
   - Perpanjangan (Renewal)
   - Perubahan Data (Data Change)
   - Upgrade/Downgrade
   - Penonaktifan (Deactivation)
   - Laporan Masalah (Problem Report)

2. REQUESTER CATEGORY (kategori_pemohon)
   - Lembaga/Fakultas (Institution)
   - Kegiatan Lembaga (Department Activity)
   - Organisasi Mahasiswa (Student Organization)
   - Kegiatan Mahasiswa (Student Activity)
   - Lainnya (Other)

3. ADMINISTRATIVE RESPONSIBLE (Penanggung Jawab Admin)
   - Name, Position, NIP
   - Office & Home Address
   - Phone & Email

4. TECHNICAL RESPONSIBLE (Penanggung Jawab Teknis)
   - Name, NIP, NIK
   - Phone, Address

5. SERVICE DETAILS (varies by service type)
   
   DOMAIN:
   - Domain name
   - Subdomain code
   
   HOSTING:
   - Storage capacity
   - Server location
   - Purpose/requirements
   - File attachments
   
   VPS:
   - OS type
   - CPU specs
   - RAM specs
   - Storage specs
   - Purpose/requirements
   - File attachments

6. SUBMISSION
   - Upload supporting documents
   - Review before final submit
   - Generate paperless form
   - Download hardcopy PDF
```

#### Form Data Storage
```
Submission (pengajuan):
├─ no_tiket: "TIK-20260504-A1B2"
├─ pengguna_uuid: User UUID
├─ unit_kerja_uuid: Department UUID
├─ jenis_layanan_uuid: Service type UUID
├─ status_uuid: Current status UUID
└─ tgl_pengajuan: Submission date

SubmissionDetail (rincian_pengajuan):
├─ pengajuan_uuid: Reference to submission
├─ nm_domain: Domain name (if domain service)
├─ kapasitas_penyimpanan: Storage capacity (if hosting)
├─ vps_os/cpu/ram/storage: VPS specs (if VPS)
├─ keterangan_keperluan: Form data as JSON
│  └─ Includes: tipe_pengajuan, kategori_pemohon,
│      admin_responsible_*, teknis_*, service-specific data
└─ file_lampiran: Path to uploaded files
```

---

## 8. CONFIGURATION & ENVIRONMENT

### 8.1 Environment Setup

#### Required Environment Variables
```
# Application
APP_NAME=SiDevTIK
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-tik.unila.ac.id

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=domaintik
DB_USERNAME=postgres
DB_PASSWORD=<secret>

# Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=<username>
MAIL_PASSWORD=<password>
MAIL_FROM_ADDRESS=noreply@domain-tik.unila.ac.id
MAIL_FROM_NAME="Domain TIK"

# Session
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=database

# SSO Configuration
SSO_BASE_URL=https://akses.unila.ac.id/api/live/v1/auth
SSO_APP_KEY=<app_key>
SSO_JWT_SECRET=<jwt_secret>

# Admin Email (notifications)
ADMIN_EMAIL=admin@domain-tik.unila.ac.id
```

### 8.2 Database Configuration

#### PostgreSQL Connection (config/database.php)
```php
'pgsql' => [
    'driver' => 'pgsql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'domaintik'),
    'username' => env('DB_USERNAME', 'postgres'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8'),
    'prefix' => '',
    'schema' => 'public',
]
```

#### Schema Support
- PostgreSQL support for multiple schemas: `akun.`, `referensi.`, `transaksi.`, `audit.`
- Models specify schema.table in `protected $table`
- Foreign key constraints with cascade options

### 8.3 Cache Configuration
```
CACHE_DRIVER=database → Uses database table (cache table)
Alternative: redis (if available)

Cached Queries:
- Role list (cached for performance)
- Service types
- Unit categories
```

### 8.4 Session Configuration
```
SESSION_DRIVER=cookie
SESSION_LIFETIME=120 minutes (2 hours)
SESSION_COOKIE_SECURE=true (HTTPS only in production)
SESSION_COOKIE_HTTP_ONLY=true (JavaScript can't access)
SESSION_COOKIE_SAME_SITE=strict
```

### 8.5 Mail Configuration

#### Supported Drivers
- SMTP (recommended)
- Mailgun
- SendGrid
- AWS SES

#### Email Templates
```
emails/
├── user-activated.blade.php     # Account activation notification
├── submission-status-changed.blade.php  # Submission status update
└── ...additional templates
```

#### Notification Emails Sent
- User registration → Admin
- User activation → User
- Submission status change → User
- Admin notifications → Admin

### 8.6 Production Deployment Settings

#### HTTPS Enforcement (AppServiceProvider)
```php
if (app()->environment('production')) {
    URL::forceScheme('https');
}
```

#### PostgreSQL Timeouts
```php
DB::statement("SET lock_timeout = '30s'");
DB::statement("SET statement_timeout = '60s'");
```

#### Recommended Production Configurations

| Setting | Value | Purpose |
|---|---|---|
| APP_DEBUG | false | Never expose stack traces |
| APP_ENV | production | Production mode |
| CACHE_DRIVER | redis | Better performance |
| SESSION_SECURE_COOKIES | true | HTTPS only |
| DB_CONNECTION | pgsql | PostgreSQL |
| LOG_CHANNEL | stack | Multi-channel logging |

---

## 9. DEPLOYMENT & RUNNING

### 9.1 Docker Environment

#### Development Command
```bash
# Setup
docker compose exec app composer install
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run build

# Development
npm run dev          # Frontend dev server (port 5173)
php artisan serve    # Backend server (port 8000)
php artisan queue:listen  # Queue worker

# All together
npm run dev:concurrently  # See composer.json scripts
```

#### Docker Conventions
- All artisan commands must run via `docker compose exec app`
- Use `docker compose up` to start services
- Use `docker compose down` to stop services
- Database migrations auto-run on deployment

### 9.2 Key Artisan Commands

```bash
# Database
php artisan migrate          # Run pending migrations
php artisan migrate:reset    # Reset all migrations
php artisan seed            # Run seeders
php artisan tinker          # Interactive shell

# Cache & Config
php artisan cache:clear     # Clear cache
php artisan config:clear    # Clear config cache
php artisan view:clear      # Clear view cache

# Testing
php artisan test            # Run tests

# Queue (if needed)
php artisan queue:listen    # Listen to queue
php artisan queue:failed    # View failed jobs
```

---

## 10. API ENDPOINTS

### 10.1 Public API Routes

#### Domain Availability Check
```
GET /api/check-domain?domain=example.com

Response:
{
    "available": true|false,
    "message": "Domain available" | "Domain already taken"
}
```

#### Submission Data Fetch (Auth Required)
```
GET /api/submission-by-ticket/{ticketNumber}
Authorization: Bearer {sanctum_token}

Response:
{
    "UUID": "uuid",
    "no_tiket": "TIK-...",
    "pengguna": { ... },
    "unitKerja": { ... },
    "jenisLayanan": { ... },
    "rincian": { ... },
    ...
}
```

### 10.2 Web Routes Summary

See [routes/web.php](routes/web.php) for complete routing structure

---

## 11. TESTING & QUALITY ASSURANCE

### 11.1 Testing Structure
```
tests/
├── Feature/         # Integration tests
├── Unit/            # Unit tests
└── TestCase.php     # Base test class
```

### 11.2 Run Tests
```bash
npm run test
# Or
php artisan test
```

---

## 12. TROUBLESHOOTING & COMMON ISSUES

### Issue: PostgreSQL Schema Not Found
**Solution:** Ensure migrations are run and schemas are created
```bash
php artisan migrate
```

### Issue: UUID Primary Key Errors
**Solution:** Models must have correct UUID configuration:
```php
use HasUuids;
protected $primaryKey = 'UUID';
protected $keyType = 'string';
public $incrementing = false;
```

### Issue: Middleware Not Working
**Solution:** Register in `bootstrap/app.php` or `AppServiceProvider`

### Issue: SSO Token Validation Fails
**Solution:** Check SSO_JWT_SECRET in environment and token expiration

### Issue: Notifications Not Sending
**Solution:** Verify MAIL_* environment variables and queue configuration

---

## 13. SECURITY CONSIDERATIONS

### 13.1 Authentication Security
- Passwords hashed with bcrypt
- Session-based auth with CSRF protection
- SSO JWT tokens validated with HMAC-SHA256
- Login attempts logged for audit trail

### 13.2 Authorization Security
- Role-based access control (RBAC)
- Middleware enforces permissions
- Account activation required before feature access
- Submission ownership verified

### 13.3 Data Security
- Sensitive fields hidden from serialization
- KTP/KTM files stored in private storage
- Audit trail immutable (no updates/deletes)
- Soft deletes for data retention

### 13.4 Production Security
- HTTPS enforced
- CSRF tokens on all forms
- SQL injection prevented via parameterized queries
- XSS prevention via Blade escaping
- CORS configured as needed

---

## 14. PERFORMANCE OPTIMIZATION

### 14.1 Database Optimization
- UUID indexes on foreign keys
- Timestamp indexes on create_at, last_update
- Query eager loading with `with()` to prevent N+1
- Soft delete queries filtered by delete_at

### 14.2 Caching Strategy
- Cache role list and permissions
- Cache service types and categories
- Cache unit list (refreshed on sync)
- Use database cache driver for persistence

### 14.3 Frontend Optimization
- Vite for bundling & HMR
- Tailwind CSS for minimal CSS output
- Alpine.js for lightweight interactivity
- dompdf caching for PDF generation

---

## 15. FUTURE ENHANCEMENTS

### Potential Improvements
- [ ] API Rate Limiting
- [ ] Advanced Search Filters
- [ ] Export Reports (Excel, CSV)
- [ ] Automated Email Reminders
- [ ] Webhook Support for External Integration
- [ ] Multi-language Support
- [ ] Mobile App/Responsive Improvements
- [ ] Real-time Notifications (WebSockets)
- [ ] Payment Gateway Integration
- [ ] Service Level Agreement (SLA) Tracking

---

## APPENDIX A: KEY FILE LOCATIONS

| Functionality | Primary File |
|---|---|
| Authentication | [AuthController.php](app/Http/Controllers/AuthController.php) |
| SSO Integration | [SSOController.php](app/Http/Controllers/Auth/SSOController.php) |
| User Registration | [RegisterController.php](app/Http/Controllers/Auth/RegisterController.php) |
| Submissions | [SubmissionController.php](app/Http/Controllers/SubmissionController.php) |
| Verification | [VerificationController.php](app/Http/Controllers/VerificationController.php) |
| Execution | [ExecutionController.php](app/Http/Controllers/ExecutionController.php) |
| Admin Panel | [AdminController.php](app/Http/Controllers/Admin/AdminController.php) |
| Notifications | [NotificationController.php](app/Http/Controllers/Admin/NotificationController.php) |
| Audit Logs | [AuditLogController.php](app/Http/Controllers/Admin/AuditLogController.php) |
| Database Schema | [Migrations](database/migrations/) |
| Services | [app/Services/](app/Services/) |
| Models | [app/Models/](app/Models/) |
| Routes | [routes/web.php](routes/web.php) |
| Configuration | [config/](config/) |

---

## APPENDIX B: GLOSSARY

| Term | Definition |
|---|---|
| **Pengajuan** | Submission/Request |
| **Verifikator** | Verifier (approval stage) |
| **Eksekutor** | Executor (execution stage) |
| **Pimpinan** | Leadership/Director |
| **a_aktif** | Active flag (account activation status) |
| **Riwayat** | History/Audit trail |
| **no_tiket** | Ticket number |
| **Rincian** | Details/Details record |
| **Keterangan** | Notes/Description |
| **Tipe Pengajuan** | Request type |

---

**End of Document**  
*For questions or updates, contact the Domain TIK Development Team*
