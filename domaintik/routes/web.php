<?php

use App\Http\Controllers\DevAuthController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dev Auth Routes (Development Only)
|--------------------------------------------------------------------------
*/
Route::get('/dev/login/{id}', [DevAuthController::class, 'login'])->name('dev.login');
Route::post('/logout', [DevAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Submission Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Form Pengajuan
    Route::get('/pengajuan/buat', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/pengajuan', [SubmissionController::class, 'store'])->name('submissions.store');
    
    // Generate PDF
    Route::get('/pengajuan/{submission}/download-form', [SubmissionController::class, 'downloadForm'])->name('submissions.download-form');
    Route::get('/pengajuan/{submission}/print-form', [SubmissionController::class, 'printForm'])->name('submissions.print-form');
    
    // Upload signed form
    Route::get('/pengajuan/{submission}/upload', [SubmissionController::class, 'showUpload'])->name('submissions.upload');
    Route::post('/pengajuan/{submission}/upload', [SubmissionController::class, 'storeUpload'])->name('submissions.upload.store');
    
    // Detail & History
    Route::get('/pengajuan/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/pengajuan', [SubmissionController::class, 'index'])->name('submissions.index');
});
