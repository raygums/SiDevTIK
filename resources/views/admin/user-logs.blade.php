@extends('layouts.dashboard')

@section('title', 'Audit Log User')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header with Back Button --}}
    <div class="mb-8">
        <a href="{{ route('admin.users.verification') }}" 
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-myunila">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali ke Verifikasi Akun
        </a>
        <h1 class="mt-4 text-2xl font-bold text-gray-900 sm:text-3xl">
            Audit Log Pengguna
        </h1>
        <p class="mt-2 text-gray-600">
            Riwayat aktivitas dan informasi detail akun pengguna.
        </p>
    </div>

    {{-- User Profile Card --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Informasi Pengguna</h2>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-2xl font-bold text-myunila">
                    {{ strtoupper(substr($user->nm, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900">{{ $user->nm }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500">Username: {{ $user->usn }}</p>
                    
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-myunila-100 px-3 py-1 text-xs font-medium text-myunila">
                            <x-icon name="shield-check" class="h-3 w-3" />
                            {{ $user->peran->nm_peran ?? 'Pengguna' }}
                        </span>
                        
                        @if($user->a_aktif)
                            <span class="inline-flex items-center gap-1 rounded-full bg-success-light px-3 py-1 text-xs font-medium text-success">
                                <x-icon name="check-circle" class="h-3 w-3" />
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-warning-light px-3 py-1 text-xs font-medium text-warning">
                                <x-icon name="clock" class="h-3 w-3" />
                                Belum Aktif
                            </span>
                        @endif

                        @if($user->sso_id)
                            <span class="inline-flex items-center gap-1 rounded-full bg-info-light px-3 py-1 text-xs font-medium text-info">
                                <x-icon name="key" class="h-3 w-3" />
                                Akun SSO
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline Audit --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Timeline Aktivitas</h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                
                {{-- Waktu Daftar --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-myunila-50">
                            <x-icon name="user-plus" class="h-5 w-5 text-myunila" />
                        </div>
                        <div class="mt-2 h-full w-0.5 bg-gray-200"></div>
                    </div>
                    <div class="flex-1 pb-6">
                        <h3 class="font-semibold text-gray-900">Pendaftaran Akun</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $waktu_daftar ? $waktu_daftar->format('d M Y, H:i') : '-' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $waktu_daftar ? $waktu_daftar->diffForHumans() : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Last Login --}}
                @if($last_login)
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success-light">
                            <x-icon name="arrow-right-on-rectangle" class="h-5 w-5 text-success" />
                        </div>
                        <div class="mt-2 h-full w-0.5 bg-gray-200"></div>
                    </div>
                    <div class="flex-1 pb-6">
                        <h3 class="font-semibold text-gray-900">Login Terakhir</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $last_login->format('d M Y, H:i') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $last_login->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Pengajuan Pertama --}}
                @if($pengajuan_pertama)
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-info-light">
                            <x-icon name="document-plus" class="h-5 w-5 text-info" />
                        </div>
                        <div class="mt-2 h-full w-0.5 bg-gray-200"></div>
                    </div>
                    <div class="flex-1 pb-6">
                        <h3 class="font-semibold text-gray-900">Pengajuan Pertama</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $pengajuan_pertama->format('d M Y, H:i') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $pengajuan_pertama->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Update Terakhir --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-light">
                            <x-icon name="pencil-square" class="h-5 w-5 text-warning" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">Update Terakhir</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $update_terakhir ? $update_terakhir->format('d M Y, H:i') : '-' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $update_terakhir ? $update_terakhir->diffForHumans() : '-' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="mt-6 grid gap-6 sm:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pengajuan</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $total_pengajuan }}</p>
                </div>
                <div class="rounded-xl bg-myunila-50 p-3">
                    <x-icon name="document-text" class="h-8 w-8 text-myunila" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pengajuan Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ $pengajuan_aktif }}</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="clock" class="h-8 w-8 text-info" />
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
