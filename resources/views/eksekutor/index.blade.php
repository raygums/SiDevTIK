@extends('layouts.dashboard')

@section('title', 'Daftar Tugas - Eksekutor')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            {{ $pageTitle ?? 'Daftar Tugas' }}
        </h1>
        <p class="mt-2 text-gray-600">
            {{ $pageDescription ?? 'Pengajuan yang sudah disetujui verifikator dan siap dikerjakan.' }}
        </p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="mb-6">
        <x-alert type="success" :message="session('success')" />
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6">
        <x-alert type="error" :message="session('error')" />
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-4">
        <a href="{{ route('eksekutor.index', array_merge(request()->except('page', 'scope'), ['scope' => 'pending'])) }}" class="block overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md {{ ($filters['scope'] ?? 'pending') === 'pending' ? 'border-myunila ring-1 ring-myunila/30' : 'border-gray-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Menunggu</p>
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="clock" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </a>

        <a href="{{ route('eksekutor.index', array_merge(request()->except('page', 'scope'), ['scope' => 'in_progress'])) }}" class="block overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md {{ ($filters['scope'] ?? 'pending') === 'in_progress' ? 'border-myunila ring-1 ring-myunila/30' : 'border-gray-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sedang Dikerjakan</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($stats['in_progress']) }}</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="cog" class="h-8 w-8 text-info" />
                </div>
            </div>
        </a>

        <a href="{{ route('eksekutor.index', array_merge(request()->except('page', 'scope'), ['scope' => 'completed_today'])) }}" class="block overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md {{ ($filters['scope'] ?? 'pending') === 'completed_today' ? 'border-myunila ring-1 ring-myunila/30' : 'border-gray-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['completed_today']) }}</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-circle" class="h-8 w-8 text-success" />
                </div>
            </div>
        </a>

        <a href="{{ route('eksekutor.index', array_merge(request()->except('page', 'scope'), ['scope' => 'rejected_today'])) }}" class="block overflow-hidden rounded-2xl border bg-white p-6 shadow-sm transition hover:shadow-md {{ ($filters['scope'] ?? 'pending') === 'rejected_today' ? 'border-myunila ring-1 ring-myunila/30' : 'border-gray-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ditolak Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($stats['rejected_today']) }}</p>
                </div>
                <div class="rounded-xl bg-red-50 p-3">
                    <x-icon name="x-circle" class="h-8 w-8 text-red-600" />
                </div>
            </div>
        </a>
    </div>

    {{-- Filters & Search --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="p-6">
            <form method="GET" action="{{ route('eksekutor.index') }}" id="filterForm">
                <input type="hidden" name="scope" value="{{ $filters['scope'] ?? 'pending' }}">
                    
                    <div class="flex flex-col gap-3 sm:flex-row">
                        {{-- Search Input --}}
                        <div class="relative flex-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-icon name="search" class="h-5 w-5 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $filters['search'] ?? '' }}"
                                placeholder="Cari nomor tiket, nama pemohon, atau domain..."
                                class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                        </div>
                        
                        {{-- Filter Button (Popup) --}}
                        @php
                            $activeFilters = collect([
                                $filters['layanan'] ?? 'all',
                                request('tanggal_dari'),
                                request('tanggal_sampai'),
                            ])->filter(fn($v) => $v && $v !== 'all')->count();
                        @endphp
                        <x-filter-popup
                            form-id="filterForm"
                            modal-id="filterModalEksekutor"
                            title="Filter Tugas"
                            reset-url="{{ route('eksekutor.index', ['scope' => $filters['scope'] ?? 'pending']) }}"
                            :badge="$activeFilters">

                            {{-- Jenis Layanan --}}
                            <div class="fp-field">
                                <label for="fp-layanan-eks">Jenis Layanan</label>
                                <select name="layanan" id="fp-layanan-eks" form="filterForm">
                                    <option value="all" {{ ($filters['layanan'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Layanan</option>
                                    <option value="domain" {{ ($filters['layanan'] ?? '') === 'domain' ? 'selected' : '' }}>Domain</option>
                                    <option value="hosting" {{ ($filters['layanan'] ?? '') === 'hosting' ? 'selected' : '' }}>Hosting</option>
                                    <option value="vps" {{ ($filters['layanan'] ?? '') === 'vps' ? 'selected' : '' }}>VPS</option>
                                </select>
                            </div>

                            {{-- Periode Tanggal --}}
                            <div class="fp-field">
                                <label>Periode Tanggal</label>
                                <div class="fp-date-row">
                                    <div class="fp-date-col">
                                        <label for="fp-dari-eks">Dari</label>
                                        <input type="date" name="tanggal_dari" id="fp-dari-eks" form="filterForm"
                                               value="{{ request('tanggal_dari') }}">
                                    </div>
                                    <div class="fp-date-col">
                                        <label for="fp-sampai-eks">Sampai</label>
                                        <input type="date" name="tanggal_sampai" id="fp-sampai-eks" form="filterForm"
                                               value="{{ request('tanggal_sampai') }}">
                                    </div>
                                </div>
                            </div>
                        </x-filter-popup>

                        {{-- Search Button --}}
                        <button 
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-dark sm:w-auto">
                            <span>Cari</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pending Submissions Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="font-semibold text-gray-900">Pengajuan Siap Dikerjakan</h2>
            </div>

            @if($submissions->isEmpty())
            <div class="p-12 text-center">
                <x-icon name="check-circle" class="mx-auto h-16 w-16 text-gray-300" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada tugas</h3>
                <p class="mt-2 text-gray-500">Semua tugas sudah dikerjakan. Bagus!</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No. Tiket</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pemohon</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Layanan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Domain</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Update</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($submissions as $submission)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @php $serviceType = strtolower($submission->jenisLayanan?->nm_layanan ?? 'domain'); @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($serviceType === 'vps') badge-service-vps
                                    @elseif($serviceType === 'hosting') badge-service-hosting
                                    @else badge-service-domain
                                    @endif">
                                    {{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate text-sm text-gray-900">{{ $submission->rincian?->nm_domain ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @php $statusName = $submission->status?->nm_status ?? '-'; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if(str_contains($statusName, 'Disetujui')) bg-warning-light text-warning
                                    @elseif($statusName === 'Sedang Dikerjakan') bg-info-light text-info
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $submission->last_update?->format('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <a href="{{ route('eksekutor.show', $submission) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg bg-myunila px-3 py-1.5 text-xs font-medium text-white transition hover:bg-myunila-dark">
                                    <x-icon name="eye" class="h-4 w-4" />
                                    Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Pagination with Items Per Page --}}
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    {{-- Items Per Page Selector --}}
                    <form method="GET" action="{{ route('eksekutor.index') }}" id="perPageForm" class="flex items-center gap-2">
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
                        {{ $submissions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        
</div>
@endsection
