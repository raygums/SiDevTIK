# 📋 Dokumentasi Implementasi: Login History Tracking System

> **Domain TIK - Laravel 12 + PostgreSQL**  
> **Version:** 2.0.0  
> **Date:** 2026-02-03  
> **Author:** Domain TIK Development Team

---

## 📑 Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Database Schema](#database-schema)
4. [Implementation Guide](#implementation-guide)
5. [API Reference](#api-reference)
6. [Query Examples](#query-examples)
7. [Security Features](#security-features)
8. [Performance Optimization](#performance-optimization)
9. [Testing Guide](#testing-guide)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

### Tujuan

Sistem login history tracking ini dirancang untuk:

- ✅ **Audit Trail Lengkap**: Mencatat setiap percobaan login (berhasil/gagal)
- ✅ **Security Monitoring**: Deteksi aktivitas mencurigakan dan brute force attacks
- ✅ **Compliance**: Memenuhi requirement audit keamanan untuk sistem pemerintah
- ✅ **User Activity Analysis**: Timeline lengkap aktivitas user untuk Admin/Pimpinan
- ✅ **Forensic Investigation**: Data lengkap untuk investigasi insiden keamanan

### Fitur Utama

| Feature | Description | Status |
|---------|-------------|--------|
| Full Login History | Semua login attempt tersimpan permanent | ✅ Implemented |
| Multiple Status Tracking | BERHASIL, GAGAL_PASSWORD, GAGAL_SUSPEND, dll | ✅ Implemented |
| IP Address Logging | IPv4 & IPv6 support dengan sanitasi | ✅ Implemented |
| User Agent Detection | Browser, OS, device info | ✅ Implemented |
| Failed Login Tracking | Password salah, akun suspended, user not found | ✅ Implemented |
| SSO Integration | Support login via SSO Unila | ✅ Implemented |
| Brute Force Detection | Monitoring suspicious IP activity | ✅ Implemented |
| Advanced Filtering | Search, date range, status, IP, dll | ✅ Implemented |
| Statistics Dashboard | Metrics dan charts untuk monitoring | ✅ Implemented |
| User Timeline | Combined login & submission activity | ✅ Implemented |

---

## 🏗️ Architecture

### System Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    USER AUTHENTICATION                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐         ┌──────────────┐                 │
│  │   Login via  │         │  Login via   │                 │
│  │   Username   │         │  SSO Unila   │                 │
│  │   Password   │         │   (LDAP)     │                 │
│  └──────┬───────┘         └──────┬───────┘                 │
│         │                        │                          │
│         ▼                        ▼                          │
│  ┌──────────────────────────────────────┐                  │
│  │      AuthController                   │                  │
│  │      SSOController                    │                  │
│  └──────────┬───────────────────────────┘                  │
│             │                                               │
│             │  Inject AuditLogService                       │
│             ▼                                               │
│  ┌──────────────────────────────────────┐                  │
│  │    AuditLogService                    │                  │
│  │    ├─ recordLoginLog()                │                  │
│  │    ├─ getLoginHistory()               │                  │
│  │    ├─ getLoginStatisticsNew()         │                  │
│  │    └─ getUserActivityTimeline()       │                  │
│  └──────────┬───────────────────────────┘                  │
│             │                                               │
│             │  Save to Database                             │
│             ▼                                               │
│  ┌──────────────────────────────────────┐                  │
│  │   LoginLog Model                      │                  │
│  │   (Eloquent ORM)                      │                  │
│  └──────────┬───────────────────────────┘                  │
│             │                                               │
│             ▼                                               │
│  ┌──────────────────────────────────────┐                  │
│  │   PostgreSQL Database                 │                  │
│  │   Schema: audit                       │                  │
│  │   Table: riwayat_login                │                  │
│  │   ├─ UUID (PK)                        │                  │
│  │   ├─ pengguna_uuid (FK)               │                  │
│  │   ├─ alamat_ip                        │                  │
│  │   ├─ perangkat (user agent)           │                  │
│  │   ├─ status_akses (enum)              │                  │
│  │   ├─ keterangan                       │                  │
│  │   └─ create_at                        │                  │
│  └───────────────────────────────────────┘                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Files Created

| File | Purpose | Lines |
|------|---------|-------|
| `database/migrations/2026_02_03_100000_create_riwayat_login_table.php` | Database schema | 230 |
| `app/Models/LoginLog.php` | Eloquent model | 380 |
| `app/Services/AuditLogService.php` | Business logic (updated) | 822 |
| `database/seeders/LoginHistorySeeder.php` | Test data generator | 420 |
| `docs/LOGIN_TRACKING_IMPLEMENTATION_EXAMPLES.php` | Integration examples | 550 |
| `docs/LOGIN_TRACKING_DOCUMENTATION.md` | This file | N/A |

**Total:** ~2,400 lines of production-ready code

---

## 💾 Database Schema

### Table: `audit.riwayat_login`

```sql
CREATE TABLE audit.riwayat_login (
    "UUID" UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pengguna_uuid UUID REFERENCES akun.pengguna(UUID) ON DELETE SET NULL,
    alamat_ip VARCHAR(45),
    perangkat TEXT,
    status_akses VARCHAR(30) NOT NULL,
    keterangan TEXT,
    create_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Indexes untuk performance
CREATE INDEX idx_riwayat_login_pengguna ON audit.riwayat_login(pengguna_uuid);
CREATE INDEX idx_riwayat_login_waktu ON audit.riwayat_login(create_at);
CREATE INDEX idx_riwayat_login_user_waktu ON audit.riwayat_login(pengguna_uuid, create_at);
CREATE INDEX idx_riwayat_login_status ON audit.riwayat_login(status_akses);
CREATE INDEX idx_riwayat_login_ip ON audit.riwayat_login(alamat_ip);
```

### Column Details

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `UUID` | UUID | No | Primary key, auto-generated |
| `pengguna_uuid` | UUID | Yes | Foreign key ke `akun.pengguna` (NULL jika user deleted) |
| `alamat_ip` | VARCHAR(45) | Yes | IP address (IPv4/IPv6) |
| `perangkat` | TEXT | Yes | User agent string (browser, OS, device) |
| `status_akses` | VARCHAR(30) | No | Status login (enum values) |
| `keterangan` | TEXT | Yes | Additional notes/error message |
| `create_at` | TIMESTAMP | No | Timestamp login attempt (indexed) |

### Status Akses Values

| Status | Deskripsi | Use Case |
|--------|-----------|----------|
| `BERHASIL` | Login sukses | Normal successful login |
| `GAGAL_PASSWORD` | Password salah | Wrong password entered |
| `GAGAL_SUSPEND` | Akun suspended/tidak aktif | User account disabled by admin |
| `GAGAL_NOT_FOUND` | User tidak ditemukan | Username doesn't exist in database |
| `GAGAL_SSO` | SSO authentication failed | SSO token invalid or expired |

### Performance Considerations

- **5 Indexes** untuk query optimization
- **UUID Primary Key** untuk distributed systems
- **ON DELETE SET NULL** untuk preserve audit trail (jika user dihapus, log tetap ada)
- **No updated_at** karena log bersifat immutable (write-once, read-many)
- **Partitioning Ready** (bisa di-partition by month untuk jutaan records)

---

## 📝 Implementation Guide

### Step 1: Run Migration

```bash
# Docker environment
docker exec -it domaintik-app bash
php artisan migrate

# Verify table created
php artisan tinker
>>> DB::select("SELECT * FROM audit.riwayat_login LIMIT 1");
```

### Step 2: Integrate ke AuthController

**File:** `app/Http/Controllers/AuthController.php`

```php
use App\Services\AuditLogService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuditLogService $auditLogService // TAMBAHKAN
    ) {}

    public function store(Request $request): RedirectResponse
    {
        // ... validation code ...

        // User tidak ditemukan
        if (!$user) {
            $this->auditLogService->recordLoginLog(
                userUuid: null,
                status: 'GAGAL_NOT_FOUND',
                request: $request,
                keterangan: "Username '{$credentials['username']}' tidak terdaftar"
            );
            return back()->withErrors([...]);
        }

        // Password salah
        if (!Hash::check($credentials['password'], $user->kata_sandi)) {
            $this->auditLogService->recordLoginLog(
                userUuid: $user->UUID,
                status: 'GAGAL_PASSWORD',
                request: $request,
                keterangan: "Password salah untuk user '{$user->usn}'"
            );
            return back()->withErrors([...]);
        }

        // Akun suspended
        if (!$user->a_aktif) {
            $this->auditLogService->recordLoginLog(
                userUuid: $user->UUID,
                status: 'GAGAL_SUSPEND',
                request: $request,
                keterangan: "Akun tidak aktif"
            );
            return back()->withErrors([...]);
        }

        // Login berhasil
        Auth::login($user);
        $this->auditLogService->recordLoginLog(
            userUuid: $user->UUID,
            status: 'BERHASIL',
            request: $request,
            keterangan: "Login berhasil via local authentication"
        );

        return redirect()->route('dashboard');
    }
}
```

### Step 3: Integrate ke SSO Controller

```php
public function handleCallback(Request $request)
{
    try {
        // ... SSO logic ...
        
        // SSO berhasil
        $this->auditLogService->recordLoginLog(
            userUuid: $user->UUID,
            status: 'BERHASIL',
            request: $request,
            keterangan: "Login berhasil via SSO Unila - NIP: {$user->nip}"
        );

        return redirect()->route('dashboard');
        
    } catch (\Exception $e) {
        // SSO gagal
        $this->auditLogService->recordLoginLog(
            userUuid: null,
            status: 'GAGAL_SSO',
            request: $request,
            keterangan: "SSO failed: " . $e->getMessage()
        );

        return redirect()->route('login')->withErrors([...]);
    }
}
```

### Step 4: Generate Test Data

```bash
# Generate login history test data
php artisan db:seed --class=LoginHistorySeeder

# Output:
# ✓ Generated 300+ normal successful logins
# ✓ Generated 50+ failed password attempts
# ✓ Generated suspicious brute force attempts
# Total: 500+ login attempts
```

### Step 5: Query Login History

```php
use App\Services\AuditLogService;

// Di Controller
$auditService = app(AuditLogService::class);

// Get all login history
$logs = $auditService->getLoginHistory(perPage: 50);

// Get statistics
$stats = $auditService->getLoginStatisticsNew();

// Get user timeline
$timeline = $auditService->getUserActivityTimeline($userUuid);
```

---

## 📚 API Reference

### AuditLogService Methods

#### `recordLoginLog()`

Record login attempt ke database.

```php
public function recordLoginLog(
    ?string $userUuid,
    string $status,
    ?\Illuminate\Http\Request $request = null,
    ?string $keterangan = null,
    ?string $customIp = null,
    ?string $customUserAgent = null
): ?LoginLog
```

**Parameters:**

- `$userUuid` (string|null): UUID user (null jika user tidak ditemukan)
- `$status` (string): Status akses (`BERHASIL`, `GAGAL_PASSWORD`, dll)
- `$request` (Request|null): Request object untuk extract IP dan User Agent
- `$keterangan` (string|null): Detail tambahan (error message)
- `$customIp` (string|null): Override IP address (optional)
- `$customUserAgent` (string|null): Override user agent (optional)

**Returns:** `LoginLog` model instance atau `null` jika gagal

**Example:**

```php
$log = $auditService->recordLoginLog(
    userUuid: $user->UUID,
    status: 'BERHASIL',
    request: $request,
    keterangan: 'Login via SSO'
);
```

---

#### `getLoginHistory()`

Query login history dengan filtering dan pagination.

```php
public function getLoginHistory(
    array $filters = [],
    int $perPage = 20
): LengthAwarePaginator
```

**Filter Options:**

```php
$filters = [
    'search' => 'john',                 // Search by name/email/username
    'user_uuid' => 'xxx-xxx-xxx',       // Specific user
    'status' => 'BERHASIL',             // Specific status
    'only_successful' => true,          // Only successful logins
    'only_failed' => true,              // Only failed logins
    'ip_address' => '192.168.1.1',      // Specific IP
    'date_from' => '2026-01-01',        // Start date
    'date_to' => '2026-01-31',          // End date
    'period' => 'today',                // today|yesterday|this_week|this_month|last_7_days|last_30_days
];
```

**Returns:** `LengthAwarePaginator` dengan eager loaded relations

**Example:**

```php
// Today's successful logins
$logs = $auditService->getLoginHistory([
    'period' => 'today',
    'only_successful' => true
], perPage: 50);

foreach ($logs as $log) {
    echo $log->pengguna->nm;        // User name
    echo $log->alamat_ip;           // IP address
    echo $log->getStatusLabel();    // "Login Berhasil"
    echo $log->getDeviceInfo();     // "Chrome on Windows"
}
```

---

#### `getLoginStatisticsNew()`

Get comprehensive statistics untuk dashboard.

```php
public function getLoginStatisticsNew(
    array $filters = []
): array
```

**Returns:**

```php
[
    'total_attempts' => 1250,
    'successful_logins' => 1100,
    'failed_logins' => 150,
    'unique_users' => 85,
    'success_rate' => 88.00,
    'today_attempts' => 45,
    'today_successful' => 42,
    'week_attempts' => 320,
    'failed_breakdown' => [
        ['status_akses' => 'GAGAL_PASSWORD', 'total' => 120],
        ['status_akses' => 'GAGAL_SUSPEND', 'total' => 25],
        // ...
    ],
    'top_ips' => [
        ['alamat_ip' => '192.168.1.100', 'total' => 150],
        // ...
    ],
]
```

**Example:**

```php
// Last month statistics
$stats = $auditService->getLoginStatisticsNew([
    'date_from' => now()->subMonth(),
    'date_to' => now(),
]);

echo "Success Rate: {$stats['success_rate']}%";
echo "Failed Logins: {$stats['failed_logins']}";
```

---

#### `getUserActivityTimeline()`

Combined timeline: login history + submission logs.

```php
public function getUserActivityTimeline(
    string $userUuid,
    int $perPage = 20
): array
```

**Returns:**

```php
[
    'user' => User model,
    'timeline' => LengthAwarePaginator [
        [
            'type' => 'login',
            'timestamp' => Carbon,
            'ip_address' => '192.168.1.1',
            'data' => [
                'status' => 'BERHASIL',
                'status_label' => 'Login Berhasil',
                'is_successful' => true,
                // ...
            ]
        ],
        [
            'type' => 'submission_status',
            'timestamp' => Carbon,
            'data' => [
                'ticket_number' => 'TIK-2026-001',
                'status_old' => 'Diajukan',
                'status_new' => 'Disetujui',
                // ...
            ]
        ],
    ]
]
```

---

### LoginLog Model Methods

#### Query Scopes

```php
// By user
LoginLog::byUser($userUuid)->get();

// By status
LoginLog::byStatus('BERHASIL')->get();
LoginLog::successful()->get();
LoginLog::failed()->get();

// Recent logs
LoginLog::recent()->take(100)->get();

// Date range
LoginLog::dateRange('2026-01-01', '2026-01-31')->get();

// From specific IP
LoginLog::fromIp('192.168.1.1')->get();

// Today/this week
LoginLog::today()->get();
LoginLog::thisWeek()->get();
```

#### Helper Methods

```php
$log = LoginLog::find($uuid);

// Check status
$log->isSuccessful();  // bool
$log->isFailed();      // bool

// Get labels
$log->getStatusLabel();    // "Login Berhasil"
$log->getDeviceInfo();     // "Chrome on Windows"
```

---

## 🔍 Query Examples

### Admin Dashboard: Recent Activity

```php
// Last 100 login attempts
$recentLogs = LoginLog::with('pengguna.peran')
    ->recent()
    ->take(100)
    ->get();
```

### Security Monitoring: Failed Logins Today

```php
$failedToday = LoginLog::today()
    ->failed()
    ->with('pengguna')
    ->get();

foreach ($failedToday as $log) {
    echo "[{$log->create_at}] {$log->alamat_ip} - {$log->getStatusLabel()}";
}
```

### Brute Force Detection

```php
// Find IPs with >10 failed attempts in last hour
$suspiciousIPs = DB::table('audit.riwayat_login')
    ->select('alamat_ip', DB::raw('COUNT(*) as attempts'))
    ->where('status_akses', '!=', 'BERHASIL')
    ->where('create_at', '>=', now()->subHour())
    ->groupBy('alamat_ip')
    ->having('attempts', '>', 10)
    ->orderByDesc('attempts')
    ->get();
```

### User-Specific Report

```php
// John's login history (last 30 days)
$userLogs = LoginLog::byUser($johnUuid)
    ->where('create_at', '>=', now()->subDays(30))
    ->orderBy('create_at', 'desc')
    ->get();

// Group by date
$byDate = $userLogs->groupBy(function($log) {
    return $log->create_at->format('Y-m-d');
});

foreach ($byDate as $date => $logs) {
    echo "$date: {$logs->count()} attempts";
}
```

---

## 🔒 Security Features

### 1. Input Sanitization

**IP Address:**
- Validasi IPv4/IPv6 format
- Batasi max 45 chars
- Strip dangerous characters

```php
private function sanitizeIpAddress(?string $ip): ?string
{
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return null;
    }
    return substr($ip, 0, 45);
}
```

**User Agent:**
- Strip HTML tags (prevent XSS)
- Remove control characters
- Batasi max 1000 chars

```php
private function sanitizeUserAgent(?string $userAgent): ?string
{
    $userAgent = strip_tags($userAgent);
    $userAgent = preg_replace('/[\x00-\x1F\x7F]/u', '', $userAgent);
    return substr($userAgent, 0, 1000);
}
```

### 2. Brute Force Protection

```php
// Check failed attempts from IP
$failedCount = LoginLog::fromIp($request->ip())
    ->where('create_at', '>=', now()->subMinutes(15))
    ->where('status_akses', '!=', 'BERHASIL')
    ->count();

if ($failedCount >= 5) {
    // Block IP
    Cache::put("blocked_ip:{$request->ip()}", true, now()->addHour());
}
```

### 3. Audit Trail Preservation

- **ON DELETE SET NULL** untuk foreign key
- Jika user dihapus, log tetap ada (pengguna_uuid jadi NULL)
- Log bersifat **immutable** (no updates/deletes)

---

## ⚡ Performance Optimization

### 1. Database Indexing

5 indexes untuk query cepat:

```sql
-- Single column indexes
CREATE INDEX idx_riwayat_login_pengguna ON audit.riwayat_login(pengguna_uuid);
CREATE INDEX idx_riwayat_login_waktu ON audit.riwayat_login(create_at);
CREATE INDEX idx_riwayat_login_status ON audit.riwayat_login(status_akses);
CREATE INDEX idx_riwayat_login_ip ON audit.riwayat_login(alamat_ip);

-- Composite index
CREATE INDEX idx_riwayat_login_user_waktu ON audit.riwayat_login(pengguna_uuid, create_at);
```

### 2. Eager Loading

Prevent N+1 query problem:

```php
// ❌ BAD: N+1 queries
$logs = LoginLog::all();
foreach ($logs as $log) {
    echo $log->pengguna->nm; // Query per iteration
}

// ✅ GOOD: Single query
$logs = LoginLog::with('pengguna.peran')->get();
foreach ($logs as $log) {
    echo $log->pengguna->nm; // No additional query
}
```

### 3. Pagination

Always paginate large datasets:

```php
// ❌ BAD: Load all data
$logs = LoginLog::all(); // Could be millions of rows

// ✅ GOOD: Paginate
$logs = LoginLog::paginate(50);
```

### 4. Query Optimization

Use `select()` untuk batasi columns:

```php
// ❌ BAD: Select all columns
LoginLog::with('pengguna')->get();

// ✅ GOOD: Select only needed columns
LoginLog::with([
    'pengguna' => fn($q) => $q->select('UUID', 'nm', 'email')
])
->select(['UUID', 'pengguna_uuid', 'status_akses', 'create_at'])
->get();
```

### 5. Partitioning (Future)

Untuk jutaan records, consider table partitioning by month:

```sql
-- Partition by month (PostgreSQL 10+)
CREATE TABLE audit.riwayat_login_2026_01 
    PARTITION OF audit.riwayat_login
    FOR VALUES FROM ('2026-01-01') TO ('2026-02-01');

CREATE TABLE audit.riwayat_login_2026_02 
    PARTITION OF audit.riwayat_login
    FOR VALUES FROM ('2026-02-01') TO ('2026-03-01');
```

---

## 🧪 Testing Guide

### 1. Run Migration

```bash
php artisan migrate

# Verify
php artisan tinker
>>> \DB::table('audit.riwayat_login')->count();
=> 0
```

### 2. Generate Test Data

```bash
php artisan db:seed --class=LoginHistorySeeder

# Expected output:
# ✓ Generated 300+ normal successful logins
# ✓ Generated 50+ failed password attempts
# ✓ Generated 8 suspended account attempts
# ✓ Generated 20+ 'user not found' attempts
# ✓ Generated 25 SSO login successes
# ✓ Generated 10 SSO failed attempts
# ✓ Generated 50 suspicious brute force attempts
# ✓ Generated 8 successful logins today (+ 5 failed)
```

### 3. Test Queries

```php
php artisan tinker

// Total logs
>>> LoginLog::count();

// Successful vs failed
>>> LoginLog::where('status_akses', 'BERHASIL')->count();
>>> LoginLog::where('status_akses', '!=', 'BERHASIL')->count();

// Today's activity
>>> LoginLog::today()->count();

// User with most logins
>>> DB::table('audit.riwayat_login')
    ->select('pengguna_uuid', DB::raw('COUNT(*) as total'))
    ->whereNotNull('pengguna_uuid')
    ->groupBy('pengguna_uuid')
    ->orderByDesc('total')
    ->first();
```

### 4. Test API

```php
use App\Services\AuditLogService;

$service = app(AuditLogService::class);

// Get statistics
$stats = $service->getLoginStatisticsNew();
print_r($stats);

// Get history
$logs = $service->getLoginHistory(['period' => 'today']);
print_r($logs->items());
```

### 5. Manual Integration Test

```bash
# Login via browser
# Check database

SELECT 
    l.create_at,
    u.nm as nama_user,
    l.status_akses,
    l.alamat_ip,
    LEFT(l.perangkat, 50) as device
FROM audit.riwayat_login l
LEFT JOIN akun.pengguna u ON l.pengguna_uuid = u."UUID"
ORDER BY l.create_at DESC
LIMIT 10;
```

---

## 🔧 Troubleshooting

### Issue 1: Migration Failed

**Error:**
```
SQLSTATE[42P07]: Duplicate table: 7 ERROR: relation "audit.riwayat_login" already exists
```

**Solution:**
```bash
# Drop table manually
php artisan tinker
>>> DB::statement('DROP TABLE IF EXISTS audit.riwayat_login CASCADE');

# Re-run migration
php artisan migrate
```

---

### Issue 2: Foreign Key Constraint Failed

**Error:**
```
SQLSTATE[23503]: Foreign key violation: 7 ERROR: insert or update on table "riwayat_login" violates foreign key constraint
```

**Solution:**
- Pastikan `pengguna_uuid` ada di tabel `akun.pengguna`
- Atau gunakan `null` jika user tidak ditemukan

```php
// ✅ CORRECT
$this->auditLogService->recordLoginLog(
    userUuid: null,  // OK untuk failed login
    status: 'GAGAL_NOT_FOUND',
    request: $request
);
```

---

### Issue 3: Logs Not Created

**Problem:** `recordLoginLog()` tidak create record

**Debug:**

```php
// Check Laravel log
tail -f storage/logs/laravel.log

// Check errors
try {
    $log = $auditLogService->recordLoginLog(...);
    dd($log);
} catch (\Exception $e) {
    dd($e->getMessage(), $e->getTraceAsString());
}
```

**Common causes:**
- Database connection error
- Validation error (status value invalid)
- Transaction rollback

---

### Issue 4: Slow Queries

**Problem:** Query login history lambat (>1s)

**Solution:**

```bash
# Check indexes
php artisan tinker
>>> DB::select("
    SELECT indexname, indexdef 
    FROM pg_indexes 
    WHERE tablename = 'riwayat_login'
");

# If indexes missing, re-run migration
php artisan migrate:refresh --step=1
```

**Optimize query:**

```php
// ❌ SLOW
$logs = LoginLog::with('pengguna.peran')->get();

// ✅ FAST
$logs = LoginLog::with([
    'pengguna:UUID,nm,email,peran_uuid' => [
        'peran:UUID,nm_peran'
    ]
])
->select(['UUID', 'pengguna_uuid', 'status_akses', 'create_at'])
->paginate(50);
```

---

### Issue 5: N+1 Query Problem

**Problem:** Terlalu banyak query saat loop

**Debug:**

```php
// Enable query log
DB::enableQueryLog();

$logs = LoginLog::all();
foreach ($logs as $log) {
    echo $log->pengguna->nm;
}

dd(DB::getQueryLog()); // Check total queries
```

**Solution:**

```php
// Always use eager loading
$logs = LoginLog::with('pengguna')->get();
```

---

## 📊 Dashboard Integration

### Example Controller

```php
namespace App\Http\Controllers\Admin;

use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(
        protected AuditLogService $auditService
    ) {}

    public function loginHistory(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'period' => $request->get('period', 'this_month'),
            'status' => $request->get('status'),
        ];

        $logs = $this->auditService->getLoginHistory($filters, perPage: 50);
        $stats = $this->auditService->getLoginStatisticsNew();

        return view('admin.audit.login-history', compact('logs', 'stats'));
    }

    public function userTimeline(Request $request, string $userUuid)
    {
        $data = $this->auditService->getUserActivityTimeline($userUuid);

        return view('admin.audit.user-timeline', $data);
    }
}
```

### Example Blade View

```blade
{{-- resources/views/admin/audit/login-history.blade.php --}}

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5>Total Attempts</h5>
                <h2>{{ number_format($stats['total_attempts']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5>Success Rate</h5>
                <h2>{{ $stats['success_rate'] }}%</h2>
            </div>
        </div>
    </div>
    <!-- More stats cards -->
</div>

<div class="card mt-4">
    <div class="card-header">
        <h4>Login History</h4>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>IP Address</th>
                    <th>Device</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->create_at->format('d M Y H:i:s') }}</td>
                    <td>
                        @if($log->pengguna)
                            {{ $log->pengguna->nm }}
                        @else
                            <em>User Deleted</em>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $log->isSuccessful() ? 'success' : 'danger' }}">
                            {{ $log->getStatusLabel() }}
                        </span>
                    </td>
                    <td>{{ $log->alamat_ip ?? '-' }}</td>
                    <td><small>{{ $log->getDeviceInfo() }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $logs->links() }}
    </div>
</div>
```

---

## 📈 Maintenance & Monitoring

### 1. Database Size Monitoring

```sql
-- Check table size
SELECT 
    pg_size_pretty(pg_total_relation_size('audit.riwayat_login')) as total_size,
    pg_size_pretty(pg_relation_size('audit.riwayat_login')) as table_size,
    pg_size_pretty(pg_indexes_size('audit.riwayat_login')) as indexes_size;
```

### 2. Archive Old Data

Consider archiving logs older than 1 year:

```php
// Archive to separate table
DB::statement("
    INSERT INTO audit.riwayat_login_archive
    SELECT * FROM audit.riwayat_login
    WHERE create_at < NOW() - INTERVAL '1 year'
");

// Delete archived data
DB::table('audit.riwayat_login')
    ->where('create_at', '<', now()->subYear())
    ->delete();
```

### 3. Regular Reports

Generate weekly security report:

```php
Artisan::command('report:login-security', function() {
    $service = app(AuditLogService::class);
    
    $stats = $service->getLoginStatisticsNew([
        'date_from' => now()->subWeek(),
        'date_to' => now(),
    ]);

    // Email to admin
    Mail::to('admin@example.com')->send(
        new LoginSecurityReport($stats)
    );
});
```

---

## 🎯 Conclusion

Sistem login history tracking ini provides:

✅ **Complete audit trail** untuk compliance  
✅ **Security monitoring** untuk detect threats  
✅ **User analytics** untuk business insights  
✅ **Production-ready** dengan performance optimization  
✅ **Well-documented** dengan extensive examples  

**Next Steps:**

1. ✅ Run migration
2. ✅ Integrate ke controllers
3. ✅ Generate test data
4. ✅ Build dashboard views
5. ✅ Setup monitoring alerts
6. ✅ Configure archiving strategy

**Support:**

- Documentation: `/docs/LOGIN_TRACKING_DOCUMENTATION.md`
- Examples: `/docs/LOGIN_TRACKING_IMPLEMENTATION_EXAMPLES.php`
- Seeder: `database/seeders/LoginHistorySeeder.php`

---

**Created with ❤️ by Domain TIK Development Team**  
**Laravel 12 | PostgreSQL 15 | PHP 8.2**
