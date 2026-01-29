@extends('layouts.app')

@section('title', 'Dashboard Eksekutor')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-600 text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard Eksekutor</h1>
                    <p class="text-gray-600">Kerjakan pengajuan domain, hosting, dan VPS</p>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('success'))
        <div class="mb-6 rounded-xl border border-success/30 bg-success-light p-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-success">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 rounded-xl border border-danger/30 bg-danger-light p-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-danger" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-danger">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        {{-- Stats Cards --}}
        <div class="mb-8 grid gap-6 sm:grid-cols-4">
            <div class="rounded-2xl border border-warning/30 bg-warning-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-warning">Menunggu</p>
                        <p class="mt-1 text-3xl font-bold text-warning">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="rounded-full bg-warning/20 p-3">
                        <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-info/30 bg-info-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-info">Sedang Dikerjakan</p>
                        <p class="mt-1 text-3xl font-bold text-info">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="rounded-full bg-info/20 p-3">
                        <svg class="h-6 w-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-success/30 bg-success-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-success">Selesai Hari Ini</p>
                        <p class="mt-1 text-3xl font-bold text-success">{{ $stats['completed_today'] }}</p>
                    </div>
                    <div class="rounded-full bg-success/20 p-3">
                        <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-danger/30 bg-danger-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-danger">Ditolak Hari Ini</p>
                        <p class="mt-1 text-3xl font-bold text-danger">{{ $stats['rejected_today'] }}</p>
                    </div>
                    <div class="rounded-full bg-danger/20 p-3">
                        <svg class="h-6 w-6 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- In Progress Section --}}
        @if($inProgress->isNotEmpty())
        <div class="mb-8 overflow-hidden rounded-2xl border-2 border-info/30 bg-white shadow-sm">
            <div class="border-b border-info/20 bg-info-light px-6 py-4">
                <h2 class="flex items-center gap-2 font-semibold text-info">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Sedang Anda Kerjakan
                </h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($inProgress as $submission)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-light text-info">
                            @php $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain'; @endphp
                            @if($serviceType === 'vps')
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                            @else
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            @endif
                        </div>
                        <div>
                            <p class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</p>
                            <p class="text-sm text-gray-600">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('eksekutor.show', $submission) }}" class="btn-primary text-sm">
                        Lanjutkan
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Pending Submissions Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="font-semibold text-gray-900">Pengajuan Siap Dikerjakan</h2>
                <p class="text-sm text-gray-500">Pengajuan yang sudah diverifikasi dan menunggu eksekusi</p>
            </div>

            @if($submissions->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada pengajuan</h3>
                <p class="mt-2 text-gray-500">Semua pengajuan sudah dikerjakan. Bagus!</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No. Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pemohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Verifikasi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @php $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain'; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($serviceType === 'vps') bg-purple-100 text-purple-800
                                    @elseif($serviceType === 'hosting') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($serviceType) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $submission->rincian?->nm_domain ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $submission->last_update?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <a href="{{ route('eksekutor.show', $submission) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg bg-purple-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-purple-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Kerjakan
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                {{ $submissions->links() }}
            </div>
            @endif
            @endif
        </div>

        {{-- Quick Links --}}
        <div class="mt-8 flex gap-4">
            <a href="{{ route('eksekutor.history') }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Eksekusi
            </a>
        </div>
    </div>
</div>
@endsection
