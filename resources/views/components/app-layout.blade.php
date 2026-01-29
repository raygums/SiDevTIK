<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Pengajuan Layanan Domain dan Hosting Universitas Lampung' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-slate-800 antialiased font-sans">

    <x-navbar />

    <main class="py-10 container mx-auto px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} UPT TIK Universitas Lampung
    </footer>

</body>
</html>
