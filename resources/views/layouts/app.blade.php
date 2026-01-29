<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Domaintik') - Layanan Domain & Hosting TIK Unila</title>
    
    {{-- Favicon --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌐</text></svg>">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="min-h-screen bg-linear-to-br from-gray-50 to-myunila-50 font-sans antialiased">
    
    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 border-b border-myunila-100 bg-white/90 backdrop-blur-lg">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 transition hover:opacity-80">
                    <img src="{{ asset('images/logo-unila.png') }}" alt="Logo Unila" class="h-12 w-auto">
                    <div class="border-l border-gray-300 pl-3">
                        <span class="text-xl font-bold text-gray-900">Domain<span class="text-myunila">TIK</span></span>
                        <p class="text-xs text-gray-500">Universitas Lampung</p>
                    </div>
                </a>
                
                {{-- Navigation Links --}}
                <div class="hidden items-center gap-6 md:flex">
                    <a href="{{ url('/') }}" class="text-sm font-medium text-gray-600 transition hover:text-myunila">Beranda</a>
                    <a href="{{ url('/#layanan') }}" class="text-sm font-medium text-gray-600 transition hover:text-myunila">Layanan</a>
                    <a href="{{ url('/#alur') }}" class="text-sm font-medium text-gray-600 transition hover:text-myunila">Alur Pengajuan</a>
                </div>
                
                {{-- Auth Section --}}
                <div class="flex items-center gap-3">
                    @auth
                        {{-- Menu untuk user yang sudah login --}}
                        <a href="{{ route('dashboard') }}" class="hidden text-sm font-medium text-gray-600 transition hover:text-myunila sm:block">
                            Dashboard
                        </a>
                        <div class="hidden items-center gap-3 sm:flex">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->nm ?? Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->peran->nm_peran ?? 'Pengguna' }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-ocean text-sm font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->nm ?? Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-error">
                                Keluar
                            </button>
                        </form>
                    @else
                        {{-- Tombol Login SSO untuk guest --}}
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-unila px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-myunila/30 transition hover:shadow-xl hover:shadow-myunila/40">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login SSO
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mx-auto mt-4 max-w-7xl px-4">
            <div class="rounded-lg border border-success/30 bg-success-light p-4 text-success">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mx-auto mt-4 max-w-7xl px-4">
            <div class="rounded-lg border border-error/30 bg-error-light p-4 text-error">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif
    
    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>
    
    {{-- Footer --}}
    <footer class="mt-auto border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-6">
                {{-- Logos --}}
                <div class="flex items-center gap-6">
                    <img src="{{ asset('images/logo-unila.png') }}" alt="Logo Unila" class="h-12 w-auto">
                    <div class="h-12 w-px bg-gray-300"></div>
                    <img src="{{ asset('images/upatik.png') }}" alt="Logo UPA TIK" class="h-12 w-auto">
                </div>
                
                {{-- Content --}}
                <div class="text-center">
                    <p class="mb-2 text-sm font-semibold text-gray-900">UPA TIK Universitas Lampung</p>
                    <p class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} Sistem Layanan Domain & Hosting Universitas Lampung
                    </p>
                </div>
                
                {{-- Links --}}
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500">
                    <a href="https://tik.unila.ac.id" target="_blank" class="transition hover:text-myunila">tik.unila.ac.id</a>
                    <span>•</span>
                    <a href="mailto:helpdesk@tik.unila.ac.id" class="transition hover:text-myunila">helpdesk@tik.unila.ac.id</a>
                    <span>•</span>
                    <a href="https://unila.ac.id" target="_blank" class="transition hover:text-myunila">unila.ac.id</a>
                </div>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>
