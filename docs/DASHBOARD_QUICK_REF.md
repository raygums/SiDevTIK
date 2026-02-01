# Quick Reference: Dashboard Multi-Role & Admin Verification

## Login Test Accounts

```bash
Admin:        admin@test.com / password
Verifikator:  verifikator@test.com / password
Eksekutor:    eksekutor@test.com / password
User:         user@test.com / password
```

## Admin Routes

| Route | Description |
|-------|-------------|
| `/dashboard` | Auto-redirect berdasarkan role |
| `/admin/users/verification` | Halaman verifikasi akun |
| `/admin/users/{uuid}/logs` | Audit log user |

## Quick Actions (Admin)

1. **Verifikasi Akun Individual**
   - Filter status: "Belum Aktif"
   - Click "Aktifkan" pada user
   - Konfirmasi

2. **Bulk Activate**
   - Check multiple users
   - Click "Aktifkan Pengguna Terpilih"
   - Konfirmasi

3. **View Audit Log**
   - Click "Log" pada user
   - Timeline: Daftar, login, pengajuan pertama

## File Structure

```
app/Services/AdminService.php                    # Business logic verifikasi
app/Http/Controllers/Admin/AdminController.php   # Thin controller
resources/views/
├── layouts/dashboard.blade.php                  # Layout dengan sidebar
├── components/sidebar.blade.php                 # Sidebar role-based
├── admin/
│   ├── dashboard.blade.php                      # Dashboard admin
│   ├── user-verification.blade.php              # Verifikasi akun
│   └── user-logs.blade.php                      # Audit logs
└── {role}/dashboard.blade.php                   # Dashboard per role
```

## Service Methods (AdminService)

```php
getUsersForVerification($filters, $perPage)  // Get filtered users
toggleUserStatus($userUuid)                  // Toggle a_aktif
bulkActivateUsers($userUuids)                // Bulk activate
getUserAuditLogs($userUuid)                  // Audit timeline
getUserStatistics()                          // Dashboard stats
```

## Sidebar Menu (Role-Based)

**Admin**: Dashboard | Verifikasi Akun | Log Audit | Kelola Peran  
**Verifikator**: Dashboard | Daftar Pengajuan | Verifikasi | Log  
**Eksekutor**: Dashboard | Daftar Tugas | Update Status | Log  
**Pengguna**: Dashboard | Buat Pengajuan | Daftar Pengajuan | Profil

## Design System

- Primary: MyUnila Blue (#0B5EA8)
- Success: Green (#10b981)
- Warning: Amber (#f59e0b)
- Error: Red (#ef4444)
- Info: Blue (#3b82f6)

## Testing Workflow

```bash
# 1. Migrate & seed
docker compose exec app php artisan migrate:fresh --seed

# 2. Login
http://localhost/login → admin@test.com / password

# 3. Dashboard auto-redirect
http://localhost/dashboard → Admin Dashboard

# 4. Verifikasi
http://localhost/admin/users/verification

# 5. Filter & activate users
```

## Middleware Protection

```php
Route::middleware(['auth', 'role:admin'])
```

## Clean Code Principle

```
Efektivitas = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas
```

---

**Full Documentation**: [ADMIN_USER_VERIFICATION_MODULE.md](./ADMIN_USER_VERIFICATION_MODULE.md)
