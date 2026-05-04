@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                Selamat Datang, {{ Auth::user()->nm ?? 'Pengguna' }}! 👋
            </h1>
            <p class="mt-2 text-gray-600">
                Kelola pengajuan domain dan hosting Anda dari dashboard ini.
            </p>
        </div>

        {{-- Quick Actions --}}
        <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Buat Pengajuan --}}
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

            {{-- Daftar Pengajuan --}}
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
                    <p class="mt-1 text-2xl font-bold text-warning">0</p>
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
                    <p class="mt-1 text-2xl font-bold text-success">0</p>
                </div>
            </div>
        </div>

        {{-- Admin Panel Link (hanya admin) --}}
        @if(Auth::user()->peran && str_contains(strtolower(Auth::user()->peran->nm_peran), 'admin'))
        <div class="mb-8">
            <a href="{{ route('admin.users.index') }}"
                class="group flex items-center justify-between rounded-2xl border border-purple-200 bg-gradient-to-r from-purple-50 to-indigo-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white shadow">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Panel Admin — Manajemen Pengguna</p>
                        <p class="text-sm text-gray-500">Kelola akun, role, dan import unit/domain dari CSV</p>
                    </div>
                </div>
                <svg class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
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
                </dl>
            </div>
        </div>

    </div>
</div>
@endsection
