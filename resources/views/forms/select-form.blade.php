@extends('layouts.main')

@section('title', 'Generate Form - ' . $submission->ticket_number)

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12">
    <div class="container mx-auto max-w-3xl px-4">
        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-myunila/10">
                <x-icon name="document-text" class="h-8 w-8 text-myunila" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Generate Form Pengajuan</h1>
            <p class="mt-2 text-gray-600">Pilih jenis form yang ingin Anda generate</p>
        </div>

        {{-- Ticket Info Card --}}
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Nomor Tiket</p>
                    <p class="font-mono text-xl font-bold text-myunila">{{ $submission->ticket_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Jenis Layanan</p>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                        @if($submission->service_type === 'vps') bg-info/20 text-info
                        @elseif($submission->service_type === 'hosting') bg-ocean-500/20 text-ocean-600
                        @else bg-myunila/20 text-myunila
                        @endif">
                        @if($submission->service_type === 'vps')
                            <x-icon name="server" class="mr-1.5 h-4 w-4" /> VPS
                        @elseif($submission->service_type === 'hosting')
                            <x-icon name="server-stack" class="mr-1.5 h-4 w-4" /> Hosting
                        @else
                            <x-icon name="globe-alt" class="mr-1.5 h-4 w-4" /> Sub Domain
                        @endif
                    </span>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 pt-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-sm text-gray-500">Pemohon</p>
                        <p class="font-medium text-gray-900">{{ $submission->applicant_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Unit</p>
                        <p class="font-medium text-gray-900">{{ $submission->unit->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-medium text-gray-900">{{ $submission->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Options --}}
        <div class="grid gap-6 md:grid-cols-2">
            {{-- Option 1: Paperless Form --}}
            <div class="group relative overflow-hidden rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-myunila hover:shadow-lg">
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-unila text-white">
                    <x-icon name="computer-desktop" class="h-7 w-7" />
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">Form Paperless</h3>
                <p class="mb-4 text-sm text-gray-600">
                    Form digital untuk pengajuan ke TIK. Dapat dilihat langsung di browser dan dicetak jika diperlukan.
                </p>
                <ul class="mb-6 space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 text-success" />
                        Proses cepat & efisien
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 text-success" />
                        Terintegrasi dengan sistem
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 text-success" />
                        Bisa dicetak langsung
                    </li>
                </ul>
                <a href="{{ route('forms.paperless', $submission->ticket_number) }}" 
                   class="btn btn-primary w-full justify-center">
                    <x-icon name="eye" class="mr-2 h-5 w-5" />
                    Lihat Form Paperless
                </a>
            </div>

            {{-- Option 2: Hardcopy Form --}}
            <div class="group relative overflow-hidden rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-ocean-500 hover:shadow-lg">
                <div class="absolute -right-4 -top-4 rotate-12">
                    <span class="inline-block rounded-full bg-warning/20 px-3 py-1 text-xs font-semibold text-warning">
                        PDF
                    </span>
                </div>
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-ocean text-white">
                    <x-icon name="document-arrow-down" class="h-7 w-7" />
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">Form Hardcopy (PDF)</h3>
                <p class="mb-4 text-sm text-gray-600">
                    Form resmi dalam format PDF untuk ditandatangani dan diserahkan ke pimpinan/dekan fakultas.
                </p>
                <ul class="mb-6 space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 text-success" />
                        Format resmi dengan kop surat
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 text-success" />
                        Kolom tanda tangan pimpinan
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 text-success" />
                        Siap cetak & arsip
                    </li>
                </ul>
                <div class="flex gap-2">
                    <a href="{{ route('forms.hardcopy.preview', $submission->ticket_number) }}" 
                       class="btn btn-outline flex-1 justify-center" target="_blank">
                        <x-icon name="eye" class="mr-2 h-5 w-5" />
                        Preview
                    </a>
                    <a href="{{ route('forms.hardcopy.download', $submission->ticket_number) }}" 
                       class="btn bg-ocean-500 text-white hover:bg-ocean-600 flex-1 justify-center">
                        <x-icon name="arrow-down-tray" class="mr-2 h-5 w-5" />
                        Download
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="mt-8 rounded-xl border border-info/30 bg-info/10 p-4">
            <div class="flex gap-3">
                <x-icon name="information-circle" class="h-6 w-6 flex-shrink-0 text-info" />
                <div class="text-sm text-gray-700">
                    <p class="font-medium text-gray-900">Kapan menggunakan masing-masing form?</p>
                    <ul class="mt-2 list-disc space-y-1 pl-4">
                        <li><strong>Form Paperless:</strong> Untuk proses pengajuan internal ke TIK. Lebih cepat dan tidak perlu cetak fisik.</li>
                        <li><strong>Form Hardcopy (PDF):</strong> Jika unit Anda memerlukan persetujuan tertulis dari pimpinan (Dekan/Kepala Unit) sebelum pengajuan.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Back Button --}}
        <div class="mt-8 text-center">
            <a href="{{ route('submissions.track', $submission->ticket_number) }}" class="btn btn-outline">
                <x-icon name="arrow-left" class="mr-2 h-5 w-5" />
                Kembali ke Tracking
            </a>
        </div>
    </div>
</div>
@endsection
