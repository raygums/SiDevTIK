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
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3.5">
            <h2 class="text-sm font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.users.verification') }}">
                
                {{-- Search Bar --}}
                <div class="mb-5">
                    <label for="search" class="mb-2 block text-sm font-medium text-gray-700">Pencarian</label>
                    <div class="flex gap-3">
                        <input 
                            type="text" 
                            id="search"
                            name="search" 
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="  Cari nama, username, atau email..."
                            class="block flex-1 rounded-lg border-gray-300 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm">
                        
                        <button 
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-myunila px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-dark focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                            <x-icon name="magnifying-glass" class="h-5 w-5" />
                            Cari
                        </button>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="my-5 border-t border-gray-200"></div>

                {{-- Filter Row --}}
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Filter: Tipe Akun --}}
                    <div>
                        <label for="tipe_akun" class="mb-2 block text-sm font-medium text-gray-700">Tipe Akun</label>
                        <select 
                            id="tipe_akun"
                            name="tipe_akun" 
                            class="block w-full rounded-lg border-gray-300 py-2.5 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm"
                            onchange="this.form.submit()">
                            <option value="all" {{ ($filters['tipe_akun'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                            <option value="sso" {{ ($filters['tipe_akun'] ?? '') === 'sso' ? 'selected' : '' }}>SSO</option>
                            <option value="lokal" {{ ($filters['tipe_akun'] ?? '') === 'lokal' ? 'selected' : '' }}>Lokal</option>
                        </select>
                    </div>

                    {{-- Filter: Status --}}
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-700">Status Aktivasi</label>
                        <select 
                            id="status"
                            name="status" 
                            class="block w-full rounded-lg border-gray-300 py-2.5 shadow-sm transition focus:border-myunila focus:ring-myunila sm:text-sm"
                            onchange="this.form.submit()">
                            <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif" {{ ($filters['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ ($filters['status'] ?? '') === 'tidak_aktif' ? 'selected' : '' }}>Belum Aktif</option>
                        </select>
                    </div>

                    {{-- Reset Filter --}}
                    <div class="flex items-end">
                        <a 
                            href="{{ route('admin.users.verification') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border-2 border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50">
                            <x-icon name="x-mark" class="h-5 w-5" />
                            Reset Filter
                        </a>
                    </div>
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
                            <div class="flex items-center justify-center gap-2">
                                {{-- Toggle Status --}}
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user->UUID) }}" class="inline">
                                    @csrf
                                    <button 
                                        type="button"
                                        onclick="confirmToggleStatus(this.form, '{{ $user->nm }}', {{ $user->a_aktif ? 'true' : 'false' }})"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium transition
                                               {{ $user->a_aktif 
                                                   ? 'bg-red-50 text-red-600 hover:bg-red-100' 
                                                   : 'bg-success-light text-success hover:bg-success' }}">
                                        @if($user->a_aktif)
                                            <x-icon name="x-circle" class="h-4 w-4" />
                                            Nonaktifkan
                                        @else
                                            <x-icon name="check-circle" class="h-4 w-4" />
                                            Aktifkan
                                        @endif
                                    </button>
                                </form>

                                {{-- View Logs --}}
                                <a 
                                    href="{{ route('admin.users.logs', $user->UUID) }}"
                                    class="inline-flex items-center gap-1 rounded-lg bg-gray-50 px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100"
                                    title="Lihat Audit Log">
                                    <x-icon name="document-text" class="h-4 w-4" />
                                    Log
                                </a>
                            </div>
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

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $users->links() }}
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
