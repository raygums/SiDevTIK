@extends('layouts.dashboard')

@section('title', 'Log Aktivitas')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Log Aktivitas
                </h1>
                <p class="mt-2 text-gray-600">
                    Monitor aktivitas pengguna sistem (login & pengajuan) secara real-time
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-icon name="clock" class="h-8 w-8 text-myunila" />
            </div>
        </div>

        {{-- Info Banner if filtering by user --}}
        @if(isset($user) && $user)
        <div class="mt-4 rounded-lg border-l-4 border-myunila bg-myunila-50 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-icon name="user" class="h-5 w-5 text-myunila" />
                    <div>
                        <p class="text-sm font-medium text-gray-900">Menampilkan aktivitas untuk:</p>
                        <p class="text-sm text-gray-600">{{ $user->nm }} ({{ $user->email }})</p>
                    </div>
                </div>
                <a href="{{ route('admin.audit.login') }}" 
                   class="rounded-md bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Tampilkan Semua
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- Statistics Cards --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Users --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="rounded-xl bg-myunila-50 p-3">
                    <x-icon name="users" class="h-8 w-8 text-myunila" />
                </div>
            </div>
        </div>

        {{-- Users With Login --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pernah Login</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($stats['users_with_login']) }}</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="user-check" class="h-8 w-8 text-info" />
                </div>
            </div>
        </div>

        {{-- Active Today --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Aktif Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['active_today']) }}</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-circle" class="h-8 w-8 text-success" />
                </div>
            </div>
        </div>

        {{-- Active This Week --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Aktif Minggu Ini</p>
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($stats['active_this_week']) }}</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="calendar" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>
    </div>

    {{-- Login Logs Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Riwayat Login Terbaru</h2>
                <span class="text-sm text-gray-500">{{ $logs->total() }} total log</span>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <form method="GET" action="{{ route('admin.audit.login') }}" id="filterFormLogin">
                {{-- Hidden input for user_uuid if filtering by user --}}
                @if(isset($filters['user_uuid']) && $filters['user_uuid'])
                <input type="hidden" name="user_uuid" value="{{ $filters['user_uuid'] }}">
                @endif
                
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
                               placeholder="Cari nama, email, atau username..."
                               class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
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
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-lg border border-gray-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                            style="display: none;">
                            
                            <div class="border-b border-gray-200 px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900">Filter</h3>
                                    <a 
                                        href="{{ route('admin.audit.login') }}"
                                        class="text-xs font-medium text-red-600 hover:text-red-700">
                                        Reset
                                    </a>
                                </div>
                            </div>

                            <div class="p-4 space-y-4">
                                {{-- Log Type Filter --}}
                                <div>
                                    <label for="log_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Log</label>
                                    <select name="log_type" 
                                            id="log_type"
                                            class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="all" {{ ($filters['log_type'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Aktivitas</option>
                                        <option value="login" {{ ($filters['log_type'] ?? '') === 'login' ? 'selected' : '' }}>Login Saja</option>
                                        <option value="submission" {{ ($filters['log_type'] ?? '') === 'submission' ? 'selected' : '' }}>Pengajuan Saja</option>
                                    </select>
                                </div>

                                {{-- Status Filter --}}
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Akun</label>
                                    <select name="status" 
                                            id="status"
                                            class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ ($filters['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ ($filters['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                                    </select>
                                </div>

                                {{-- Has Login Filter --}}
                                <div>
                                    <label for="has_login" class="block text-sm font-medium text-gray-700 mb-2">Riwayat Login</label>
                                    <select name="has_login" 
                                            id="has_login"
                                            class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="yes" {{ ($filters['has_login'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Sudah Pernah Login</option>
                                        <option value="no" {{ ($filters['has_login'] ?? '') === 'no' ? 'selected' : '' }}>Belum Pernah Login</option>
                                        <option value="" {{ ($filters['has_login'] ?? '') === '' ? 'selected' : '' }}>Semua</option>
                                    </select>
                                </div>

                                {{-- Date Range Filter --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Login</label>
                                    <div class="space-y-2">
                                        <input type="date" 
                                               name="date_from" 
                                               id="date_from"
                                               value="{{ $filters['date_from'] ?? '' }}"
                                               class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm"
                                               placeholder="Dari">
                                        <input type="date" 
                                               name="date_to" 
                                               id="date_to"
                                               value="{{ $filters['date_to'] ?? '' }}"
                                               class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm"
                                               placeholder="Sampai">
                                    </div>
                                </div>

                                {{-- Apply Button --}}
                                <div class="flex gap-2 pt-2 border-t border-gray-100 mt-4">
                                    <button 
                                        type="submit"
                                        class="flex-1 rounded-lg bg-myunila px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Search Button --}}
                    <button type="submit" 
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                        <span>Cari</span>
                    </button>
                </div>
            </form>
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
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Aktivitas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Waktu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Detail
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($logs as $log)
                    @php
                        $isLoginLog = isset($log->status_akses);
                        $user = $isLoginLog ? $log->user : ($log->creator ?? $log->pengguna);
                    @endphp
                    <tr class="transition hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($isLoginLog)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">
                                    <x-icon name="login" class="mr-1 h-3 w-3" />
                                    Login
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-800">
                                    <x-icon name="document-text" class="mr-1 h-3 w-3" />
                                    Pengajuan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user)
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-xs font-bold text-myunila">
                                    {{ strtoupper(substr($user->nm ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->nm ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email ?? '-' }}</div>
                                </div>
                            </div>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($isLoginLog)
                                <div class="text-sm text-gray-900">
                                    @if($log->status_akses === 'BERHASIL')
                                        <span class="font-medium text-success">Login Berhasil</span>
                                    @elseif($log->status_akses === 'GAGAL_PASSWORD')
                                        <span class="font-medium text-danger">Login Gagal - Password Salah</span>
                                    @elseif($log->status_akses === 'GAGAL_SUSPEND')
                                        <span class="font-medium text-warning">Login Gagal - Akun Suspended</span>
                                    @elseif($log->status_akses === 'GAGAL_NOT_FOUND')
                                        <span class="font-medium text-gray-600">Login Gagal - User Tidak Ditemukan</span>
                                    @elseif($log->status_akses === 'GAGAL_SSO')
                                        <span class="font-medium text-danger">Login Gagal - SSO Error</span>
                                    @else
                                        <span>{{ $log->status_akses }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">IP: {{ $log->alamat_ip ?? '-' }}</div>
                            @else
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">Status Pengajuan:</span>
                                    @if($log->statusLama)
                                        {{ $log->statusLama->nm_status }}
                                    @endif
                                    →
                                    @if($log->statusBaru)
                                        {{ $log->statusBaru->nm_status }}
                                    @endif
                                </div>
                                @if($log->catatan_log)
                                <div class="text-xs text-gray-500">{{ Str::limit($log->catatan_log, 50) }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->create_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $log->create_at->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            @if(!$isLoginLog && $log->pengajuan)
                                <a href="{{ route('admin.submissions.show', $log->pengajuan->UUID) }}" 
                                   class="font-medium text-myunila hover:underline">
                                    Lihat Pengajuan
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
