<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FormGeneratorController extends Controller
{
    /**
     * Halaman pemilihan jenis form yang akan digenerate
     */
    public function selectForm(string $ticketNumber)
    {
        $submission = Submission::with(['unitKerja.category', 'rincian', 'jenisLayanan', 'status'])
            ->where('no_tiket', $ticketNumber)
            ->firstOrFail();

        return view('forms.select-form', compact('submission'));
    }

    /**
     * Tampilkan form paperless (untuk TIK)
     * Form digital yang bisa dilihat dan dicetak langsung
     */
    public function showPaperless(string $ticketNumber)
    {
        $submission = Submission::with(['unitKerja.category', 'rincian', 'jenisLayanan', 'pengguna'])
            ->where('no_tiket', $ticketNumber)
            ->firstOrFail();

        return view('forms.form-paperless', compact('submission'));
    }

    /**
     * Generate dan download form hardcopy sebagai PDF
     * Untuk diserahkan ke pimpinan/dekan
     */
    public function downloadHardcopy(string $ticketNumber)
    {
        $submission = Submission::with(['unitKerja.category', 'rincian', 'jenisLayanan', 'pengguna'])
            ->where('no_tiket', $ticketNumber)
            ->firstOrFail();

        $serviceName = match($submission->jenisLayanan?->nm_layanan) {
            'hosting' => 'Hosting',
            'vps' => 'VPS',
            default => 'Sub_Domain'
        };
        
        // Get tipe pengajuan from keterangan_keperluan
        $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);
        $tipePengajuan = $keterangan['tipe_pengajuan'] ?? 'pengajuan_baru';
        $tipePengajuanLabel = match($tipePengajuan) {
            'pengajuan_baru' => 'Pengajuan_Baru',
            'perpanjangan' => 'Perpanjangan',
            'perubahan_data' => 'Perubahan_Data',
            'upgrade_downgrade' => 'Upgrade_Downgrade',
            'penonaktifan' => 'Penonaktifan',
            'laporan_masalah' => 'Laporan_Masalah',
            default => 'Permohonan'
        };

        $filename = "Form_{$tipePengajuanLabel}_{$serviceName}_{$ticketNumber}.pdf";

        $pdf = Pdf::loadView('forms.form-hardcopy', compact('submission'));
        
        // Set paper size dan orientasi
        $pdf->setPaper('A4', 'portrait');
        
        // Disable cache untuk selalu generate fresh PDF
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
            'enable_html5_parser' => true,
            'enable_css_float' => true,
            'enable_remote' => true,
        ]);

        return $pdf->download($filename);
    }

    /**
     * Preview form hardcopy di browser (tanpa download)
     */
    public function previewHardcopy(string $ticketNumber)
    {
        $submission = Submission::with(['unitKerja.category', 'rincian', 'jenisLayanan', 'pengguna'])
            ->where('no_tiket', $ticketNumber)
            ->firstOrFail();

        $pdf = Pdf::loadView('forms.form-hardcopy', compact('submission'));
        $pdf->setPaper('A4', 'portrait');
        
        // Disable cache untuk selalu generate fresh PDF
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
            'enable_html5_parser' => true,
            'enable_css_float' => true,
            'enable_remote' => true,
        ]);

        return $pdf->stream("Form_{$ticketNumber}.pdf");
    }
}
