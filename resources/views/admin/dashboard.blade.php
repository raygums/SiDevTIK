@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Dashboard Admin
        </h1>
        <p class="mt-2 text-gray-600">
            Selamat datang, {{ Auth::user()->nm }}. Kelola dan verifikasi akun pengguna dari sini.
        </p>
        <p class="mt-1 text-sm text-gray-500">
            <svg class="inline h-4 w-4 text-info" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Anda memiliki akses untuk mengelola akun dengan role <span class="font-semibold">Pengguna</span>. Role lain dikelola oleh Pimpinan.
        </p>
    </div>

    {{-- Quick Stats --}}
    <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Users --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($userStats['total']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $userStats['aktif'] }} aktif, {{ $userStats['nonaktif'] }} non-aktif
                    </p>
                </div>
                <div class="rounded-xl bg-myunila-50 p-3">
                    <x-icon name="users" class="h-8 w-8 text-myunila" />
                </div>
            </div>
        </div>

        {{-- Pending Verification --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Menunggu Verifikasi</p>
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($userStats['nonaktif']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Akun belum diaktifkan</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="clock" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>

        {{-- Total Submissions --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($submissionStats['total']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $submissionStats['bulan_ini'] }} bulan ini</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="document-text" class="h-8 w-8 text-info" />
                </div>
            </div>
        </div>

        {{-- Approved Submissions --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pengajuan Selesai</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($submissionStats['disetujui']) }}</p>
                    <p class="mt-1 text-xs text-gray-500">Total disetujui</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-badge" class="h-8 w-8 text-success" />
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8 grid gap-6 md:grid-cols-2">
        {{-- Verifikasi Akun --}}
        <a href="{{ route('admin.users.verification') }}" 
           class="group overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="rounded-xl bg-myunila-50 p-3 transition group-hover:bg-myunila-100">
                    <x-icon name="user-check" class="h-8 w-8 text-myunila" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Verifikasi Akun Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Aktivasi dan kelola status akun pengguna yang baru mendaftar.
                    </p>
                    @if($userStats['nonaktif'] > 0)
                    <span class="mt-3 inline-flex items-center rounded-full bg-warning-light px-3 py-1 text-xs font-medium text-warning">
                        {{ $userStats['nonaktif'] }} akun menunggu verifikasi
                    </span>
                    @endif
                </div>
                <x-icon name="chevron-right" class="h-5 w-5 flex-shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-myunila" />
            </div>
        </a>

        {{-- Log Audit (Placeholder) --}}
        <a href="{{ route('admin.audit.aktivitas') }}" 
           class="group block overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-200 hover:border-myunila-300 hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="rounded-xl bg-gray-100 p-3 transition-colors group-hover:bg-myunila-50">
                    <x-icon name="document-text" class="h-8 w-8 text-gray-400 transition-colors group-hover:text-myunila-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Log Audit Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Lihat riwayat aktivitas dan audit trail pengguna sistem.
                    </p>
                </div>
                <div class="flex items-center text-gray-400 transition-transform group-hover:translate-x-1 group-hover:text-myunila-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    {{-- Recent User Registrations --}}
    @if($recentUsers->count() > 0)
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Pendaftaran Terbaru (Menunggu Verifikasi)</h2>
                <a href="{{ route('admin.users.verification', ['status' => 'tidak_aktif']) }}" 
                   class="text-sm font-medium text-myunila hover:underline">
                    Lihat Semua
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($recentUsers as $user)
            <div class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-sm font-bold text-myunila">
                        {{ strtoupper(substr($user->nm, 0, 2)) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $user->nm }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if($user->sso_id)
                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                            <x-icon name="key" class="h-3 w-3" />
                            SSO
                        </span>
                    @endif
                    <span class="text-xs text-gray-500">
                        {{ $user->create_at->diffForHumans() }}
                    </span>
                    <a href="{{ route('admin.users.verification', ['search' => $user->email]) }}" 
                       class="inline-flex items-center gap-1 rounded-lg bg-myunila-50 px-3 py-1.5 text-xs font-medium text-myunila transition hover:bg-myunila-100">
                        <x-icon name="arrow-right" class="h-3 w-3" />
                        Verifikasi
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
