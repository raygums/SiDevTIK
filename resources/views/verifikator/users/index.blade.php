<x-app-layout>
    <x-slot name="title">Manajemen User - Verifikator</x-slot>

    <div class="mx-auto max-w-7xl">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                Manajemen User
            </h1>
            <p class="mt-2 text-gray-600">
                Verifikasi dan kelola status aktivasi akun pengguna sistem.
            </p>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="mb-6">
            <x-alert type="success" :message="session('success')" />
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6">
            <x-alert type="error" :message="session('error')" />
        </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total User</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="rounded-xl bg-myunila-50 p-3">
                        <svg class="h-8 w-8 text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Belum Aktif</p>
                        <p class="mt-2 text-3xl font-bold text-warning">{{ $stats['tidak_aktif'] }}</p>
                    </div>
                    <div class="rounded-xl bg-warning-light p-3">
                        <svg class="h-8 w-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sudah Aktif</p>
                        <p class="mt-2 text-3xl font-bold text-success">{{ $stats['aktif'] }}</p>
                    </div>
                    <div class="rounded-xl bg-success-light p-3">
                        <svg class="h-8 w-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Akun SSO</p>
                        <p class="mt-2 text-3xl font-bold text-info">{{ $stats['sso'] }}</p>
                    </div>
                    <div class="rounded-xl bg-info-light p-3">
                        <svg class="h-8 w-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="p-6">
                <form method="GET" action="{{ route('verifikator.users.index') }}" id="filterFormVerifikator">
                    
                    {{-- Search Bar with Filter Button --}}
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                id="search" 
                                value="{{ $filters['search'] }}"
                                placeholder="Cari nama, username, atau email..."
                                class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                        </div>
                        
                        {{-- Filter Button --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button 
                                type="button"
                                @click="open = !open"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span>Filter</span>
                            </button>

                            {{-- Filter Dropdown --}}
                            <div 
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-lg border border-gray-200 bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                                style="display: none;">
                                
                                <div class="border-b border-gray-200 px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900">Filter</h3>
                                        <button 
                                            type="button"
                                            @click="document.getElementById('status').value='all'; document.getElementById('tipe_akun').value='all'; document.getElementById('identity').value='all'; document.getElementById('filterFormVerifikator').submit();"
                                            class="text-xs font-medium text-red-600 hover:text-red-700">
                                            Reset
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4 space-y-4">
                                    {{-- Status Filter --}}
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select 
                                            name="status" 
                                            id="status"
                                            class="block w-full rounded-lg border-gray-300 py-2 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                            <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>Semua</option>
                                            <option value="tidak_aktif" {{ $filters['status'] === 'tidak_aktif' ? 'selected' : '' }}>Belum Aktif</option>
                                            <option value="aktif" {{ $filters['status'] === 'aktif' ? 'selected' : '' }}>Sudah Aktif</option>
                                        </select>
                                    </div>

                                    {{-- Tipe Akun Filter --}}
                                    <div>
                                        <label for="tipe_akun" class="block text-sm font-medium text-gray-700 mb-2">Tipe Akun</label>
                                        <select 
                                            name="tipe_akun" 
                                            id="tipe_akun"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                            <option value="all" {{ $filters['tipe_akun'] === 'all' ? 'selected' : '' }}>Semua</option>
                                            <option value="sso" {{ $filters['tipe_akun'] === 'sso' ? 'selected' : '' }}>Akun SSO</option>
                                            <option value="lokal" {{ $filters['tipe_akun'] === 'lokal' ? 'selected' : '' }}>Akun Lokal</option>
                                        </select>
                                    </div>

                                    {{-- Identity Filter --}}
                                    <div>
                                        <label for="identity" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                                        <select 
                                            name="identity" 
                                            id="identity"
                                            class="block w-full rounded-lg border-gray-300 py-2 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                            <option value="all" {{ $filters['identity'] === 'all' ? 'selected' : '' }}>Semua</option>
                                            <option value="mahasiswa" {{ $filters['identity'] === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                            <option value="dosen_tendik" {{ $filters['identity'] === 'dosen_tendik' ? 'selected' : '' }}>Dosen/Tendik</option>
                                        </select>
                                    </div>

                                    {{-- Apply Button --}}
                                    <div class="flex gap-2 pt-2 border-t border-gray-100 mt-4">
                                        <button 
                                            type="submit"
                                            class="flex-1 rounded-lg bg-myunila px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                                            Terapkan Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Search Button --}}
                        <button 
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                            <span>Cari</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- User Table --}}
        @if($users->isNotEmpty())
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <h2 class="font-semibold text-gray-900">Daftar User ({{ $users->total() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                Tipe Akun
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                Kategori
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                Peran
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($users as $user)
                        <tr class="transition hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-myunila">
                                        <span class="text-sm font-semibold">{{ substr($user->nm, 0, 2) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->nm }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->usn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($user->sso_id)
                                <span class="inline-flex items-center gap-1 rounded-full bg-info-light px-2.5 py-0.5 text-xs font-medium text-info">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                    SSO
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Lokal
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($user->id_pd)
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800">
                                    Mahasiswa
                                </span>
                                @elseif($user->id_sdm)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                    Dosen/Tendik
                                </span>
                                @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                    Umum
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                {{ $user->peran->nm_peran ?? 'Pengguna' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if($user->a_aktif)
                                <span class="inline-flex items-center gap-1 rounded-full bg-success-light px-2.5 py-0.5 text-xs font-medium text-success">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-warning-light px-2.5 py-0.5 text-xs font-medium text-warning">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Belum Aktif
                                </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <form method="POST" action="{{ route('verifikator.users.toggle', $user->UUID) }}" class="inline">
                                    @csrf
                                    <button 
                                        type="submit"
                                        onclick="return confirm('Apakah Anda yakin ingin {{ $user->a_aktif ? 'menonaktifkan' : 'mengaktifkan' }} user ini?')"
                                        class="inline-flex items-center gap-1 rounded-lg {{ $user->a_aktif ? 'bg-warning text-white hover:bg-warning-dark' : 'bg-success text-white hover:bg-green-600' }} px-3 py-1.5 text-xs font-semibold shadow-sm transition"
                                    >
                                        @if($user->a_aktif)
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Nonaktifkan
                                        @else
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Aktifkan
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>
        @else
        {{-- Empty State --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-gray-900">Tidak Ada Data User</h3>
            <p class="mb-6 text-sm text-gray-500">
                Tidak ditemukan user yang sesuai dengan filter yang Anda pilih.
            </p>
            <a 
                href="{{ route('verifikator.users.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-myunila px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-600"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset Filter
            </a>
        </div>
        @endif

    </div>

</x-app-layout>
