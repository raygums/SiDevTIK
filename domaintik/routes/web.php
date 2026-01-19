<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;


// Public Routes
Route::get('/', function () {
    return view('home');
})->name('home');


//Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // --- Authentication ---
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // --- Dashboard ---
    Route::get('/dashboard', function () {
        return view('design.dashboard'); // Sesuaikan dengan lokasi file view dashboard kamu
    })->name('dashboard');

    // --- Fitur Pengajuan ---
    // Group ini bisa diakses oleh User biasa
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

    // --- Admin Routes (Protected by RoleMiddleware) ---
    // Hanya user dengan role 'admin' yang bisa masuk sini
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', function () {
            return "Halaman Manajemen User (Admin Only)";
        })->name('users');
        
        // Nanti tambahkan route approval/verifikasi di sini atau di group verifikator
    });

});