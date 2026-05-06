@extends('layouts.dashboard')

@section('title', 'Log Status Pengajuan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Log Status Pengajuan
                </h1>
                <p class="mt-2 text-gray-600">
                    Audit trail perubahan status pengajuan domain & hosting
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-icon name="document-text" class="h-8 w-8 text-myunila" />
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Logs --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Log</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_logs']) }}</p>
                </div>
                <div class="rounded-xl bg-myunila-50 p-3">
                    <x-icon name="document-text" class="h-8 w-8 text-myunila" />
                </div>
            </div>
        </div>

        {{-- Logs Today --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Log Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['logs_today']) }}</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="calendar" class="h-8 w-8 text-success" />
                </div>
            </div>
        </div>

        {{-- Logs This Week --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Log Minggu Ini</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($stats['logs_this_week']) }}</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="chart-bar" class="h-8 w-8 text-info" />
                </div>
            </div>
        </div>

        {{-- Most Active Status --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Status Terbanyak</p>
                    <p class="mt-2 text-lg font-bold text-warning">
                        {{ $stats['most_active_statuses']->first()?->nm_status ?? 'N/A' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $stats['most_active_statuses']->first()?->total ?? 0 }} perubahan
                    </p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="fire" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>
    </div>

    {{-- Submission Logs Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Riwayat Perubahan Status Terbaru</h2>
                <span class="text-sm text-gray-500">{{ $logs->total() }} total log</span>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <form method="GET" action="{{ route('admin.audit.submissions') }}" id="filterFormSubmissions">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Cari no. tiket, nama, atau email..."
                               class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                    </div>

                    {{-- Filter Button (Popup) --}}
                    @php
                        $activeFilters = collect([
                            $filters['service_type'] ?? '',
                            $filters['status'] ?? '',
                            $filters['date_from'] ?? '',
                            $filters['date_to'] ?? '',
                        ])->filter()->count();
                    @endphp
                    <x-filter-popup
                        form-id="filterFormSubmissions"
                        modal-id="filterModalSubmissions"
                        title="Filter Log Status"
                        reset-url="{{ route('admin.audit.submissions') }}"
                        :badge="$activeFilters">

                        {{-- Jenis Layanan --}}
                        <div class="fp-field">
                            <label for="fp-service-sub">Jenis Layanan</label>
                            <select name="service_type" id="fp-service-sub" form="filterFormSubmissions">
                                <option value="">Semua Layanan</option>
                                <option value="domain" {{ ($filters['service_type'] ?? '') === 'domain' ? 'selected' : '' }}>Domain</option>
                                <option value="hosting" {{ ($filters['service_type'] ?? '') === 'hosting' ? 'selected' : '' }}>Hosting</option>
                                <option value="vps" {{ ($filters['service_type'] ?? '') === 'vps' ? 'selected' : '' }}>VPS</option>
                            </select>
                        </div>

                        {{-- Status Baru --}}
                        <div class="fp-field">
                            <label for="fp-status-sub">Status Baru</label>
                            <select name="status" id="fp-status-sub" form="filterFormSubmissions">
                                <option value="">Semua Status</option>
                                <option value="Diajukan" {{ ($filters['status'] ?? '') === 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="Sedang Dikerjakan" {{ ($filters['status'] ?? '') === 'Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                                <option value="Selesai" {{ ($filters['status'] ?? '') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Ditolak" {{ ($filters['status'] ?? '') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- Periode Perubahan --}}
                        <div class="fp-field">
                            <label>Periode Perubahan</label>
                            <div class="fp-date-row">
                                <div class="fp-date-col">
                                    <label for="fp-dari-sub">Dari</label>
                                    <input type="date" name="date_from" id="fp-dari-sub" form="filterFormSubmissions"
                                           value="{{ $filters['date_from'] ?? '' }}">
                                </div>
                                <div class="fp-date-col">
                                    <label for="fp-sampai-sub">Sampai</label>
                                    <input type="date" name="date_to" id="fp-sampai-sub" form="filterFormSubmissions"
                                           value="{{ $filters['date_to'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </x-filter-popup>

                    {{-- Search Button --}}
                    <button type="submit" 
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>
        </div>

        @if($logs->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="exclamation-circle" class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Data Ditemukan</h3>
            <p class="mt-2 text-sm text-gray-500">Coba ubah filter atau reset pencarian</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            No. Tiket
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Pemohon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Layanan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Perubahan Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Waktu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Diubah Oleh
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($logs as $log)
                    <tr class="transition hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="font-mono text-sm font-semibold text-myunila">
                                {{ $log->pengajuan?->no_tiket ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->pengajuan?->pengguna?->nm ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $log->pengajuan?->pengguna?->email ?? '-' }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @php 
                                $serviceType = $log->pengajuan?->jenisLayanan?->nm_layanan ?? 'domain';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($serviceType === 'vps') badge-service-vps
                                @elseif($serviceType === 'hosting') badge-service-hosting
                                @else badge-service-domain
                                @endif">
                                {{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                    {{ $log->statusLama?->nm_status ?? 'N/A' }}
                                </span>
                                <x-icon name="arrow-right" class="h-4 w-4 text-gray-400" />
                                @php
                                    $newStatus = $log->statusBaru?->nm_status ?? '';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($newStatus === 'Selesai') bg-success-light text-success
                                    @elseif(str_contains($newStatus, 'Ditolak')) bg-danger-light text-danger
                                    @elseif($newStatus === 'Sedang Dikerjakan') bg-info-light text-info
                                    @elseif($newStatus === 'Diajukan') bg-warning-light text-warning
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ $newStatus }}
                                </span>
                            </div>
                            @if($log->catatan_log)
                            <div class="mt-1 text-xs text-gray-500">
                                {{ Str::limit($log->catatan_log, 50) }}
                            </div>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->create_at?->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $log->create_at?->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $log->creator?->nm ?? 'Sistem' }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination with Items Per Page --}}
        @if($logs->hasPages() || $logs->total() > 10)
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Items Per Page Selector --}}
                <form method="GET" action="{{ route('admin.audit.submissions') }}" class="flex items-center gap-2">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page_submissions" class="text-sm text-gray-700">Tampilkan:</label>
                    <select name="per_page" 
                            id="per_page_submissions"
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
        @endif
    </div>
</div>

@endsection
