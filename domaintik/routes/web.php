<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// --- GUEST ROUTES (Hanya untuk yang BELUM login) ---
Route::middleware('guest')->group(function () {
    // Halaman Login
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    // Proses Login
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// --- AUTHENTICATED ROUTES (Harus LOGIN dulu) ---
Route::middleware('auth')->group(function () {
    
    // Proses Logout
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Dashboard Utama
    Route::get('/dashboard', function () {
        // Saat ini mengarah ke view design yang sudah ada
        return view('design.dashboard');
    })->name('dashboard');

    // --- ROLE: ADMIN ONLY ---
    // Middleware 'role:admin' memastikan hanya admin yang bisa akses
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', function () {
            return "Halaman Manajemen User (Hanya Admin)";
        })->name('admin.users');
        
        // Tambahkan route pengelolaan domain di sini nanti
    });

    // --- ROLE: VERIFIKATOR & ADMIN ---
    // Middleware 'role:admin,verifikator' membolehkan kedua role ini akses
    Route::middleware('role:admin,verifikator')->group(function () {
        Route::get('/verifikasi-berkas', function () {
            return "Halaman Verifikasi Berkas";
        })->name('verifikasi.index');
    });

});
