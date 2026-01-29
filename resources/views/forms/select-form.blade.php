@extends('layouts.app')

@section('title', 'Generate Form - ' . $submission->no_tiket)

@section('content')
<div class="min-h-screen py-8 lg:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-success-light">
                <svg class="h-8 w-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Formulir Berhasil Dibuat!</h1>
            <p class="mt-2 text-gray-600">Pilih jenis form yang ingin Anda generate</p>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="mb-6 rounded-xl border border-success/30 bg-success-light p-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-success">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        {{-- Ticket Info Card --}}
        @php
            $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);
            $tipePengajuan = $keterangan['tipe_pengajuan'] ?? 'pengajuan_baru';
            $tipePengajuanLabel = match($tipePengajuan) {
                'pengajuan_baru' => 'Pengajuan Baru',
                'perpanjangan' => 'Perpanjangan',
                'perubahan_data' => 'Perubahan Data',
                'upgrade_downgrade' => 'Upgrade/Downgrade',
                'penonaktifan' => 'Penonaktifan',
                'laporan_masalah' => 'Laporan Masalah',
                default => 'Pengajuan'
            };
            $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain';
        @endphp

        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Tiket</p>
                        <p class="font-mono text-xl font-bold text-myunila">{{ $submission->no_tiket }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Jenis Layanan</p>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium
                            @if($serviceType === 'vps') bg-info text-white
                            @elseif($serviceType === 'hosting') bg-gradient-ocean text-white
                            @else bg-gradient-unila text-white
                            @endif">
                            @if($serviceType === 'vps')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                VPS
                            @elseif($serviceType === 'hosting')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                </svg>
                                Hosting
                            @else
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Sub Domain
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid gap-4 sm:grid-cols-4">
                    <div>
                        <p class="text-sm text-gray-500">Tipe Pengajuan</p>
                        <p class="font-medium text-gray-900">{{ $tipePengajuanLabel }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Domain/Layanan</p>
                        <p class="font-medium text-gray-900">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Organisasi</p>
                        <p class="font-medium text-gray-900">{{ $keterangan['nama_organisasi'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-medium text-gray-900">{{ $submission->create_at?->format('d M Y') ?? now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Options --}}
        <div class="grid gap-6 md:grid-cols-2">
            {{-- Option 1: Paperless Form --}}
            <div class="group relative overflow-hidden rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-myunila hover:shadow-lg">
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-unila text-white">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">Form Paperless</h3>
                <p class="mb-4 text-sm text-gray-600">
                    Form digital untuk pengajuan ke TIK. Dapat dilihat langsung di browser dan dicetak jika diperlukan.
                </p>
                <ul class="mb-6 space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Proses cepat & efisien
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Terintegrasi dengan sistem
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Bisa dicetak langsung (Ctrl+P)
                    </li>
                </ul>
                <a href="{{ route('forms.paperless', $submission->no_tiket) }}" 
                   class="btn-primary w-full justify-center inline-flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Lihat Form Paperless
                </a>
            </div>

            {{-- Option 2: Hardcopy Form --}}
            <div class="group relative overflow-hidden rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-sm transition-all hover:border-info hover:shadow-lg">
                <div class="absolute -right-3 -top-3 rotate-12">
                    <span class="inline-block rounded-full bg-warning-light px-3 py-1 text-xs font-semibold text-warning">
                        PDF
                    </span>
                </div>
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-info text-white">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">Form Hardcopy (PDF)</h3>
                <p class="mb-4 text-sm text-gray-600">
                    Form resmi dalam format PDF untuk ditandatangani dan diserahkan ke atasan (Kajur/Dekan/Wakil Rektor).
                </p>
                <ul class="mb-6 space-y-2 text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Format resmi dengan kop surat
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Kolom tanda tangan atasan
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Siap cetak & arsip
                    </li>
                </ul>
                <div class="flex gap-2">
                    <a href="{{ route('forms.hardcopy.preview', $submission->no_tiket) }}" 
                       class="btn-secondary flex-1 justify-center inline-flex items-center gap-2" target="_blank">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview
                    </a>
                    <a href="{{ route('forms.hardcopy.download', $submission->no_tiket) }}" 
                       class="flex-1 justify-center inline-flex items-center gap-2 rounded-xl bg-info px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-info/90">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="mt-8 rounded-2xl border border-info/30 bg-info-light p-6">
            <div class="flex gap-4">
                <svg class="h-6 w-6 flex-shrink-0 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">Langkah Selanjutnya</p>
                    <ol class="mt-2 list-inside list-decimal space-y-1">
                        <li><strong>Download PDF Hardcopy</strong> dan cetak formulir</li>
                        <li>Minta <strong>tanda tangan basah</strong> dari atasan (Kajur/Dekan/Wakil Rektor)</li>
                        <li>Scan formulir yang sudah ditandatangani</li>
                        <li>Upload scan formulir ke sistem</li>
                    </ol>
                    <p class="mt-3 text-xs text-gray-500">
                        <strong>Catatan:</strong> Form Paperless dan Hardcopy berisi data yang sama. Paperless untuk sistem internal TIK, Hardcopy untuk persetujuan atasan.
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('submissions.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Lihat Daftar Pengajuan
            </a>
            <a href="{{ url('/') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
