<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DomainTIK') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-600">
    <div class="flex h-screen overflow-hidden">
        
        {{-- Content Area (70%) - Scrollable --}}
        <div class="flex-1 lg:w-[70%] overflow-y-auto bg-slate-50">
            <div class="flex items-center justify-center min-h-full px-4 py-8 sm:px-6 lg:px-12 xl:px-16">
                <div class="w-full max-w-5xl bg-white rounded-xl shadow-lg border border-slate-200 p-8 lg:p-10 my-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        {{-- Sidebar Image (30%) - Fixed & Centered --}}
        <div class="hidden lg:flex lg:w-[30%] relative bg-slate-900 overflow-hidden">
            <img src="{{ asset('images/unila.jpg') }}" class="absolute inset-0 w-full h-full object-cover opacity-60" alt="Gedung Rektorat Unila">
            
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 to-slate-800/80"></div>

            {{-- Content Always Centered --}}
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center p-8">
                    <div class="space-y-6">
                        <img src="{{ asset('images/logo-unila.png') }}" alt="Logo Unila" class="h-20 w-auto mx-auto drop-shadow-lg">
                        
                        <div class="space-y-3">
                            <h1 class="text-3xl font-bold text-white leading-tight">
                                DomainTIK
                            </h1>
                            <p class="text-base text-slate-200 max-w-xs mx-auto font-medium">
                                UPA TIK Universitas Lampung
                            </p>
                            <p class="text-sm text-slate-300 max-w-xs mx-auto">
                                Portal terintegrasi untuk pengelolaan infrastruktur web akademik.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>
