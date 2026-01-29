@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-unila text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Admin Dashboard</h1>
                    <p class="text-gray-600">Overview sistem pengajuan domain & hosting</p>
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

        {{-- Main Stats --}}
        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Pengajuan --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['total_pengajuan'] }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $stats['pengajuan_bulan_ini'] }} bulan ini</p>
                    </div>
                    <div class="rounded-full bg-myunila-50 p-3">
                        <svg class="h-6 w-6 text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Menunggu Verifikasi --}}
            <div class="rounded-2xl border border-warning/30 bg-warning-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-warning">Menunggu Verifikasi</p>
                        <p class="mt-1 text-3xl font-bold text-warning">{{ $stats['menunggu_verifikasi'] }}</p>
                        <p class="mt-1 text-xs text-warning/70">perlu ditinjau</p>
                    </div>
                    <div class="rounded-full bg-warning/20 p-3">
                        <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Sedang Dikerjakan --}}
            <div class="rounded-2xl border border-info/30 bg-info-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-info">Sedang Dikerjakan</p>
                        <p class="mt-1 text-3xl font-bold text-info">{{ $stats['sedang_dikerjakan'] }}</p>
                        <p class="mt-1 text-xs text-info/70">+ {{ $stats['menunggu_eksekusi'] }} menunggu</p>
                    </div>
                    <div class="rounded-full bg-info/20 p-3">
                        <svg class="h-6 w-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="rounded-2xl border border-success/30 bg-success-light p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-success">Selesai</p>
                        <p class="mt-1 text-3xl font-bold text-success">{{ $stats['selesai'] }}</p>
                        <p class="mt-1 text-xs text-success/70">{{ $stats['ditolak'] }} ditolak</p>
                    </div>
                    <div class="rounded-full bg-success/20 p-3">
                        <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Left Column --}}
            <div class="space-y-8 lg:col-span-2">
                {{-- Recent Submissions --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Pengajuan Terbaru</h2>
                    </div>

                    @if($recentSubmissions->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada pengajuan</h3>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tiket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Layanan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($recentSubmissions as $submission)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($submission->unitKerja?->nm_lmbg ?? '-', 30) }}</div>
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
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @php $status = $submission->status?->nm_status ?? ''; @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            @if($status === 'Selesai') bg-success-light text-success
                                            @elseif(str_contains($status, 'Ditolak')) bg-danger-light text-danger
                                            @elseif($status === 'Sedang Dikerjakan') bg-info-light text-info
                                            @elseif($status === 'Diajukan') bg-warning-light text-warning
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $submission->status?->nm_status ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ $submission->tgl_pengajuan?->format('d M Y') ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Quick Links --}}
                <div class="grid gap-4 sm:grid-cols-3">
                    <a href="{{ route('verifikator.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="rounded-lg bg-teal-100 p-2 text-teal-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Verifikasi</p>
                            <p class="text-xs text-gray-500">{{ $stats['menunggu_verifikasi'] }} pending</p>
                        </div>
                    </a>

                    <a href="{{ route('eksekutor.index') }}" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="rounded-lg bg-purple-100 p-2 text-purple-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Eksekusi</p>
                            <p class="text-xs text-gray-500">{{ $stats['menunggu_eksekusi'] + $stats['sedang_dikerjakan'] }} aktif</p>
                        </div>
                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="rounded-lg bg-gray-100 p-2 text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Kelola User</p>
                            <p class="text-xs text-gray-500">{{ $stats['total_users'] }} users</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                {{-- Stats per Layanan --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-gray-900">Pengajuan per Layanan</h3>
                    <div class="space-y-4">
                        @forelse($layananStats as $layanan)
                        @php
                            $colors = [
                                'domain' => ['bg' => 'bg-green-500', 'light' => 'bg-green-100'],
                                'hosting' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-100'],
                                'vps' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-100'],
                            ];
                            $color = $colors[$layanan->nm_layanan] ?? ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100'];
                            $total = $stats['total_pengajuan'] > 0 ? $stats['total_pengajuan'] : 1;
                            $percent = round(($layanan->submissions_count / $total) * 100);
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ ucfirst($layanan->nm_layanan) }}</span>
                                <span class="text-gray-500">{{ $layanan->submissions_count }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full {{ $color['light'] }}">
                                <div class="h-2 rounded-full {{ $color['bg'] }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Tidak ada data</p>
                        @endforelse
                    </div>
                </div>

                {{-- User Stats --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-gray-900">User per Role</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-myunila"></span>
                                <span class="text-sm text-gray-600">Admin</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $userStats['admin'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-teal-500"></span>
                                <span class="text-sm text-gray-600">Verifikator</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $userStats['verifikator'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-purple-500"></span>
                                <span class="text-sm text-gray-600">Eksekutor</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $userStats['eksekutor'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full bg-gray-400"></span>
                                <span class="text-sm text-gray-600">Pengguna</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $userStats['pengguna'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- System Info --}}
                <div class="rounded-2xl border border-myunila/20 bg-myunila-50 p-6">
                    <h3 class="mb-3 font-semibold text-myunila">Informasi Sistem</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Laravel</dt>
                            <dd class="font-medium text-gray-900">{{ app()->version() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">PHP</dt>
                            <dd class="font-medium text-gray-900">{{ phpversion() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Environment</dt>
                            <dd class="font-medium text-gray-900">{{ app()->environment() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
