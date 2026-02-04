<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - DomainTIK</title>
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/logo-unila.png') }}" type="image/png">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-slate-800 antialiased font-sans">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="hidden lg:block lg:w-64 lg:flex-shrink-0">
            <x-sidebar />
        </aside>

        {{-- Main Content Area --}}
        <div class="flex flex-1 flex-col overflow-hidden">
            
            {{-- Top Navigation Bar (Mobile) --}}
            <header class="border-b border-gray-200 bg-white lg:hidden">
                <div class="flex h-16 items-center justify-between px-4">
                    <div class="flex items-center gap-3">
                        <button 
                            type="button"
                            id="mobile-menu-toggle"
                            class="rounded-lg p-2 text-gray-600 hover:bg-gray-100">
                            <x-icon name="bars-3" class="h-6 w-6" />
                        </button>
                        <h1 class="text-lg font-bold text-myunila">DomainTIK</h1>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->nm }}</span>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto bg-gray-50">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div 
        id="mobile-sidebar-overlay" 
        class="fixed inset-0 z-40 hidden bg-gray-900/50 lg:hidden"
        onclick="toggleMobileSidebar()">
    </div>

    {{-- Mobile Sidebar --}}
    <aside 
        id="mobile-sidebar" 
        class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full transform transition-transform duration-300 ease-in-out lg:hidden">
        <x-sidebar />
    </aside>

    {{-- JavaScript for Mobile Menu --}}
    <script>
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');

        function toggleMobileSidebar() {
            mobileSidebar.classList.toggle('-translate-x-full');
            mobileSidebarOverlay.classList.toggle('hidden');
        }

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', toggleMobileSidebar);
        }
    </script>

</body>
</html>
