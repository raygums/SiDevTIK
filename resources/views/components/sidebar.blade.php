@props(['active' => null])

@php
    $user = Auth::user();
    $role = $user->peran->nm_peran ?? 'Pengguna';
    
    // Tentukan menu berdasarkan role
    $menus = [];
    
    // Menu untuk Admin
    if (str_contains(strtolower($role), 'admin')) {
        $menus = [
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
            ],
            [
                'title' => 'Manajemen Pengguna',
                'route' => 'admin.users.verification',
                'icon' => 'user-check',
            ],
            [
                'title' => 'Log Aktivitas Login',
                'route' => 'admin.audit.login',
                'icon' => 'clock',
            ],
            [
                'title' => 'Log Status Pengajuan',
                'route' => 'admin.audit.submissions',
                'icon' => 'document-text',
            ],
        ];
    }
    
    // Menu untuk Verifikator
    elseif (strtolower($role) === 'verifikator') {
        $menus = [
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
            ],
            [
                'title' => 'Daftar Pengajuan',
                'route' => 'verifikator.index',
                'icon' => 'clipboard-list',
            ],
            [
                'title' => 'Verifikasi Permohonan',
                'route' => null, // Placeholder - not yet implemented
                'icon' => 'check-circle',
            ],
            [
                'title' => 'Log Aktivitas Verifikasi',
                'route' => null, // Placeholder - not yet implemented
                'icon' => 'document-text',
            ],
        ];
    }
    
    // Menu untuk Eksekutor
    elseif (strtolower($role) === 'eksekutor') {
        $menus = [
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
            ],
            [
                'title' => 'Daftar Tugas',
                'route' => 'eksekutor.index',
                'icon' => 'clipboard-list',
            ],
            [
                'title' => 'Update Status Selesai',
                'route' => null, // Placeholder - not yet implemented
                'icon' => 'check-badge',
            ],
            [
                'title' => 'Log Perubahan Status',
                'route' => null, // Placeholder - not yet implemented
                'icon' => 'document-text',
            ],
        ];
    }
    
    // Menu untuk Pengguna biasa
    else {
        $menus = [
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
            ],
            [
                'title' => 'Buat Pengajuan',
                'route' => 'submissions.create',
                'icon' => 'plus-circle',
            ],
            [
                'title' => 'Daftar Pengajuan',
                'route' => 'submissions.index',
                'icon' => 'clipboard-list',
            ],
            [
                'title' => 'Profil Saya',
                'route' => null, // Placeholder - not yet implemented
                'icon' => 'user',
            ],
        ];
    }
    
    // Helper untuk cek active
    $isActive = fn($route) => request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<div {{ $attributes->merge(['class' => 'flex h-screen flex-col bg-white border-r border-gray-200']) }}>
    {{-- Logo & Brand --}}
    <div class="flex h-16 items-center border-b border-gray-200 px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-3 transition hover:opacity-80">
            {{-- Logo Placeholder - Ganti dengan logo actual --}}
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-unila shadow-md">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-myunila">DOMAINTIK</h1>
                <p class="text-xs text-gray-500">UPT TIK Unila</p>
            </div>
        </a>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <div class="space-y-1">
            @foreach($menus as $menu)
                @php
                    $routeExists = $menu['route'] && \Illuminate\Support\Facades\Route::has($menu['route']);
                    $active = $menu['route'] ? $isActive($menu['route']) : false;
                @endphp
                
                @if($routeExists)
                    <a href="{{ route($menu['route']) }}" 
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                              {{ $active 
                                  ? 'bg-myunila-50 text-myunila' 
                                  : 'text-gray-700 hover:bg-gray-50 hover:text-myunila' }}">
                        <x-icon :name="$menu['icon']" class="h-5 w-5 flex-shrink-0 
                            {{ $active ? 'text-myunila' : 'text-gray-400 group-hover:text-myunila' }}" />
                        <span>{{ $menu['title'] }}</span>
                    </a>
                @else
                    {{-- Placeholder untuk route yang belum dibuat --}}
                    <div class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-400 cursor-not-allowed opacity-50">
                        <x-icon :name="$menu['icon']" class="h-5 w-5 flex-shrink-0" />
                        <span>{{ $menu['title'] }}</span>
                        <span class="ml-auto text-xs bg-gray-100 px-2 py-0.5 rounded-full">Soon</span>
                    </div>
                @endif
            @endforeach
        </div>
    </nav>

    {{-- User Profile & Logout --}}
    <div class="border-t border-gray-200 p-4">
        <div class="mb-3 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-myunila-100 text-myunila">
                <span class="text-sm font-bold">{{ substr($user->nm, 0, 2) }}</span>
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="truncate text-sm font-medium text-gray-900">{{ $user->nm }}</p>
                <p class="truncate text-xs text-gray-500">{{ $role }}</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">
                <x-icon name="logout" class="h-5 w-5" />
                <span>Keluar</span>
            </button>
        </form>
    </div>
</div>
