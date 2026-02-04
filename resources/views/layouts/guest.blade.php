<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Login' }} - DomainTIK</title>
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/logo-unila.png') }}" type="image/png">
    
    {{-- Fonts - Instrument Sans from Bunny Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-myunila-50/30 to-gray-50 font-sans antialiased">
    
    <div class="flex min-h-screen">
        {{-- Left Side - Branding & Hero Section --}}
        <div class="hidden w-1/2 flex-col justify-between bg-gradient-to-br from-myunila via-myunila-600 to-myunila-800 p-12 lg:flex">
            {{-- Logo Header --}}
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-kemendikbud.png') }}" alt="Kemendikbud" class="h-14 w-auto" onerror="this.style.display='none'">
                <img src="{{ asset('images/logo-blu.png') }}" alt="BLU" class="h-14 w-auto" onerror="this.style.display='none'">
                <img src="{{ asset('images/logo-unila.png') }}" alt="Unila" class="h-14 w-auto" onerror="this.style.display='none'">
            </div>

            {{-- Hero Content --}}
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/be-strong.png') }}" alt="Be Strong" class="h-24 w-auto" onerror="this.style.display='none'">
                    <div class="h-16 w-px bg-white/30"></div>
                    <img src="{{ asset('images/logo-unila-text.png') }}" alt="Universitas Lampung" class="h-12 w-auto" onerror="this.style.display='none'">
                </div>

                <h1 class="text-4xl font-bold leading-tight text-white">
                    DOMAINTIK
                </h1>
                
                <p class="text-lg text-myunila-100">
                    Sistem Layanan Domain & Hosting
                </p>

                <p class="text-base text-myunila-200">
                    Portal terintegrasi Universitas Lampung untuk pengelolaan infrastruktur web akademik
                </p>

                <p class="text-sm text-myunila-200">
                    Berintegritas Anti Korupsi
                </p>
            </div>

            {{-- Footer Tagline --}}
            <div class="text-sm text-myunila-100">
                © {{ date('Y') }} Universitas Lampung. All rights reserved.
            </div>
        </div>

        {{-- Right Side - Login Form --}}
        <div class="flex w-full items-center justify-center p-6 lg:w-1/2 lg:p-12">
            <div class="w-full max-w-md">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-success/30 bg-success-light p-4 text-sm text-success">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-error/30 bg-error-light p-4 text-sm text-error">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-info/30 bg-info-light p-4 text-sm text-info">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Main Content Slot --}}
                {{ $slot }}
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
