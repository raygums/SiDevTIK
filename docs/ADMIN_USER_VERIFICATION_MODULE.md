# Dashboard Multi-Role & Admin User Verification Module

## Overview

Implementasi dashboard multi-role yang terintegrasi dengan sidebar dinamis dan fitur verifikasi akun pengguna khusus untuk Admin. Dashboard dirancang dengan prinsip Clean Code dan Service Pattern untuk memisahkan business logic dari presentation layer.

---

## Architecture

### Design Pattern

1. **Service Pattern**: Business logic terpisah di `AdminService.php` dan `UserService.php`
2. **Thin Controller**: Controllers hanya handle HTTP request/response
3. **Component-Based UI**: Reusable components untuk sidebar, icons, alerts
4. **Role-Based Access Control**: Middleware `role:admin` untuk proteksi route

### File Structure

```
app/
├── Services/
│   └── AdminService.php              # Business logic admin (verifikasi, audit logs)
├── Http/
│   └── Controllers/
│       ├── Admin/
│       │   └── AdminController.php   # Thin controller untuk fitur admin
│       └── DashboardController.php   # Multi-role dashboard controller

resources/views/
├── layouts/
│   └── dashboard.blade.php           # Layout dashboard dengan sidebar
├── components/
│   └── sidebar.blade.php             # Sidebar component (role-based menu)
├── admin/
│   ├── dashboard.blade.php           # Dashboard admin
│   ├── user-verification.blade.php   # Halaman verifikasi akun
│   └── user-logs.blade.php          # Audit logs user
├── verifikator/
│   └── dashboard.blade.php           # Dashboard verifikator (placeholder)
├── eksekutor/
│   └── dashboard.blade.php           # Dashboard eksekutor (placeholder)
└── dashboard.blade.php               # Dashboard pengguna biasa

routes/
└── web.php                           # Admin routes dengan middleware role:admin
```

---

## Features

### 1. Dashboard Multi-Role

**Automatic Role Detection**  
Dashboard secara otomatis mendeteksi role user dan menampilkan dashboard sesuai:

- **Admin**: Dashboard admin dengan statistik user & verifikasi akun
- **Verifikator**: Dashboard verifikator dengan pengajuan pending
- **Eksekutor**: Dashboard eksekutor dengan daftar tugas
- **Pengguna**: Dashboard pengguna biasa dengan pengajuan mereka

**Implementation:**
```php
// DashboardController.php
public function index()
{
    $user = Auth::user();
    $roleName = strtolower($user->peran->nm_peran ?? 'pengguna');

    if (str_contains($roleName, 'admin')) {
        return $this->adminDashboard();
    }
    
    if ($roleName === 'verifikator') {
        return $this->verifikatorDashboard();
    }
    
    if ($roleName === 'eksekutor') {
        return $this->eksekutorDashboard();
    }

    return $this->penggunaDashboard();
}
```

---

### 2. Sidebar Component (Dynamic Menu)

**Role-Based Navigation**  
Sidebar secara dinamis menampilkan menu sesuai role:

**Admin Menu:**
- Dashboard
- Verifikasi Akun Pengguna → `admin.users.verification`
- Log Audit Pengguna → `admin.users.logs` (placeholder)
- Kelola Peran → `admin.roles` (placeholder)

**Verifikator Menu:**
- Dashboard
- Daftar Pengajuan
- Verifikasi Permohonan (placeholder)
- Log Aktivitas Verifikasi (placeholder)

**Eksekutor Menu:**
- Dashboard
- Daftar Tugas
- Update Status Selesai (placeholder)
- Log Perubahan Status (placeholder)

**Pengguna Menu:**
- Dashboard
- Buat Pengajuan
- Daftar Pengajuan
- Profil Saya (placeholder)

**Logo & Branding:**
- Logo Domaintik di pojok kiri atas
- Link ke landing page `route('home')`
- Gradient MyUnila color (#0B5EA8)

---

### 3. Admin User Verification

**Fitur Verifikasi Akun:**

#### a. Advanced Filtering
```
- Tipe Akun: SSO / Lokal / Semua
- Status: Aktif / Belum Aktif / Semua
- Search: Nama, Username, Email
- Auto-submit on filter change
```

#### b. Statistics Dashboard
```
- Total User
- Menunggu Verifikasi (a_aktif = false)
- Sudah Aktif (a_aktif = true)
- Akun SSO (sso_id NOT NULL)
```

#### c. User Actions
1. **Toggle Status** (Individual)
   - Route: `POST /admin/users/{uuid}/toggle-status`
   - Confirmation dialog sebelum toggle
   - Logging aktivitas admin

2. **Bulk Activate** (Multiple)
   - Checkbox selection untuk user yang inactive
   - Route: `POST /admin/users/bulk-activate`
   - Validation: minimal 1 user selected

3. **View Audit Log**
   - Route: `GET /admin/users/{uuid}/logs`
   - Timeline: Waktu daftar, login terakhir, pengajuan pertama
   - Statistics: Total pengajuan, pengajuan aktif

---

## Service Layer

### AdminService.php

**Methods:**

1. `getUsersForVerification($filters, $perPage)` - Get filtered users dengan pagination
2. `toggleUserStatus($userUuid)` - Toggle a_aktif (true ↔ false)
3. `bulkActivateUsers($userUuids)` - Aktivasi multiple users sekaligus
4. `getUserAuditLogs($userUuid)` - Get audit trail user (timeline aktivitas)
5. `getUserStatistics()` - Get statistics untuk admin dashboard
6. `getRecentRegistrations($limit)` - Get recent user registrations
7. `searchUsers($keyword, $perPage)` - Advanced search users

**Clean Code Principles:**
```php
/**
 * Design Pattern: Service Pattern
 * Efektivitas = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas
 */
```

---

## Routes

### Admin Routes (Protected: `role:admin`)

```php
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Verification
    Route::get('/users/verification', [AdminController::class, 'userVerification'])->name('users.verification');
    Route::post('/users/{uuid}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/bulk-activate', [AdminController::class, 'bulkActivate'])->name('users.bulk-activate');
    
    // Audit Logs
    Route::get('/users/{uuid}/logs', [AdminController::class, 'userLogs'])->name('users.logs');
});
```

---

## UI/UX Design

### Design System

- **Font**: Instrument Sans (via Tailwind config)
- **Primary Color**: MyUnila Blue (#0B5EA8)
- **Components**: Tailwind CSS utility-first
- **Icons**: Heroicons (via x-icon component)

### Color Palette

```css
MyUnila (Primary): #0B5EA8
Success: #10b981 (green-500)
Warning: #f59e0b (amber-500)
Error/Danger: #ef4444 (red-500)
Info: #3b82f6 (blue-500)
```

### Responsive Layout

- **Desktop (lg+)**: Sidebar always visible (w-64)
- **Mobile**: Hamburger menu → slide-in sidebar
- **Overlay**: Dark backdrop on mobile sidebar open

---

## Usage Guide

### 1. Login sebagai Admin

```bash
Email: admin@test.com
Password: password
```

### 2. Akses Dashboard Admin

Setelah login, otomatis redirect ke `/dashboard` yang menampilkan:
- Statistics: Total user, pending verification, total submissions
- Quick Actions: Card "Verifikasi Akun Pengguna"
- Recent Registrations: List user baru yang belum aktif

### 3. Verifikasi Akun

**Step-by-step:**

1. Click "Verifikasi Akun Pengguna" di dashboard
2. Filter user berdasarkan status "Belum Aktif"
3. Review user details (nama, email, tipe SSO/Lokal)
4. Click "Aktifkan" untuk individual user
   - Konfirmasi → Submit → User a_aktif = true
5. **Bulk Activate** (opsional):
   - Check multiple users
   - Click "Aktifkan Pengguna Terpilih"
   - Konfirmasi → All selected users activated

### 4. View Audit Log

1. Click "Log" pada row user
2. Lihat timeline: Daftar akun, login terakhir, pengajuan pertama
3. View statistics: Total pengajuan, pengajuan aktif

---

## Testing

### Test Accounts

**Admin:**
```
Email: admin@test.com
Password: password
Role: Administrator
Status: Aktif
```

**Verifikator:**
```
Email: verifikator@test.com
Password: password
Role: Verifikator
Status: Aktif
```

**Inactive Users (untuk testing verifikasi):**
```
1. user.inactive@test.com
2. mahasiswa@student.unila.ac.id (SSO)
3. dosen@unila.ac.id (SSO)
4. tendik@unila.ac.id (SSO)
```

### Test Workflow

```bash
# 1. Setup database
docker compose exec app php artisan migrate:fresh --seed

# 2. Login sebagai admin
http://localhost/login
Email: admin@test.com
Password: password

# 3. Verifikasi user
http://localhost/admin/users/verification

# 4. Filter: Status = "Belum Aktif"
# 5. Aktifkan 1-2 user
# 6. Check audit log
```

---

## API Endpoints

| Method | Endpoint | Description | Middleware |
|--------|----------|-------------|-----------|
| GET | `/dashboard` | Multi-role dashboard | auth |
| GET | `/admin/dashboard` | Admin dashboard | auth, role:admin |
| GET | `/admin/users/verification` | User verification page | auth, role:admin |
| POST | `/admin/users/{uuid}/toggle-status` | Toggle user status | auth, role:admin |
| POST | `/admin/users/bulk-activate` | Bulk activate users | auth, role:admin |
| GET | `/admin/users/{uuid}/logs` | User audit logs | auth, role:admin |

---

## Database Schema

### User Activation Status

```sql
-- Schema: akun.pengguna
a_aktif BOOLEAN DEFAULT FALSE  -- Status aktivasi (Admin toggle via UI)
```

### Audit Columns

```sql
create_at TIMESTAMP       -- Waktu daftar akun
last_login_at TIMESTAMP   -- Login terakhir
last_update TIMESTAMP     -- Update terakhir
```

---

## Logging

### Admin Actions Logged

```php
Log::info('Admin Toggle User Status', [
    'admin_uuid' => auth()->user()->UUID,
    'admin_name' => auth()->user()->nm,
    'user_uuid' => $userUuid,
    'user_name' => $user->nm,
    'old_status' => $oldStatus ? 'Active' : 'Inactive',
    'new_status' => $newStatus ? 'Active' : 'Inactive',
]);
```

### Log File Location

```
storage/logs/laravel.log
```

---

## Security

### Role-Based Access Control

```php
// Middleware stack
Route::middleware(['auth', 'role:admin'])
```

### CSRF Protection

```blade
<form method="POST" action="{{ route('admin.users.toggle-status', $uuid) }}">
    @csrf
    <!-- Form content -->
</form>
```

### Input Validation

```php
$request->validate([
    'user_uuids' => 'required|array|min:1',
    'user_uuids.*' => 'required|uuid',
]);
```

---

## Future Enhancements

### Placeholder Features (Segera Hadir)

1. **Log Audit Pengguna** (Full implementation)
2. **Kelola Peran** (CRUD for roles)
3. **Verifikator Features**:
   - Verifikasi Permohonan
   - Log Aktivitas Verifikasi
4. **Eksekutor Features**:
   - Update Status Selesai
   - Log Perubahan Status
5. **Profile Management** (untuk semua role)

---

## Troubleshooting

### Issue: Sidebar tidak muncul

**Solution:**
```blade
// Pastikan view extends layouts.dashboard
@extends('layouts.dashboard')

// Bukan layouts.app
```

### Issue: Menu role tidak sesuai

**Solution:**
```php
// Check role name di database
DB::table('akun.peran')->select('nm_peran')->get();

// Expected: "Administrator", "Verifikator", "Eksekutor", "Pengguna"
```

### Issue: Route admin tidak accessible

**Solution:**
```php
// Check middleware RoleMiddleware registered di bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

## Code Quality Standards

### Clean Code Principles

```
Efektivitas = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas

1. Keterbacaan: Penamaan variabel jelas, komentar informatif
2. Kesederhanaan: Satu method satu responsibility
3. Konsistensi: Naming convention Bahasa Indonesia untuk DB
4. Reusabilitas: Service pattern, component-based UI
```

### Naming Conventions

- **Database Columns**: Bahasa Indonesia (`a_aktif`, `nm_peran`, `create_at`)
- **Routes**: kebab-case (`users.verification`, `toggle-status`)
- **Methods**: camelCase (`toggleUserStatus`, `getUserStatistics`)
- **Classes**: PascalCase (`AdminService`, `AdminController`)

---

## Credits

- **Framework**: Laravel 12.x
- **UI Framework**: Tailwind CSS 3.x
- **Icons**: Heroicons
- **Design System**: MyUnila Brand Guidelines

---

**Last Updated**: January 30, 2026  
**Version**: 1.0.0  
**Author**: Domain TIK Development Team
