<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExecutionController;
use App\Http\Controllers\FormGeneratorController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;


// ==========================================
// PUBLIC ROUTES (Bisa diakses tanpa login)
// ==========================================
Route::get('/', function () {
    return view('home');
})->name('home');

// --- Generate Form Routes (Public untuk akses mudah) ---
Route::prefix('form')->name('forms.')->group(function () {
    Route::get('/{ticketNumber}', [FormGeneratorController::class, 'selectForm'])->name('select');
    Route::get('/{ticketNumber}/paperless', [FormGeneratorController::class, 'showPaperless'])->name('paperless');
    Route::get('/{ticketNumber}/hardcopy/preview', [FormGeneratorController::class, 'previewHardcopy'])->name('hardcopy.preview');
    Route::get('/{ticketNumber}/hardcopy/download', [FormGeneratorController::class, 'downloadHardcopy'])->name('hardcopy.download');
});


// ==========================================
// SSO ROUTES (sesuai dengan URL yang didaftarkan di akses.unila.ac.id)
// ==========================================
Route::get('/login/sso', [SSOController::class, 'redirectToSSO'])->name('sso.login');
Route::get('/auth/sso/callback', [SSOController::class, 'handleCallback'])->name('sso.callback');


// ==========================================
// GUEST ROUTES (Hanya untuk yang belum login)
// ==========================================
Route::middleware('guest')->group(function () {
    // Menampilkan halaman login dengan dua opsi: kredensial lokal dan SSO
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    
    // Proses login dengan kredensial lokal (username & password)
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');

    // --- Registrasi User Mandiri ---
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});


// ==========================================
// AUTHENTICATED ROUTES (Harus login dulu)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // --- Authentication ---
    Route::post('/logout', [SSOController::class, 'logout'])->name('logout');

    // --- Dashboard (setelah login, redirect berdasarkan role) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Fitur Pengajuan (Membutuhkan akun aktif) ---
    Route::middleware('active')->prefix('pengajuan')->name('submissions.')->group(function () {
        // Form & Store
        Route::get('/buat', [SubmissionController::class, 'create'])->name('create');
        Route::post('/', [SubmissionController::class, 'store'])->name('store');
        
        // List & Detail
        Route::get('/', [SubmissionController::class, 'index'])->name('index');
        Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
        
        // Actions (Upload, Download, Print)
        Route::get('/{submission}/download-form', [SubmissionController::class, 'downloadForm'])->name('download-form');
        Route::get('/{submission}/print-form', [SubmissionController::class, 'printForm'])->name('print-form');
        Route::get('/{submission}/upload', [SubmissionController::class, 'showUpload'])->name('upload');
        Route::post('/{submission}/upload', [SubmissionController::class, 'storeUpload'])->name('upload.store');
        
        // Quick Submit (Development/Testing only)
        Route::post('/{submission}/quick-submit', [SubmissionController::class, 'quickSubmit'])->name('quick-submit');
    });

    // --- Admin Routes ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // User Verification & Management
        Route::get('/users/verification', [\App\Http\Controllers\Admin\AdminController::class, 'userVerification'])->name('users.verification');
        Route::post('/users/{uuid}/toggle-status', [\App\Http\Controllers\Admin\AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::post('/users/bulk-activate', [\App\Http\Controllers\Admin\AdminController::class, 'bulkActivate'])->name('users.bulk-activate');
        Route::get('/users/never-logged-in', [\App\Http\Controllers\Admin\AdminController::class, 'usersNeverLoggedIn'])->name('users.never-logged-in');
        
        // Audit Logs (Activity: Login & Submission)
        Route::get('/audit/aktivitas', [\App\Http\Controllers\Admin\AuditLogController::class, 'loginLogs'])->name('audit.aktivitas');
        Route::get('/audit/submissions', [\App\Http\Controllers\Admin\AuditLogController::class, 'submissionLogs'])->name('audit.submissions');
        Route::get('/audit/user/{uuid}', [\App\Http\Controllers\Admin\AuditLogController::class, 'userDetail'])->name('audit.user-detail');
    });

    // --- Verifikator Routes ---
    Route::middleware('role:verifikator')->prefix('verifikator')->name('verifikator.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'verifikator'])->name('dashboard');
        
        // Pengajuan Verification
        Route::get('/daftar-pengajuan', [VerificationController::class, 'index'])->name('index');
        Route::get('/riwayat-verifikasi', [VerificationController::class, 'history'])->name('history');
        Route::get('/log-aktivitas', [VerificationController::class, 'myHistory'])->name('my-history');
        Route::get('/{submission}', [VerificationController::class, 'show'])->name('show');
        Route::post('/{submission}/approve', [VerificationController::class, 'approve'])->name('approve');
        Route::post('/{submission}/reject', [VerificationController::class, 'reject'])->name('reject');
    });

    // --- Eksekutor Routes ---
    Route::middleware('role:eksekutor')->prefix('eksekutor')->name('eksekutor.')->group(function () {
        Route::get('/', [ExecutionController::class, 'index'])->name('index');
        Route::get('/riwayat', [ExecutionController::class, 'history'])->name('history');
        Route::get('/log-pekerjaan', [ExecutionController::class, 'myHistory'])->name('my-history');
        Route::get('/timeline/{submission}', [ExecutionController::class, 'timeline'])->name('timeline');
        Route::get('/{submission}', [ExecutionController::class, 'show'])->name('show');
        Route::post('/{submission}/accept', [ExecutionController::class, 'accept'])->name('accept');
        Route::post('/{submission}/complete', [ExecutionController::class, 'complete'])->name('complete');
        Route::post('/{submission}/reject', [ExecutionController::class, 'reject'])->name('reject');
    });

});