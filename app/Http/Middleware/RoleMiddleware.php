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

        // 2. Ambil role user saat ini
        // Kita handle support untuk Enum (Laravel 11/12 Casting) atau String biasa
        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        // 3. Admin/Super Admin bisa akses semua route
        if ($userRole === 'admin') {
            return $next($request);
        }

        // 4. Cek apakah role user termasuk dalam role yang diizinkan route ini
        // Contoh penggunaan di route: middleware('role:admin,verifikator')
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 5. Jika tidak cocok, tolak akses (403 Forbidden)
        abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
    }
}