<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (! Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Ambil role user dari relasi peran
        // User model punya accessor getRoleAttribute() yang return lowercase nm_peran
        $userRole = $user->role; // Ini akan call accessor getRoleAttribute()

        // 3. Pimpinan (Super Admin) bisa akses semua route
        if (str_contains(strtolower($userRole), 'pimpinan')) {
            return $next($request);
        }

        // 4. Admin bisa akses semua route kecuali yang khusus pimpinan
        // Check dengan str_contains karena role bisa "Administrator" atau "Admin"
        if (str_contains(strtolower($userRole), 'admin')) {
            // Admin tidak bisa akses halaman khusus pimpinan
            if (in_array('pimpinan', array_map('strtolower', $roles)) && count($roles) === 1) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
            }
            return $next($request);
        }

        // 5. Cek apakah role user termasuk dalam role yang diizinkan route ini
        // Contoh penggunaan di route: middleware('role:admin,verifikator')
        // Normalize roles untuk case-insensitive comparison
        $normalizedRoles = array_map('strtolower', $roles);
        
        if (in_array(strtolower($userRole), $normalizedRoles)) {
            return $next($request);
        }

        // 5. Jika tidak cocok, tolak akses (403 Forbidden)
        abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
    }
}