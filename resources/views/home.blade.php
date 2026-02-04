@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden py-16 lg:py-24">
        {{-- Background Decoration --}}
        <div class="absolute inset-0 -z-10">
            <div class="absolute -left-4 top-20 h-72 w-72 rounded-full bg-myunila/20 blur-3xl"></div>
            <div class="absolute -right-4 bottom-20 h-72 w-72 rounded-full bg-myunila-600/20 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-myunila-300/10 blur-3xl"></div>
        </div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                {{-- Logo --}}
                <div class="mb-8 flex items-center justify-center gap-4">
                    <img src="{{ asset('images/logo-unila.png') }}" alt="Logo Unila" class="h-16 w-auto sm:h-20">
                    <div class="h-16 w-px bg-gray-300 sm:h-20"></div>
                    <img src="{{ asset('images/upatik.png') }}" alt="Logo UPA TIK" class="h-16 w-auto sm:h-20">
                </div>
                
                {{-- Title --}}
                <h1 class="mb-4 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                    Selamat Datang di
                    <span class="block text-myunila">
                        DomainTIK
                    </span>
                </h1>
                
                {{-- Badge --}}
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-myunila-200 bg-myunila-50 px-4 py-2 text-sm font-medium text-myunila">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-myunila-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-myunila"></span>
                    </span>
                    Sistem Layanan Digital UPA TIK Universitas Lampung
                </div>
                
                {{-- Description --}}
                <p class="mx-auto mb-8 max-w-2xl text-base text-gray-600 sm:text-lg">
                    Platform digital untuk pengajuan layanan <strong>Domain (.unila.ac.id)</strong> dan 
                    <strong>Hosting</strong> bagi seluruh unit kerja di lingkungan Universitas Lampung.
                </p>
                
                {{-- CTA Buttons --}}
                <div class="mb-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @auth
                        {{-- Jika sudah login, tampilkan tombol ke dashboard --}}
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-unila px-8 py-4 text-base font-semibold text-white shadow-lg shadow-myunila/30 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-myunila/40">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Buka Dashboard
                        </a>
                        <a href="{{ route('submissions.create') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-myunila bg-white px-8 py-4 text-base font-semibold text-myunila transition hover:-translate-y-0.5 hover:bg-myunila-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Buat Pengajuan Baru
                        </a>
                    @else
                        {{-- Jika belum login, tampilkan tombol Login SSO --}}
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-3 rounded-xl bg-gradient-unila px-8 py-4 text-base font-semibold text-white shadow-lg shadow-myunila/30 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-myunila/40">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login dengan SSO Unila
                        </a>
                        <a href="#layanan" class="inline-flex items-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-8 py-4 text-base font-semibold text-gray-700 transition hover:-translate-y-0.5 hover:border-myunila hover:text-myunila">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Lihat Layanan
                        </a>
                    @endauth
                </div>
                
                {{-- Quick Stats --}}
                <div class="flex flex-wrap items-center justify-center gap-6 text-center sm:gap-8">
                    <div class="rounded-lg bg-white/80 px-6 py-4 shadow-sm backdrop-blur">
                        <p class="text-3xl font-bold text-myunila">100+</p>
                        <p class="text-sm text-gray-600">Domain Aktif</p>
                    </div>
                    <div class="rounded-lg bg-white/80 px-6 py-4 shadow-sm backdrop-blur">
                        <p class="text-3xl font-bold text-myunila-600">50+</p>
                        <p class="text-sm text-gray-600">Hosting Aktif</p>
                    </div>
                    <div class="rounded-lg bg-white/80 px-6 py-4 shadow-sm backdrop-blur">
                        <p class="text-3xl font-bold text-info">20+</p>
                        <p class="text-sm text-gray-600">VPS Aktif</p>
                    </div>
                    <div class="rounded-lg bg-white/80 px-6 py-4 shadow-sm backdrop-blur">
                        <p class="text-3xl font-bold text-success">8</p>
                        <p class="text-sm text-gray-600">Fakultas Terlayani</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    {{-- Layanan Section --}}
    <section id="layanan" class="py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">Pilih Layanan</h2>
                <p class="mx-auto max-w-2xl text-gray-600">
                    Silakan pilih jenis layanan yang ingin Anda ajukan. Pastikan Anda sudah login dengan akun SSO Unila.
                </p>
            </div>
            
            {{-- Service Cards --}}
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                {{-- Card: Domain --}}
                <div class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-lg shadow-gray-200/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-myunila/20 lg:p-8">
                    {{-- Gradient Background on Hover --}}
                    <div class="absolute inset-0 -z-10 bg-linear-to-br from-myunila-50 to-myunila-100 opacity-0 transition-opacity group-hover:opacity-100"></div>
                    
                    {{-- Icon --}}
                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-unila text-white shadow-lg shadow-myunila/30">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    
                    {{-- Content --}}
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Pengajuan Domain</h3>
                    <p class="mb-4 text-sm text-gray-600">
                        Ajukan subdomain <code class="rounded bg-myunila-100 px-1.5 py-0.5 font-mono text-xs text-myunila">*.unila.ac.id</code> 
                        untuk website unit kerja atau kegiatan resmi.
                    </p>
                    
                    {{-- Features --}}
                    <ul class="mb-6 space-y-2">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Subdomain resmi .unila.ac.id
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Konfigurasi DNS fleksibel
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Pointing ke server eksternal
                        </li>
                    </ul>
                    
                    {{-- CTA Button --}}
                    @auth
                        <a href="{{ route('submissions.create', ['type' => 'domain']) }}" class="btn-primary inline-flex w-full items-center justify-center gap-2 text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajukan Domain
                        </a>
                    @else
                        <button onclick="alert('Silakan login terlebih dahulu untuk mengajukan layanan.')" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-500 transition-all hover:bg-gray-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Login untuk Mengajukan
                        </button>
                    @endauth
                </div>
                
                {{-- Card: Hosting --}}
                <div class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-lg shadow-gray-200/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-myunila/20 lg:p-8">
                    {{-- Gradient Background on Hover --}}
                    <div class="absolute inset-0 -z-10 bg-linear-to-br from-myunila-50 to-myunila-100 opacity-0 transition-opacity group-hover:opacity-100"></div>
                    
                    {{-- Icon --}}
                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-ocean text-white shadow-lg shadow-myunila/30">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                    </div>
                    
                    {{-- Content --}}
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Pengajuan Hosting</h3>
                    <p class="mb-4 text-sm text-gray-600">
                        Ajukan akun hosting (cPanel) untuk website atau sistem informasi unit kerja.
                    </p>
                    
                    {{-- Features --}}
                    <ul class="mb-6 space-y-2">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            cPanel dengan kontrol penuh
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            PHP, MySQL, SSL Certificate
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Kuota sesuai kebutuhan
                        </li>
                    </ul>
                    
                    {{-- CTA Button --}}
                    @auth
                        <a href="{{ route('submissions.create', ['type' => 'hosting']) }}" class="btn-primary inline-flex w-full items-center justify-center gap-2 text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajukan Hosting
                        </a>
                    @else
                        <button onclick="alert('Silakan login terlebih dahulu untuk mengajukan layanan.')" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-500 transition-all hover:bg-gray-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Login untuk Mengajukan
                        </button>
                    @endauth
                </div>
                
                {{-- Card: VPS --}}
                <div class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-lg shadow-gray-200/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-info/20 lg:p-8">
                    {{-- Gradient Background on Hover --}}
                    <div class="absolute inset-0 -z-10 bg-linear-to-br from-info-light to-info/20 opacity-0 transition-opacity group-hover:opacity-100"></div>
                    
                    {{-- Badge New --}}
                    <div class="absolute right-4 top-4">
                        <span class="inline-flex items-center rounded-full bg-info px-2.5 py-0.5 text-xs font-semibold text-white">
                            Baru
                        </span>
                    </div>
                    
                    {{-- Icon --}}
                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-info text-white shadow-lg shadow-info/30">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    
                    {{-- Content --}}
                    <h3 class="mb-2 text-xl font-bold text-gray-900">Pengajuan VPS</h3>
                    <p class="mb-4 text-sm text-gray-600">
                        Ajukan Virtual Private Server untuk aplikasi yang membutuhkan resource dedicated.
                    </p>
                    
                    {{-- Features --}}
                    <ul class="mb-6 space-y-2">
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Root access penuh
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            CPU, RAM, Storage dedicated
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="h-4 w-4 shrink-0 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            OS pilihan (Ubuntu, CentOS)
                        </li>
                    </ul>
                    
                    {{-- CTA Button --}}
                    @auth
                        <a href="{{ route('submissions.create', ['type' => 'vps']) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-info px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-info/30 transition-all hover:bg-info/90">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajukan VPS
                        </a>
                    @else
                        <button onclick="alert('Silakan login terlebih dahulu untuk mengajukan layanan.')" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-500 transition-all hover:bg-gray-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Login untuk Mengajukan
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </section>
    
    {{-- Alur Pengajuan Section --}}
    <section id="alur" class="bg-white py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">Alur Pengajuan</h2>
                <p class="mx-auto max-w-2xl text-gray-600">
                    Proses pengajuan layanan yang mudah dan terstruktur. Sistem hybrid antara digital dan dokumen fisik.
                </p>
            </div>
            
            {{-- Steps with connected line --}}
            <div class="relative">
                {{-- Horizontal Connector Line (behind circles) --}}
                <div class="absolute left-0 right-0 top-7 hidden h-1 md:block">
                    <div class="mx-auto flex max-w-3xl">
                        <div class="h-full w-1/4 bg-myunila"></div>
                        <div class="h-full w-1/4 bg-myunila-600"></div>
                        <div class="h-full w-1/4 bg-myunila-700"></div>
                        <div class="h-full w-1/4 bg-success"></div>
                    </div>
                </div>
                
                <div class="grid gap-8 md:grid-cols-4">
                    {{-- Step 1 --}}
                    <div class="relative text-center">
                        <div class="relative z-10 mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-unila text-xl font-bold text-white shadow-lg shadow-myunila/30 ring-4 ring-white">
                            1
                        </div>
                        <h3 class="mb-2 font-bold text-gray-900">Isi Formulir Online</h3>
                        <p class="text-sm text-gray-600">Lengkapi data pemohon, unit kerja, atasan, dan detail teknis layanan yang dibutuhkan.</p>
                    </div>
                    
                    {{-- Step 2 --}}
                    <div class="relative text-center">
                        <div class="relative z-10 mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-myunila-600 text-xl font-bold text-white shadow-lg shadow-myunila/30 ring-4 ring-white">
                            2
                        </div>
                        <h3 class="mb-2 font-bold text-gray-900">Cetak & Tanda Tangan</h3>
                        <p class="text-sm text-gray-600">Download PDF formulir, cetak, minta tanda tangan atasan (basah), lalu scan ulang.</p>
                    </div>
                    
                    {{-- Step 3 --}}
                    <div class="relative text-center">
                        <div class="relative z-10 mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-myunila-700 text-xl font-bold text-white shadow-lg shadow-myunila/30 ring-4 ring-white">
                            3
                        </div>
                        <h3 class="mb-2 font-bold text-gray-900">Upload & Submit</h3>
                        <p class="text-sm text-gray-600">Upload scan formulir bertanda tangan beserta foto/scan identitas (KTM/Karpeg).</p>
                    </div>
                    
                    {{-- Step 4 --}}
                    <div class="text-center">
                        <div class="relative z-10 mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-success text-xl font-bold text-white shadow-lg shadow-success/30 ring-4 ring-white">
                            4
                        </div>
                        <h3 class="mb-2 font-bold text-gray-900">Layanan Aktif</h3>
                        <p class="text-sm text-gray-600">Tim TIK memverifikasi dan memproses. Anda akan menerima notifikasi saat layanan aktif.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    {{-- Info Box --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-gradient-unila p-8 text-white shadow-2xl lg:p-12">
                <div class="flex flex-col items-center gap-8 lg:flex-row lg:justify-between">
                    <div class="text-center lg:text-left">
                        <h3 class="mb-2 text-2xl font-bold lg:text-3xl">Butuh Bantuan?</h3>
                        <p class="text-myunila-100">
                            Hubungi tim Helpdesk TIK Unila untuk pertanyaan atau kendala teknis.
                        </p>
                    </div>
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <a href="mailto:helpdesk@tik.unila.ac.id" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 font-semibold text-myunila shadow-lg transition hover:bg-myunila-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Email Helpdesk
                        </a>
                        <a href="https://tik.unila.ac.id" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-white/30 bg-white/10 px-6 py-3 font-semibold text-white backdrop-blur transition hover:bg-white/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Website TIK
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
