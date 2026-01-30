# Arsitektur SSO-Gate: Autentikasi Selektif pada Domaintik

## Overview

SSO-Gate adalah fitur keamanan yang memastikan tidak semua pengguna yang login via SSO Unila otomatis mendapatkan akses penuh ke sistem. User baru yang pertama kali login akan memiliki status `a_aktif = false` dan harus diverifikasi oleh Tim Verifikator sebelum dapat menggunakan fitur pengajuan.

## Prinsip Desain

**Efektivitas Kode = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas**

Implementasi ini mengikuti prinsip:
- **Clean Code**: Kode mudah dibaca dengan naming yang jelas
- **SOLID**: Single Responsibility pada setiap komponen
- **DRY**: Tidak ada duplikasi logic
- **High Performance**: Menggunakan database transaction dan query yang efisien

## Komponen Implementasi

### 1. Middleware: `EnsureUserIsActive`

**Lokasi**: `/app/Http/Middleware/EnsureUserIsActive.php`

**Fungsi**:
- Memeriksa status `a_aktif` pada user yang terautentikasi
- Memblokir akses ke fitur pengajuan jika `a_aktif = false`
- Mengizinkan akses ke dashboard dengan flag khusus untuk UI adjustment

**Logic Flow**:
```
User Request
    │
    ├─ User belum login? → Skip (handled by auth middleware)
    │
    ├─ User.a_aktif = true? → Allow full access
    │
    └─ User.a_aktif = false?
         │
         ├─ Route = dashboard/logout? → Allow with flag
         │
         └─ Route lainnya → Redirect ke dashboard dengan error message
```

**Whitelist Routes**:
- `dashboard` - User dapat melihat status akun
- `logout` - User dapat logout kapan saja

**Registration**: 
```php
// bootstrap/app.php
$middleware->alias([
    'active' => \App\Http\Middleware\EnsureUserIsActive::class,
]);
```

**Usage**:
```php
// routes/web.php
Route::middleware('active')->prefix('pengajuan')->group(function () {
    // Protected routes
});
```

### 2. SSOController Enhancement

**Lokasi**: `/app/Http/Controllers/Auth/SSOController.php`

**Perubahan pada `findOrCreateUser()` method**:

1. **User Existing**: UPDATE data dari SSO, **PRESERVE** status `a_aktif`
2. **User Baru**: CREATE dengan `a_aktif = false` (default inactive)

**Cascade Lookup Strategy**:
```
1. Search by sso_id (most reliable)
   ↓
2. Search by username (usn) → Link existing account
   ↓
3. Search by email → Fallback identifier
   ↓
4. Create new user with a_aktif = false
```

**Transaction Safety**:
```php
try {
    DB::beginTransaction();
    $user = $this->findOrCreateUser($payload);
    Auth::login($user, true);
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Handle error
}
```

**Logging**: Setiap operasi dicatat dengan detail untuk audit trail

### 3. UI State Handling

**Lokasi**: `/resources/views/dashboard.blade.php`

**Conditional Rendering**:

#### A. Alert Banner (User Inactive)
```blade
@if(!Auth::user()->a_aktif)
<div class="mb-8 overflow-hidden rounded-xl border border-warning">
    <!-- Warning message -->
</div>
@endif
```

#### B. Feature Cards
- **User Aktif**: Card interaktif dengan link ke fitur
- **User Tidak Aktif**: Card disabled dengan icon lock dan opacity

#### C. Stats Display
```blade
{{ Auth::user()->a_aktif ? ($stats['dalam_proses'] ?? 0) : '-' }}
```

#### D. Status Badge di Info Akun
```blade
@if(Auth::user()->a_aktif)
    <span>Aktif</span>
@else
    <span>Menunggu Verifikasi</span>
@endif
```

### 4. Routes Configuration

**Lokasi**: `/routes/web.php`

**Protected Routes**:
```php
Route::middleware(['auth', 'active'])->prefix('pengajuan')->group(function () {
    // Semua route pengajuan dilindungi
});
```

**Open Routes** (accessible meskipun inactive):
```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...);  // ✓ Allowed
    Route::post('/logout', ...);    // ✓ Allowed
});
```

## Database Schema

**Table**: `akun.pengguna`

**Kolom Kunci**:
- `UUID` (PK): Primary key user
- `sso_id`: ID dari SSO Unila
- `a_aktif` (boolean): Status aktivasi akun
- `last_login_at`: Timestamp login terakhir
- `last_login_ip`: IP address login terakhir

**Default Values untuk User Baru**:
```php
'a_aktif' => false,              // CRITICAL: Default inactive
'kata_sandi' => bcrypt(random), // Random (tidak digunakan)
'peran_uuid' => 'Pengguna',     // Default role
```

## User Flow

### Scenario 1: User Baru Login via SSO

1. User mengakses `/login/sso`
2. Redirect ke SSO Unila
3. User login di SSO, redirect ke `/auth/sso/callback?token=xxx`
4. System validate token
5. User tidak ditemukan → **CREATE** dengan `a_aktif = false`
6. User di-login ke sistem
7. Redirect ke dashboard dengan pesan sukses
8. Dashboard menampilkan **banner warning** dan **fitur terkunci**
9. User menunggu aktivasi dari Verifikator

### Scenario 2: User Existing Login via SSO

1. User mengakses `/login/sso`
2. Redirect ke SSO Unila
3. User login di SSO, redirect ke `/auth/sso/callback?token=xxx`
4. System validate token
5. User ditemukan → **UPDATE** data dari SSO, **KEEP** `a_aktif` status
6. User di-login ke sistem
7. Redirect ke dashboard
8. Jika `a_aktif = true`: Full access
9. Jika `a_aktif = false`: Dashboard dengan banner warning

### Scenario 3: User Inactive Mencoba Akses Fitur

1. User (inactive) mengklik "Buat Pengajuan"
2. Middleware `EnsureUserIsActive` intercept request
3. Route bukan dalam whitelist → **BLOCK**
4. Redirect ke dashboard dengan error message
5. Dashboard tetap menampilkan banner warning

## Security Considerations

1. **Default Deny**: User baru default inactive (security-first approach)
2. **Transaction Integrity**: Menggunakan DB transaction untuk data consistency
3. **Audit Trail**: Semua operasi SSO di-log dengan detail
4. **Preserve Status**: Update dari SSO tidak mengubah status aktivasi existing user
5. **Clear Feedback**: User mendapat feedback jelas tentang status akun mereka

## Aktivasi User (Verifikator Side)

Untuk mengaktifkan user, Verifikator dapat menggunakan:

```php
// Di Admin/Verifikator Controller
$user = User::findOrFail($uuid);
$user->update([
    'a_aktif' => true,
    'last_update' => now(),
    'id_updater' => Auth::id(),
]);

Log::info('User Activated', [
    'user_uuid' => $user->UUID,
    'activated_by' => Auth::user()->usn,
]);
```

## Testing Checklist

- [ ] User baru via SSO memiliki `a_aktif = false`
- [ ] User existing via SSO preserve `a_aktif` status
- [ ] User inactive dapat akses dashboard
- [ ] User inactive dapat logout
- [ ] User inactive **TIDAK** dapat akses pengajuan
- [ ] Dashboard menampilkan banner warning untuk user inactive
- [ ] Dashboard menampilkan feature cards disabled untuk user inactive
- [ ] Status badge menampilkan "Menunggu Verifikasi" untuk user inactive
- [ ] Error message jelas saat user inactive mencoba akses fitur
- [ ] Verifikator dapat mengaktifkan user
- [ ] Setelah aktivasi, user dapat akses semua fitur

## Maintenance Notes

### Menambah Route ke Whitelist

Edit `/app/Http/Middleware/EnsureUserIsActive.php`:

```php
protected array $allowedRoutes = [
    'dashboard',
    'logout',
    'profile',        // Tambah route baru
];
```

### Menambah Route yang Perlu Proteksi

Edit `/routes/web.php`:

```php
Route::middleware(['auth', 'active'])->group(function () {
    // Tambahkan route yang perlu proteksi
});
```

## Performance Impact

- **Minimal Overhead**: Middleware hanya check boolean flag
- **No Extra Queries**: Status `a_aktif` sudah loaded saat user login
- **Efficient UI Rendering**: Conditional rendering Blade sangat cepat
- **Transaction Safety**: DB transaction memastikan data integrity tanpa significant performance cost

## Future Enhancements

1. **Auto-activation**: Untuk user dengan domain email tertentu (@unila.ac.id)
2. **Notification**: Email notification ke user saat akun diaktifkan
3. **Approval Workflow**: Multi-level approval process
4. **Grace Period**: Temporary access untuk urgent cases
5. **Activity Log**: Detail log user activity untuk audit

---

**Created**: January 30, 2026  
**Author**: Domain-TIK Development Team  
**Version**: 1.0.0
