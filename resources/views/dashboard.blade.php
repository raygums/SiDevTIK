@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                Selamat Datang, {{ Auth::user()->nm ?? 'Pengguna' }}
            </h1>
            <p class="mt-2 text-gray-600">
                Kelola pengajuan domain dan hosting Anda dari dashboard ini.
            </p>
        </div>

        {{-- Inactive Account Alert (SSO-Gate) --}}
        @if(!Auth::user()->a_aktif)
        <div class="mb-8 overflow-hidden rounded-xl border border-warning bg-warning-light shadow-md">
            <div class="flex items-start gap-4 p-5">
                <div class="flex-shrink-0">
                    <svg class="h-7 w-7 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-warning-dark">Akun Belum Aktif</h3>
                    <p class="mt-1 text-sm text-gray-700">
                        Akun Anda sedang dalam proses verifikasi oleh Tim Verifikator. 
                        Seluruh fitur pengajuan (Sub-domain, Hosting, VPS) akan tersedia setelah akun Anda diaktifkan.
                    </p>
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Proses verifikasi biasanya memakan waktu 1-2 hari kerja.</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Buat Pengajuan --}}
            @if(Auth::user()->a_aktif)
            <a href="{{ route('submissions.create') }}" class="group relative overflow-hidden rounded-2xl border border-myunila-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-myunila/20">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-myunila-50 transition group-hover:bg-myunila-100"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-unila text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Buat Pengajuan</h3>
                    <p class="mt-1 text-sm text-gray-500">Ajukan domain atau hosting baru</p>
                </div>
            </a>
            @else
            <div class="group relative overflow-hidden rounded-2xl border border-gray-300 bg-gray-50 p-6 shadow-sm opacity-60 cursor-not-allowed">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gray-100"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gray-200 text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-500">Buat Pengajuan</h3>
                    <p class="mt-1 text-sm text-gray-400">Memerlukan aktivasi akun</p>
                </div>
            </div>
            @endif

            {{-- Daftar Pengajuan --}}
            @if(Auth::user()->a_aktif)
            <a href="{{ route('submissions.index') }}" class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gray-50 transition group-hover:bg-gray-100"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Daftar Pengajuan</h3>
                    <p class="mt-1 text-sm text-gray-500">Lihat semua pengajuan Anda</p>
                </div>
            </a>
            @else
            <div class="group relative overflow-hidden rounded-2xl border border-gray-300 bg-gray-50 p-6 shadow-sm opacity-60 cursor-not-allowed">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gray-100"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gray-200 text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-500">Daftar Pengajuan</h3>
                    <p class="mt-1 text-sm text-gray-400">Memerlukan aktivasi akun</p>
                </div>
            </div>
            @endif

            {{-- Status Pengajuan --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-warning-light"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-warning-light text-warning">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Dalam Proses</h3>
                    <p class="mt-1 text-2xl font-bold text-warning">{{ Auth::user()->a_aktif ? ($stats['dalam_proses'] ?? 0) : '-' }}</p>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-success-light"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-success-light text-success">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Selesai</h3>
                    <p class="mt-1 text-2xl font-bold text-success">{{ Auth::user()->a_aktif ? ($stats['selesai'] ?? 0) : '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Recent Submissions --}}
        @if(Auth::user()->a_aktif && isset($submissions) && $submissions->isNotEmpty())
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h2 class="font-semibold text-gray-900">Pengajuan Terbaru Anda</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($submissions as $submission)
                <a href="{{ route('submissions.show', $submission) }}" class="flex items-center justify-between p-4 hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-myunila-50 text-myunila">
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
                            <p class="text-sm text-gray-600">{{ $submission->rincian?->nm_domain ?? ucfirst($serviceType) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php $status = $submission->status?->nm_status ?? ''; @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($status === 'Selesai') bg-success-light text-success
                            @elseif(str_contains($status, 'Ditolak')) bg-danger-light text-danger
                            @elseif($status === 'Sedang Dikerjakan') bg-info-light text-info
                            @elseif(in_array($status, ['Diajukan', 'Disetujui Verifikator'])) bg-warning-light text-warning
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $submission->status?->nm_status ?? 'Draft' }}
                        </span>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-3">
                <a href="{{ route('submissions.index') }}" class="text-sm font-medium text-myunila hover:underline">
                    Lihat semua pengajuan →
                </a>
            </div>
        </div>
        @endif

        {{-- Info Card --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <h2 class="font-semibold text-gray-900">Informasi Akun</h2>
            </div>
            <div class="p-6">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-gray-900">{{ Auth::user()->nm ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Username / NIP</dt>
                        <dd class="mt-1 text-gray-900">{{ Auth::user()->usn ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-gray-900">{{ Auth::user()->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-myunila-100 px-3 py-1 text-sm font-medium text-myunila">
                                {{ Auth::user()->peran->nm_peran ?? 'Pengguna' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status Akun</dt>
                        <dd class="mt-1">
                            @if(Auth::user()->a_aktif)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-success-light px-3 py-1 text-sm font-medium text-success">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-warning-light px-3 py-1 text-sm font-medium text-warning">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Menunggu Verifikasi
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Login Terakhir</dt>
                        <dd class="mt-1 text-gray-900">
                            {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

    </div>
</div>
@endsection
