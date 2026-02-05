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
     * Dashboard utama - redirect berdasarkan role atau tampilkan dashboard sesuai role
     */
    public function index()
    {
        $user = Auth::user();
        $roleName = strtolower($user->peran->nm_peran ?? 'pengguna');

        // Pimpinan (Super Admin) mendapat dashboard tersendiri
        if (str_contains($roleName, 'pimpinan')) {
            return redirect()->route('pimpinan.dashboard');
        }

        // Admin dan role khusus mendapat dashboard tersendiri
        if (str_contains($roleName, 'admin')) {
            return $this->adminDashboard();
        }
        
        if ($roleName === 'verifikator') {
            return $this->verifikatorDashboard();
        }
        
        if ($roleName === 'eksekutor') {
            return $this->eksekutorDashboard();
        }

        // Default: Dashboard Pengguna biasa
        return $this->penggunaDashboard();
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
    private function adminDashboard()
    {
        // Stats akun pengguna - Admin hanya mengelola user dengan role "Pengguna"
        $penggunaQuery = User::whereHas('peran', function($q) {
            $q->where('nm_peran', 'Pengguna');
        });

        $userStats = [
            'total' => (clone $penggunaQuery)->count(),
            'aktif' => (clone $penggunaQuery)->where('a_aktif', true)->count(),
            'nonaktif' => (clone $penggunaQuery)->where('a_aktif', false)->count(),
            'sso' => (clone $penggunaQuery)->whereNotNull('sso_id')->count(),
            'lokal' => (clone $penggunaQuery)->whereNull('sso_id')->count(),
        ];

        // Stats pengajuan
        $submissionStats = [
            'total' => Submission::count(),
            'bulan_ini' => Submission::whereMonth('tgl_pengajuan', now()->month)
                ->whereYear('tgl_pengajuan', now()->year)
                ->count(),
            'menunggu_verifikasi' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Diajukan'))
                ->count(),
            'disetujui' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Selesai'))
                ->count(),
        ];

        // Recent user registrations (need verification) - Hanya role Pengguna
        $recentUsers = User::where('a_aktif', false)
            ->whereHas('peran', function($q) {
                $q->where('nm_peran', 'Pengguna');
            })
            ->with('peran')
            ->latest('create_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('userStats', 'submissionStats', 'recentUsers'));
    }

    /**
     * Dashboard untuk Verifikator
     */
    private function verifikatorDashboard()
    {
        // Stats verifikasi
        $stats = [
            'menunggu' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Diajukan'))->count(),
            'disetujui_hari_ini' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Disetujui Verifikator'))
                ->whereDate('last_update', today())
                ->count(),
            'ditolak_hari_ini' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Ditolak Verifikator'))
                ->whereDate('last_update', today())
                ->count(),
        ];

        // Pengajuan yang perlu diverifikasi
        $pendingSubmissions = Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Diajukan'))
            ->with(['pengguna', 'unitKerja', 'jenisLayanan', 'status'])
            ->latest('tgl_pengajuan')
            ->take(5)
            ->get();

        return view('verifikator.dashboard', compact('stats', 'pendingSubmissions'));
    }

    /**
     * Dashboard untuk Eksekutor
     */
    private function eksekutorDashboard()
    {
        // Stats eksekusi
        $stats = [
            'tugas_baru' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Disetujui Verifikator'))->count(),
            'sedang_dikerjakan' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Sedang Dikerjakan'))->count(),
            'selesai_hari_ini' => Submission::whereHas('status', fn($q) => $q->where('nm_status', 'Selesai'))
                ->whereDate('last_update', today())
                ->count(),
        ];

        // Tugas yang perlu dikerjakan
        $tasks = Submission::whereHas('status', fn($q) => $q->whereIn('nm_status', ['Disetujui Verifikator', 'Sedang Dikerjakan']))
            ->with(['pengguna', 'unitKerja', 'jenisLayanan', 'status'])
            ->latest('tgl_pengajuan')
            ->take(5)
            ->get();

        return view('eksekutor.dashboard', compact('stats', 'tasks'));
    }
}
