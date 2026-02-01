@extends('layouts.dashboard')

@section('title', 'Dashboard Eksekutor')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Dashboard Eksekutor
        </h1>
        <p class="mt-2 text-gray-600">
            Eksekusi dan selesaikan pengajuan yang sudah disetujui verifikator.
        </p>
    </div>

    {{-- Quick Stats --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-warning-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tugas Baru</p>
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($stats['tugas_baru']) }}</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="inbox" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-info-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sedang Dikerjakan</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($stats['sedang_dikerjakan']) }}</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="cog" class="h-8 w-8 text-info" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-success-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['selesai_hari_ini']) }}</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-badge" class="h-8 w-8 text-success" />
                </div>
            </div>
        </div>
    </div>

    {{-- Task List --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Daftar Tugas</h2>
        </div>
        @if($tasks->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($tasks as $task)
            <div class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50">
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900">{{ $task->rincian->nm_subdomain ?? 'N/A' }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $task->pengguna->nm }} - {{ $task->unitKerja->nm_unit ?? 'N/A' }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-myunila-100 px-2 py-0.5 text-xs font-medium text-myunila">
                            {{ $task->jenisLayanan->nm_layanan }}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $task->status->nm_status === 'Sedang Dikerjakan' ? 'bg-info-light text-info' : 'bg-warning-light text-warning' }}">
                            {{ $task->status->nm_status }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $task->tgl_pengajuan->diffForHumans() }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('eksekutor.show', $task->UUID) }}" 
                   class="ml-4 inline-flex items-center gap-1 rounded-lg bg-myunila px-4 py-2 text-sm font-semibold text-white transition hover:bg-myunila-dark">
                    Kerjakan
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <x-icon name="check-badge" class="mx-auto h-16 w-16 text-gray-300" />
            <p class="mt-4 text-lg font-medium text-gray-900">Tidak ada tugas saat ini</p>
            <p class="mt-1 text-sm text-gray-500">Semua tugas sudah selesai</p>
        </div>
        @endif
    </div>

</div>
@endsection
