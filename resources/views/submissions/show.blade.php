@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $submission->no_tiket)

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('submissions.index') }}" class="mb-4 inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-myunila">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Pengajuan
            </a>
            
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Detail Pengajuan</h1>
                    <p class="mt-1 font-mono text-lg text-myunila">{{ $submission->no_tiket }}</p>
                </div>
                
                {{-- Status Badge --}}
                @php
                    $statusName = strtolower($submission->status?->nm_status ?? 'draft');
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-700',
                        'diajukan' => 'bg-info-light text-info',
                        'menunggu verifikasi' => 'bg-warning-light text-warning',
                        'disetujui verifikator' => 'bg-myunila-100 text-myunila',
                        'dalam proses' => 'bg-myunila-200 text-myunila-700',
                        'selesai' => 'bg-success-light text-success',
                        'ditolak verifikator' => 'bg-danger-light text-danger',
                        'ditolak eksekutor' => 'bg-danger-light text-danger',
                    ];
                    $statusColor = $statusColors[$statusName] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium {{ $statusColor }}">
                    <span class="h-2 w-2 rounded-full bg-current"></span>
                    {{ $submission->status?->nm_status ?? 'Draft' }}
                </div>
            </div>
        </div>

        @php
            $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);
            $fileLampiran = json_decode($submission->rincian?->file_lampiran ?? '{}', true);
            $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain';
        @endphp

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Layanan Info --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Informasi Layanan</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Tipe Pengajuan</span>
                            <span class="font-medium text-gray-900">
                                @php
                                    $tipePengajuan = $keterangan['tipe_pengajuan'] ?? 'pengajuan_baru';
                                    $tipeLabels = [
                                        'pengajuan_baru' => 'Pengajuan Baru',
                                        'perpanjangan' => 'Perpanjangan',
                                        'perubahan_data' => 'Perubahan Data',
                                        'upgrade_downgrade' => 'Upgrade/Downgrade',
                                        'penonaktifan' => 'Penonaktifan',
                                        'laporan_masalah' => 'Laporan Masalah',
                                    ];
                                @endphp
                                {{ $tipeLabels[$tipePengajuan] ?? ucfirst($tipePengajuan) }}
                            </span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Jenis Layanan</span>
                            <span class="font-medium text-gray-900">{{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Domain Diminta</span>
                            <span class="font-mono font-medium text-myunila">{{ $submission->rincian?->nm_domain ?? '-' }}</span>
                        </div>
                        @if($submission->rincian?->kapasitas_penyimpanan)
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Kapasitas Storage</span>
                            <span class="font-medium text-gray-900">{{ $submission->rincian->kapasitas_penyimpanan }}</span>
                        </div>
                        @endif

                        {{-- VPS Details --}}
                        @if($serviceType === 'vps' && !empty($keterangan['vps']))
                        <div class="px-6 py-4">
                            <span class="text-gray-600">Spesifikasi VPS</span>
                            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <div class="rounded-lg bg-gray-50 p-2 text-center">
                                    <p class="text-xs text-gray-500">CPU</p>
                                    <p class="font-medium text-gray-900">{{ $keterangan['vps']['cpu'] ?? '-' }} Core</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-2 text-center">
                                    <p class="text-xs text-gray-500">RAM</p>
                                    <p class="font-medium text-gray-900">{{ $keterangan['vps']['ram'] ?? '-' }} GB</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-2 text-center">
                                    <p class="text-xs text-gray-500">Storage</p>
                                    <p class="font-medium text-gray-900">{{ $keterangan['vps']['storage'] ?? '-' }} GB</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-2 text-center">
                                    <p class="text-xs text-gray-500">OS</p>
                                    <p class="font-medium text-gray-900">{{ $keterangan['vps']['os'] ?? '-' }}</p>
                                </div>
                            </div>
                            @if(!empty($keterangan['vps']['purpose']))
                            <p class="mt-2 text-sm text-gray-600">Tujuan: {{ $keterangan['vps']['purpose'] }}</p>
                            @endif
                        </div>
                        @endif

                        {{-- Hosting Details --}}
                        @if($serviceType === 'hosting' && !empty($keterangan['hosting']))
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Kuota Hosting</span>
                            <span class="font-medium text-gray-900">{{ $keterangan['hosting']['quota'] ?? '-' }} MB</span>
                        </div>
                        @endif

                        {{-- Existing Service Info (for non-new submissions) --}}
                        @if(!empty($keterangan['existing']))
                        <div class="px-6 py-4 bg-yellow-50">
                            <span class="text-gray-600 font-medium">Layanan Existing</span>
                            <div class="mt-2 space-y-2 text-sm">
                                @if(!empty($keterangan['existing']['domain']))
                                <p><span class="text-gray-500">Domain:</span> <span class="font-mono">{{ $keterangan['existing']['domain'] }}</span></p>
                                @endif
                                @if(!empty($keterangan['existing']['ticket']))
                                <p><span class="text-gray-500">No. Tiket Lama:</span> {{ $keterangan['existing']['ticket'] }}</p>
                                @endif
                                @if(!empty($keterangan['existing']['expired']))
                                <p><span class="text-gray-500">Tanggal Expired:</span> {{ $keterangan['existing']['expired'] }}</p>
                                @endif
                                @if(!empty($keterangan['existing']['notes']))
                                <p><span class="text-gray-500">Keterangan:</span> {{ $keterangan['existing']['notes'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Organisasi & Penanggung Jawab --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Organisasi & Penanggung Jawab</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Kategori Pemohon</span>
                            <span class="font-medium text-gray-900">
                                @php
                                    $kategoriPemohon = $keterangan['kategori_pemohon'] ?? '-';
                                    $kategoriLabels = [
                                        'lembaga_fakultas' => 'Lembaga/Fakultas',
                                        'kegiatan_lembaga' => 'Kegiatan Lembaga',
                                        'organisasi_mahasiswa' => 'Organisasi Mahasiswa',
                                        'kegiatan_mahasiswa' => 'Kegiatan Mahasiswa',
                                        'lainnya' => 'Lainnya',
                                    ];
                                @endphp
                                {{ $kategoriLabels[$kategoriPemohon] ?? $kategoriPemohon }}
                            </span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Nama Organisasi</span>
                            <span class="font-medium text-gray-900">{{ $keterangan['nama_organisasi'] ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Admin Contact --}}
                    @if(!empty($keterangan['admin']))
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-myunila mb-3">Penanggung Jawab Administratif</h3>
                        <div class="grid gap-3 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="text-gray-500">Nama:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['admin']['name'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Jabatan:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['admin']['position'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">NIP:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['admin']['nip'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['admin']['email'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Telepon:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['admin']['phone'] ?? '-' }}</span>
                            </div>
                            @if(!empty($keterangan['admin']['alamat_kantor']))
                            <div class="sm:col-span-2">
                                <span class="text-gray-500">Alamat Kantor:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['admin']['alamat_kantor'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Tech Contact --}}
                    @if(!empty($keterangan['teknis']))
                    <div class="border-t border-gray-200 px-6 py-4">
                        <h3 class="text-sm font-semibold text-myunila mb-3">Penanggung Jawab Teknis</h3>
                        <div class="grid gap-3 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="text-gray-500">Nama:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['teknis']['name'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">NIP/NIM:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['teknis']['nip'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['teknis']['email'] ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Telepon:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['teknis']['phone'] ?? '-' }}</span>
                            </div>
                            @if(!empty($keterangan['teknis']['alamat_kantor']))
                            <div class="sm:col-span-2">
                                <span class="text-gray-500">Alamat Kantor:</span>
                                <span class="font-medium text-gray-900">{{ $keterangan['teknis']['alamat_kantor'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Generated Forms --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Formulir Pengajuan</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            {{-- Paperless Form --}}
                            <a href="{{ route('forms.paperless', $submission->no_tiket) }}" target="_blank" class="group rounded-xl border-2 border-myunila/20 p-4 hover:border-myunila hover:bg-myunila-50 transition">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-myunila-100 text-myunila group-hover:bg-myunila group-hover:text-white transition">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 group-hover:text-myunila">Form Paperless</p>
                                        <p class="text-xs text-gray-500">Versi digital tanpa tanda tangan</p>
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                            </a>

                            {{-- Hardcopy Form --}}
                            <a href="{{ route('forms.hardcopy.preview', $submission->no_tiket) }}" target="_blank" class="group rounded-xl border-2 border-gray-200 p-4 hover:border-myunila hover:bg-myunila-50 transition">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-gray-600 group-hover:bg-myunila group-hover:text-white transition">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 group-hover:text-myunila">Form Hardcopy</p>
                                        <p class="text-xs text-gray-500">Versi cetak untuk tanda tangan manual</p>
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Dokumen Upload</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            {{-- Signed Form --}}
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ !empty($fileLampiran['signed_form']) ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-400' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Formulir Bertanda Tangan</p>
                                        @if(!empty($fileLampiran['signed_form']))
                                            <a href="{{ asset('storage/' . $fileLampiran['signed_form']) }}" target="_blank" class="text-sm text-myunila hover:underline inline-flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-500">Belum diupload</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Identity --}}
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ !empty($fileLampiran['identity']) ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-400' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Identitas (KTM/Karpeg)</p>
                                        @if(!empty($fileLampiran['identity']))
                                            <a href="{{ asset('storage/' . $fileLampiran['identity']) }}" target="_blank" class="text-sm text-myunila hover:underline inline-flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-500">Belum diupload</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if(strtolower($submission->status?->nm_status ?? '') === 'draft')
                            <div class="mt-4">
                                <a href="{{ route('submissions.upload', $submission) }}" class="btn-primary inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload Dokumen
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Applicant Info --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Pemohon</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-unila text-lg font-bold text-white">
                                {{ strtoupper(substr($submission->pengguna?->nm ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 truncate">{{ $submission->pengguna?->nm ?? '-' }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $submission->pengguna?->email ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">NPM/NIP</span>
                                <span class="font-medium text-gray-900">{{ $submission->pengguna?->usn ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Riwayat Aktivitas</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($submission->riwayat->sortByDesc('create_at') as $log)
                                <div class="flex gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-myunila-100 text-xs font-medium text-myunila">
                                        {{ strtoupper(substr($log->creator?->nm ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm">
                                            <span class="font-medium text-gray-900">{{ $log->creator?->nm ?? 'System' }}</span>
                                        </p>
                                        @if($log->statusBaru)
                                        <p class="text-xs text-myunila font-medium">
                                            @if($log->statusLama)
                                                {{ $log->statusLama->nm_status }} → 
                                            @endif
                                            {{ $log->statusBaru->nm_status }}
                                        </p>
                                        @endif
                                        @if($log->catatan_log)
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $log->catatan_log }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $log->create_at?->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="p-6">
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dibuat</span>
                                <span class="font-medium text-gray-900">{{ $submission->create_at?->format('d M Y') ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir Update</span>
                                <span class="font-medium text-gray-900">{{ $submission->last_update?->format('d M Y, H:i') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
