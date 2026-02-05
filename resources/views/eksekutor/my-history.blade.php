@extends('layouts.dashboard')

@section('title', 'Log Pekerjaan - Eksekutor')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Log Pekerjaan
        </h1>
        <p class="mt-2 text-gray-600">
            Riwayat pengajuan yang telah Anda proses.
        </p>
    </div>

    {{-- Filters & Search --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="p-6">
            <form method="GET" action="{{ route('eksekutor.my-history') }}" id="filterForm">
                
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
                            placeholder="Cari nomor tiket atau nama pemohon..."
                            class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                    </div>
                    
                    {{-- Filter Button --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
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
                            x-transition
                            class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-lg border border-gray-200 bg-white shadow-lg"
                            style="display: none;">
                            
                            <div class="border-b border-gray-200 px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900">Filter</h3>
                                    <button 
                                        type="button"
                                        onclick="window.location.href='{{ route('eksekutor.my-history') }}'"
                                        class="text-xs font-medium text-red-600 hover:text-red-700">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 space-y-4">
                                {{-- Layanan Filter --}}
                                <div>
                                    <label for="layanan" class="block text-sm font-medium text-gray-700 mb-2">Jenis Layanan</label>
                                    <select 
                                        id="layanan"
                                        name="layanan" 
                                        class="block w-full rounded-lg border-gray-300 py-2 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="all" {{ ($filters['layanan'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Layanan</option>
                                        <option value="domain" {{ ($filters['layanan'] ?? '') === 'domain' ? 'selected' : '' }}>Domain</option>
                                        <option value="hosting" {{ ($filters['layanan'] ?? '') === 'hosting' ? 'selected' : '' }}>Hosting</option>
                                        <option value="vps" {{ ($filters['layanan'] ?? '') === 'vps' ? 'selected' : '' }}>VPS</option>
                                    </select>
                                </div>

                                {{-- Status Filter --}}
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select 
                                        id="status"
                                        name="status" 
                                        class="block w-full rounded-lg border-gray-300 py-2 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                        <option value="sedang_dikerjakan" {{ ($filters['status'] ?? '') === 'sedang_dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                                        <option value="selesai" {{ ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="ditolak" {{ ($filters['status'] ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>

                                {{-- Periode Tanggal Filter --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Tanggal</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <input 
                                                type="date" 
                                                name="tanggal_dari"
                                                value="{{ request('tanggal_dari') }}"
                                                placeholder="Dari"
                                                class="block w-full rounded-lg border-gray-300 py-2 px-3 text-sm shadow-sm transition focus:border-myunila focus:ring-myunila">
                                        </div>
                                        <div>
                                            <input 
                                                type="date" 
                                                name="tanggal_sampai"
                                                value="{{ request('tanggal_sampai') }}"
                                                placeholder="Sampai"
                                                class="block w-full rounded-lg border-gray-300 py-2 px-3 text-sm shadow-sm transition focus:border-myunila focus:ring-myunila">
                                        </div>
                                    </div>
                                </div>

                                {{-- Apply Button --}}
                                <div class="flex gap-2 pt-2 border-t border-gray-100">
                                    <button 
                                        type="submit"
                                        class="flex-1 rounded-lg bg-myunila px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-dark">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

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

    {{-- Sedang Dikerjakan Section --}}
    @if(isset($inProgress) && $inProgress->isNotEmpty())
    <div class="mb-8 overflow-hidden rounded-2xl border-2 border-info/30 bg-white shadow-sm">
        <div class="border-b border-info/20 bg-info-light px-6 py-4">
            <h2 class="flex items-center gap-2 font-semibold text-info">
                <x-icon name="cog" class="h-5 w-5" />
                Sedang Anda Kerjakan
            </h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($inProgress as $submission)
            <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-light text-info">
                        @php $serviceType = strtolower($submission->jenisLayanan?->nm_layanan ?? 'domain'); @endphp
                        @if($serviceType === 'vps')
                        <x-icon name="server" class="h-5 w-5" />
                        @elseif($serviceType === 'hosting')
                        <x-icon name="server" class="h-5 w-5" />
                        @else
                        <x-icon name="globe-alt" class="h-5 w-5" />
                        @endif
                    </div>
                    <div>
                        <p class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</p>
                        <p class="text-sm text-gray-600">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @if($serviceType === 'vps') badge-service-vps
                        @elseif($serviceType === 'hosting') badge-service-hosting
                        @else badge-service-domain
                        @endif">
                        {{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}
                    </span>
                    <a href="{{ route('eksekutor.show', $submission) }}" 
                       class="inline-flex items-center gap-1 rounded-lg bg-myunila px-3 py-1.5 text-xs font-medium text-white transition hover:bg-myunila-dark">
                        Lanjutkan
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        {{-- Table Header --}}
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Riwayat Pekerjaan Saya</h2>
        </div>

        @if($submissions->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="document-text" class="mx-auto h-16 w-16 text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada riwayat</h3>
            <p class="mt-2 text-gray-500">Pengajuan yang Anda kerjakan akan muncul di sini.</p>
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Proses</th>
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
                                @if($statusName === 'Selesai') bg-success-light text-success
                                @elseif(str_contains($statusName, 'Ditolak')) bg-danger-light text-danger
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
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('eksekutor.show', $submission) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-200"
                                   title="Review Pengajuan">
                                    <x-icon name="eye" class="h-4 w-4" />
                                    Review
                                </a>
                                <a href="{{ route('eksekutor.timeline', $submission) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg bg-purple-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-purple-700"
                                   title="Timeline Perubahan Status">
                                    <x-icon name="clock" class="h-4 w-4" />
                                    Timeline
                                </a>
                            </div>
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
                <form method="GET" action="{{ route('eksekutor.my-history') }}" id="perPageForm" class="flex items-center gap-2">
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
