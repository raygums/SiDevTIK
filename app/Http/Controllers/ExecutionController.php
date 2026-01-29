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
     * Dashboard Eksekutor - Daftar pengajuan yang perlu dieksekusi
     */
    public function index(): View
    {
        // Ambil status yang sudah disetujui verifikator
        $pendingStatuses = StatusPengajuan::whereIn('nm_status', [
            'Disetujui Verifikator', 
            'Menunggu Eksekusi'
        ])->pluck('UUID');

        $submissions = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
            ->whereIn('status_uuid', $pendingStatuses)
            ->orderBy('create_at', 'desc')
            ->paginate(10);

        // Pengajuan yang sedang dikerjakan oleh eksekutor ini
        $inProgressStatus = StatusPengajuan::where('nm_status', 'Sedang Dikerjakan')->first();
        $inProgress = collect();
        if ($inProgressStatus) {
            $inProgress = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
                ->where('status_uuid', $inProgressStatus->UUID)
                ->orderBy('last_update', 'desc')
                ->get();
        }

        // Statistik
        $stats = [
            'pending' => Submission::whereIn('status_uuid', $pendingStatuses)->count(),
            'in_progress' => $inProgress->count(),
            'completed_today' => $this->getCompletedTodayCount(),
            'rejected_today' => $this->getRejectedTodayCount(),
        ];

        return view('eksekutor.index', compact('submissions', 'inProgress', 'stats'));
    }

    /**
     * Detail pengajuan untuk eksekusi
     */
    public function show(Submission $submission): View
    {
        $submission->load(['pengguna', 'unitKerja.category', 'jenisLayanan', 'status', 'rincian', 'riwayat.status']);
        
        return view('eksekutor.show', compact('submission'));
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
     * Riwayat eksekusi
     */
    public function history(): View
    {
        $completedStatus = StatusPengajuan::where('nm_status', 'Selesai')->first();
        $rejectedStatus = StatusPengajuan::where('nm_status', 'Ditolak Eksekutor')->first();

        $statusIds = collect([$completedStatus?->UUID, $rejectedStatus?->UUID])->filter();

        $submissions = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
            ->whereIn('status_uuid', $statusIds)
            ->orderBy('last_update', 'desc')
            ->paginate(15);

        return view('eksekutor.history', compact('submissions'));
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
