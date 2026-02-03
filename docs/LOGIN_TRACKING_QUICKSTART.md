# 🚀 Quick Start: Login History Tracking

**Laravel 12 | PostgreSQL | Domain TIK**

---

## 📦 Files Created

```
✅ database/migrations/2026_02_03_100000_create_riwayat_login_table.php
✅ app/Models/LoginLog.php
✅ app/Services/AuditLogService.php (updated)
✅ database/seeders/LoginHistorySeeder.php
✅ docs/LOGIN_TRACKING_IMPLEMENTATION_EXAMPLES.php
✅ docs/LOGIN_TRACKING_DOCUMENTATION.md
✅ docs/LOGIN_TRACKING_QUICKSTART.md (this file)
```

---

## ⚡ 5-Minute Setup

### Step 1: Run Migration (30 seconds)

```bash
cd /home/firman/projects/Domain-TIK
docker exec -it domaintik-app bash

php artisan migrate

# Expected output:
# Migrating: 2026_02_03_100000_create_riwayat_login_table
# Migrated:  2026_02_03_100000_create_riwayat_login_table (120.45ms)
```

### Step 2: Generate Test Data (1 minute)

```bash
php artisan db:seed --class=LoginHistorySeeder

# Expected output:
# ✅ Login History Seeder Completed!
# Total Login Attempts: 500+
# Successful Logins: 400+
# Failed Attempts: 100+
```

### Step 3: Test Queries (1 minute)

```bash
php artisan tinker
```

```php
// Check total logs
LoginLog::count();

// Get recent logs
LoginLog::with('pengguna')->recent()->take(10)->get();

// Get statistics
$stats = app(\App\Services\AuditLogService::class)->getLoginStatisticsNew();
print_r($stats);

exit
```

### Step 4: Integrate to AuthController (2 minutes)

**File:** `app/Http/Controllers/AuthController.php`

```php
// 1. Import service
use App\Services\AuditLogService;

// 2. Inject di constructor
public function __construct(
    protected AuthService $authService,
    protected AuditLogService $auditLogService // ADD THIS
) {}

// 3. Add logging di store() method - SETELAH Auth::login()
Auth::login($user);
$this->auditLogService->recordLoginLog(
    userUuid: $user->UUID,
    status: 'BERHASIL',
    request: $request,
    keterangan: 'Login berhasil via local authentication'
);
```

### Step 5: Verify Integration (30 seconds)

```bash
# Login via browser: http://localhost/login
# Username: superadmin@unila.ac.id
# Password: password

# Check log created
php artisan tinker
```

```php
LoginLog::latest('create_at')->first();
// Should show your recent login

exit
```

---

## 📊 Quick Commands

### Database Queries

```bash
php artisan tinker
```

```php
use App\Models\LoginLog;
use App\Services\AuditLogService;

// Total logs
LoginLog::count();

// Today's successful logins
LoginLog::today()->where('status_akses', 'BERHASIL')->count();

// Failed logins last 7 days
LoginLog::where('create_at', '>=', now()->subDays(7))
    ->where('status_akses', '!=', 'BERHASIL')
    ->count();

// Most active user
DB::table('audit.riwayat_login')
    ->select('pengguna_uuid', DB::raw('COUNT(*) as total'))
    ->whereNotNull('pengguna_uuid')
    ->groupBy('pengguna_uuid')
    ->orderByDesc('total')
    ->first();

// Suspicious IPs (failed attempts > 10)
DB::table('audit.riwayat_login')
    ->select('alamat_ip', DB::raw('COUNT(*) as attempts'))
    ->where('status_akses', '!=', 'BERHASIL')
    ->groupBy('alamat_ip')
    ->having('attempts', '>', 10)
    ->orderByDesc('attempts')
    ->get();

// Get statistics via service
$service = app(AuditLogService::class);
$stats = $service->getLoginStatisticsNew();
print_r($stats);

// Get login history
$logs = $service->getLoginHistory(['period' => 'today']);
print_r($logs->items());
```

---

## 🎯 Common Use Cases

### 1. Record Successful Login

```php
$this->auditLogService->recordLoginLog(
    userUuid: $user->UUID,
    status: 'BERHASIL',
    request: $request,
    keterangan: 'Login berhasil'
);
```

### 2. Record Failed Login (Wrong Password)

```php
$this->auditLogService->recordLoginLog(
    userUuid: $user->UUID,
    status: 'GAGAL_PASSWORD',
    request: $request,
    keterangan: "Password salah untuk user '{$user->usn}'"
);
```

### 3. Record User Not Found

```php
$this->auditLogService->recordLoginLog(
    userUuid: null,
    status: 'GAGAL_NOT_FOUND',
    request: $request,
    keterangan: "Username '{$username}' tidak terdaftar"
);
```

### 4. Record Account Suspended

```php
$this->auditLogService->recordLoginLog(
    userUuid: $user->UUID,
    status: 'GAGAL_SUSPEND',
    request: $request,
    keterangan: 'Akun tidak aktif'
);
```

### 5. Record SSO Success

```php
$this->auditLogService->recordLoginLog(
    userUuid: $user->UUID,
    status: 'BERHASIL',
    request: $request,
    keterangan: "Login berhasil via SSO Unila - NIP: {$user->nip}"
);
```

---

## 📈 Query Examples

### Get Today's Activity

```php
$service = app(\App\Services\AuditLogService::class);

$logs = $service->getLoginHistory([
    'period' => 'today'
], perPage: 50);

foreach ($logs as $log) {
    echo "{$log->create_at} - {$log->pengguna->nm} - {$log->getStatusLabel()}\n";
}
```

### Get Failed Logins This Week

```php
$failed = $service->getLoginHistory([
    'period' => 'this_week',
    'only_failed' => true
]);

echo "Failed logins this week: {$failed->total()}\n";
```

### Get User-Specific History

```php
$userLogs = $service->getLoginHistory([
    'user_uuid' => $userUuid,
    'date_from' => '2026-01-01',
    'date_to' => '2026-01-31'
]);
```

### Get Security Statistics

```php
$stats = $service->getLoginStatisticsNew([
    'date_from' => now()->subMonth(),
    'date_to' => now(),
]);

echo "Total Attempts: {$stats['total_attempts']}\n";
echo "Success Rate: {$stats['success_rate']}%\n";
echo "Failed Logins: {$stats['failed_logins']}\n";
```

---

## 🛠️ Troubleshooting

### Migration Failed?

```bash
# Drop table manually
php artisan tinker
>>> DB::statement('DROP TABLE IF EXISTS audit.riwayat_login CASCADE');
>>> exit

# Re-run
php artisan migrate
```

### Logs Not Created?

```php
// Check Laravel log
tail -f storage/logs/laravel.log

// Test manually
php artisan tinker
>>> use App\Services\AuditLogService;
>>> $service = app(AuditLogService::class);
>>> $log = $service->recordLoginLog(
...     userUuid: 'test-uuid',
...     status: 'BERHASIL',
...     request: request(),
...     keterangan: 'Test log'
... );
>>> dd($log);
```

### Slow Queries?

```bash
# Check indexes exist
php artisan tinker
>>> DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'riwayat_login'");

# Should show 5 indexes
```

---

## 📚 Documentation

- **Full Documentation:** [docs/LOGIN_TRACKING_DOCUMENTATION.md](LOGIN_TRACKING_DOCUMENTATION.md)
- **Implementation Examples:** [docs/LOGIN_TRACKING_IMPLEMENTATION_EXAMPLES.php](LOGIN_TRACKING_IMPLEMENTATION_EXAMPLES.php)
- **This Quick Start:** [docs/LOGIN_TRACKING_QUICKSTART.md](LOGIN_TRACKING_QUICKSTART.md)

---

## ✅ Checklist

Setup:
- [ ] Migration executed successfully
- [ ] Test data generated (500+ logs)
- [ ] Queries tested via tinker
- [ ] AuditLogService injected to AuthController
- [ ] recordLoginLog() called on login
- [ ] Verified log created in database

Optional:
- [ ] SSO Controller integrated
- [ ] Dashboard views created
- [ ] Security alerts configured
- [ ] Archiving strategy planned

---

## 🎯 Next Steps

1. **Integrate ke SSO Controller** (jika ada SSO login)
2. **Build Admin Dashboard Views** untuk visualisasi data
3. **Setup Monitoring Alerts** untuk suspicious activity
4. **Configure Archiving** untuk data lama (>1 year)
5. **Document for Team** share ke tim developer

---

**Ready to use! 🚀**

For detailed documentation, see: [LOGIN_TRACKING_DOCUMENTATION.md](LOGIN_TRACKING_DOCUMENTATION.md)
