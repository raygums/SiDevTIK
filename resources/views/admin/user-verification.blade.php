@extends('layouts.dashboard')

@section('title', 'Verifikasi Akun Pengguna - Admin')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
            Verifikasi Akun Pengguna
        </h1>
        <p class="mt-2 text-gray-600">
            Kelola dan verifikasi status aktivasi akun pengguna dengan role <span class="font-semibold text-myunila">Pengguna</span>.
        </p>
        <p class="mt-1 text-sm text-gray-500">
            <svg class="inline h-4 w-4 text-info" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Role Admin, Verifikator, Eksekutor, dan Pimpinan hanya dapat dikelola oleh Pimpinan.
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
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total User</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="rounded-xl bg-myunila-50 p-3">
                    <x-icon name="users" class="h-8 w-8 text-myunila" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Menunggu Verifikasi</p>
                    <p class="mt-2 text-3xl font-bold text-warning">{{ number_format($stats['nonaktif']) }}</p>
                </div>
                <div class="rounded-xl bg-warning-light p-3">
                    <x-icon name="clock" class="h-8 w-8 text-warning" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sudah Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-success">{{ number_format($stats['aktif']) }}</p>
                </div>
                <div class="rounded-xl bg-success-light p-3">
                    <x-icon name="check-circle" class="h-8 w-8 text-success" />
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Akun SSO</p>
                    <p class="mt-2 text-3xl font-bold text-info">{{ number_format($stats['sso']) }}</p>
                </div>
                <div class="rounded-xl bg-info-light p-3">
                    <x-icon name="key" class="h-8 w-8 text-info" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.users.verification') }}" id="filterForm">
                
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
                            id="search"
                            name="search" 
                            value="{{ $filters['search'] ?? '' }}"
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
                                        @click="document.getElementById('status').value='all'; document.getElementById('tipe_akun').value='all'; document.getElementById('filterForm').submit();"
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
                                        id="status"
                                        name="status" 
                                        class="block w-full rounded-lg border-gray-300 py-2 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                                        <option value="aktif" {{ ($filters['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="tidak_aktif" {{ ($filters['status'] ?? '') === 'tidak_aktif' ? 'selected' : '' }}>Belum Aktif</option>
                                    </select>
                                </div>

                                {{-- Tipe Akun Filter --}}
                                <div>
                                    <label for="tipe_akun" class="block text-sm font-medium text-gray-700 mb-2">Tipe Akun</label>
                                    <select 
                                        id="tipe_akun"
                                        name="tipe_akun" 
                                        class="block w-full rounded-lg border-gray-300 py-2 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                                        <option value="all" {{ ($filters['tipe_akun'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                                        <option value="sso" {{ ($filters['tipe_akun'] ?? '') === 'sso' ? 'selected' : '' }}>SSO</option>
                                        <option value="lokal" {{ ($filters['tipe_akun'] ?? '') === 'lokal' ? 'selected' : '' }}>Lokal</option>
                                    </select>
                                </div>

                                {{-- Apply Button --}}
                                <div class="flex gap-2 pt-2 border-t border-gray-100 mt-4">
                                    <button 
                                        type="submit"
                                        class="flex-1 rounded-lg bg-myunila px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-dark focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Search Button --}}
                    <button 
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-dark focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 sm:w-auto">
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Actions --}}
    @if($users->where('a_aktif', false)->count() > 0)
    <div class="mb-4">
        <form method="POST" action="{{ route('admin.users.bulk-activate') }}" id="bulkActivateForm">
            @csrf
            <button 
                type="button"
                onclick="confirmBulkActivate()"
                class="inline-flex items-center gap-2 rounded-lg bg-success px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-success-dark">
                <x-icon name="check-badge" class="h-5 w-5" />
                Aktifkan Pengguna Terpilih
            </button>
        </form>
    </div>
    @endif

    {{-- User Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if($users->where('a_aktif', false)->count() > 0)
                        <th scope="col" class="px-6 py-3 text-left">
                            <input 
                                type="checkbox" 
                                id="selectAll"
                                class="h-4 w-4 rounded border-gray-300 text-myunila focus:ring-myunila"
                                onclick="toggleAllCheckboxes()">
                        </th>
                        @endif
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Pengguna
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Tipe
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Terdaftar
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($users as $user)
                    <tr class="transition hover:bg-gray-50">
                        @if(!$user->a_aktif)
                        <td class="px-6 py-4">
                            <input 
                                type="checkbox" 
                                name="user_uuids[]" 
                                value="{{ $user->UUID }}"
                                form="bulkActivateForm"
                                class="user-checkbox h-4 w-4 rounded border-gray-300 text-myunila focus:ring-myunila">
                        </td>
                        @elseif($users->where('a_aktif', false)->count() > 0)
                        <td class="px-6 py-4"></td>
                        @endif
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-sm font-bold text-myunila">
                                    {{ strtoupper(substr($user->nm, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->nm }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->usn }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-myunila-100 px-2.5 py-0.5 text-xs font-medium text-myunila">
                                {{ $user->peran->nm_peran ?? 'Pengguna' }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if($user->sso_id)
                                <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                    <x-icon name="key" class="h-4 w-4 text-info" />
                                    SSO
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                    <x-icon name="user" class="h-4 w-4 text-gray-400" />
                                    Lokal
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            @if($user->a_aktif)
                                <span class="inline-flex items-center gap-1 rounded-full bg-success-light px-2.5 py-0.5 text-xs font-medium text-success">
                                    <x-icon name="check-circle" class="h-4 w-4" />
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-warning-light px-2.5 py-0.5 text-xs font-medium text-warning">
                                    <x-icon name="clock" class="h-4 w-4" />
                                    Belum Aktif
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->create_at ? $user->create_at->format('d M Y H:i') : '-' }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            {{-- Toggle Status --}}
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user->UUID) }}" class="inline">
                                @csrf
                                <button 
                                    type="button"
                                    onclick="confirmToggleStatus(this.form, '{{ $user->nm }}', {{ $user->a_aktif ? 'true' : 'false' }})"
                                    class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium transition
                                           {{ $user->a_aktif 
                                               ? 'bg-red-50 text-red-600 hover:bg-red-100' 
                                               : 'bg-success-light text-success hover:bg-green-200' }}">
                                    @if($user->a_aktif)
                                        <x-icon name="x-circle" class="h-4 w-4" />
                                        Nonaktifkan
                                    @else
                                        <x-icon name="check-circle" class="h-4 w-4" />
                                        Aktifkan
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $users->where('a_aktif', false)->count() > 0 ? 7 : 6 }}" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <x-icon name="users" class="h-16 w-16 text-gray-300" />
                                <p class="mt-4 text-lg font-medium text-gray-900">Tidak ada user ditemukan</p>
                                <p class="mt-1 text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination with Items Per Page --}}
        @if($users->hasPages() || $users->total() > 10)
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Items Per Page Selector --}}
                <form method="GET" action="{{ route('admin.users.verification') }}" id="perPageForm" class="flex items-center gap-2">
                    {{-- Preserve all existing query params --}}
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page_users" class="text-sm text-gray-700">Tampilkan:</label>
                    <select name="per_page" 
                            id="per_page_users"
                            onchange="this.form.submit()"
                            class="rounded-md border-gray-300 py-1.5 pl-3 pr-8 text-sm focus:border-myunila focus:ring-myunila">
                        <option value="10" {{ (int)request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ (int)request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ (int)request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ (int)request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="text-sm text-gray-700">per halaman</span>
                </form>

                {{-- Pagination Links --}}
                <div class="flex-1 flex justify-end">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- JavaScript for Confirmations & Bulk Actions --}}
<script>
function confirmToggleStatus(form, userName, isActive) {
    const action = isActive ? 'menonaktifkan' : 'mengaktifkan';
    if (confirm(`Apakah Anda yakin ingin ${action} akun "${userName}"?`)) {
        form.submit();
    }
}

function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function confirmBulkActivate() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih minimal 1 user untuk diaktifkan.');
        return;
    }
    
    if (confirm(`Aktifkan ${checkboxes.length} user yang dipilih?`)) {
        document.getElementById('bulkActivateForm').submit();
    }
}
</script>
@endsection
