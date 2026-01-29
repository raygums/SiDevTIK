@extends('layouts.app')

@section('title', 'Review Pengajuan - ' . $submission->no_tiket)

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('verifikator.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-myunila">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        {{-- Header --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Tiket</p>
                        <p class="font-mono text-xl font-bold text-myunila">{{ $submission->no_tiket }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-flex items-center rounded-full bg-warning-light px-3 py-1 text-sm font-medium text-warning">
                            {{ $submission->status?->nm_status ?? 'Menunggu Verifikasi' }}
                        </span>
                    </div>
                </div>
            </div>

            @php
                $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain';
                $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);
            @endphp

            <div class="p-6 space-y-6">
                {{-- Info Pemohon --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Informasi Pemohon</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Nama</p>
                            <p class="font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Unit Kerja</p>
                            <p class="font-medium text-gray-900">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Organisasi</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['nama_organisasi'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Tanggal Pengajuan</p>
                            <p class="font-medium text-gray-900">{{ $submission->create_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Info Layanan --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Detail Layanan</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-myunila-50 p-4">
                            <p class="text-xs text-myunila-700">Jenis Layanan</p>
                            <p class="font-semibold text-myunila">{{ ucfirst($serviceType) }}</p>
                        </div>
                        <div class="rounded-lg bg-myunila-50 p-4">
                            <p class="text-xs text-myunila-700">Domain/Hostname</p>
                            <p class="font-mono font-semibold text-myunila">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                        </div>
                    </div>

                    @if($serviceType === 'vps')
                    <div class="mt-4 grid gap-4 sm:grid-cols-4">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">OS</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['vps_os'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">CPU</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['vps_cpu'] ?? '-' }} Core</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">RAM</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['vps_ram'] ?? '-' }} GB</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Storage</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['vps_storage'] ?? '-' }} GB</p>
                        </div>
                    </div>
                    @elseif($serviceType === 'hosting')
                    <div class="mt-4">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Kuota Storage</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['hosting_quota'] ?? '-' }} MB</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Kontak --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Kontak</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs font-medium text-gray-500">Admin Contact</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $keterangan['admin_nama'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['admin_email'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['admin_hp'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs font-medium text-gray-500">Tech Contact</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $keterangan['tech_nama'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['tech_email'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['tech_hp'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tujuan Penggunaan --}}
                @if(!empty($keterangan['tujuan_penggunaan']))
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Tujuan Penggunaan</h3>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="whitespace-pre-line text-sm text-gray-700">{{ $keterangan['tujuan_penggunaan'] }}</p>
                    </div>
                </div>
                @endif

                {{-- Riwayat --}}
                @if($submission->riwayat->isNotEmpty())
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Riwayat Pengajuan</h3>
                    <div class="space-y-2">
                        @foreach($submission->riwayat as $log)
                        <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="h-2 w-2 rounded-full bg-myunila"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $log->status?->nm_status ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $log->create_at?->format('d M Y, H:i') }}</p>
                                @if($log->catatan_log)
                                <p class="mt-1 text-sm text-gray-600">{{ $log->catatan_log }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-900">Keputusan Verifikasi</h3>
            
            <div class="grid gap-6 sm:grid-cols-2">
                {{-- Approve Form --}}
                <form action="{{ route('verifikator.approve', $submission) }}" method="POST" class="rounded-xl border-2 border-success/30 bg-success-light p-4">
                    @csrf
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-success">Setujui Pengajuan</p>
                            <p class="text-xs text-gray-600">Teruskan ke Eksekutor</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-success focus:ring-success" placeholder="Catatan untuk eksekutor..."></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-success px-4 py-2 text-sm font-semibold text-white hover:bg-success/90">
                        ✓ Setujui & Teruskan
                    </button>
                </form>

                {{-- Reject Form --}}
                <form action="{{ route('verifikator.reject', $submission) }}" method="POST" class="rounded-xl border-2 border-danger/30 bg-danger-light p-4" id="rejectForm">
                    @csrf
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-danger text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-danger">Tolak Pengajuan</p>
                            <p class="text-xs text-gray-600">Kembalikan ke pemohon</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_penolakan" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-danger focus:ring-danger @error('alasan_penolakan') border-danger @enderror" placeholder="Jelaskan alasan penolakan..."></textarea>
                        @error('alasan_penolakan')
                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-semibold text-white hover:bg-danger/90">
                        ✗ Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
