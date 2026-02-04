<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - DomainTIK</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/logo-unila.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-600">
    <div class="min-h-screen flex">
        
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 overflow-hidden">
            <img src="{{ asset('images/unila.jpg') }}" class="absolute inset-0 w-full h-full object-cover opacity-60" alt="Gedung Rektorat Unila">
            
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 to-slate-800/80"></div>

            <div class="relative z-10 w-full flex flex-col justify-center items-center text-center p-16">
                <div class="space-y-8">
                    <img src="{{ asset('images/be-strong.png') }}" alt="Logo" class="h-14 w-auto mx-auto drop-shadow-lg">
                    
                    <div class="space-y-4">
                        <h1 class="text-5xl font-bold text-white leading-tight">
                            Sistem Layanan<br>
                            Domain & Hosting
                        </h1>
                        <p class="text-base text-slate-300 max-w-md mx-auto">
                            Portal terintegrasi Universitas Lampung untuk pengelolaan infrastruktur web akademik.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col justify-center px-4 py-12 sm:px-6 lg:px-20 xl:px-24 bg-slate-50">
            <div class="mx-auto w-full max-w-sm lg:w-96 bg-white rounded-xl shadow-lg border border-slate-200 p-8">
                {{ $slot }}
            </div>
        </div>
        
    </div>
</body>
</html>