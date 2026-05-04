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

class VerificationController extends Controller
{
    /**
     * Dashboard Verifikator - Daftar pengajuan yang perlu diverifikasi
     */
    public function index(Request $request): View
    {
        // Get filters from request
        $filters = [
            'search' => $request->get('search'),
            'layanan' => $request->get('layanan', 'all'),
            'tanggal_dari' => $request->get('tanggal_dari'),
            'tanggal_sampai' => $request->get('tanggal_sampai'),
            'sort_by' => $request->get('sort_by', 'create_at'),
            'sort_dir' => $request->get('sort_dir', 'desc'),
        ];

        // Get per page from request (default: 20, allowed: 10, 20, 50, 100)
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        // Ambil status "Diajukan" atau "Menunggu Verifikasi"
        $pendingStatuses = StatusPengajuan::whereIn('nm_status', ['Diajukan', 'Menunggu Verifikasi'])
            ->pluck('UUID');

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
                $q->where('nm_layanan', $filters['layanan']);
            });
        }

        // Apply date range filter
        if ($filters['tanggal_dari']) {
            $query->whereDate('create_at', '>=', $filters['tanggal_dari']);
        }
        if ($filters['tanggal_sampai']) {
            $query->whereDate('create_at', '<=', $filters['tanggal_sampai']);
        }

        // Apply sorting
        $query->orderBy($filters['sort_by'], $filters['sort_dir']);

        $submissions = $query->paginate($perPage)->appends($request->except('page'));

        // Statistik
        $stats = [
            'pending' => Submission::whereIn('status_uuid', $pendingStatuses)->count(),
            'approved_today' => $this->getApprovedTodayCount('Verifikator'),
            'rejected_today' => $this->getRejectedTodayCount('Verifikator'),
        ];

        return view('verifikator.index', compact('submissions', 'stats', 'filters'));
    }

    /**
     * Detail pengajuan untuk verifikasi
     */
    public function show(Submission $submission): View
    {
        $submission->load(['pengguna', 'unitKerja.category', 'jenisLayanan', 'status', 'rincian', 'riwayat.statusLama', 'riwayat.statusBaru', 'riwayat.creator']);

        $logs = $submission->riwayat()->with(['statusBaru', 'creator'])->orderBy('create_at', 'desc')->get();

        $executionLog = SubmissionLog::with(['creator.peran', 'statusBaru'])
            ->where('pengajuan_uuid', $submission->UUID)
            ->whereHas('statusBaru', function ($q) {
                $q->whereIn('nm_status', ['Sedang Dikerjakan', 'Selesai', 'Ditolak Eksekutor']);
            })
            ->latest('create_at')
            ->first();
        
        return view('verifikator.show', compact('submission', 'logs', 'executionLog'));
    }

    /**
     * Approve pengajuan (Verifikator)
     */
    public function approve(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Validate current status allows approval
            $allowedStatuses = ['Diajukan', 'Menunggu Verifikasi'];
            if (!in_array($submission->status?->nm_status, $allowedStatuses, true)) {
                return back()->with('error', 'Pengajuan ini tidak dalam status yang dapat diverifikasi.');
            }

            // Cari status "Disetujui Verifikator"
            $newStatus = StatusPengajuan::where('nm_status', 'Disetujui Verifikator')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Disetujui Verifikator" tidak ditemukan di database.');
            }

            // Capture old status before update
            $oldStatusUuid = $submission->status_uuid;

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_lama_uuid' => $oldStatusUuid,
                'status_baru_uuid' => $newStatus->UUID,
                'catatan_log' => $request->input('catatan', 'Pengajuan disetujui oleh Verifikator.'),
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('verifikator.index')
                ->with('success', 'Pengajuan berhasil disetujui dan diteruskan ke Eksekutor.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', config('app.debug')
                ? 'Terjadi kesalahan (approve): ' . $e->getMessage()
                : 'Terjadi kesalahan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Reject pengajuan (Verifikator)
     */
    public function reject(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:1000',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // Validate current status allows rejection
            $allowedStatuses = ['Diajukan', 'Menunggu Verifikasi'];
            if (!in_array($submission->status?->nm_status, $allowedStatuses, true)) {
                return back()->with('error', 'Pengajuan ini tidak dalam status yang dapat ditolak.');
            }

            // Cari status "Ditolak Verifikator"
            $newStatus = StatusPengajuan::where('nm_status', 'Ditolak Verifikator')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Ditolak Verifikator" tidak ditemukan di database.');
            }

            // Capture old status before update
            $oldStatusUuid = $submission->status_uuid;

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log dengan alasan penolakan
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_lama_uuid' => $oldStatusUuid,
                'status_baru_uuid' => $newStatus->UUID,
                'catatan_log' => 'DITOLAK: ' . $request->input('alasan_penolakan'),
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('verifikator.index')
                ->with('success', 'Pengajuan ditolak. Pemohon akan menerima notifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', config('app.debug')
                ? 'Terjadi kesalahan (reject): ' . $e->getMessage()
                : 'Terjadi kesalahan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Riwayat verifikasi
     */
    public function history(Request $request): View
    {
        // Get filters from request
        $filters = [
            'status_scope' => $request->get('status_scope', 'all'),
            'search' => $request->get('search'),
            'layanan' => $request->get('layanan', 'all'),
            'tanggal_dari' => $request->get('tanggal_dari'),
            'tanggal_sampai' => $request->get('tanggal_sampai'),
        ];

        // Get per page from request (default: 20, allowed: 10, 20, 50, 100)
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $historyStatuses = StatusPengajuan::whereIn('nm_status', [
            'Disetujui Verifikator',
            'Ditolak Verifikator',
            'Menunggu Eksekusi',
            'Sedang Dikerjakan',
            'Selesai',
            'Ditolak Eksekutor',
        ])->pluck('UUID');

        $query = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian', 'updater.peran', 'latestLog.statusBaru', 'latestLog.creator.peran'])
            ->where(function($q) use ($historyStatuses) {
                $q->whereIn('status_uuid', $historyStatuses)
                  ->orWhereHas('riwayat', function($q) use ($historyStatuses) {
                      $q->whereIn('status_baru_uuid', $historyStatuses);
                  });
            });

        // Scope filter dari dashboard cards
        if ($filters['status_scope'] !== 'all') {
            $approvedStatus = StatusPengajuan::where('nm_status', 'Disetujui Verifikator')->first();
            $rejectedStatus = StatusPengajuan::where('nm_status', 'Ditolak Verifikator')->first();
            $waitingExecutionStatuses = StatusPengajuan::whereIn('nm_status', ['Disetujui Verifikator', 'Menunggu Eksekusi'])->pluck('UUID');
            $inProgressStatus = StatusPengajuan::where('nm_status', 'Sedang Dikerjakan')->first();
            $doneStatus = StatusPengajuan::where('nm_status', 'Selesai')->first();
            $rejectedExecutionStatus = StatusPengajuan::where('nm_status', 'Ditolak Eksekutor')->first();

            switch ($filters['status_scope']) {
                case 'approved_today':
                    if ($approvedStatus) {
                        $query->whereHas('riwayat', function ($q) use ($approvedStatus) {
                            $q->where('status_baru_uuid', $approvedStatus->UUID)
                                ->whereDate('create_at', today());
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                    break;

                case 'rejected_today':
                    if ($rejectedStatus) {
                        $query->whereHas('riwayat', function ($q) use ($rejectedStatus) {
                            $q->where('status_baru_uuid', $rejectedStatus->UUID)
                                ->whereDate('create_at', today());
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                    break;

                case 'waiting_execution':
                    $query->whereIn('status_uuid', $waitingExecutionStatuses);
                    break;

                case 'in_progress':
                    if ($inProgressStatus) {
                        $query->where('status_uuid', $inProgressStatus->UUID);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                    break;

                case 'completed_today':
                    if ($doneStatus) {
                        $query->whereHas('riwayat', function ($q) use ($doneStatus) {
                            $q->where('status_baru_uuid', $doneStatus->UUID)
                                ->whereDate('create_at', today());
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                    break;

                case 'rejected_execution_today':
                    if ($rejectedExecutionStatus) {
                        $query->whereHas('riwayat', function ($q) use ($rejectedExecutionStatus) {
                            $q->where('status_baru_uuid', $rejectedExecutionStatus->UUID)
                                ->whereDate('create_at', today());
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                    break;
            }
        }

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
                $q->where('nm_layanan', $filters['layanan']);
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

        return view('verifikator.history', compact('submissions', 'filters'));
    }

    /**
     * Riwayat verifikasi yang dilakukan oleh user yang login
     */
    public function myHistory(Request $request): View
    {
        // Get filters from request
        $filters = [
            'search' => $request->get('search'),
            'layanan' => $request->get('layanan', 'all'),
            'tanggal_dari' => $request->get('tanggal_dari'),
            'tanggal_sampai' => $request->get('tanggal_sampai'),
        ];

        // Get per page from request (default: 20, allowed: 10, 20, 50, 100)
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $approvedStatus = StatusPengajuan::where('nm_status', 'Disetujui Verifikator')->first();
        $rejectedStatus = StatusPengajuan::where('nm_status', 'Ditolak Verifikator')->first();

        $statusIds = collect([$approvedStatus?->UUID, $rejectedStatus?->UUID])->filter();

        // Get submissions yang diverifikasi oleh user yang login
        // Menggunakan riwayat log (bukan id_updater) agar tidak hilang saat eksekutor memproses
        $query = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian', 'updater'])
            ->whereHas('riwayat', function($q) use ($statusIds) {
                $q->where('id_creator', Auth::id())
                  ->whereIn('status_baru_uuid', $statusIds);
            });

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
                $q->where('nm_layanan', $filters['layanan']);
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

        return view('verifikator.my-history', compact('submissions', 'filters'));
    }

    /**
     * Pending pengajuan (Menunggu klarifikasi dari pemohon)
     */
    public function pending(Request $request, Submission $submission): RedirectResponse
    {
        $request->validate([
            'alasan_pending' => 'required|string|min:10|max:1000',
        ], [
            'alasan_pending.required' => 'Alasan pending wajib diisi.',
            'alasan_pending.min' => 'Alasan pending minimal 10 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // Validate current status allows pending
            $allowedStatuses = ['Diajukan', 'Menunggu Verifikasi'];
            if (!in_array($submission->status?->nm_status, $allowedStatuses, true)) {
                return back()->with('error', 'Pengajuan ini tidak dalam status yang dapat di-pending.');
            }

            // Cari status "Pending Verifikasi"
            $newStatus = StatusPengajuan::where('nm_status', 'Pending Verifikasi')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Pending Verifikasi" tidak ditemukan di database.');
            }

            // Capture old status before update
            $oldStatusUuid = $submission->status_uuid;

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log dengan alasan pending
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
                'status_lama_uuid' => $oldStatusUuid,
                'status_baru_uuid' => $newStatus->UUID,
                'catatan_log' => 'PENDING: ' . $request->input('alasan_pending'),
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('verifikator.index')
                ->with('success', 'Pengajuan dipending. Pemohon akan menerima notifikasi untuk memberikan klarifikasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', config('app.debug')
                ? 'Terjadi kesalahan (pending): ' . $e->getMessage()
                : 'Terjadi kesalahan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Helper: Hitung yang disetujui hari ini
     */
    private function getApprovedTodayCount(string $role): int
    {
        $statusName = $role === 'Verifikator' ? 'Disetujui Verifikator' : 'Selesai';
        $status = StatusPengajuan::where('nm_status', $statusName)->first();
        
        if (!$status) return 0;

        return SubmissionLog::where('status_baru_uuid', $status->UUID)
            ->whereDate('create_at', today())
            ->count();
    }

    /**
     * Helper: Hitung yang ditolak hari ini
     */
    private function getRejectedTodayCount(string $role): int
    {
        $statusName = $role === 'Verifikator' ? 'Ditolak Verifikator' : 'Ditolak Eksekutor';
        $status = StatusPengajuan::where('nm_status', $statusName)->first();
        
        if (!$status) return 0;

        return SubmissionLog::where('status_baru_uuid', $status->UUID)
            ->whereDate('create_at', today())
            ->count();
    }
}
