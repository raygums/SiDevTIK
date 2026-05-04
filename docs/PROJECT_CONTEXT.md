# 📋 KONTEKS LENGKAP PROYEK DOMAINTIK

> Dokumen ini berisi konteks lengkap proyek untuk referensi pengembangan lebih lanjut.
> **Last Updated:** 25 Januari 2026

---

## 1. INFORMASI UMUM

| Item | Value |
|------|-------|
| **Nama Proyek** | Domaintik (Domain TIK Unila) |
| **Framework** | Laravel 12.x |
| **Database** | PostgreSQL (dengan multiple schemas) |
| **PHP Version** | 8.2+ |
| **Environment** | Docker |

### Docker Command Convention
Semua command artisan **HARUS** dijalankan via Docker:

```bash
# Masuk ke container
docker compose exec app bash

# Atau jalankan langsung
docker compose exec app php artisan migrate
docker compose exec app php artisan view:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

---

## 2. TUJUAN PROYEK

Sistem manajemen permohonan layanan **Sub Domain, Hosting, dan VPS** untuk **UPT TIK Universitas Lampung (Unila)**.

### Fitur Utama:
- ✅ Formulir permohonan layanan (Domain, Hosting, VPS)
- ✅ Dual output form: **Paperless** (digital untuk TIK) & **Hardcopy PDF** (untuk Pimpinan/Dekan)
- ✅ Tracking pengajuan via nomor tiket
- ✅ Sistem autentikasi pengguna
- ✅ Audit trail untuk semua perubahan

---

## 3. ARSITEKTUR DATABASE

### PostgreSQL Schemas:
```
├── akun          → User & Role management
├── referensi     → Master data (kategori, unit, jenis layanan, status)
├── transaksi     → Data pengajuan (header & detail)
└── audit         → Log perubahan status
```

### Tabel Utama:

| Schema | Tabel | Deskripsi |
|--------|-------|-----------|
| `akun` | `pengguna` | Data user (UUID, nm, usn, email, kata_sandi, peran_uuid) |
| `akun` | `peran` | Role/peran user |
| `akun` | `pemetaan_peran_sso` | Mapping SSO attribute ke role |
| `referensi` | `kategori_unit` | Kategori unit kerja (Fakultas, UPT, dll) |
| `referensi` | `unit_kerja` | Unit kerja (nm_lmbg, kode_unit, kategori_uuid) |
| `referensi` | `jenis_layanan` | Tipe layanan (domain, hosting, vps) |
| `referensi` | `status_pengajuan` | Status (Draft, Diajukan, Diverifikasi, Diproses, Selesai, Ditolak) |
| `transaksi` | `pengajuan` | Header pengajuan (no_tiket, pengguna_uuid, unit_kerja_uuid, jenis_layanan_uuid, status_uuid) |
| `transaksi` | `rincian_pengajuan` | Detail pengajuan (nm_domain, kapasitas_penyimpanan, keterangan_keperluan) |
| `audit` | `riwayat_pengajuan` | Log perubahan status |

### Konvensi Database:
- **Primary Key:** `UUID` (bukan `id`)
- **Timestamps:** `create_at` dan `last_update` (bukan Laravel default `created_at`/`updated_at`)
- **Naming:** Bahasa Indonesia (nm_lmbg, no_tiket, kata_sandi, dll)
- **Soft Delete:** `delete_at` (nullable timestamp)
- **UUID Extension:** `gen_random_uuid()` dari PostgreSQL

### Full Audit Columns (pada sebagian besar tabel):
```sql
create_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
last_update     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE
last_sync       TIMESTAMP NULLABLE
delete_at       TIMESTAMP NULLABLE
expired_date    TIMESTAMP NULLABLE
id_creator      UUID NULLABLE (FK ke akun.pengguna)
id_updater      UUID NULLABLE (FK ke akun.pengguna)
```

---

## 4. STRUKTUR MODEL LARAVEL

### Model → Table Mapping:

| Model | Table | Primary Key |
|-------|-------|-------------|
| `User` | `akun.pengguna` | UUID |
| `Peran` | `akun.peran` | UUID |
| `UnitCategory` | `referensi.kategori_unit` | UUID |
| `Unit` | `referensi.unit_kerja` | UUID |
| `JenisLayanan` | `referensi.jenis_layanan` | UUID |
| `StatusPengajuan` | `referensi.status_pengajuan` | UUID |
| `Submission` | `transaksi.pengajuan` | UUID |
| `SubmissionDetail` | `transaksi.rincian_pengajuan` | UUID |
| `SubmissionLog` | `audit.riwayat_pengajuan` | UUID |

### Konfigurasi Model Penting:
```php
<?php
// Semua model dengan UUID harus punya konfigurasi ini:

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExampleModel extends Model
{
    use HasUuids;
    
    protected $table = 'schema.table_name';
    protected $primaryKey = 'UUID';
    protected $keyType = 'string';
    public $incrementing = false;
    
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'last_update';
}
```

### Relationships (Submission Model):
```php
$submission->pengguna()      → BelongsTo User
$submission->unitKerja()     → BelongsTo Unit
$submission->jenisLayanan()  → BelongsTo JenisLayanan
$submission->status()        → BelongsTo StatusPengajuan
$submission->rincian()       → HasOne SubmissionDetail
$submission->riwayat()       → HasMany SubmissionLog
$submission->creator()       → BelongsTo User (id_creator)
$submission->updater()       → BelongsTo User (id_updater)
```

---

## 5. ROUTING STRUCTURE

### File: `routes/web.php`

```php
// ==========================================
// PUBLIC ROUTES (Bisa diakses tanpa login)
// ==========================================
GET  /                                      → Home page (3 service cards)
GET  /form/{ticketNumber}                   → Select form type (forms.select)
GET  /form/{ticketNumber}/paperless         → View paperless form (forms.paperless)
GET  /form/{ticketNumber}/hardcopy/preview  → Preview PDF (forms.hardcopy.preview)
GET  /form/{ticketNumber}/hardcopy/download → Download PDF (forms.hardcopy.download)

// ==========================================
// GUEST ONLY (Hanya untuk yang belum login)
// ==========================================
GET  /login                                 → Login page (login)
POST /login                                 → Login action (login.store)

// ==========================================
// AUTHENTICATED (Harus login dulu)
// ==========================================
POST /logout                                → Logout (logout)
GET  /dashboard                             → Dashboard (dashboard)

// --- Fitur Pengajuan ---
GET  /pengajuan/buat?type=domain            → Create form (submissions.create)
POST /pengajuan                             → Store submission (submissions.store)
GET  /pengajuan                             → List submissions (submissions.index)
GET  /pengajuan/{submission}                → Show detail (submissions.show)
GET  /pengajuan/{submission}/download-form  → Download form (submissions.download-form)
GET  /pengajuan/{submission}/print-form     → Print form (submissions.print-form)
GET  /pengajuan/{submission}/upload         → Upload page (submissions.upload)
POST /pengajuan/{submission}/upload         → Store upload (submissions.upload.store)

// --- Admin Routes ---
GET  /admin/users                           → User management (admin.users)
```

---

## 6. CONTROLLERS

### SubmissionController (`app/Http/Controllers/SubmissionController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /pengajuan | List pengajuan user yang login |
| `create(Request $request)` | GET /pengajuan/buat | Form pengajuan (param: `?type=domain\|hosting\|vps`) |
| `store(Request $request)` | POST /pengajuan | Simpan pengajuan + detail + log |
| `show($submission)` | GET /pengajuan/{id} | Detail pengajuan |
| `downloadForm($submission)` | GET /pengajuan/{id}/download-form | Download form PDF |
| `printForm($submission)` | GET /pengajuan/{id}/print-form | Print form |
| `showUpload($submission)` | GET /pengajuan/{id}/upload | Halaman upload dokumen |
| `storeUpload($submission)` | POST /pengajuan/{id}/upload | Simpan upload |

### FormGeneratorController (`app/Http/Controllers/FormGeneratorController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| `selectForm($ticketNumber)` | GET /form/{ticket} | Halaman pilih tipe form output |
| `showPaperless($ticketNumber)` | GET /form/{ticket}/paperless | Form digital (view di browser) |
| `previewHardcopy($ticketNumber)` | GET /form/{ticket}/hardcopy/preview | Preview PDF di browser |
| `downloadHardcopy($ticketNumber)` | GET /form/{ticket}/hardcopy/download | Download PDF |

### AuthController (`app/Http/Controllers/AuthController.php`)

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /login | Halaman login |
| `store(Request)` | POST /login | Proses login |
| `destroy()` | POST /logout | Logout |

---

## 7. VIEWS STRUCTURE

```
resources/views/
├── home.blade.php               → Landing page dengan 3 kartu layanan (Domain, Hosting, VPS)
├── dashboard.blade.php          → Dashboard setelah login
│
├── layouts/
│   └── app.blade.php            → Master layout (head, navbar, footer)
│
├── auth/
│   └── login.blade.php          → Form login
│
├── submissions/
│   ├── create.blade.php         → Form pengajuan (dynamic: domain/hosting/vps)
│   ├── index.blade.php          → Daftar pengajuan user
│   └── show.blade.php           → Detail pengajuan (belum dibuat)
│
├── forms/
│   ├── select-form.blade.php    → Pilih paperless atau hardcopy
│   ├── form-paperless.blade.php → Form digital (belum dibuat)
│   └── form-hardcopy.blade.php  → Template PDF (belum dibuat)
│
├── components/
│   └── icon.blade.php           → Reusable SVG icons (Heroicons)
│
└── partials/
    └── (navbar, footer, dll)
```

---

## 8. TIPE LAYANAN

### 1. Domain (Sub Domain)
- **Field:** `requested_domain` → hasil: `xxx.unila.ac.id`
- **Jenis Domain:**
  - `lembaga_fakultas` - Lembaga / Fakultas / Jurusan
  - `kegiatan_lembaga` - Kegiatan Lembaga / Fakultas / Jurusan
  - `organisasi_mahasiswa` - Organisasi Mahasiswa
  - `kegiatan_mahasiswa` - Kegiatan Mahasiswa
  - `lainnya` - Lain-lain

### 2. Hosting
- **Field:** `requested_domain` (nama akun hosting)
- **Field:** `hosting_quota` - Pilihan:
  - 500 MB
  - 1 GB (1000 MB)
  - 2 GB (2000 MB)
  - 5 GB (5000 MB)

### 3. VPS (Virtual Private Server)
- **Field:** `requested_domain` (hostname VPS)
- **Field:** `vps_cpu` - Pilihan: 1, 2, 4 Core
- **Field:** `vps_ram` - Pilihan: 1, 2, 4, 8 GB
- **Field:** `vps_storage` - Pilihan: 20, 40, 80, 100 GB
- **Field:** `vps_os` - Pilihan:
  - Ubuntu 22.04 LTS
  - Ubuntu 20.04 LTS
  - CentOS 8
  - Debian 11
- **Field:** `vps_purpose` (tujuan penggunaan - textarea)

---

## 9. FORM FIELDS (create.blade.php)

### Section 1: Data Sub Domain
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `jenis_domain` | radio | ✅ | 5 pilihan jenis domain |
| `nama_organisasi` | text | ✅ | Nama lembaga/organisasi/kegiatan |

### Section 2: Penanggung Jawab Administratif
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `admin_responsible_name` | text | ✅ | Nama pejabat |
| `admin_responsible_position` | text | ✅ | Jabatan |
| `admin_responsible_nip` | text | ❌ | NIP/NPM |
| `admin_alamat_kantor` | text | ❌ | Alamat kantor |
| `admin_alamat_rumah` | text | ❌ | Alamat rumah |
| `admin_telepon_kantor` | tel | ❌ | Telepon kantor |
| `admin_responsible_phone` | tel | ✅ | HP |
| `admin_email` | email | ✅ | Email |

### Section 3: Penanggung Jawab Teknis
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `tech_name` | text | ✅ | Nama pengelola teknis |
| `tech_nip` | text | ✅ | NIP/NIM |
| `tech_phone` | tel | ✅ | Telepon |
| `tech_alamat_kantor` | text | ❌ | Alamat kantor |
| `tech_alamat_rumah` | text | ❌ | Alamat rumah |
| `tech_email` | email | ✅ | Email |

### Section 4: Data Layanan
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `requested_domain` | text | ✅ | Min 2, Max 12 char, lowercase+number+dash |
| `admin_password` | text | ✅ | Password hint (6-8 char) |
| `vps_cpu` | select | VPS only | 1, 2, 4 Core |
| `vps_ram` | select | VPS only | 1, 2, 4, 8 GB |
| `vps_storage` | select | VPS only | 20, 40, 80, 100 GB |
| `vps_os` | select | VPS only | Ubuntu/CentOS/Debian |
| `vps_purpose` | textarea | VPS only | Tujuan penggunaan |
| `hosting_quota` | select | Hosting only | 500MB - 5GB |

### Hidden Fields (for DB compatibility):
- `unit_id`
- `application_name`
- `description`
- `request_type` (domain/hosting/vps)

---

## 10. PACKAGE DEPENDENCIES

### composer.json
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "barryvdh/laravel-dompdf": "^3.x"
    }
}
```

### DomPDF Configuration
```php
// config/dompdf.php sudah di-publish
// Storage: storage/app/dompdf/
```

---

## 11. TICKET NUMBER FORMAT

```
TIK-YYYYMMDD-XXXX

Contoh: TIK-20260125-A1B2
```

**Generator Method:**
```php
// app/Models/Submission.php
public static function generateTicketNumber(): string
{
    $prefix = 'TIK';
    $date = now()->format('Ymd');
    $random = strtoupper(substr(md5(uniqid()), 0, 4));
    
    return "{$prefix}-{$date}-{$random}";
}
```

---

## 12. DATA STORAGE

### Keterangan Keperluan (JSON)
Form submission menyimpan data extended sebagai JSON di kolom `keterangan_keperluan`:

```json
{
    "jenis_domain": "lembaga_fakultas",
    "nama_organisasi": "Fakultas Teknik",
    "admin": {
        "name": "Dr. Ahmad",
        "position": "Dekan",
        "nip": "198501012010011001",
        "email": "ahmad@unila.ac.id",
        "phone": "081234567890"
    },
    "tech": {
        "name": "Budi",
        "nip": "2015001001",
        "email": "budi@students.unila.ac.id",
        "phone": "082345678901"
    },
    "password_hint": "pass123",
    "vps_specs": {
        "cpu": "2",
        "ram": "4",
        "storage": "40",
        "os": "ubuntu-22.04",
        "purpose": "Hosting aplikasi SIAKAD"
    }
}
```

---

## 13. CATATAN NAMA KOLOM PENTING

| Laravel Convention | Actual Column Name | Table |
|--------------------|-------------------|-------|
| `password` | `kata_sandi` | akun.pengguna |
| `name` | `nm` | akun.pengguna |
| `username` | `usn` | akun.pengguna |
| `created_at` | `create_at` | semua tabel |
| `updated_at` | `last_update` | semua tabel |
| `id` | `UUID` | semua tabel |
| `ticket_number` | `no_tiket` | transaksi.pengajuan |
| `domain_name` | `nm_domain` | transaksi.rincian_pengajuan |
| `storage_capacity` | `kapasitas_penyimpanan` | transaksi.rincian_pengajuan |
| `role_name` | `nm_peran` | akun.peran |
| `category_name` | `nm_kategori` | referensi.kategori_unit |
| `unit_name` | `nm_lmbg` | referensi.unit_kerja |
| `service_name` | `nm_layanan` | referensi.jenis_layanan |
| `status_name` | `nm_status` | referensi.status_pengajuan |

---

## 14. FLOW APLIKASI

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER FLOW                                │
└─────────────────────────────────────────────────────────────────┘

1. User mengakses halaman home (/)
   └── Melihat 3 kartu layanan: Domain, Hosting, VPS

2. User login (/login)
   └── Autentikasi dengan usn/email + kata_sandi

3. User pilih layanan dan klik "Ajukan Sekarang"
   └── Redirect ke /pengajuan/buat?type=domain (atau hosting/vps)

4. User mengisi form pengajuan
   └── Section 1: Data Sub Domain
   └── Section 2: Penanggung Jawab Administratif
   └── Section 3: Penanggung Jawab Teknis
   └── Section 4: Data Layanan (dynamic berdasarkan type)

5. User submit form (POST /pengajuan)
   └── Create: transaksi.pengajuan (header)
   └── Create: transaksi.rincian_pengajuan (detail)
   └── Create: audit.riwayat_pengajuan (log: "Draft")

6. Redirect ke halaman pilih form (/form/{no_tiket})
   └── User pilih: Paperless atau Hardcopy PDF

7a. Jika Paperless (/form/{no_tiket}/paperless)
    └── Tampilkan form digital yang bisa dicetak (Ctrl+P)

7b. Jika Hardcopy (/form/{no_tiket}/hardcopy/download)
    └── Generate dan download PDF

8. User mencetak form, minta tanda tangan pejabat

9. User upload scan form yang sudah ditandatangani
   └── /pengajuan/{id}/upload

10. Admin memproses pengajuan
    └── Status berubah: Draft → Diajukan → Diverifikasi → Diproses → Selesai
```

---

## 15. ISSUE/BUG YANG SUDAH DIPERBAIKI

| Issue | Solusi |
|-------|--------|
| Model table mismatch | Model awalnya pakai nama tabel English, sudah diubah ke Indonesian sesuai migration |
| UUID Primary Key | Semua model sudah pakai `HasUuids` trait dengan `$primaryKey = 'UUID'` |
| Timestamp columns | Custom `CREATED_AT` dan `UPDATED_AT` constants di setiap model |
| Icon component missing | Dibuat `app/View/Components/Icon.php` + `resources/views/components/icon.blade.php` |
| File create.blade.php corrupt | Ada 200+ baris kosong + kode terbalik, sudah di-recreate |

---

## 16. PENDING TASKS

### High Priority:
- [ ] **Seed reference data:**
  - `referensi.jenis_layanan` (domain, hosting, vps)
  - `referensi.status_pengajuan` (Draft, Diajukan, Diverifikasi, Diproses, Selesai, Ditolak)
  - `referensi.kategori_unit` (Fakultas, UPT, Biro, dll)
  - `referensi.unit_kerja` (daftar unit kerja Unila)
  - `akun.peran` (Admin, User, dll)

- [ ] **Fix FormGeneratorController:** Method `showPaperless` & `downloadHardcopy` masih pakai `ticket_number` bukan `no_tiket`

- [ ] **Create form templates:**
  - `forms/form-paperless.blade.php`
  - `forms/form-hardcopy.blade.php`

- [ ] **Create select-form view:** `forms/select-form.blade.php`

### Medium Priority:
- [ ] Implement file upload for signed documents
- [ ] Admin panel for managing submissions
- [ ] Email notifications

### Low Priority:
- [ ] SSO integration with Unila SSO
- [ ] API endpoints for mobile app
- [ ] Dashboard statistics

---

## 17. FILE STRUCTURE

```
domaintik/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── FormGeneratorController.php
│   │       └── SubmissionController.php
│   ├── Models/
│   │   ├── JenisLayanan.php
│   │   ├── Peran.php
│   │   ├── StatusPengajuan.php
│   │   ├── Submission.php
│   │   ├── SubmissionDetail.php
│   │   ├── SubmissionLog.php
│   │   ├── Unit.php
│   │   ├── UnitCategory.php
│   │   └── User.php
│   └── View/
│       └── Components/
│           └── Icon.php
├── config/
│   └── dompdf.php
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 0001_01_01_000001_create_cache_table.php
│       ├── 0001_01_01_000002_create_jobs_table.php
│       ├── 2026_01_11_160603_setup_schemas.php
│       ├── 2026_01_15_083058_crete_master_tables.php
│       └── 2026_01_15_083246_create_transaction_tables.php
├── resources/
│   └── views/
│       ├── auth/
│       ├── components/
│       ├── forms/
│       ├── layouts/
│       ├── partials/
│       ├── submissions/
│       ├── dashboard.blade.php
│       └── home.blade.php
├── routes/
│   └── web.php
├── docker-compose.yml
├── Dockerfile
└── docker-entrypoint.sh
```

---

## 18. ENVIRONMENT VARIABLES

```env
APP_NAME=Domaintik
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=domaintik
DB_USERNAME=postgres
DB_PASSWORD=secret
```

---

## 19. REFERENSI

- Laravel 12.x Documentation: https://laravel.com/docs/12.x
- DomPDF Package: https://github.com/barryvdh/laravel-dompdf
- Heroicons: https://heroicons.com/
- TailwindCSS: https://tailwindcss.com/

---


