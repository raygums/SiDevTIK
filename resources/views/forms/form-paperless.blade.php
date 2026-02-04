@extends('layouts.app')

@section('title', 'Form Paperless - ' . $submission->no_tiket)

@section('content')
@php
    $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain';
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
@endphp

<div class="min-h-screen py-8 lg:py-12 print:py-0 print:min-h-0">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 print:max-w-full print:px-0">
        
        {{-- Print Button (hide on print) --}}
        <div class="mb-6 flex items-center justify-between print:hidden">
            <a href="{{ route('forms.select', $submission->no_tiket) }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <button onclick="window.print()" class="btn-primary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak (Ctrl+P)
            </button>
        </div>

        {{-- Form Container --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm print:rounded-none print:border-none print:shadow-none">
            
            {{-- Header --}}
            <div class="border-b border-gray-200 bg-gradient-to-r from-myunila to-myunila-700 px-6 py-6 print:bg-myunila">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/20">
                            @if($serviceType === 'vps')
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            @elseif($serviceType === 'hosting')
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            @endif
                        </div>
                        <div class="text-white">
                            <h1 class="text-xl font-bold sm:text-2xl">
                                Form {{ $tipePengajuanLabel }}
                                @if($serviceType === 'vps')
                                    Layanan VPS
                                @elseif($serviceType === 'hosting')
                                    Layanan Hosting
                                @else
                                    Layanan Sub Domain
                                @endif
                            </h1>
                            <p class="text-sm text-white/80">UPA TIK Universitas Lampung</p>
                        </div>
                    </div>
                    <div class="text-right text-white">
                        <p class="text-sm text-white/80">No. Tiket</p>
                        <p class="font-mono text-lg font-bold">{{ $submission->no_tiket }}</p>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6 sm:p-8">
                
                {{-- Ticket & Date Info --}}
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-gray-50 p-4 print:bg-gray-100">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium
                            @if($serviceType === 'vps') bg-info text-white
                            @elseif($serviceType === 'hosting') bg-gradient-ocean text-white
                            @else bg-gradient-unila text-white
                            @endif">
                            {{ ucfirst($serviceType) }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-myunila-100 px-3 py-1 text-sm font-medium text-myunila">
                            {{ $tipePengajuanLabel }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Tanggal:</span>
                        {{ $submission->create_at?->format('d F Y, H:i') ?? now()->format('d F Y, H:i') }} WIB
                    </div>
                </div>

                {{-- Existing Service Info (if not new submission) --}}
                @if($tipePengajuan !== 'pengajuan_baru' && !empty($keterangan['existing']))
                <div class="mb-6 rounded-xl border border-warning/30 bg-warning-light p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-900">
                        <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Layanan Existing
                    </h3>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @if(!empty($keterangan['existing']['domain']))
                        <div>
                            <p class="text-xs text-gray-500">Domain/Layanan yang Dimaksud</p>
                            <p class="font-mono font-medium text-gray-900">{{ $keterangan['existing']['domain'] }}</p>
                        </div>
                        @endif
                        @if(!empty($keterangan['existing']['ticket']))
                        <div>
                            <p class="text-xs text-gray-500">No. Tiket Sebelumnya</p>
                            <p class="font-mono font-medium text-gray-900">{{ $keterangan['existing']['ticket'] }}</p>
                        </div>
                        @endif
                        @if(!empty($keterangan['existing']['expired']))
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Expired</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['existing']['expired'] }}</p>
                        </div>
                        @endif
                        @if(!empty($keterangan['existing']['notes']))
                        <div class="sm:col-span-2">
                            <p class="text-xs text-gray-500">Keterangan</p>
                            <p class="font-medium text-gray-900 whitespace-pre-line">{{ $keterangan['existing']['notes'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Section 1: Data Pemohon/Organisasi --}}
                <div class="mb-6">
                    <h3 class="mb-4 flex items-center gap-2 border-b border-gray-200 pb-2 text-base font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">1</span>
                        Data Pemohon / Organisasi
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Nama Lembaga / Organisasi / Kegiatan</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['nama_organisasi'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Kategori</p>
                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $keterangan['kategori_pemohon'] ?? '-')) }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 sm:col-span-2">
                            <p class="text-xs text-gray-500">Unit Kerja</p>
                            <p class="font-medium text-gray-900">{{ $submission->unitKerja?->nm_unit ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Penanggung Jawab Administratif --}}
                <div class="mb-6">
                    <h3 class="mb-4 flex items-center gap-2 border-b border-gray-200 pb-2 text-base font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">2</span>
                        Penanggung Jawab Administratif
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Nama Lengkap</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['name'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Kategori</p>
                            <p class="font-medium text-gray-900">{{ isset($keterangan['kategori_admin']) ? ucfirst($keterangan['kategori_admin']) : '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Jabatan</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['position'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">NIP/NIDN</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['nip'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['email'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">No. HP</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['phone'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Telepon Kantor</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['telepon_kantor'] ?? '-' }}</p>
                        </div>
                        @if(!empty($keterangan['admin']['alamat_kantor']))
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Alamat Kantor</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['alamat_kantor'] }}</p>
                        </div>
                        @endif
                        @if(!empty($keterangan['admin']['alamat_rumah']))
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Alamat Rumah</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['admin']['alamat_rumah'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Section 3: Penanggung Jawab Teknis --}}
                <div class="mb-6">
                    <h3 class="mb-4 flex items-center gap-2 border-b border-gray-200 pb-2 text-base font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">3</span>
                        Penanggung Jawab Teknis
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Nama Lengkap</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['name'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Kategori</p>
                            <p class="font-medium text-gray-900">{{ isset($keterangan['kategori_tech']) ? ucfirst($keterangan['kategori_tech']) : '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">NIP/NPM</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['nip'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">NIK/Passport</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['nik'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['email'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">No. HP</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['phone'] ?? '-' }}</p>
                        </div>
                        @if(!empty($keterangan['tech']['alamat_kantor']))
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Alamat Kantor</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['alamat_kantor'] }}</p>
                        </div>
                        @endif
                        @if(!empty($keterangan['tech']['alamat_rumah']))
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Alamat Rumah</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['tech']['alamat_rumah'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Section 4: Detail Layanan --}}
                <div class="mb-6">
                    <h3 class="mb-4 flex items-center gap-2 border-b border-gray-200 pb-2 text-base font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">4</span>
                        @if($serviceType === 'vps')
                            Spesifikasi VPS
                        @elseif($serviceType === 'hosting')
                            Detail Hosting
                        @else
                            Detail Sub Domain
                        @endif
                    </h3>
                    
                    <div class="grid gap-4 sm:grid-cols-2">
                        @if($serviceType === 'domain')
                            <div class="rounded-lg bg-myunila-50 p-4 sm:col-span-2">
                                <p class="text-xs text-myunila-700">Sub Domain yang Diminta</p>
                                <p class="font-mono text-lg font-bold text-myunila">{{ $submission->rincian?->nm_domain ?? '-' }}.unila.ac.id</p>
                            </div>
                        @elseif($serviceType === 'hosting')
                            <div class="rounded-lg bg-myunila-50 p-4">
                                <p class="text-xs text-myunila-700">Nama Akun Hosting</p>
                                <p class="font-mono text-lg font-bold text-myunila">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">Kuota Storage</p>
                                <p class="font-medium text-gray-900">{{ $keterangan['hosting']['quota'] ?? $submission->rincian?->kapasitas_penyimpanan ?? '-' }} MB</p>
                            </div>
                        @elseif($serviceType === 'vps')
                            <div class="rounded-lg bg-myunila-50 p-4 sm:col-span-2">
                                <p class="text-xs text-myunila-700">Hostname VPS</p>
                                <p class="font-mono text-lg font-bold text-myunila">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">CPU</p>
                                <p class="font-medium text-gray-900">{{ $keterangan['vps']['cpu'] ?? '-' }} Core</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">RAM</p>
                                <p class="font-medium text-gray-900">{{ $keterangan['vps']['ram'] ?? '-' }} GB</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">Storage</p>
                                <p class="font-medium text-gray-900">{{ $keterangan['vps']['storage'] ?? $submission->rincian?->kapasitas_penyimpanan ?? '-' }} GB</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">Sistem Operasi</p>
                                <p class="font-medium text-gray-900">{{ $keterangan['vps']['os'] ?? '-' }}</p>
                            </div>
                            @if(!empty($keterangan['vps']['purpose']))
                            <div class="rounded-lg bg-gray-50 p-3 sm:col-span-2">
                                <p class="text-xs text-gray-500">Tujuan Penggunaan VPS</p>
                                <p class="font-medium text-gray-900 whitespace-pre-line">{{ $keterangan['vps']['purpose'] }}</p>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Section 5: Tujuan/Keterangan --}}
                @if(!empty($keterangan['tujuan_penggunaan']) || !empty($keterangan['detail_masalah']))
                <div class="mb-6">
                    <h3 class="mb-4 flex items-center gap-2 border-b border-gray-200 pb-2 text-base font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">5</span>
                        @if($tipePengajuan === 'laporan_masalah')
                            Deskripsi Masalah
                        @else
                            Tujuan & Keterangan
                        @endif
                    </h3>
                    <div class="space-y-3">
                        @if(!empty($keterangan['tujuan_penggunaan']))
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Tujuan Penggunaan</p>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $keterangan['tujuan_penggunaan'] }}</p>
                        </div>
                        @endif
                        @if(!empty($keterangan['detail_masalah']))
                        <div class="rounded-lg bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">
                                @if($tipePengajuan === 'laporan_masalah')
                                    Detail Masalah
                                @else
                                    Keterangan Tambahan
                                @endif
                            </p>
                            <p class="mt-1 whitespace-pre-line text-gray-900">{{ $keterangan['detail_masalah'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- User Info --}}
                <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <h4 class="mb-3 text-sm font-semibold text-gray-700">Diajukan Oleh</h4>
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-myunila text-lg font-bold text-white">
                            {{ strtoupper(substr($submission->pengguna?->nm ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $submission->pengguna?->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 text-center text-sm text-gray-500 print:bg-gray-100">
                <p>Dokumen ini digenerate secara otomatis oleh sistem <strong>DomainTIK</strong> - UPA TIK Universitas Lampung</p>
                <p class="mt-1">{{ config('app.url') }} | Tiket: {{ $submission->no_tiket }}</p>
            </div>

        </div>

        {{-- Action Buttons (hide on print) --}}
        <div class="mt-6 flex flex-wrap items-center justify-center gap-4 print:hidden">
            <a href="{{ route('forms.select', $submission->no_tiket) }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Pilihan Form
            </a>
            <a href="{{ route('forms.hardcopy.download', $submission->no_tiket) }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF Hardcopy
            </a>
        </div>

    </div>
</div>

{{-- Print Styles --}}
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        nav, footer, .print\\:hidden {
            display: none !important;
        }
        
        .print\\:py-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        
        .print\\:min-h-0 {
            min-height: 0 !important;
        }
        
        .print\\:max-w-full {
            max-width: 100% !important;
        }
        
        .print\\:px-0 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        .print\\:rounded-none {
            border-radius: 0 !important;
        }
        
        .print\\:border-none {
            border: none !important;
        }
        
        .print\\:shadow-none {
            box-shadow: none !important;
        }
    }
</style>
@endsection
