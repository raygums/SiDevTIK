@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna - Pimpinan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('pimpinan.dashboard') }}" class="text-gray-400 transition hover:text-gray-600">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Manajemen Pengguna</h1>
                <p class="mt-1 text-gray-600">Kelola semua pengguna sistem dengan role berbeda.</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
            <p class="text-sm text-gray-500">Total</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-2xl font-bold text-info">{{ number_format($stats['by_role']['pengguna']) }}</p>
            <p class="text-sm text-gray-500">Pengguna</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-2xl font-bold text-warning">{{ number_format($stats['by_role']['verifikator']) }}</p>
            <p class="text-sm text-gray-500">Verifikator</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-2xl font-bold text-success">{{ number_format($stats['by_role']['eksekutor']) }}</p>
            <p class="text-sm text-gray-500">Eksekutor</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['by_role']['admin']) }}</p>
            <p class="text-sm text-gray-500">Admin</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-2xl font-bold text-danger">{{ number_format($stats['nonaktif']) }}</p>
            <p class="text-sm text-gray-500">Non-Aktif</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 rounded-xl border border-success/30 bg-success-light p-4">
        <div class="flex items-center gap-3">
            <x-icon name="check-circle" class="h-5 w-5 text-success" />
            <p class="text-sm font-medium text-success">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 rounded-xl border border-danger/30 bg-danger-light p-4">
        <div class="flex items-center gap-3">
            <x-icon name="x-circle" class="h-5 w-5 text-danger" />
            <p class="text-sm font-medium text-danger">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Filter & Search --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Filter & Pencarian</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('pimpinan.users') }}" id="filterForm">
                <div class="grid gap-4 md:grid-cols-5">
                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Cari Pengguna</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="Nama, email, username, NIP..."
                               class="form-input w-full">
                    </div>

                    {{-- Role Filter --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="form-select w-full" onchange="this.form.submit()">
                            <option value="all" {{ ($filters['role'] ?? '') === 'all' ? 'selected' : '' }}>Semua Role</option>
                            <option value="Pengguna" {{ ($filters['role'] ?? '') === 'Pengguna' ? 'selected' : '' }}>Pengguna</option>
                            <option value="Verifikator" {{ ($filters['role'] ?? '') === 'Verifikator' ? 'selected' : '' }}>Verifikator</option>
                            <option value="Eksekutor" {{ ($filters['role'] ?? '') === 'Eksekutor' ? 'selected' : '' }}>Eksekutor</option>
                            <option value="Admin" {{ ($filters['role'] ?? '') === 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Pimpinan" {{ ($filters['role'] ?? '') === 'Pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="form-select w-full" onchange="this.form.submit()">
                            <option value="all" {{ ($filters['status'] ?? '') === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif" {{ ($filters['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ ($filters['status'] ?? '') === 'tidak_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>

                    {{-- Tipe Akun Filter --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tipe Akun</label>
                        <select name="tipe_akun" class="form-select w-full" onchange="this.form.submit()">
                            <option value="all" {{ ($filters['tipe_akun'] ?? '') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                            <option value="sso" {{ ($filters['tipe_akun'] ?? '') === 'sso' ? 'selected' : '' }}>SSO</option>
                            <option value="lokal" {{ ($filters['tipe_akun'] ?? '') === 'lokal' ? 'selected' : '' }}>Lokal</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" class="btn-primary">
                        <x-icon name="magnifying-glass" class="mr-2 h-4 w-4" />
                        Cari
                    </button>
                    <a href="{{ route('pimpinan.users') }}" class="btn-secondary">
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div id="bulkActionsBar" class="mb-4 hidden rounded-xl border border-myunila-200 bg-myunila-50 p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-myunila">
                <span id="selectedCount">0</span> pengguna dipilih
            </span>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('pimpinan.users.bulk-activate') }}" id="bulkActivateForm" class="inline">
                    @csrf
                    <input type="hidden" name="user_uuids" id="bulkActivateUuids">
                    <button type="submit" class="btn-sm bg-success text-white hover:bg-success/90">
                        <x-icon name="check" class="mr-1 h-4 w-4" />
                        Aktifkan
                    </button>
                </form>
                <form method="POST" action="{{ route('pimpinan.users.bulk-deactivate') }}" id="bulkDeactivateForm" class="inline">
                    @csrf
                    <input type="hidden" name="user_uuids" id="bulkDeactivateUuids">
                    <button type="submit" class="btn-sm bg-danger text-white hover:bg-danger/90">
                        <x-icon name="x-mark" class="mr-1 h-4 w-4" />
                        Nonaktifkan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" id="selectAll" class="h-4 w-4 rounded border-gray-300">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Terdaftar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($users as $user)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($user->UUID !== auth()->user()->UUID)
                            <input type="checkbox" class="user-checkbox h-4 w-4 rounded border-gray-300" 
                                   value="{{ $user->UUID }}" data-status="{{ $user->a_aktif ? 'aktif' : 'nonaktif' }}">
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-myunila-100 text-sm font-bold text-myunila">
                                    {{ strtoupper(substr($user->nm, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('pimpinan.user-detail', $user->UUID) }}" 
                                       class="font-medium text-gray-900 hover:text-myunila">
                                        {{ $user->nm }}
                                    </a>
                                    <p class="truncate text-sm text-gray-500">{{ $user->email }}</p>
                                    @if($user->nip)
                                    <p class="text-xs text-gray-400">NIP: {{ $user->nip }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $roleName = $user->peran?->nm_peran ?? 'Pengguna';
                                $roleClass = match($roleName) {
                                    'Admin' => 'bg-purple-100 text-purple-700',
                                    'Verifikator' => 'bg-warning-light text-warning',
                                    'Eksekutor' => 'bg-success-light text-success',
                                    'Pimpinan' => 'bg-myunila-100 text-myunila',
                                    default => 'bg-info-light text-info',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClass }}">
                                {{ $roleName }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->a_aktif)
                            <span class="inline-flex items-center gap-1 rounded-full bg-success-light px-2.5 py-0.5 text-xs font-medium text-success">
                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-danger-light px-2.5 py-0.5 text-xs font-medium text-danger">
                                <span class="h-1.5 w-1.5 rounded-full bg-danger"></span>
                                Non-Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->sso_id)
                            <span class="inline-flex rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">SSO</span>
                            @else
                            <span class="inline-flex rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Lokal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $user->create_at?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                {{-- View Detail --}}
                                <a href="{{ route('pimpinan.user-detail', $user->UUID) }}" 
                                   class="rounded p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                                   title="Lihat Detail">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>

                                {{-- Toggle Status --}}
                                @if($user->UUID !== auth()->user()->UUID)
                                <form method="POST" action="{{ route('pimpinan.users.toggle-status', $user->UUID) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="rounded p-1.5 transition {{ $user->a_aktif ? 'text-danger hover:bg-danger-light' : 'text-success hover:bg-success-light' }}"
                                            title="{{ $user->a_aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            onclick="return confirm('{{ $user->a_aktif ? 'Nonaktifkan' : 'Aktifkan' }} pengguna ini?')">
                                        <x-icon :name="$user->a_aktif ? 'x-circle' : 'check-circle'" class="h-4 w-4" />
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <x-icon name="users" class="mx-auto h-12 w-12 text-gray-300" />
                            <p class="mt-4 text-lg font-medium text-gray-900">Tidak ada pengguna</p>
                            <p class="mt-1 text-sm text-gray-500">Coba ubah filter pencarian Anda.</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActivateUuids = document.getElementById('bulkActivateUuids');
    const bulkDeactivateUuids = document.getElementById('bulkDeactivateUuids');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCountSpan.textContent = count;
        bulkActionsBar.classList.toggle('hidden', count === 0);

        // Collect UUIDs
        const uuids = Array.from(checkedBoxes).map(cb => cb.value);
        bulkActivateUuids.value = JSON.stringify(uuids);
        bulkDeactivateUuids.value = JSON.stringify(uuids);
    }

    selectAllCheckbox?.addEventListener('change', function() {
        userCheckboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        updateBulkActions();
    });

    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    // Fix form submission for bulk actions
    document.getElementById('bulkActivateForm')?.addEventListener('submit', function(e) {
        const uuids = JSON.parse(bulkActivateUuids.value || '[]');
        if (uuids.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 pengguna.');
            return;
        }
        
        // Convert to proper form data
        bulkActivateUuids.name = '';
        uuids.forEach(uuid => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_uuids[]';
            input.value = uuid;
            this.appendChild(input);
        });
    });

    document.getElementById('bulkDeactivateForm')?.addEventListener('submit', function(e) {
        const uuids = JSON.parse(bulkDeactivateUuids.value || '[]');
        if (uuids.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 pengguna.');
            return;
        }
        
        if (!confirm('Nonaktifkan ' + uuids.length + ' pengguna?')) {
            e.preventDefault();
            return;
        }
        
        // Convert to proper form data
        bulkDeactivateUuids.name = '';
        uuids.forEach(uuid => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_uuids[]';
            input.value = uuid;
            this.appendChild(input);
        });
    });
});
</script>
@endpush
@endsection
