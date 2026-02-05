@extends('layouts.dashboard')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Dashboard Pimpinan
        </h1>
        <p class="mt-2 text-gray-600">
            Selamat datang, {{ Auth::user()->nm }}. Pantau dan kelola seluruh aktivitas sistem dari sini.
        </p>
        <p class="mt-1 text-sm text-gray-500">
            <svg class="inline h-4 w-4 text-myunila" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Anda memiliki akses penuh untuk mengelola semua role pengguna dan melihat log aktivitas sistem.
        </p>
    </div>

    {{-- User Statistics Cards --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
        {{-- Total Users --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['users']['total']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $stats['users']['aktif'] }} aktif
                    </p>
                </div>
                <div class="rounded-xl bg-myunila-50 p-3">
                    <x-icon name="users" class="h-6 w-6 text-myunila" />
                </div>
            </div>
        </div>

        {{-- Pengguna --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pengguna</p>
                    <p class="mt-1 text-2xl font-bold text-info">{{ number_format($stats['users']['by_role']['pengguna']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Role user biasa</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="user" class="h-6 w-6 text-info" />
                </div>
            </div>
        </div>

        {{-- Verifikator --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Verifikator</p>
                    <p class="mt-1 text-2xl font-bold text-warning">{{ number_format($stats['users']['by_role']['verifikator']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Tim verifikasi</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="check-circle" class="h-6 w-6 text-warning" />
                </div>
            </div>
        </div>

        {{-- Eksekutor --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Eksekutor</p>
                    <p class="mt-1 text-2xl font-bold text-success">{{ number_format($stats['users']['by_role']['eksekutor']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Tim eksekusi</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="cog" class="h-6 w-6 text-success" />
                </div>
            </div>
        </div>

        {{-- Admin --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Admin</p>
                    <p class="mt-1 text-2xl font-bold text-purple-600">{{ number_format($stats['users']['by_role']['admin']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Administrator</p>
                </div>
                <div class="rounded-xl bg-purple-50 p-3">
                    <x-icon name="shield-check" class="h-6 w-6 text-purple-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- Submission Statistics --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['submissions']['total']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $stats['submissions']['bulan_ini'] }} bulan ini</p>
                </div>
                <div class="rounded-xl bg-gray-100 p-3">
                    <x-icon name="document-text" class="h-6 w-6 text-gray-600" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dalam Proses</p>
                    <p class="mt-1 text-2xl font-bold text-warning">{{ number_format($stats['submissions']['diajukan'] + $stats['submissions']['diverifikasi'] + $stats['submissions']['dikerjakan']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Sedang diproses</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="clock" class="h-6 w-6 text-warning" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai</p>
                    <p class="mt-1 text-2xl font-bold text-success">{{ number_format($stats['submissions']['selesai']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Berhasil diselesaikan</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-badge" class="h-6 w-6 text-success" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ditolak</p>
                    <p class="mt-1 text-2xl font-bold text-danger">{{ number_format($stats['submissions']['ditolak']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Total ditolak</p>
                </div>
                <div class="rounded-xl bg-danger-light p-3">
                    <x-icon name="x-circle" class="h-6 w-6 text-danger" />
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8 grid gap-6 md:grid-cols-2">
        {{-- Manajemen Pengguna --}}
        <a href="{{ route('pimpinan.users') }}" 
           class="group overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="rounded-xl bg-myunila-50 p-3 transition group-hover:bg-myunila-100">
                    <x-icon name="users" class="h-8 w-8 text-myunila" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Manajemen Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Kelola semua pengguna sistem termasuk User, Verifikator, Eksekutor, dan Admin.
                    </p>
                    @if($stats['users']['nonaktif'] > 0)
                    <span class="mt-3 inline-flex items-center rounded-full bg-warning-light px-3 py-1 text-xs font-medium text-warning">
                        {{ $stats['users']['nonaktif'] }} akun belum aktif
                    </span>
                    @endif
                </div>
                <x-icon name="chevron-right" class="h-5 w-5 flex-shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-myunila" />
            </div>
        </a>

        {{-- Log Aktivitas --}}
        <a href="{{ route('pimpinan.activity-logs') }}" 
           class="group overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="rounded-xl bg-info-light p-3 transition group-hover:bg-info/20">
                    <x-icon name="clock" class="h-8 w-8 text-info" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Log Aktivitas Sistem</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Pantau semua aktivitas verifikasi dan eksekusi pengajuan layanan.
                    </p>
                    <span class="mt-3 inline-flex items-center rounded-full bg-info-light px-3 py-1 text-xs font-medium text-info">
                        {{ $stats['activities']['today'] }} aktivitas hari ini
                    </span>
                </div>
                <x-icon name="chevron-right" class="h-5 w-5 flex-shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-info" />
            </div>
        </a>
    </div>

    {{-- Recent Activity Logs --}}
    @if($recentLogs->count() > 0)
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Aktivitas Terbaru</h2>
                <a href="{{ route('pimpinan.activity-logs') }}" class="text-sm font-medium text-myunila hover:underline">
                    Lihat Semua
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentLogs as $log)
            <a href="{{ route('pimpinan.activity-detail', $log->UUID) }}" 
               class="flex items-center gap-4 px-6 py-4 transition hover:bg-gray-50">
                {{-- Status Icon --}}
                <div class="flex-shrink-0">
                    @php
                        $statusName = $log->statusBaru?->nm_status ?? '';
                        $iconClass = match(true) {
                            str_contains($statusName, 'Ditolak') => 'bg-danger-light text-danger',
                            str_contains($statusName, 'Selesai') => 'bg-success-light text-success',
                            str_contains($statusName, 'Disetujui') || str_contains($statusName, 'Dikerjakan') => 'bg-info-light text-info',
                            str_contains($statusName, 'Diajukan') => 'bg-warning-light text-warning',
                            default => 'bg-gray-100 text-gray-600',
                        };
                        $iconName = match(true) {
                            str_contains($statusName, 'Ditolak') => 'x-circle',
                            str_contains($statusName, 'Selesai') => 'check-badge',
                            str_contains($statusName, 'Disetujui') => 'check-circle',
                            str_contains($statusName, 'Dikerjakan') => 'cog',
                            default => 'arrow-right',
                        };
                    @endphp
                    <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $iconClass }}">
                        <x-icon :name="$iconName" class="h-5 w-5" />
                    </div>
                </div>

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-semibold text-myunila">{{ $log->pengajuan?->no_tiket ?? '-' }}</span>
                        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">
                            {{ ucfirst($log->pengajuan?->jenisLayanan?->nm_layanan ?? 'domain') }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-600 truncate">
                        <span class="font-medium">{{ $log->creator?->nm ?? 'System' }}</span>
                        <span class="text-gray-400">({{ $log->creator?->peran?->nm_peran ?? '-' }})</span>
                        mengubah status ke
                        <span class="font-medium text-gray-900">{{ $log->statusBaru?->nm_status ?? '-' }}</span>
                    </p>
                    <p class="mt-0.5 text-xs text-gray-400">
                        {{ $log->create_at?->diffForHumans() ?? '-' }}
                    </p>
                </div>

                {{-- Arrow --}}
                <x-icon name="chevron-right" class="h-5 w-5 flex-shrink-0 text-gray-400" />
            </a>
            @endforeach
        </div>
    </div>
    @else
    <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
        <x-icon name="clock" class="mx-auto h-12 w-12 text-gray-300" />
        <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Aktivitas</h3>
        <p class="mt-2 text-sm text-gray-500">Aktivitas sistem akan muncul di sini.</p>
    </div>
    @endif

</div>
@endsection
