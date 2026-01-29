<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormGeneratorController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoAuthController;


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
// GUEST ROUTES (Hanya untuk yang belum login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});


// ==========================================
// AUTHENTICATED ROUTES (Harus login dulu)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // --- Authentication ---
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // --- Dashboard (setelah login) ---
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- Fitur Pengajuan ---
    Route::prefix('pengajuan')->name('submissions.')->group(function () {
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
    });

    // --- Admin Routes ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', function () {
            return "Halaman Manajemen User (Admin Only)";
        })->name('users');
    });
    // Group SSO Auth
    Route::prefix('auth/sso')->name('auth.sso.')->group(function () {
    Route::get('/redirect', [SsoAuthController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [SsoAuthController::class, 'callback'])->name('callback');
    });

    // Halaman Khusus Status Pending (User belum diapprove admin)
    Route::get('/auth/pending-approval', function () {
    return view('auth.pending-approval');
    })->name('auth.pending');

});