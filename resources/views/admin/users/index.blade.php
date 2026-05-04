@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Verifikasi Akun Pengguna</h1>
        <p class="mt-1 text-sm text-gray-500">
            Kelola aktivasi akun, ubah role pengguna, tambah akun lokal baru, dan import data unit/subdomain dari file CSV.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('import_result'))
        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
            <p class="font-semibold text-blue-800">Hasil Import CSV:</p>
            <ul class="mt-1 list-inside list-disc text-sm text-blue-700">
                @foreach(session('import_result') as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Top Row: Tambah Akun + Import CSV --}}
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Tambah Akun Baru --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-base font-semibold text-gray-900">Tambah Akun Baru</h2>
            <p class="mb-5 text-xs text-gray-500">Buat akun lokal dengan username, password, dan role yang ditentukan admin.</p>

            <form action="{{ route('admin.users.create-account') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="nm" value="{{ old('nm') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-myunila focus:outline-none focus:ring-1 focus:ring-myunila"
                        placeholder="Nama lengkap pengguna">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="usn" value="{{ old('usn') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-myunila focus:outline-none focus:ring-1 focus:ring-myunila"
                            placeholder="username">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-myunila focus:outline-none focus:ring-1 focus:ring-myunila"
                            placeholder="email@unila.ac.id">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="kata_sandi" required minlength="8"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-myunila focus:outline-none focus:ring-1 focus:ring-myunila"
                            placeholder="Min. 8 karakter">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                        <select name="peran_uuid" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-myunila focus:outline-none focus:ring-1 focus:ring-myunila">
                            <option value="">Pilih role</option>
                            @foreach($perans as $peran)
                                <option value="{{ $peran->UUID }}" {{ old('peran_uuid') == $peran->UUID ? 'selected' : '' }}>
                                    {{ $peran->nm_peran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="a_aktif" id="a_aktif" value="1" checked
                        class="h-4 w-4 rounded border-gray-300 text-myunila focus:ring-myunila">
                    <label for="a_aktif" class="text-sm text-gray-600">Langsung aktifkan akun</label>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-myunila px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Akun
                </button>
            </form>
        </div>

        {{-- Import CSV --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-1 text-base font-semibold text-gray-900">Import Unit/Subdomain dari CSV</h2>
            <p class="mb-4 text-xs text-gray-500">
                Upload file CSV untuk mengimpor daftar unit kerja (domain/subdomain) ke sistem.
                <a href="{{ route('admin.users.csv-template') }}" class="font-medium text-myunila underline hover:opacity-80">
                    Download template CSV
                </a>
            </p>

            <form action="{{ route('admin.users.import-csv') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="form-import-csv">
                @csrf

                {{-- Drop Zone --}}
                <div id="drop-zone"
                    class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center transition hover:border-myunila hover:bg-myunila-50"
                    onclick="document.getElementById('csv_file').click()">
                    <svg class="mb-2 h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-600" id="drop-label">Klik atau drag & drop file CSV di sini</p>
                    <p class="mt-1 text-xs text-gray-400">Format: .csv | Maks. 2 MB</p>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" class="hidden"
                        onchange="updateDropLabel(this)">
                </div>

                {{-- Mode Import --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Mode Import</label>
                    <div class="flex gap-4">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="mode" value="upsert" checked
                                class="h-4 w-4 text-myunila focus:ring-myunila">
                            <span class="text-sm text-gray-700">
                                <span class="font-medium">Upsert</span>
                                <span class="text-gray-500">– tambah baru & perbarui yang ada</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" name="mode" value="insert"
                                class="h-4 w-4 text-myunila focus:ring-myunila">
                            <span class="text-sm text-gray-700">
                                <span class="font-medium">Insert Only</span>
                                <span class="text-gray-500">– lewati data yang sudah ada</span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Panduan Kolom --}}
                <div class="rounded-lg bg-amber-50 p-3 text-xs text-amber-800">
                    <p class="mb-1 font-semibold">📋 Format kolom CSV:</p>
                    <code class="font-mono">nm_lmbg, kode_unit, nm_kategori, a_aktif</code>
                    <p class="mt-1 text-amber-600">Kolom <strong>nm_lmbg</strong> dan <strong>nm_kategori</strong> wajib diisi.</p>
                </div>

                <button type="submit" id="btn-import"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-myunila to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel Daftar Pengguna --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-900">Daftar Pengguna Terdaftar</h2>
            <span class="rounded-full bg-myunila-50 px-3 py-1 text-xs font-medium text-myunila">
                {{ $users->total() }} pengguna
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Pengguna</th>
                        <th class="px-6 py-3">Username</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Terdaftar</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-myunila to-blue-600 text-sm font-bold text-white">
                                        {{ strtoupper(substr($user->nm, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->nm }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $user->usn }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.users.update-role', $user->UUID) }}" method="POST" class="inline-flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <select name="peran_uuid" onchange="this.form.submit()"
                                        class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs focus:border-myunila focus:outline-none">
                                        <option value="">— Pilih —</option>
                                        @foreach($perans as $peran)
                                            <option value="{{ $peran->UUID }}" {{ $user->peran_uuid == $peran->UUID ? 'selected' : '' }}>
                                                {{ $peran->nm_peran }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->a_aktif)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($user->create_at)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.users.toggle', $user->UUID) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium transition
                                        {{ $user->a_aktif
                                            ? 'bg-red-50 text-red-600 hover:bg-red-100'
                                            : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                        {{ $user->a_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="mx-auto mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-sm">Belum ada pengguna terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update label drop zone saat file dipilih
    function updateDropLabel(input) {
        const label = document.getElementById('drop-label');
        if (input.files && input.files[0]) {
            label.textContent = '✅ ' + input.files[0].name;
            label.classList.add('text-myunila', 'font-semibold');
        }
    }

    // Drag & drop
    const zone = document.getElementById('drop-zone');
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-myunila', 'bg-myunila-50'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('border-myunila', 'bg-myunila-50'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('border-myunila', 'bg-myunila-50');
        const input = document.getElementById('csv_file');
        input.files = e.dataTransfer.files;
        updateDropLabel(input);
    });
</script>
@endpush
