@extends('layouts.dashboard')

@section('title', 'Dashboard Verifikator')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Dashboard Verifikator
        </h1>
        <p class="mt-2 text-gray-600">
            Verifikasi dan kelola pengajuan layanan domain & hosting.
        </p>
    </div>

    {{-- Quick Stats --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Menunggu Verifikasi</p>
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($stats['menunggu']) }}</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="clock" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Disetujui Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['disetujui_hari_ini']) }}</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-circle" class="h-8 w-8 text-success" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ditolak Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($stats['ditolak_hari_ini']) }}</p>
                </div>
                <div class="rounded-xl bg-red-50 p-3">
                    <x-icon name="x-circle" class="h-8 w-8 text-red-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Submissions --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Pengajuan Menunggu Verifikasi</h2>
        </div>
        @if($pendingSubmissions->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($pendingSubmissions as $submission)
            <div class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50">
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900">{{ $submission->rincian?->nm_domain ?? 'N/A' }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $submission->pengguna->nm }} - {{ $submission->unitKerja->nm_lmbg ?? 'N/A' }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        @php $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain'; @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            @if($serviceType === 'vps') badge-service-vps
                            @elseif($serviceType === 'hosting') badge-service-hosting
                            @else badge-service-domain
                            @endif">
                            {{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $submission->tgl_pengajuan->diffForHumans() }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('verifikator.show', $submission->UUID) }}" 
                   class="ml-4 inline-flex items-center gap-1 rounded-lg bg-myunila px-4 py-2 text-sm font-semibold text-white transition hover:bg-myunila-dark">
                    Verifikasi
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <x-icon name="check-badge" class="mx-auto h-16 w-16 text-gray-300" />
            <p class="mt-4 text-lg font-medium text-gray-900">Tidak ada pengajuan menunggu</p>
            <p class="mt-1 text-sm text-gray-500">Semua pengajuan sudah diverifikasi</p>
        </div>
        @endif
    </div>

</div>
@endsection
