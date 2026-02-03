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
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-myunila text-white font-bold text-lg">
                        {{ strtoupper(substr($user->nm, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900">{{ $user->nm }}</p>
                        <p class="text-sm text-gray-600">{{ $user->email }} • {{ $user->usn }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.audit.aktivitas') }}" 
                   class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 border border-gray-300">
                    ← Kembali ke Daftar
                </a>
            </div>
        </div>
        @endif
    </div>

    {{-- USER DETAIL MODE: Timeline View --}}
    @if(isset($user) && $user && isset($logs))
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left Column: User Info & Stats --}}
        <div class="space-y-6">
            {{-- User Profile Card --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gradient-to-r from-myunila to-myunila-600 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-myunila font-bold text-2xl shadow-lg">
                            {{ strtoupper(substr($user->nm, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">{{ $user->nm }}</h3>
                            <p class="text-sm text-myunila-100">{{ $user->peran->nm_peran ?? 'Pengguna' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3 text-sm">
                        <x-icon name="mail" class="h-5 w-5 text-gray-400" />
                        <span class="text-gray-600">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <x-icon name="user" class="h-5 w-5 text-gray-400" />
                        <span class="text-gray-600">{{ $user->usn }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <x-icon name="calendar" class="h-5 w-5 text-gray-400" />
                        <span class="text-gray-600">Terdaftar {{ $user->create_at->format('d M Y') }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        @if($user->a_aktif)
                            <span class="inline-flex items-center gap-1 rounded-full bg-success-light px-3 py-1 text-sm font-medium text-success">
                                <x-icon name="check-circle" class="h-4 w-4" />
                                Akun Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">
                                <x-icon name="x-circle" class="h-4 w-4" />
                                Akun Non-Aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Activity Summary --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">Ringkasan Aktivitas</h3>
                </div>
                <div class="p-6 space-y-4">
                    @php
                        $totalLogin = $logs->filter(fn($log) => isset($log->status_akses) && $log->status_akses === 'BERHASIL')->count();
                        $totalSubmission = $logs->filter(fn($log) => isset($log->pengajuan_uuid))->count();
                        $failedLogin = $logs->filter(fn($log) => isset($log->status_akses) && $log->status_akses !== 'BERHASIL')->count();
                    @endphp
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-success-light p-2">
                                <x-icon name="check-circle" class="h-5 w-5 text-success" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Login Berhasil</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalLogin }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-purple-100 p-2">
                                <x-icon name="document-text" class="h-5 w-5 text-purple-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Pengajuan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalSubmission }}</p>
                            </div>
                        </div>
                    </div>

                    @if($failedLogin > 0)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-red-100 p-2">
                                <x-icon name="x-circle" class="h-5 w-5 text-red-600" />
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Login Gagal</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $failedLogin }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">Total aktivitas tercatat</p>
                        <p class="text-lg font-semibold text-myunila">{{ $logs->count() }} aktivitas</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Timeline --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">Timeline Aktivitas</h3>
                    <p class="text-sm text-gray-500 mt-1">Riwayat aktivitas pengguna secara kronologis</p>
                </div>

                @if($logs->isEmpty())
                <div class="p-12 text-center">
                    <x-icon name="exclamation-circle" class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Aktivitas</h3>
                    <p class="mt-2 text-sm text-gray-500">User ini belum memiliki riwayat aktivitas</p>
                </div>
                @else
                <div class="p-6">
                    <div class="space-y-0">
                        @foreach($logs as $index => $log)
                        @php
                            $isLoginLog = isset($log->status_akses);
                            $isLastItem = $index === $logs->count() - 1;
                        @endphp
                        
                        <div class="relative flex gap-4 pb-8 {{ $isLastItem ? '' : '' }}">
                            {{-- Timeline Line --}}
                            @if(!$isLastItem)
                            <div class="absolute left-6 top-12 bottom-0 w-0.5 bg-gray-200"></div>
                            @endif

                            {{-- Icon --}}
                            <div class="relative z-10 flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full border-4 border-white shadow-md
                                {{ $isLoginLog 
                                    ? ($log->status_akses === 'BERHASIL' ? 'bg-success' : 'bg-red-500')
                                    : 'bg-purple-500' }}">
                                @if($isLoginLog)
                                    @if($log->status_akses === 'BERHASIL')
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                    @else
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    @endif
                                @else
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 pb-4">
                                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            @if($isLoginLog)
                                                <h4 class="font-semibold text-gray-900">
                                                    @if($log->status_akses === 'BERHASIL')
                                                        Login Berhasil
                                                    @elseif($log->status_akses === 'GAGAL_PASSWORD')
                                                        Login Gagal - Password Salah
                                                    @elseif($log->status_akses === 'GAGAL_SUSPEND')
                                                        Login Gagal - Akun Suspended
                                                    @elseif($log->status_akses === 'GAGAL_NOT_FOUND')
                                                        Login Gagal - User Tidak Ditemukan
                                                    @elseif($log->status_akses === 'GAGAL_SSO')
                                                        Login Gagal - SSO Error
                                                    @else
                                                        {{ $log->status_akses }}
                                                    @endif
                                                </h4>
                                                <p class="text-sm text-gray-500 mt-1">Aktivitas Login</p>
                                            @else
                                                <h4 class="font-semibold text-gray-900">Perubahan Status Pengajuan</h4>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    @if($log->statusLama)
                                                        {{ $log->statusLama->nm_status }}
                                                    @endif
                                                    →
                                                    @if($log->statusBaru)
                                                        {{ $log->statusBaru->nm_status }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $isLoginLog 
                                                ? ($log->status_akses === 'BERHASIL' ? 'bg-success-light text-success' : 'bg-red-100 text-red-700')
                                                : 'bg-purple-100 text-purple-700' }}">
                                            {{ $isLoginLog ? 'Login' : 'Pengajuan' }}
                                        </span>
                                    </div>

                                    {{-- Details --}}
                                    <div class="mt-3 space-y-2 text-sm">
                                        @if($isLoginLog)
                                            @if($log->alamat_ip)
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <x-icon name="globe-alt" class="h-4 w-4 text-gray-400" />
                                                <span>IP Address: <span class="font-mono">{{ $log->alamat_ip }}</span></span>
                                            </div>
                                            @endif
                                            @if($log->perangkat)
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <x-icon name="device-mobile" class="h-4 w-4 text-gray-400" />
                                                <span class="text-xs">{{ Str::limit($log->perangkat, 60) }}</span>
                                            </div>
                                            @endif
                                        @else
                                            @if($log->catatan_log)
                                            <div class="flex items-start gap-2 text-gray-600">
                                                <x-icon name="chat-alt" class="h-4 w-4 text-gray-400 mt-0.5" />
                                                <span>{{ $log->catatan_log }}</span>
                                            </div>
                                            @endif
                                            @if($log->pengajuan)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('submissions.show', $log->pengajuan->UUID) }}" 
                                                   class="inline-flex items-center gap-1 text-myunila hover:underline font-medium">
                                                    <x-icon name="external-link" class="h-4 w-4" />
                                                    Lihat Pengajuan #{{ $log->pengajuan->no_tiket }}
                                                </a>
                                            </div>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Footer: Time --}}
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <x-icon name="clock" class="h-4 w-4" />
                                            <span>{{ $log->create_at->format('d M Y, H:i') }} WIB</span>
                                            <span class="text-gray-400">•</span>
                                            <span>{{ $log->create_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination for Timeline View --}}
                    @if($logs->hasPages() || $logs->total() > 10)
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            {{-- Items Per Page Selector --}}
                            <form method="GET" action="{{ route('admin.audit.aktivitas') }}" class="flex items-center gap-2">
                                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                
                                <label for="per_page_timeline" class="text-sm text-gray-700">Tampilkan:</label>
                                <select name="per_page" 
                                        id="per_page_timeline"
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
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- NORMAL MODE: Show stats and tables --}}
    @else
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
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($stats['unique_users']) }}</p>
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
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['today_successful']) }}</p>
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
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($stats['week_attempts']) }}</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="calendar" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>
    </div>

    {{-- Dynamic Content: User List or Activity List --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                @if(isset($users))
                    <h2 class="font-semibold text-gray-900">Daftar Pengguna</h2>
                    <span class="text-sm text-gray-500">{{ $users->total() }} pengguna</span>
                @else
                    <h2 class="font-semibold text-gray-900">Riwayat Aktivitas</h2>
                    <span class="text-sm text-gray-500">{{ $logs->total() }} aktivitas</span>
                @endif
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <form method="GET" action="{{ route('admin.audit.aktivitas') }}" id="filterFormLogin">
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
                                        href="{{ route('admin.audit.aktivitas') }}"
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
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Login</label>
                                    <select name="status" 
                                            id="status"
                                            class="block w-full rounded-lg border-gray-300 py-2 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="">Semua Status</option>
                                        <option value="BERHASIL" {{ ($filters['status'] ?? '') === 'BERHASIL' ? 'selected' : '' }}>Berhasil</option>
                                        <option value="GAGAL_PASSWORD" {{ ($filters['status'] ?? '') === 'GAGAL_PASSWORD' ? 'selected' : '' }}>Gagal - Password</option>
                                        <option value="GAGAL_SUSPEND" {{ ($filters['status'] ?? '') === 'GAGAL_SUSPEND' ? 'selected' : '' }}>Gagal - Suspend</option>
                                        <option value="GAGAL_SSO" {{ ($filters['status'] ?? '') === 'GAGAL_SSO' ? 'selected' : '' }}>Gagal - SSO</option>
                                    </select>
                                </div>

                                {{-- Date Range Filter --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label for="date_from" class="block text-xs text-gray-500 mb-1">Dari</label>
                                            <input type="date" 
                                                   name="date_from" 
                                                   id="date_from"
                                                   value="{{ $filters['date_from'] ?? '' }}"
                                                   class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-myunila focus:ring-myunila">
                                        </div>
                                        <div>
                                            <label for="date_to" class="block text-xs text-gray-500 mb-1">Sampai</label>
                                            <input type="date" 
                                                   name="date_to" 
                                                   id="date_to"
                                                   value="{{ $filters['date_to'] ?? '' }}"
                                                   class="block w-full rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-myunila focus:ring-myunila">
                                        </div>
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

        {{-- MODE 1: User List View --}}
        @if(isset($users))
        @if($users->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="users" class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Pengguna</h3>
            <p class="mt-2 text-sm text-gray-500">Sistem belum memiliki pengguna terdaftar</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Peran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Tipe Akun
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            IP Terakhir
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Login Terakhir
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                            Total Pengajuan
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($users as $user)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-sm font-bold text-myunila">
                                    {{ strtoupper(substr($user->nm, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->nm }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->usn }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-myunila-100 px-2.5 py-0.5 text-xs font-medium text-myunila">
                                {{ $user->peran->nm_peran ?? 'Pengguna' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->sso_id)
                                <span class="inline-flex items-center gap-1 rounded-full bg-info-light px-2.5 py-1 text-xs font-medium text-info">
                                    <x-icon name="key" class="h-3.5 w-3.5" />
                                    SSO
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                    <x-icon name="user" class="h-3.5 w-3.5" />
                                    Lokal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <x-icon name="globe-alt" class="h-4 w-4 text-gray-400" />
                                {{ $user->last_login_ip ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($user->last_login_at)
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $user->last_login_at->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $user->last_login_at->format('H:i') }} WIB
                                </div>
                            @else
                                <span class="text-gray-400">Belum pernah login</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-800">
                                {{ number_format($user->total_submissions) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a 
                                href="{{ route('admin.audit.aktivitas', ['user_uuid' => $user->UUID]) }}"
                                class="inline-flex items-center gap-1 rounded-lg bg-myunila px-3 py-1.5 text-sm font-medium text-white transition hover:bg-myunila-600"
                                title="Lihat Detail Aktivitas">
                                <x-icon name="eye" class="h-4 w-4" />
                                Log
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination with Items Per Page --}}
        @if($users->hasPages() || $users->total() > 10)
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Items Per Page Selector --}}
                <form method="GET" action="{{ route('admin.audit.aktivitas') }}" class="flex items-center gap-2">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page_users" class="text-sm text-gray-700">Tampilkan:</label>
                    <select name="per_page" 
                            id="per_page_users"
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
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
        @endif

        {{-- MODE 2: Activity List View --}}
        @else
        @if($logs->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="exclamation-circle" class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Aktivitas Ditemukan</h3>
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
                        
                        // Determine user for this log entry
                        if ($isLoginLog) {
                            // Login log - user is directly on pengguna relation
                            $user = $log->pengguna;
                        } else {
                            // Submission log - user is on pengajuan.pengguna relation
                            $user = $log->pengajuan->pengguna ?? null;
                        }
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
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium text-gray-900">{{ $user->nm ?? 'Unknown' }}</div>
                                    <div class="truncate text-xs text-gray-500">{{ $user->email ?? '-' }}</div>
                                </div>
                            </div>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($isLoginLog)
                                <div class="space-y-1">
                                    @if($log->status_akses === 'BERHASIL')
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="flex h-2 w-2 rounded-full bg-success"></span>
                                            <span class="text-sm font-medium text-success">Login Berhasil</span>
                                        </div>
                                    @elseif($log->status_akses === 'GAGAL_PASSWORD')
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="flex h-2 w-2 rounded-full bg-danger"></span>
                                            <span class="text-sm font-medium text-danger">Password Salah</span>
                                        </div>
                                    @elseif($log->status_akses === 'GAGAL_SUSPEND')
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="flex h-2 w-2 rounded-full bg-warning"></span>
                                            <span class="text-sm font-medium text-warning">Akun Suspended</span>
                                        </div>
                                    @elseif($log->status_akses === 'GAGAL_NOT_FOUND')
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="flex h-2 w-2 rounded-full bg-gray-400"></span>
                                            <span class="text-sm font-medium text-gray-600">User Tidak Ditemukan</span>
                                        </div>
                                    @elseif($log->status_akses === 'GAGAL_SSO')
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="flex h-2 w-2 rounded-full bg-danger"></span>
                                            <span class="text-sm font-medium text-danger">SSO Error</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-900">{{ $log->status_akses }}</span>
                                    @endif
                                    @if($log->alamat_ip)
                                        <div class="text-xs text-gray-500">{{ $log->alamat_ip }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 text-sm">
                                        @if($log->statusLama)
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                                {{ $log->statusLama->nm_status }}
                                            </span>
                                        @endif
                                        <x-icon name="arrow-right" class="h-3 w-3 text-gray-400" />
                                        @if($log->statusBaru)
                                            <span class="inline-flex items-center rounded-full bg-myunila-100 px-2 py-0.5 text-xs font-medium text-myunila">
                                                {{ $log->statusBaru->nm_status }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($log->catatan_log)
                                        <div class="text-xs text-gray-500">{{ Str::limit($log->catatan_log, 60) }}</div>
                                    @endif
                                </div>
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
                                <a href="{{ route('submissions.show', $log->pengajuan->UUID) }}" 
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

        {{-- Pagination with Items Per Page --}}
        @if($logs->hasPages() || $logs->total() > 10)
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Items Per Page Selector --}}
                <form method="GET" action="{{ route('admin.audit.aktivitas') }}" class="flex items-center gap-2">
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
        @endif
        @endif
    </div>
    @endif
</div>

@endsection
