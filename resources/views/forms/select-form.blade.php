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
                            @if($serviceType === 'vps') badge-service-vps
                            @elseif($serviceType === 'hosting') badge-service-hosting
                            @else badge-service-domain
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

        {{-- Langkah Selanjutnya --}}
        <div class="grid gap-6 md:grid-cols-2">
            {{-- Button 1: Format Dokumen Pengajuan --}}
            <a href="{{ route('forms.paperless', $submission->no_tiket) }}" 
               class="group block rounded-2xl border-2 border-gray-200 bg-white p-8 shadow-sm transition-all hover:border-myunila hover:shadow-xl hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-unila text-white shadow-lg">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="mb-3 text-xl font-bold text-gray-900 group-hover:text-myunila transition">
                        Format Dokumen Pengajuan
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Lihat form paperless dan download PDF untuk ditandatangani atasan
                    </p>
                    <div class="mt-6 flex items-center gap-2 text-myunila font-semibold">
                        <span>Lihat Format</span>
                        <svg class="h-5 w-5 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Button 2: Upload Dokumen --}}
            <a href="{{ route('submissions.upload', $submission) }}" 
               class="group block rounded-2xl border-2 border-gray-200 bg-white p-8 shadow-sm transition-all hover:border-success hover:shadow-xl hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <div class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-success text-white shadow-lg">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3 class="mb-3 text-xl font-bold text-gray-900 group-hover:text-success transition">
                        Upload Dokumen
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Upload formulir yang sudah ditandatangani atasan (PDF/Scan)
                    </p>
                    <div class="mt-6 flex items-center gap-2 text-success font-semibold">
                        <span>Upload File</span>
                        <svg class="h-5 w-5 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                </div>
            </a>
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
                        <li>Lihat dan download <strong>format dokumen</strong> dari sistem</li>
                        <li>Cetak dan minta <strong>tanda tangan</strong> dari atasan</li>
                        <li>Scan formulir yang sudah ditandatangani</li>
                        <li><strong>Upload</strong> scan formulir ke sistem</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Back Button --}}
        <div class="mt-8 flex justify-center">
            <a href="{{ route('submissions.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Pengajuan
            </a>
        </div>
    </div>
</div>
@endsection
