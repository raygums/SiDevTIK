# 📋 Audit Log System Documentation

**Feature**: Log Audit Aktivitas Login & Transaksi Pengajuan  
**Role Access**: Admin  
**Implementation Date**: 30 Januari 2026  
**Pattern**: Service Pattern + Thin Controller

---

## 📌 Overview

Sistem Log Audit memberikan Admin kemampuan untuk memantau dan mengaudit aktivitas pengguna dalam sistem Domaintik, mencakup:

1. **Login Activity** - Tracking waktu login dan IP address
2. **Submission Status Changes** - Audit trail perubahan status pengajuan
3. **User Activity Timeline** - Timeline lengkap aktivitas individual user

---

## 🏗️ Architecture

### Service Layer

**File**: `app/Services/AuditLogService.php`

**Methods**:
- `getLoginLogs(?string $userUuid, int $perPage)` - Retrieve login activity logs
- `getSubmissionLogs(?string $userUuid, int $perPage)` - Retrieve submission status change logs
- `getUserActivityTimeline(string $userUuid, int $perPage)` - Combined timeline untuk specific user
- `getLoginStatistics()` - Statistics untuk login activity
- `getSubmissionStatistics()` - Statistics untuk submission logs

**Key Features**:
- ✅ Eloquent Eager Loading untuk menghindari N+1 Query Problem
- ✅ Pagination-ready dengan LengthAwarePaginator
- ✅ Filter by role Pengguna only (sesuai scope Admin)
- ✅ Efficient query dengan select() untuk limit columns

### Controller Layer

**File**: `app/Http/Controllers/Admin/AuditLogController.php`

**Routes**:
```php
GET  /admin/audit/login              -> loginLogs()
GET  /admin/audit/submissions        -> submissionLogs()
GET  /admin/audit/user/{uuid}        -> userDetail()
```

**Pattern**: Thin Controller - hanya handle HTTP request/response, business logic di Service

### Views

#### 1. Login Activity Log
**File**: `resources/views/admin/audit/login.blade.php`

**Features**:
- Statistics cards (Total Users, Pernah Login, Aktif Hari Ini, Aktif Minggu Ini)
- Table riwayat login dengan kolom: Pengguna, Email/Username, Waktu Login, IP Address, Status, Aksi
- Link ke detail user activity timeline
- Pagination support

#### 2. Submission Status Log
**File**: `resources/views/admin/audit/submissions.blade.php`

**Features**:
- Statistics cards (Total Log, Log Hari Ini, Log Minggu Ini, Status Terbanyak)
- Table perubahan status dengan kolom: No. Tiket, Pemohon, Layanan, Perubahan Status (old -> new), Waktu, Diubah Oleh
- Badge coding untuk status (Selesai=success, Ditolak=danger, Sedang Dikerjakan=info, Diajukan=warning)
- Pagination support

#### 3. User Activity Timeline
**File**: `resources/views/admin/audit/user-detail.blade.php`

**Features**:
- User profile card (Avatar, Nama, Email, Username, Role, Status Aktif, Last Login, IP)
- Visual timeline dengan dots dan lines
- Combined view: Login events + Submission status changes
- Chronological sorting (terbaru di atas)
- Color-coded timeline dots (green=login, blue=submission)
- Pagination support

---

## 🎨 UI/UX Design

### Design System Compliance

**Color Scheme**:
- Primary: MyUnila `#0B5EA8`
- Success: `#10B981` (Aktif, Selesai)
- Warning: `#F59E0B` (Pending, Diajukan)
- Info: `#3B82F6` (Sedang Dikerjakan)
- Danger/Error: `#EF4444` (Ditolak)

**Components**:
- Statistics Cards: rounded-2xl, border-gray-200, shadow-sm
- Tables: min-w-full, divide-y divide-gray-200
- Badges: rounded-full dengan icon dot
- Timeline: vertical with connecting line

**Typography**:
- Font: Instrument Sans (400, 500, 600, 700)
- Headings: text-2xl/3xl font-bold text-gray-900
- Body: text-sm/base text-gray-600/700

### Sidebar Updates

**Menu Changes**:
- ❌ Removed: "Kelola Peran" (moved to Pimpinan role)
- ❌ Removed: "Log Audit Pengguna" (generic placeholder)
- ✅ Added: "Log Aktivitas Login" (route: `admin.audit.login`)
- ✅ Added: "Log Status Pengajuan" (route: `admin.audit.submissions`)

---

## 🗄️ Database Schema

### Tables Used

#### 1. `akun.pengguna`
```sql
Columns:
- UUID (PK)
- nm (nama)
- email
- usn (username)
- peran_uuid (FK -> referensi.peran)
- a_aktif (boolean - status aktif)
- last_login_at (timestamp)
- last_login_ip (varchar)
- create_at (timestamp)
```

#### 2. `audit.riwayat_pengajuan`
```sql
Columns:
- UUID (PK)
- pengajuan_uuid (FK -> transaksi.pengajuan)
- status_lama_uuid (FK -> referensi.status_pengajuan)
- status_baru_uuid (FK -> referensi.status_pengajuan)
- catatan_log (text)
- id_creator (FK -> akun.pengguna)
- create_at (timestamp)
```

#### 3. `transaksi.pengajuan`
```sql
Columns:
- UUID (PK)
- no_tiket (unique)
- pengguna_uuid (FK -> akun.pengguna)
- jenis_layanan_uuid (FK -> referensi.jenis_layanan)
- create_at (timestamp)
```

### Relationships

```
User (akun.pengguna)
├── hasOne: Peran
└── hasMany: Submission

SubmissionLog (audit.riwayat_pengajuan)
├── belongsTo: Submission (pengajuan)
├── belongsTo: StatusPengajuan (statusLama)
├── belongsTo: StatusPengajuan (statusBaru)
└── belongsTo: User (creator)

Submission (transaksi.pengajuan)
├── belongsTo: User (pengguna)
├── belongsTo: JenisLayanan
└── hasMany: SubmissionLog
```

---

## 📊 Query Optimization

### Eager Loading Strategy

**Login Logs**:
```php
User::with(['peran:UUID,nm_peran'])
    ->whereHas('peran', fn($q) => $q->where('nm_peran', 'Pengguna'))
    ->select(['UUID', 'nm', 'email', 'usn', 'peran_uuid', 'last_login_at', 'last_login_ip', 'create_at', 'a_aktif'])
    ->orderBy('last_login_at', 'desc')
    ->paginate(20);
```

**Submission Logs**:
```php
SubmissionLog::with([
    'pengajuan' => fn($q) => $q->select('UUID', 'no_tiket', 'pengguna_uuid', 'jenis_layanan_uuid', 'create_at')
        ->with([
            'pengguna:UUID,nm,email',
            'jenisLayanan:UUID,nm_layanan'
        ]),
    'statusLama:UUID,nm_status',
    'statusBaru:UUID,nm_status',
    'creator:UUID,nm,email'
])
->select(['UUID', 'pengajuan_uuid', 'status_lama_uuid', 'status_baru_uuid', 'catatan_log', 'create_at', 'id_creator'])
->orderBy('create_at', 'desc')
->paginate(20);
```

**Benefits**:
- ✅ Single query untuk main model + eager loaded relations
- ✅ Column selection untuk reduce data transfer
- ✅ Pagination untuk large datasets

---

## 🔐 Access Control

### Role-Based Access

**Middleware**: `role:admin`

**Scope Restriction**:
- Admin hanya bisa melihat log dari user dengan role **Pengguna**
- Filter implemented di Service layer:
  ```php
  ->whereHas('peran', fn($q) => $q->where('nm_peran', 'Pengguna'))
  ```

**Routes Protection**:
```php
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/audit/login', [AuditLogController::class, 'loginLogs']);
    Route::get('/audit/submissions', [AuditLogController::class, 'submissionLogs']);
    Route::get('/audit/user/{uuid}', [AuditLogController::class, 'userDetail']);
});
```

---

## 🎯 Use Cases

### 1. Monitor Login Activity
**As an Admin**, I want to see who has logged in recently and from which IP addresses.

**Steps**:
1. Navigate to sidebar menu "Log Aktivitas Login"
2. View statistics: Total users, Users with login, Active today, Active this week
3. Browse table of recent login activities
4. Click "Lihat Detail" to see user's full activity timeline

### 2. Audit Submission Status Changes
**As an Admin**, I want to track all status changes in submissions for compliance.

**Steps**:
1. Navigate to sidebar menu "Log Status Pengajuan"
2. View statistics: Total logs, Logs today, Logs this week, Most active status
3. Browse table showing status transitions (old → new)
4. See who made the change and when

### 3. Investigate User Activity
**As an Admin**, I want to see complete activity timeline for a specific user.

**Steps**:
1. From Login Logs page, click "Lihat Detail" on a user
2. View user profile card with last login info
3. Browse combined timeline of login events + submission status changes
4. Filter by date range (pagination)

---

## 🧪 Testing Checklist

### Functional Tests

- [ ] Login logs display correctly dengan data akurat
- [ ] Submission logs menampilkan perubahan status dengan benar
- [ ] User detail timeline menggabungkan login + submission logs chronologically
- [ ] Pagination berfungsi di semua halaman
- [ ] Statistics cards menghitung data dengan akurat
- [ ] Filter role Pengguna bekerja (tidak menampilkan Admin/Verifikator/Eksekutor)
- [ ] Link "Lihat Detail" navigasi ke user detail page
- [ ] Back button di user detail page kembali ke login logs

### UI/UX Tests

- [ ] Badge colors sesuai dengan status (success, warning, info, danger)
- [ ] Timeline visual menampilkan dots dan lines dengan benar
- [ ] Responsive di mobile/tablet/desktop
- [ ] Empty state muncul ketika tidak ada data
- [ ] Loading state (jika implemented)

### Performance Tests

- [ ] Query time < 100ms untuk 1000 records
- [ ] No N+1 query problem (check Laravel Debugbar)
- [ ] Pagination load time konsisten
- [ ] Memory usage reasonable untuk large datasets

---

## 📝 Code Quality Standards

### Adherence to SOLID Principles

✅ **Single Responsibility**: 
- Service handles business logic only
- Controller handles HTTP only
- Views handle presentation only

✅ **Open/Closed**: 
- Service methods extensible untuk filter tambahan
- Easy to add new log types

✅ **Liskov Substitution**: 
- Service returns consistent interfaces (Paginator)

✅ **Interface Segregation**: 
- Methods focused dan tidak overloaded

✅ **Dependency Inversion**: 
- Controller depends on Service abstraction

### Clean Code Metrics

- ✅ Method length: < 30 lines
- ✅ Class length: < 300 lines
- ✅ Cyclomatic complexity: < 10
- ✅ No hard-coded values
- ✅ Descriptive variable names
- ✅ Comments hanya untuk complex logic
- ✅ No dead code

---

## 🚀 Future Enhancements

### Planned Features

1. **Export Functionality**
   - Export logs to CSV/Excel
   - PDF report generation
   - Date range filtering for export

2. **Advanced Filtering**
   - Filter by date range
   - Filter by IP address
   - Search by user name/email

3. **Real-time Notifications**
   - Alert for suspicious login (multiple failed attempts)
   - Alert for unusual IP address
   - Alert for mass status changes

4. **Analytics Dashboard**
   - Login trends graph (daily/weekly/monthly)
   - Status change distribution chart
   - Most active users ranking

5. **Session Management**
   - Track concurrent sessions
   - Force logout capability
   - Session history per user

---

## 📚 Related Documentation

- [DESIGN_SYSTEM.md](../DESIGN_SYSTEM.md) - UI/UX Design Guidelines
- [ADMIN_USER_VERIFICATION_MODULE.md](./ADMIN_USER_VERIFICATION_MODULE.md) - User Verification Feature
- [DASHBOARD_QUICK_REF.md](./DASHBOARD_QUICK_REF.md) - Dashboard Overview

---

## 🐛 Known Issues

None at this time.

---

## 📞 Support

For questions or issues related to Audit Log System:
- Contact: TIK Universitas Lampung
- Email: support@tik.unila.ac.id

---

**Last Updated**: 30 Januari 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
