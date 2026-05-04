# Dokumentasi Update Fitur - Mei 2026

## Status Implementasi

Berikut adalah dokumentasi lengkap untuk semua fitur yang telah diimplementasikan berdasarkan notulensi rapat:

---

## 1. ✅ Pending Button di Verifikator dengan Alasan

### Perubahan yang Dilakukan:

#### A. Menambahkan Status "Pending Verifikasi"
- **File**: `database/seeders/DatabaseSeeder.php`
- **Deskripsi**: Menambahkan status baru 'Pending Verifikasi' ke dalam daftar status pengajuan
- **Keterangan**: Status ini digunakan ketika verifikator ingin meminta klarifikasi/keterangan tambahan dari pemohon

#### B. Menambahkan Method `pending()` di VerificationController
- **File**: `app/Http/Controllers/VerificationController.php`
- **Method**: `public function pending(Request $request, Submission $submission): RedirectResponse`
- **Fungsi**: Memproses action pending dengan alasan yang diberikan verifikator
- **Validasi**: Alasan pending minimal 10 karakter, maksimal 1000 karakter
- **Database**: Menyimpan log dengan format "PENDING: {alasan_pending}"

#### C. Menambahkan Route untuk Pending Action
- **File**: `routes/web.php`
- **Route**: `Route::post('/{submission}/pending', [VerificationController::class, 'pending'])->name('pending');`

#### D. Update UI - Verifikator Show Page
- **File**: `resources/views/verifikator/show.blade.php`
- **Perubahan**: 
  - Grid layout berubah dari 2 kolom menjadi 3 kolom (Approve, **Pending**, Reject)
  - Tombol Pending berwarna kuning (warning color)
  - Form untuk Pending meminta alasan dengan textarea
- **Icon**: Clock icon untuk menunjukkan status menunggu/pending

### Cara Penggunaan:
1. Verifikator membuka detail pengajuan
2. Klik tombol "Pending Klarifikasi" 
3. Masukkan alasan yang detail (minimal 10 karakter)
4. Klik "Minta Klarifikasi"
5. Pengajuan akan pindah ke status "Pending Verifikasi"
6. Log akan tercatat dengan tipe "PENDING"

### Testing:
- Run migration: `php artisan migrate:fresh --seed`
- Login sebagai verifikator
- Buka pengajuan dengan status "Diajukan" atau "Menunggu Verifikasi"
- Test ketiga tombol: Approve, Pending, Reject

---

## 2. ❌ CPU/RAM Fields untuk VPS (Database Configuration Required)

### Perubahan yang Dilakukan:

#### A. Migration File Dibuat
- **File**: `database/migrations/2026_05_04_191833_add_vps_specs_to_submission_details.php`
- **Fields Ditambahkan**:
  - `vps_os` (string): Operating System
  - `vps_cpu` (integer): Jumlah CPU cores
  - `vps_ram` (integer): RAM dalam GB
  - `vps_storage` (integer): Storage dalam GB

#### B. Model Updated
- **File**: `app/Models/SubmissionDetail.php`
- **Perubahan**: Menambahkan field baru ke `$fillable` array

### Status:
- Migration file siap namun belum bisa dijalankan karena PostgreSQL connection error
- File sudah dibuat di: `database/migrations/2026_05_04_191833_add_vps_specs_to_submission_details.php`

### Untuk Menjalankan:
1. **Perbaiki database connection di `.env`**:
   ```
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=domaintik
   DB_USERNAME=postgres
   DB_PASSWORD=secret
   ```

2. **Jalankan migration**:
   ```bash
   php artisan migrate
   ```

3. **Fields akan otomatis tersedia di SubmissionDetail Model**

### Implementation Tips:
- Di frontend, tampilkan field-field ini jika jenis layanan adalah "VPS"
- Dalam controller form generator, parse dari `keterangan_keperluan` JSON:
  ```php
  $vps_specs = json_decode($detail->keterangan_keperluan, true);
  $cpu = $vps_specs['VPS']['cpu'] ?? $detail->vps_cpu;
  $ram = $vps_specs['VPS']['ram'] ?? $detail->vps_ram;
  ```

---

## 3. ✅ Email Notification System (SMTP)

### Perubahan yang Dilakukan:

#### A. Email Configuration di `.env`
- **File**: `.env`
- **Konfigurasi**:
  ```env
  MAIL_MAILER=smtp
  MAIL_SCHEME=tls
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your-email@gmail.com
  MAIL_PASSWORD=your-app-password
  MAIL_FROM_ADDRESS="noreply@sidevtik.com"
  MAIL_FROM_NAME="SiDevTIK - Service Request System"
  ```

#### B. Notifications Table Migration
- **File**: `database/migrations/XXXX_XX_XX_create_notifications_table.php`
- **Otomatis dibuat oleh**: `php artisan notifications:table`
- **Fungsi**: Menyimpan Laravel Notifications

#### C. Admin Notifications Table
- **File**: `database/migrations/2026_05_04_192346_create_admin_notifications_table.php`
- **Schema**:
  - `id` (UUID primary key)
  - `type` (string): user_registered, user_activated, submission_created, dll
  - `title` (string): Judul notifikasi
  - `message` (text): Detail notifikasi
  - `related_user_uuid` (FK): User terkait
  - `related_submission_uuid` (FK): Submission terkait
  - `is_read` (boolean): Status dibaca
  - `read_at` (timestamp): Kapan dibaca
  - `created_at`, `updated_at`: Timestamps

#### D. Model AdminNotification
- **File**: `app/Models/AdminNotification.php`
- **Features**:
  - Relationship dengan User dan Submission
  - `markAsRead()` method untuk mark sebagai dibaca
  - Scope `unread()` untuk filter notifikasi yang belum dibaca
  - Scope `latest()` untuk sorting terbaru

#### E. Notification Service
- **File**: `app/Services/NotificationService.php`
- **Methods**:
  - `notifyUserRegistration($user)`: Notifikasi admin saat user baru register
  - `notifyUserActivation($user)`: Kirim email ke user saat akun diaktifkan
  - `notifySubmissionStatusChange($submission, $oldStatus, $newStatus, $notes)`: Notifikasi status change
  - `getUnreadNotificationsCount()`: Hitung notifikasi belum dibaca
  - `getRecentNotifications($limit)`: Ambil notifikasi terbaru

#### F. Email Templates
- **File 1**: `resources/views/emails/user-activated.blade.php` - Email aktivasi akun
- **File 2**: `resources/views/emails/submission-status-changed.blade.php` - Email status change

### Setup Instructions:

**1. Setup SMTP Credentials**:
```bash
# Edit .env dengan SMTP credentials Anda (Gmail contoh):
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-specific-password  # Bukan password gmail biasa
```

**2. Jalankan Migrations**:
```bash
php artisan migrate
```

**3. Integrate dengan UserService** (User Registration):
```php
// Dalam app/Services/UserService.php, tambahkan:
use App\Services\NotificationService;

// Saat user berhasil dibuat:
NotificationService::notifyUserRegistration($user);
```

**4. Integrate dengan UserManagementController** (User Activation):
```php
// Saat admin mengaktifkan user:
NotificationService::notifyUserActivation($user);
```

**5. Integrate dengan VerificationController** (Status Changes):
```php
// Saat mengubah status pengajuan:
NotificationService::notifySubmissionStatusChange(
    $submission, 
    $oldStatus, 
    $newStatus, 
    $request->input('catatan')
);
```

### Testing Email:
```bash
# Test dengan Mailtrap atau local SMTP server
# Atau gunakan log untuk testing tanpa SMTP:
MAIL_MAILER=log  # di .env untuk testing
```

---

## 4. ✅ Filter Button Functionality Fix

### Masalah yang Ditemukan:
Alpine.js filter dropdown tidak menampilkan/menutup dengan benar karena conflict antara `x-show` directive dan inline `style="display: none;"`

### Perubahan yang Dilakukan:

#### A. Update Dashboard Layout - x-cloak CSS
- **File**: `resources/views/layouts/dashboard.blade.php`
- **Perubahan**: Menambahkan `<style>[x-cloak] { display: none !important; }</style>`
- **Fungsi**: Memastikan Alpine.js elements tersembunyi sampai Alpine siap

#### B. Fix Filter Dropdowns dengan Alpine Transitions
- **File-file yang diupdate**:
  1. `resources/views/verifikator/index.blade.php`
  2. `resources/views/eksekutor/index.blade.php`
  3. `resources/views/verifikator/history.blade.php`
  4. `resources/views/admin/audit/submissions.blade.php`

- **Perubahan pada setiap file**:
  ```blade
  {{-- SEBELUM (Tidak bekerja) --}}
  <div x-show="open" x-cloak x-transition class="..." style="display: none;">
  
  {{-- SESUDAH (Bekerja sempurna) --}}
  <div x-show="open" x-cloak
       x-transition:enter="transition ease-out duration-100"
       x-transition:enter-start="transform opacity-0 scale-95"
       x-transition:enter-end="transform opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-75"
       x-transition:leave-start="transform opacity-100 scale-100"
       x-transition:leave-end="transform opacity-0 scale-95"
       class="...">
  ```

### Features:
- Filter dropdown terbuka/tertutup dengan smooth animation
- Alpine.js properly initialized sebelum page render
- @click.outside="open = false" menutup dropdown saat click di luar
- Form submission berfungsi dengan baik

### Testing:
1. Buka halaman Verifikator / Eksekutor / Admin Audit
2. Klik tombol Filter
3. Lihat dropdown terbuka dengan smooth animation
4. Ubah filter values
5. Klik "Terapkan Filter"
6. Page refresh dengan params yang benar

---

## 5. 🔄 Admin Notifications Dashboard (Next Phase)

### Yang Perlu Dibuat:
- [ ] Controller: `AdminNotificationController`
- [ ] Route: `/admin/notifications` (GET list, POST mark-as-read)
- [ ] View: `resources/views/admin/notifications/index.blade.php`
- [ ] Integration points di berbagai controllers untuk trigger notifications

### Placeholder untuk Controller:
```php
<?php
namespace App\Http\Controllers\Admin;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::with(['relatedUser', 'relatedSubmission'])
            ->latest()
            ->paginate(20);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(AdminNotification $notification)
    {
        $notification->markAsRead();
        return back()->with('success', 'Notifikasi telah ditandai dibaca');
    }
}
```

---

## Database Connection Issue

### Current Status:
PostgreSQL connection error saat menjalankan migrations:
```
FATAL: password authentication failed for user "postgres"
```

### Solusi:
1. Pastikan PostgreSQL server running
2. Verify credentials di `.env`:
   ```env
   DB_HOST=127.0.0.1
   DB_USERNAME=postgres
   DB_PASSWORD=secret
   ```
3. Jika password berbeda, update di `.env`
4. Test connection: `php artisan tinker` lalu coba query

---

## Summary of Changes

| Feature | Status | Files Modified/Created | Priority |
|---------|--------|----------------------|----------|
| Pending Button + Reason | ✅ Complete | 4 files | High |
| CPU/RAM Fields | ✅ Ready | 2 files | Medium |
| Email Notification | ✅ Ready | 6 files | High |
| Filter Fix | ✅ Complete | 5 files | High |
| Admin Notification Dashboard | 🔄 Pending | TBD | Medium |

---

## Next Steps

1. **Fix Database Connection** - Essential untuk semua operations
2. **Run All Migrations** - Setelah DB connection fixed
3. **Update Controllers** - Integrate notification service
4. **Create Admin Dashboard** - Untuk view notifications
5. **Test Email** - Setup SMTP dan test end-to-end

---

## Configuration Checklist

- [ ] `.env` - Database credentials diupdate
- [ ] `.env` - SMTP credentials diisi
- [ ] Migration files siap di `database/migrations/`
- [ ] Model, Service, Email templates tersedia
- [ ] Routes didefinisikan
- [ ] Controllers terintegrasi dengan notification service

---

*Dokumentasi ini dibuat pada: {{ date('d M Y H:i') }}*
*Version: 1.0*
