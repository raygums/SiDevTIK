@extends('layouts.dashboard')

@section('title', 'Log Aktivitas Sistem - Pimpinan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('pimpinan.dashboard') }}" class="text-gray-400 transition hover:text-gray-600">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Log Aktivitas Sistem</h1>
                <p class="mt-1 text-gray-600">Pantau semua aktivitas verifikasi dan eksekusi pengajuan layanan.</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Aktivitas (7 hari)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_7_days']) }}</p>
                </div>
                <div class="rounded-lg bg-myunila-50 p-2">
                    <x-icon name="chart-bar" class="h-6 w-6 text-myunila" />
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hari Ini</p>
                    <p class="text-2xl font-bold text-info">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="rounded-lg bg-info-light p-2">
                    <x-icon name="clock" class="h-6 w-6 text-info" />
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Oleh Verifikator</p>
                    <p class="text-2xl font-bold text-warning">{{ number_format($stats['by_role']['Verifikator'] ?? 0) }}</p>
                </div>
                <div class="rounded-lg bg-warning-light p-2">
                    <x-icon name="check-circle" class="h-6 w-6 text-warning" />
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Oleh Eksekutor</p>
                    <p class="text-2xl font-bold text-success">{{ number_format($stats['by_role']['Eksekutor'] ?? 0) }}</p>
                </div>
                <div class="rounded-lg bg-success-light p-2">
                    <x-icon name="cog" class="h-6 w-6 text-success" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('pimpinan.activity-logs') }}">
                <div class="grid gap-4 md:grid-cols-6">
                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Cari</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="No. tiket, domain, nama..."
                               class="form-input w-full">
                    </div>

                    {{-- Actor Role --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Pelaku</label>
                        <select name="actor_role" class="form-select w-full" onchange="this.form.submit()">
                            <option value="all" {{ ($filters['actor_role'] ?? '') === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="Pengguna" {{ ($filters['actor_role'] ?? '') === 'Pengguna' ? 'selected' : '' }}>Pengguna</option>
                            <option value="Verifikator" {{ ($filters['actor_role'] ?? '') === 'Verifikator' ? 'selected' : '' }}>Verifikator</option>
                            <option value="Eksekutor" {{ ($filters['actor_role'] ?? '') === 'Eksekutor' ? 'selected' : '' }}>Eksekutor</option>
                        </select>
                    </div>

                    {{-- Action Type --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tipe Aksi</label>
                        <select name="action_type" class="form-select w-full" onchange="this.form.submit()">
                            <option value="all" {{ ($filters['action_type'] ?? '') === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="submitted" {{ ($filters['action_type'] ?? '') === 'submitted' ? 'selected' : '' }}>Pengajuan Baru</option>
                            <option value="approved" {{ ($filters['action_type'] ?? '') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ ($filters['action_type'] ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                               class="form-input w-full">
                    </div>

                    {{-- Date To --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                               class="form-input w-full">
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" class="btn-primary">
                        <x-icon name="magnifying-glass" class="mr-2 h-4 w-4" />
                        Cari
                    </button>
                    <a href="{{ route('pimpinan.activity-logs') }}" class="btn-secondary">
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity Logs List --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Daftar Aktivitas</h2>
        </div>
        
        @if($logs->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($logs as $log)
            <a href="{{ route('pimpinan.activity-detail', $log->UUID) }}" 
               class="flex items-start gap-4 px-6 py-4 transition hover:bg-gray-50">
                {{-- Status Icon --}}
                <div class="flex-shrink-0 pt-0.5">
                    @php
                        $statusName = $log->statusBaru?->nm_status ?? '';
                        $iconData = match(true) {
                            str_contains($statusName, 'Ditolak') => ['class' => 'bg-danger-light text-danger', 'icon' => 'x-circle'],
                            str_contains($statusName, 'Selesai') => ['class' => 'bg-success-light text-success', 'icon' => 'check-badge'],
                            str_contains($statusName, 'Disetujui') => ['class' => 'bg-info-light text-info', 'icon' => 'check-circle'],
                            str_contains($statusName, 'Dikerjakan') => ['class' => 'bg-warning-light text-warning', 'icon' => 'cog'],
                            str_contains($statusName, 'Diajukan') => ['class' => 'bg-myunila-50 text-myunila', 'icon' => 'paper-airplane'],
                            default => ['class' => 'bg-gray-100 text-gray-600', 'icon' => 'arrow-right'],
                        };
                    @endphp
                    <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $iconData['class'] }}">
                        <x-icon :name="$iconData['icon']" class="h-5 w-5" />
                    </div>
                </div>

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    {{-- Header --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-sm font-semibold text-myunila">
                            {{ $log->pengajuan?->no_tiket ?? '-' }}
                        </span>
                        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">
                            {{ ucfirst($log->pengajuan?->jenisLayanan?->nm_layanan ?? 'domain') }}
                        </span>
                        @php
                            $statusClass = match(true) {
                                str_contains($statusName, 'Ditolak') => 'bg-danger-light text-danger',
                                str_contains($statusName, 'Selesai') => 'bg-success-light text-success',
                                str_contains($statusName, 'Disetujui') || str_contains($statusName, 'Dikerjakan') => 'bg-info-light text-info',
                                default => 'bg-warning-light text-warning',
                            };
                        @endphp
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">
                            {{ $statusName }}
                        </span>
                    </div>

                    {{-- Description --}}
                    <p class="mt-1 text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ $log->creator?->nm ?? 'System' }}</span>
                        <span class="text-gray-400">({{ $log->creator?->peran?->nm_peran ?? '-' }})</span>
                        @if($log->statusLama)
                        mengubah status dari <span class="text-gray-500">{{ $log->statusLama?->nm_status }}</span> ke
                        @else
                        membuat pengajuan dengan status
                        @endif
                        <span class="font-medium text-gray-900">{{ $statusName }}</span>
                    </p>

                    {{-- Domain Info --}}
                    @if($log->pengajuan?->rincian?->nm_domain)
                    <p class="mt-0.5 text-sm text-gray-500">
                        Domain: <span class="font-mono">{{ $log->pengajuan->rincian->nm_domain }}</span>
                    </p>
                    @endif

                    {{-- Note Preview --}}
                    @if($log->catatan_log)
                    <p class="mt-1 text-sm text-gray-500 line-clamp-1">
                        <x-icon name="chat-bubble-left" class="mr-1 inline h-3.5 w-3.5" />
                        {{ $log->catatan_log }}
                    </p>
                    @endif

                    {{-- Timestamp --}}
                    <p class="mt-2 text-xs text-gray-400">
                        <x-icon name="clock" class="mr-1 inline h-3.5 w-3.5" />
                        {{ $log->create_at?->format('d M Y, H:i') }} WIB
                        <span class="ml-1 text-gray-300">•</span>
                        <span class="ml-1">{{ $log->create_at?->diffForHumans() }}</span>
                    </p>
                </div>

                {{-- Arrow --}}
                <div class="flex-shrink-0 self-center">
                    <x-icon name="chevron-right" class="h-5 w-5 text-gray-400" />
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $logs->links() }}
        </div>
        @endif

        @else
        <div class="p-12 text-center">
            <x-icon name="clock" class="mx-auto h-12 w-12 text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Aktivitas</h3>
            <p class="mt-2 text-sm text-gray-500">Belum ada aktivitas yang sesuai dengan filter.</p>
        </div>
        @endif
    </div>
</div>
@endsection
