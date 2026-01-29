<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Menampilkan halaman login.
     */
    public function index(): View
    {
        return view('auth.login'); 
    }

    /**
     * Memproses login.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'usn' => 'required|string',
            'kata_sandi' => 'required|string',
        ]);

        $ssoGroup = $request->input('sso_group');
        $nip      = $request->input('nip');
        $nama     = $request->input('nama');

        // ==========================================================
        // PERBAIKAN 1: LOGIKA MAPPING ROLE (Tabel & Kolom Baru)
        // ==========================================================
        
        // Cari Role Default (Pengguna) dulu sebagai fallback
        $defaultRoleUuid = DB::table('akun.peran')
                            ->where('nm_peran', 'Pengguna')
                            ->value('UUID');

        // Cek Mapping di database baru
        $mapping = DB::table('akun.pemetaan_peran_sso')
                     ->where('atribut_sso', $ssoGroup)
                     ->first();

        // Ambil UUID Role dari mapping, atau pakai default
        $targetRoleUuid = $mapping ? $mapping->peran_uuid : $defaultRoleUuid;

        // ==========================================================
        // PERBAIKAN 2: CEK WHITELIST (Handling UUID)
        // ==========================================================
        
        // Kita cari UUID untuk setiap role
        $adminRoleUuid = DB::table('akun.peran')
                           ->where('nm_peran', 'Administrator')
                           ->value('UUID');
        
        $verifikatorRoleUuid = DB::table('akun.peran')
                                 ->where('nm_peran', 'Verifikator')
                                 ->value('UUID');
        
        $eksekutorRoleUuid = DB::table('akun.peran')
                               ->where('nm_peran', 'Eksekutor')
                               ->value('UUID');

        // Daftar NIP Spesial berdasarkan role
        $specialAdmins = [
            '198501012010011001', // NIP Admin TIK
            'admin',              // Username simpel
        ];
        
        $specialVerifikators = [
            '198702152011012002', // NIP Siti Nurhaliza (Verifikator)
            'verifikator',        // Username simpel
        ];
        
        $specialEksekutors = [
            '199003202015011003', // NIP Andi Prasetyo (Eksekutor)
            'eksekutor',          // Username simpel
        ];

        // Assign role berdasarkan NIP
        if (in_array($nip, $specialAdmins) && $adminRoleUuid) {
            $targetRoleUuid = $adminRoleUuid;
        } elseif (in_array($nip, $specialVerifikators) && $verifikatorRoleUuid) {
            $targetRoleUuid = $verifikatorRoleUuid;
        } elseif (in_array($nip, $specialEksekutors) && $eksekutorRoleUuid) {
            $targetRoleUuid = $eksekutorRoleUuid;
        }

        return back()->withErrors([
            'usn' => 'Username atau password salah.',
        ])->onlyInput('usn');
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (method_exists($this->authService, 'logout')) {
            $this->authService->logout();
        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }
}