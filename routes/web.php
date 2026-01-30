<?php

use App\Http\Controllers\AuthController;
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
        Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/users', function () {
            return "Halaman Manajemen User (Admin Only)";
        })->name('users');
    });

    // --- Verifikator Routes ---
    Route::middleware('role:verifikator')->prefix('verifikator')->name('verifikator.')->group(function () {
        Route::get('/', [VerificationController::class, 'index'])->name('index');
        Route::get('/riwayat', [VerificationController::class, 'history'])->name('history');
        Route::get('/{submission}', [VerificationController::class, 'show'])->name('show');
        Route::post('/{submission}/approve', [VerificationController::class, 'approve'])->name('approve');
        Route::post('/{submission}/reject', [VerificationController::class, 'reject'])->name('reject');
    });

    // --- Eksekutor Routes ---
    Route::middleware('role:eksekutor')->prefix('eksekutor')->name('eksekutor.')->group(function () {
        Route::get('/', [ExecutionController::class, 'index'])->name('index');
        Route::get('/riwayat', [ExecutionController::class, 'history'])->name('history');
        Route::get('/{submission}', [ExecutionController::class, 'show'])->name('show');
        Route::post('/{submission}/accept', [ExecutionController::class, 'accept'])->name('accept');
        Route::post('/{submission}/complete', [ExecutionController::class, 'complete'])->name('complete');
        Route::post('/{submission}/reject', [ExecutionController::class, 'reject'])->name('reject');
    });

});