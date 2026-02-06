@extends('layouts.dashboard')

@section('title', 'Detail Aktivitas - Pimpinan')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('pimpinan.activity-logs') }}" class="text-gray-400 transition hover:text-gray-600">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Detail Aktivitas</h1>
                <p class="mt-1 text-gray-600">Informasi lengkap perubahan status pengajuan.</p>
            </div>
        </div>
    </div>

    @php
        $statusName = $log->statusBaru?->nm_status ?? '';
        $statusClass = match(true) {
            str_contains($statusName, 'Ditolak') => 'bg-danger-light text-danger border-danger/20',
            str_contains($statusName, 'Selesai') => 'bg-success-light text-success border-success/20',
            str_contains($statusName, 'Disetujui') || str_contains($statusName, 'Dikerjakan') => 'bg-info-light text-info border-info/20',
            default => 'bg-warning-light text-warning border-warning/20',
        };
    @endphp

    {{-- Status Change Card --}}
    <div class="mb-6 overflow-hidden rounded-2xl border {{ $statusClass }} shadow-sm">
        <div class="p-6">
            <div class="flex items-start gap-4">
                @php
                    $iconData = match(true) {
                        str_contains($statusName, 'Ditolak') => ['class' => 'bg-danger text-white', 'icon' => 'x-circle'],
                        str_contains($statusName, 'Selesai') => ['class' => 'bg-success text-white', 'icon' => 'check-badge'],
                        str_contains($statusName, 'Disetujui') => ['class' => 'bg-info text-white', 'icon' => 'check-circle'],
                        str_contains($statusName, 'Dikerjakan') => ['class' => 'bg-warning text-white', 'icon' => 'cog'],
                        default => ['class' => 'bg-myunila text-white', 'icon' => 'paper-airplane'],
                    };
                @endphp
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full {{ $iconData['class'] }}">
                    <x-icon :name="$iconData['icon']" class="h-7 w-7" />
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $statusName }}</h2>
                    <p class="mt-1 text-gray-600">
                        @if($log->statusLama)
                        Diubah dari <span class="font-medium">{{ $log->statusLama->nm_status }}</span>
                        @else
                        Status awal pengajuan
                        @endif
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                        <x-icon name="clock" class="mr-1 inline h-4 w-4" />
                        {{ $log->create_at?->format('d F Y, H:i') }} WIB
                        <span class="ml-1 text-gray-300">•</span>
                        <span class="ml-1">{{ $log->create_at?->diffForHumans() }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Performer Info --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Dilakukan Oleh</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-lg font-bold text-myunila">
                    {{ strtoupper(substr($log->creator?->nm ?? 'S', 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $log->creator?->nm ?? 'System' }}</p>
                    <p class="text-sm text-gray-500">{{ $log->creator?->email ?? '-' }}</p>
                    @php
                        $roleName = $log->creator?->peran?->nm_peran ?? 'System';
                        $roleClass = match($roleName) {
                            'Admin' => 'bg-purple-100 text-purple-700',
                            'Verifikator' => 'bg-warning-light text-warning',
                            'Eksekutor' => 'bg-success-light text-success',
                            'Pimpinan' => 'bg-myunila-100 text-myunila',
                            default => 'bg-info-light text-info',
                        };
                    @endphp
                    <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClass }}">
                        {{ $roleName }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes/Catatan --}}
    @if($log->catatan_log)
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Catatan</h3>
        </div>
        <div class="p-6">
            <p class="whitespace-pre-line text-gray-700">{{ $log->catatan_log }}</p>
        </div>
    </div>
    @endif

    {{-- Submission Info --}}
    @if($log->pengajuan)
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Informasi Pengajuan</h3>
                <span class="rounded bg-myunila-100 px-2 py-0.5 text-xs font-medium text-myunila">
                    {{ ucfirst($log->pengajuan->jenisLayanan?->nm_layanan ?? 'domain') }}
                </span>
            </div>
        </div>
        <div class="divide-y divide-gray-100">
            {{-- Ticket Number --}}
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">No. Tiket</span>
                <span class="font-mono font-semibold text-myunila">{{ $log->pengajuan->no_tiket }}</span>
            </div>

            {{-- Domain --}}
            @if($log->pengajuan->rincian?->nm_domain)
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Domain</span>
                <span class="font-mono text-gray-900">{{ $log->pengajuan->rincian->nm_domain }}</span>
            </div>
            @endif

            {{-- Applicant --}}
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Pemohon</span>
                <div class="text-right">
                    <p class="font-medium text-gray-900">{{ $log->pengajuan->pengguna?->nm ?? '-' }}</p>
                    <p class="text-sm text-gray-500">{{ $log->pengajuan->pengguna?->email ?? '-' }}</p>
                </div>
            </div>

            {{-- Unit Kerja --}}
            @if($log->pengajuan->unitKerja)
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Unit Kerja</span>
                <div class="text-right">
                    <p class="font-medium text-gray-900">{{ $log->pengajuan->unitKerja->nm_lmbg }}</p>
                    <p class="text-sm text-gray-500">{{ $log->pengajuan->unitKerja->category?->nm_kategori_unit ?? '-' }}</p>
                </div>
            </div>
            @endif

            {{-- Submission Date --}}
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Tanggal Pengajuan</span>
                <span class="text-gray-900">{{ $log->pengajuan->create_at?->format('d M Y, H:i') }} WIB</span>
            </div>
        </div>
    </div>

    {{-- Additional Submission Details --}}
    @php
        $keterangan = json_decode($log->pengajuan->rincian?->keterangan_keperluan ?? '{}', true);
    @endphp
    @if(!empty($keterangan))
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Detail Permohonan</h3>
        </div>
        <div class="p-6">
            <div class="grid gap-4 sm:grid-cols-2">
                @if(!empty($keterangan['nama_organisasi']))
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Organisasi</p>
                    <p class="font-medium text-gray-900">{{ $keterangan['nama_organisasi'] }}</p>
                </div>
                @endif

                @if(!empty($keterangan['admin']['name']))
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Admin Pengelola</p>
                    <p class="font-medium text-gray-900">{{ $keterangan['admin']['name'] }}</p>
                    <p class="text-sm text-gray-500">{{ $keterangan['admin']['email'] ?? '' }}</p>
                </div>
                @endif

                @if(!empty($keterangan['tech']['name']))
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Penanggung Jawab Teknis</p>
                    <p class="font-medium text-gray-900">{{ $keterangan['tech']['name'] }}</p>
                    <p class="text-sm text-gray-500">{{ $keterangan['tech']['email'] ?? '' }}</p>
                </div>
                @endif

                @if(!empty($keterangan['tipe_pengajuan']))
                <div class="rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500">Tipe Pengajuan</p>
                    <p class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $keterangan['tipe_pengajuan'])) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('pimpinan.activity-logs') }}" class="btn-secondary">
            <x-icon name="arrow-left" class="mr-2 h-4 w-4" />
            Kembali ke Daftar
        </a>
        
        @if($log->pengajuan)
        <a href="{{ route('submissions.show', $log->pengajuan->UUID) }}" class="btn-primary" target="_blank">
            <x-icon name="eye" class="mr-2 h-4 w-4" />
            Lihat Pengajuan
        </a>
        @endif
    </div>
</div>
@endsection
