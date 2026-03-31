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
    <form method="GET" action="{{ route('pimpinan.activity-logs') }}" id="filterForm" class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex flex-col md:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Cari No. tiket, domain, nama..."
                           class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                </div>
            </div>

            {{-- Filter Component (Alpine.js) --}}
            <div class="flex items-center gap-2" x-data="{ open: false }" @click.outside="open = false">
                <div class="relative">
                    <button 
                        type="button"
                        @click="open = !open"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>

                    {{-- Filter Dropdown --}}
                    <div 
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-lg border border-gray-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                        style="display: none;">
                        
                        <div class="border-b border-gray-200 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">Filter</h3>
                                <a href="{{ route('pimpinan.activity-logs') }}" class="text-xs font-medium text-red-600 hover:text-red-700">
                                    Reset
                                </a>
                            </div>
                        </div>

                        <div class="p-4 space-y-4 text-left">
                            {{-- Actor Role --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Pelaku</label>
                                <select name="actor_role" class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                    <option value="all" {{ ($filters['actor_role'] ?? '') === 'all' ? 'selected' : '' }}>Semua</option>
                                    <option value="Pengguna" {{ ($filters['actor_role'] ?? '') === 'Pengguna' ? 'selected' : '' }}>Pengguna</option>
                                    <option value="Verifikator" {{ ($filters['actor_role'] ?? '') === 'Verifikator' ? 'selected' : '' }}>Verifikator</option>
                                    <option value="Eksekutor" {{ ($filters['actor_role'] ?? '') === 'Eksekutor' ? 'selected' : '' }}>Eksekutor</option>
                                </select>
                            </div>

                            {{-- Action Type --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Tipe Aksi</label>
                                <select name="action_type" class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                    <option value="all" {{ ($filters['action_type'] ?? '') === 'all' ? 'selected' : '' }}>Semua</option>
                                    <option value="submitted" {{ ($filters['action_type'] ?? '') === 'submitted' ? 'selected' : '' }}>Pengajuan Baru</option>
                                    <option value="approved" {{ ($filters['action_type'] ?? '') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ ($filters['action_type'] ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            {{-- Date From & To --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                           class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                           class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                </div>
                            </div>

                            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-myunila px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Search Button --}}
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                    <span>Cari</span>
                </button>
            </div>
        </div>
    </form>

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
        @if($logs->hasPages() || $logs->total() > 10)
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Items Per Page Selector --}}
                <form method="GET" action="{{ route('pimpinan.activity-logs') }}" id="perPageForm" class="flex items-center gap-2">
                    {{-- Preserve all existing query params --}}
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page_logs" class="text-sm text-gray-700">Tampilkan:</label>
                    <select name="per_page" 
                            id="per_page_logs"
                            onchange="this.form.submit()"
                            class="rounded-md border-gray-300 py-1.5 pl-3 pr-8 text-sm focus:border-myunila focus:ring-myunila">
                        <option value="10" {{ (int)request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ (int)request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ (int)request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-sm text-gray-700">per halaman</span>
                </form>

                {{-- Pagination Links --}}
                <div class="flex-1 flex justify-end">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
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
