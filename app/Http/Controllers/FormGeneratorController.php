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

        $filename = "Form_Permohonan_{$serviceName}_{$ticketNumber}.pdf";

        $pdf = Pdf::loadView('forms.form-hardcopy', compact('submission'));
        
        // Set paper size dan orientasi
        $pdf->setPaper('A4', 'portrait');

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

        return $pdf->stream("Form_{$ticketNumber}.pdf");
    }
}
