<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionLog;
use App\Models\StatusPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ExecutionController extends Controller
{
    /**
     * Daftar Tugas - Pengajuan yang perlu dieksekusi
     */
    public function index(Request $request): View
    {
        // Get filters from request
        $filters = [
            'search' => $request->get('search'),
            'layanan' => $request->get('layanan', 'all'),
            'tanggal_dari' => $request->get('tanggal_dari'),
            'tanggal_sampai' => $request->get('tanggal_sampai'),
        ];

        // Get per page from request
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        // Ambil status yang sudah disetujui verifikator
        $pendingStatuses = StatusPengajuan::whereIn('nm_status', [
            'Disetujui Verifikator', 
            'Menunggu Eksekusi'
        ])->pluck('UUID');

        $query = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
            ->whereIn('status_uuid', $pendingStatuses);

        // Apply search filter
        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('no_tiket', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('pengguna', function($q) use ($filters) {
                      $q->where('nm', 'like', '%' . $filters['search'] . '%');
                  })
                  ->orWhereHas('rincian', function($q) use ($filters) {
                      $q->where('nm_domain', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        // Apply layanan filter
        if ($filters['layanan'] !== 'all') {
            $query->whereHas('jenisLayanan', function($q) use ($filters) {
                $q->whereRaw('LOWER(nm_layanan) = ?', [strtolower($filters['layanan'])]);
            });
        }

        // Apply date range filter
        if ($filters['tanggal_dari']) {
            $query->whereDate('create_at', '>=', $filters['tanggal_dari']);
        }
        if ($filters['tanggal_sampai']) {
            $query->whereDate('create_at', '<=', $filters['tanggal_sampai']);
        }

        $submissions = $query->orderBy('last_update', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Statistik
        $inProgressStatus = StatusPengajuan::where('nm_status', 'Sedang Dikerjakan')->first();
        $inProgressCount = 0;
        if ($inProgressStatus) {
            $inProgressCount = Submission::where('status_uuid', $inProgressStatus->UUID)->count();
        }

        $stats = [
            'pending' => Submission::whereIn('status_uuid', $pendingStatuses)->count(),
            'in_progress' => $inProgressCount,
            'completed_today' => $this->getCompletedTodayCount(),
            'rejected_today' => $this->getRejectedTodayCount(),
        ];

        return view('eksekutor.index', compact('submissions', 'stats', 'filters'));
    }

    /**
     * Detail pengajuan untuk eksekusi
     */
    public function show(Submission $submission): View
    {
        $submission->load(['pengguna', 'unitKerja.category', 'jenisLayanan', 'status', 'rincian', 'riwayat.statusLama', 'riwayat.statusBaru', 'riwayat.creator']);
        
        // Get verification log (catatan dari verifikator)
        $verificationLog = SubmissionLog::with(['creator.peran'])
            ->where('pengajuan_uuid', $submission->UUID)
            ->whereHas('statusBaru', function($q) {
                $q->whereIn('nm_status', ['Disetujui Verifikator', 'Ditolak Verifikator']);
            })
            ->latest('create_at')
            ->first();
        
        // Get all logs for timeline in sidebar
        $logs = $submission->riwayat()->with(['statusBaru', 'creator'])->orderBy('create_at', 'desc')->get();
        
        return view('eksekutor.show', compact('submission', 'verificationLog', 'logs'));
    }

    /**
     * Terima dan mulai kerjakan (Eksekutor)
     */
    public function accept(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Cari status "Sedang Dikerjakan"
            $newStatus = StatusPengajuan::where('nm_status', 'Sedang Dikerjakan')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Sedang Dikerjakan" tidak ditemukan di database.');
            }

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_baru_uuid' => $newStatus->UUID,
                'catatan_log' => $request->input('catatan', 'Pengajuan diterima dan sedang dikerjakan oleh Eksekutor.'),
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('eksekutor.index')
                ->with('success', 'Pengajuan diterima. Silakan kerjakan sesuai permintaan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Selesaikan pengajuan (Eksekutor)
     */
    public function complete(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'catatan' => 'nullable|string|max:1000',
            'hasil_eksekusi' => 'nullable|string|max:2000',
        ]);

        try {
            DB::beginTransaction();

            // Cari status "Selesai"
            $newStatus = StatusPengajuan::where('nm_status', 'Selesai')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Selesai" tidak ditemukan di database.');
            }

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log dengan hasil eksekusi
            $logMessage = 'Pengajuan SELESAI dikerjakan.';
            if ($request->filled('hasil_eksekusi')) {
                $logMessage .= "\n\nHasil: " . $request->input('hasil_eksekusi');
            }
            if ($request->filled('catatan')) {
                $logMessage .= "\n\nCatatan: " . $request->input('catatan');
            }

            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_baru_uuid' => $newStatus->UUID,
                'catatan_log' => $logMessage,
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('eksekutor.index')
                ->with('success', 'Pengajuan berhasil diselesaikan! Pemohon akan menerima notifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pengajuan (Eksekutor) - Ada kendala
     */
    public function reject(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:1000',
        ], [
            'alasan_penolakan.required' => 'Alasan/kendala wajib diisi.',
            'alasan_penolakan.min' => 'Alasan/kendala minimal 10 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // Cari status "Ditolak Eksekutor"
            $newStatus = StatusPengajuan::where('nm_status', 'Ditolak Eksekutor')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Ditolak Eksekutor" tidak ditemukan di database.');
            }

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log dengan alasan/kendala
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_baru_uuid' => $newStatus->UUID,
                'catatan_log' => 'DITOLAK EKSEKUTOR - Kendala: ' . $request->input('alasan_penolakan'),
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('eksekutor.index')
                ->with('success', 'Pengajuan ditolak karena kendala. Pemohon akan menerima notifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Log Perubahan Status - Semua perubahan status oleh verifikator dan eksekutor
     */
    public function history(Request $request): View
    {
        // Get filters from request
        $filters = [
            'search' => $request->get('search'),
            'layanan' => $request->get('layanan', 'all'),
            'tanggal_dari' => $request->get('tanggal_dari'),
            'tanggal_sampai' => $request->get('tanggal_sampai'),
        ];

        // Get per page from request
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        // Status yang relevan untuk eksekutor (diproses oleh verifikator atau eksekutor)
        $relevantStatuses = StatusPengajuan::whereIn('nm_status', [
            'Disetujui Verifikator',
            'Ditolak Verifikator', 
            'Sedang Dikerjakan',
            'Selesai',
            'Ditolak Eksekutor'
        ])->pluck('UUID');

        $query = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian', 'updater.peran'])
            ->whereIn('status_uuid', $relevantStatuses);

        // Apply search filter
        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('no_tiket', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('pengguna', function($q) use ($filters) {
                      $q->where('nm', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        // Apply layanan filter
        if ($filters['layanan'] !== 'all') {
            $query->whereHas('jenisLayanan', function($q) use ($filters) {
                $q->whereRaw('LOWER(nm_layanan) = ?', [strtolower($filters['layanan'])]);
            });
        }

        // Apply date range filter
        if ($filters['tanggal_dari']) {
            $query->whereDate('last_update', '>=', $filters['tanggal_dari']);
        }
        if ($filters['tanggal_sampai']) {
            $query->whereDate('last_update', '<=', $filters['tanggal_sampai']);
        }

        $submissions = $query->orderBy('last_update', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('eksekutor.history', compact('submissions', 'filters'));
    }

    /**
     * Log Pekerjaan - Pengajuan yang diproses oleh eksekutor yang login
     */
    public function myHistory(Request $request): View
    {
        // Get filters from request
        $filters = [
            'search' => $request->get('search'),
            'layanan' => $request->get('layanan', 'all'),
            'status' => $request->get('status', 'all'),
            'tanggal_dari' => $request->get('tanggal_dari'),
            'tanggal_sampai' => $request->get('tanggal_sampai'),
        ];

        // Get per page from request
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $query = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
            ->where('id_updater', Auth::id());

        // Apply search filter
        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('no_tiket', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('pengguna', function($q) use ($filters) {
                      $q->where('nm', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        // Apply layanan filter
        if ($filters['layanan'] !== 'all') {
            $query->whereHas('jenisLayanan', function($q) use ($filters) {
                $q->whereRaw('LOWER(nm_layanan) = ?', [strtolower($filters['layanan'])]);
            });
        }

        // Apply status filter
        if ($filters['status'] !== 'all') {
            $statusName = match($filters['status']) {
                'sedang_dikerjakan' => 'Sedang Dikerjakan',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak Eksekutor',
                default => null,
            };
            if ($statusName) {
                $query->whereHas('status', function($q) use ($statusName) {
                    $q->where('nm_status', $statusName);
                });
            }
        }

        // Apply date range filter
        if ($filters['tanggal_dari']) {
            $query->whereDate('last_update', '>=', $filters['tanggal_dari']);
        }
        if ($filters['tanggal_sampai']) {
            $query->whereDate('last_update', '<=', $filters['tanggal_sampai']);
        }

        $submissions = $query->orderBy('last_update', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Pengajuan yang sedang dikerjakan oleh eksekutor yang login
        $inProgressStatus = StatusPengajuan::where('nm_status', 'Sedang Dikerjakan')->first();
        $inProgress = collect();
        if ($inProgressStatus) {
            $inProgress = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
                ->where('status_uuid', $inProgressStatus->UUID)
                ->where('id_updater', Auth::id())
                ->orderBy('last_update', 'desc')
                ->get();
        }

        return view('eksekutor.my-history', compact('submissions', 'inProgress', 'filters'));
    }

    /**
     * Timeline perubahan status pengajuan
     */
    public function timeline(Submission $submission): View
    {
        $submission->load(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian']);

        $logs = SubmissionLog::with(['statusLama', 'statusBaru', 'creator.peran'])
            ->where('pengajuan_uuid', $submission->UUID)
            ->orderBy('create_at', 'desc')
            ->get();

        return view('eksekutor.timeline', compact('submission', 'logs'));
    }

    /**
     * Helper: Hitung yang selesai hari ini
     */
    private function getCompletedTodayCount(): int
    {
        $status = StatusPengajuan::where('nm_status', 'Selesai')->first();
        if (!$status) return 0;

        return SubmissionLog::where('status_baru_uuid', $status->UUID)
            ->whereDate('create_at', today())
            ->count();
    }

    /**
     * Helper: Hitung yang ditolak hari ini
     */
    private function getRejectedTodayCount(): int
    {
        $status = StatusPengajuan::where('nm_status', 'Ditolak Eksekutor')->first();
        if (!$status) return 0;

        return SubmissionLog::where('status_baru_uuid', $status->UUID)
            ->whereDate('create_at', today())
            ->count();
    }
}
