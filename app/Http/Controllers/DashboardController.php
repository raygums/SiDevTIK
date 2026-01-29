<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Submission;
use App\Models\User;
use App\Models\StatusPengajuan;
use App\Models\JenisLayanan;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role user
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'verifikator' => redirect()->route('verifikator.index'),
            'eksekutor' => redirect()->route('eksekutor.index'),
            default => $this->penggunaDashboard(),
        };
    }

    /**
     * Dashboard untuk Pengguna biasa
     */
    private function penggunaDashboard()
    {
        $user = Auth::user();
        
        $submissions = Submission::where('pengguna_uuid', $user->UUID)
            ->with(['status', 'jenisLayanan', 'rincian'])
            ->latest('tgl_pengajuan')
            ->take(5)
            ->get();

        $stats = [
            'total' => Submission::where('pengguna_uuid', $user->UUID)->count(),
            'dalam_proses' => Submission::where('pengguna_uuid', $user->UUID)
                ->whereHas('status', fn($q) => $q->whereNotIn('nm_status', ['Selesai', 'Ditolak Verifikator', 'Ditolak Eksekutor', 'Draft']))
                ->count(),
            'selesai' => Submission::where('pengguna_uuid', $user->UUID)
                ->whereHas('status', fn($q) => $q->where('nm_status', 'Selesai'))
                ->count(),
            'ditolak' => Submission::where('pengguna_uuid', $user->UUID)
                ->whereHas('status', fn($q) => $q->whereIn('nm_status', ['Ditolak Verifikator', 'Ditolak Eksekutor']))
                ->count(),
        ];

        return view('dashboard', compact('submissions', 'stats'));
    }

    /**
     * Dashboard untuk Admin/Super Admin
     */
    public function adminDashboard()
    {
        // Stats overview
        $stats = [
            'total_pengajuan' => Submission::count(),
            'pengajuan_bulan_ini' => Submission::whereMonth('tgl_pengajuan', now()->month)
                ->whereYear('tgl_pengajuan', now()->year)
                ->count(),
            'menunggu_verifikasi' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Diajukan'))
                ->count(),
            'menunggu_eksekusi' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Disetujui Verifikator'))
                ->count(),
            'sedang_dikerjakan' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Sedang Dikerjakan'))
                ->count(),
            'selesai' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Selesai'))
                ->count(),
            'ditolak' => Submission::whereHas('status', fn($q) => $q->whereIn('nm_status', ['Ditolak Verifikator', 'Ditolak Eksekutor']))
                ->count(),
            'total_users' => User::count(),
        ];

        // Stats per layanan
        $layananStats = JenisLayanan::withCount('submissions')->get();

        // Recent submissions
        $recentSubmissions = Submission::with(['pengguna', 'status', 'jenisLayanan', 'rincian', 'unitKerja'])
            ->latest('tgl_pengajuan')
            ->take(10)
            ->get();

        // Users per role
        $userStats = [
            'admin' => User::where('role', 'admin')->count(),
            'verifikator' => User::where('role', 'verifikator')->count(),
            'eksekutor' => User::where('role', 'eksekutor')->count(),
            'pengguna' => User::where('role', 'pengguna')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'layananStats', 'recentSubmissions', 'userStats'));
    }
}
