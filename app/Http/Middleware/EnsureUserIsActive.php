<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memastikan user yang terautentikasi memiliki status aktif.
 * 
 * Flow Logic:
 * 1. Jika user belum login -> skip (handled by auth middleware)
 * 2. Jika user.a_aktif = false:
 *    - Route /dashboard -> izinkan akses, set flag untuk UI
 *    - Route lainnya (pengajuan, etc) -> blokir dengan redirect
 * 3. Jika user.a_aktif = true -> izinkan semua akses
 * 
 * Efektivitas = Keterbacaan + Kesederhanaan + Konsistensi + Reusabilitas
 */
class EnsureUserIsActive
{
    /**
     * Whitelist route yang tetap dapat diakses meskipun akun belum aktif.
     * Route ini dibiarkan accessible untuk memberi feedback kepada user.
     */
    protected array $allowedRoutes = [
        'dashboard',
        'logout',
    ];

    /**
     * Handle incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip jika user belum login (akan ditangani oleh auth middleware)
        if (!$request->user()) {
            return $next($request);
        }

        $user = $request->user();

        // User aktif, berikan akses penuh
        if ($user->a_aktif) {
            return $next($request);
        }

        // User TIDAK aktif: cek apakah route yang diakses termasuk whitelist
        if ($this->isAllowedRoute($request)) {
            // Set flag untuk memberi tahu UI bahwa user belum aktif
            // Flag ini dapat digunakan di Blade untuk menampilkan banner warning
            $request->attributes->set('user_inactive', true);
            return $next($request);
        }

        // Blokir akses ke route selain whitelist
        // Redirect ke dashboard dengan pesan error yang jelas
        return redirect()
            ->route('dashboard')
            ->with('error', 'Akun Anda belum diaktifkan oleh Admin. Harap tunggu proses aktivasi sebelum menggunakan fitur ini.');
    }

    /**
     * Periksa apakah route yang diakses termasuk dalam whitelist.
     * 
     * Menggunakan route name untuk fleksibilitas dan maintainability.
     * Alternatif lain adalah menggunakan path matching, namun route name
     * lebih reliable ketika ada perubahan URL structure.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isAllowedRoute(Request $request): bool
    {
        $currentRouteName = $request->route()?->getName();

        if (!$currentRouteName) {
            return false;
        }

        // Cek exact match atau prefix match (untuk nested routes)
        foreach ($this->allowedRoutes as $allowedRoute) {
            if ($currentRouteName === $allowedRoute || 
                str_starts_with($currentRouteName, $allowedRoute . '.')) {
                return true;
            }
        }

        return false;
    }
}
