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

        // 3. Admin/Super Admin bisa akses semua route
        // Check dengan str_contains karena role bisa "Administrator" atau "Admin"
        if (str_contains(strtolower($userRole), 'admin')) {
            return $next($request);
        }

        // 4. Cek apakah role user termasuk dalam role yang diizinkan route ini
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