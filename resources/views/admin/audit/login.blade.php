@extends('layouts.dashboard')

@section('title', 'Log Aktivitas Login')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Log Aktivitas Login
                </h1>
                <p class="mt-2 text-gray-600">
                    Monitor aktivitas login pengguna sistem secara real-time
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-icon name="clock" class="h-8 w-8 text-myunila" />
            </div>
        </div>
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
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Riwayat Login Terbaru</h2>
                <span class="text-sm text-gray-500">{{ $logs->total() }} total log</span>
            </div>
        </div>

        {{-- Advanced Filters --}}
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <form method="GET" action="{{ route('admin.audit.login') }}" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari Pengguna</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Nama, email, atau username..."
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status Akun</label>
                        <select name="status" 
                                id="status"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ ($filters['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ ($filters['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>

                    {{-- Has Login --}}
                    <div>
                        <label for="has_login" class="block text-sm font-medium text-gray-700">Riwayat Login</label>
                        <select name="has_login" 
                                id="has_login"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                            <option value="yes" {{ ($filters['has_login'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Sudah Pernah Login</option>
                            <option value="no" {{ ($filters['has_login'] ?? '') === 'no' ? 'selected' : '' }}>Belum Pernah Login</option>
                            <option value="" {{ ($filters['has_login'] ?? '') === '' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Tanggal Login</label>
                        <div class="mt-1 flex gap-2">
                            <input type="date" 
                                   name="date_from" 
                                   id="date_from"
                                   value="{{ $filters['date_from'] ?? '' }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm"
                                   placeholder="Dari">
                            <input type="date" 
                                   name="date_to" 
                                   id="date_to"
                                   value="{{ $filters['date_to'] ?? '' }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm"
                                   placeholder="Sampai">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 rounded-lg bg-myunila px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                        <x-icon name="search" class="h-4 w-4" />
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.audit.login') }}" 
                       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                        <x-icon name="x" class="h-4 w-4" />
                        Reset Filter
                    </a>
                    @if(array_filter($filters))
                    <span class="text-sm text-gray-500">
                        Filter aktif: <span class="font-semibold text-myunila">{{ count(array_filter($filters)) }}</span>
                    </span>
                    @endif
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
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Email / Username
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Waktu Login
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            IP Address
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($logs as $log)
                    <tr class="transition hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-sm font-bold text-myunila">
                                    {{ strtoupper(substr($log->nm, 0, 2)) }}
                                </div>
                                <div class="font-medium text-gray-900">{{ $log->nm }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $log->email }}</div>
                            <div class="text-xs text-gray-500">{{ $log->usn }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->last_login_at?->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $log->last_login_at?->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="font-mono text-xs text-gray-600">
                                {{ $log->last_login_ip ?? '-' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($log->a_aktif)
                            <span class="inline-flex items-center rounded-full bg-success-light px-2.5 py-0.5 text-xs font-medium text-success">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-success"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                Non-Aktif
                            </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <a href="{{ route('admin.audit.user-detail', $log->UUID) }}" 
                               class="font-medium text-myunila hover:underline">
                                Lihat Detail
                            </a>
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
