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
    public function index(): View
    {
        // Ambil status "Diajukan" atau "Menunggu Verifikasi"
        $pendingStatuses = StatusPengajuan::whereIn('nm_status', ['Diajukan', 'Menunggu Verifikasi'])
            ->pluck('UUID');

        $submissions = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
            ->whereIn('status_uuid', $pendingStatuses)
            ->orderBy('create_at', 'desc')
            ->paginate(10);

        // Statistik
        $stats = [
            'pending' => Submission::whereIn('status_uuid', $pendingStatuses)->count(),
            'approved_today' => $this->getApprovedTodayCount('Verifikator'),
            'rejected_today' => $this->getRejectedTodayCount('Verifikator'),
        ];

        return view('verifikator.index', compact('submissions', 'stats'));
    }

    /**
     * Detail pengajuan untuk verifikasi
     */
    public function show(Submission $submission): View
    {
        $submission->load(['pengguna', 'unitKerja.category', 'jenisLayanan', 'status', 'rincian', 'riwayat.status']);
        
        return view('verifikator.show', compact('submission'));
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

            // Cari status "Disetujui Verifikator"
            $newStatus = StatusPengajuan::where('nm_status', 'Disetujui Verifikator')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Disetujui Verifikator" tidak ditemukan di database.');
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
                'catatan_log' => $request->input('catatan', 'Pengajuan disetujui oleh Verifikator.'),
                'id_creator' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('verifikator.index')
                ->with('success', 'Pengajuan berhasil disetujui dan diteruskan ke Eksekutor.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

            // Cari status "Ditolak Verifikator"
            $newStatus = StatusPengajuan::where('nm_status', 'Ditolak Verifikator')->first();
            
            if (!$newStatus) {
                throw new \Exception('Status "Ditolak Verifikator" tidak ditemukan di database.');
            }

            // Update status pengajuan
            $submission->update([
                'status_uuid' => $newStatus->UUID,
                'id_updater' => Auth::id(),
            ]);

            // Buat log dengan alasan penolakan
            SubmissionLog::create([
                'pengajuan_uuid' => $submission->UUID,
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
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat verifikasi
     */
    public function history(): View
    {
        $approvedStatus = StatusPengajuan::where('nm_status', 'Disetujui Verifikator')->first();
        $rejectedStatus = StatusPengajuan::where('nm_status', 'Ditolak Verifikator')->first();

        $statusIds = collect([$approvedStatus?->UUID, $rejectedStatus?->UUID])->filter();

        $submissions = Submission::with(['pengguna', 'unitKerja', 'jenisLayanan', 'status', 'rincian'])
            ->whereIn('status_uuid', $statusIds)
            ->orWhereHas('riwayat', function($q) use ($statusIds) {
                $q->whereIn('status_baru_uuid', $statusIds);
            })
            ->orderBy('last_update', 'desc')
            ->paginate(15);

        return view('verifikator.history', compact('submissions'));
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
