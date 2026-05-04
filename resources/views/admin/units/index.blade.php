@extends('layouts.dashboard')

@section('title', 'Manajemen Unit & Domain')

@section('content')
<div class="px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Unit & Domain</h1>
            <p class="mt-2 text-sm text-gray-600">
                Kelola daftar unit kerja dan sub unit, serta sinkronisasi data melalui file CSV.
            </p>
        </div>
        <div class="mt-4 flex gap-3 sm:mt-0">
            <button type="button" onclick="document.getElementById('modal-tambah-unit').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-myunila px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-700">
                <x-icon name="plus" class="h-4 w-4" />
                Tambah Unit
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        {{-- Tabel Daftar Unit --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Unit Kerja</h2>
                
                {{-- Search --}}
                <form method="GET" action="{{ route('admin.units.index') }}" class="w-full sm:max-w-xs relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <x-icon name="magnifying-glass" class="h-4 w-4 text-gray-400" />
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode unit..."
                        class="block w-full rounded-lg border-gray-300 pl-10 text-sm focus:border-myunila focus:ring-myunila">
                </form>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Nama Lembaga</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Kode Unit / Domain</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Kategori</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($units as $unit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $unit->nm_lmbg }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($unit->kode_unit)
                                    <div class="font-mono text-sm font-semibold text-myunila">{{ $unit->kode_unit }}</div>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <a href="https://{{ $unit->kode_unit }}.unila.ac.id" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                            <x-icon name="link" class="h-3 w-3" />
                                            {{ strtolower($unit->kode_unit) }}.unila.ac.id
                                        </a>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $unit->category?->nm_kategori ?? 'Umum' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($unit->a_aktif)
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="editUnit('{{ $unit->UUID }}', '{{ addslashes($unit->nm_lmbg) }}', '{{ $unit->kode_unit }}', '{{ $unit->kategori_uuid }}', {{ $unit->a_aktif ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <x-icon name="pencil-square" class="h-5 w-5" />
                                    </button>
                                    <form action="{{ route('admin.units.destroy', $unit->UUID) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <x-icon name="trash" class="h-5 w-5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada data unit.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $units->links() }}
            </div>
        </div>

        {{-- Form Import CSV --}}
        <div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sticky top-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Import dari CSV</h2>
                    <p class="text-sm text-gray-500 mt-1">Upload file CSV untuk mengimpor atau memperbarui daftar unit kerja secara massal.</p>
                </div>

                <form method="POST" action="{{ route('admin.units.sync') }}" enctype="multipart/form-data" class="space-y-4" id="form-csv-unit">
                    @csrf

                    {{-- Drop Zone --}}
                    <div id="csv-drop-zone"
                        class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-7 text-center transition hover:border-myunila hover:bg-myunila-50"
                        onclick="document.getElementById('csv_file').click()">
                        <x-icon name="document-arrow-up" class="mb-2 h-9 w-9 text-gray-400" />
                        <p class="text-sm font-medium text-gray-600" id="csv-drop-label">Klik atau drag & drop file CSV di sini</p>
                        <p class="mt-1 text-xs text-gray-400">Format: .csv &nbsp;|&nbsp; Maks. 5 MB</p>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" class="hidden"
                            onchange="updateCsvLabel(this)">
                    </div>

                    {{-- Mode --}}
                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-gray-700">Mode Sinkronisasi:</span>
                        <div class="flex flex-col gap-2 rounded-lg border border-gray-200 p-3">
                            <label class="flex cursor-pointer items-start gap-2">
                                <input type="radio" name="mode" value="upsert" checked class="mt-0.5 h-4 w-4 text-myunila focus:ring-myunila">
                                <span class="text-sm text-gray-700 leading-tight"><span class="font-medium block">Upsert</span> <span class="text-gray-500 text-xs">Tambah data baru & perbarui data lama</span></span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-2">
                                <input type="radio" name="mode" value="insert" class="mt-0.5 h-4 w-4 text-myunila focus:ring-myunila">
                                <span class="text-sm text-gray-700 leading-tight"><span class="font-medium block">Insert Only</span> <span class="text-gray-500 text-xs">Hanya tambah data baru (lewati data lama)</span></span>
                            </label>
                        </div>
                    </div>

                    {{-- Panduan kolom --}}
                    <div class="rounded-lg bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        <span class="font-semibold block mb-1">📋 Format Kolom CSV:</span>
                        <code class="font-mono block bg-white/50 p-1 rounded border border-amber-200 text-[11px] mb-2">nm_lmbg, kode_unit, nm_kategori, a_aktif</code>
                        <div class="flex justify-between items-center">
                            <span class="text-amber-600">Kolom <code class="font-bold">nm_lmbg</code> wajib diisi.</span>
                            <a href="{{ route('admin.units.csv-template') }}" class="font-medium text-myunila hover:underline flex items-center gap-1">
                                <x-icon name="arrow-down-tray" class="h-3 w-3" />
                                Template
                            </a>
                        </div>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-myunila to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:opacity-90">
                        <x-icon name="arrow-up-tray" class="h-4 w-4" />
                        Mulai Import
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah / Edit Unit --}}
<div id="modal-tambah-unit" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative z-10 inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-icon name="server" class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Tambah Unit Baru</h3>
                        <div class="mt-4">
                            <form id="form-unit" method="POST" action="{{ route('admin.units.store') }}">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="nm_lmbg" class="block text-sm font-medium text-gray-700">Nama Lembaga <span class="text-red-500">*</span></label>
                                        <input type="text" name="nm_lmbg" id="nm_lmbg" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                    </div>
                                    
                                    <div>
                                        <label for="kode_unit" class="block text-sm font-medium text-gray-700">Kode Unit / Domain Utama</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="text" name="kode_unit" id="kode_unit" placeholder="Misal: tik"
                                                class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-myunila focus:ring-myunila sm:text-sm">
                                            <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                                                .unila.ac.id
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="kategori_uuid" class="block text-sm font-medium text-gray-700">Kategori</label>
                                        <select name="kategori_uuid" id="kategori_uuid" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila sm:text-sm">
                                            <option value="">Pilih Kategori...</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->UUID }}">{{ $cat->nm_kategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex items-center mt-4">
                                        <input id="a_aktif" name="a_aktif" type="checkbox" value="1" checked
                                            class="h-4 w-4 rounded border-gray-300 text-myunila focus:ring-myunila">
                                        <label for="a_aktif" class="ml-2 block text-sm text-gray-900">
                                            Unit Aktif
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" onclick="document.getElementById('form-unit').submit()" class="inline-flex w-full justify-center rounded-md border border-transparent bg-myunila px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-myunila-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Simpan
                </button>
                <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateCsvLabel(input) {
        const label = document.getElementById('csv-drop-label');
        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
            label.classList.add('text-myunila', 'font-semibold');
        } else {
            label.textContent = 'Klik atau drag & drop file CSV di sini';
            label.classList.remove('text-myunila', 'font-semibold');
        }
    }

    // Drag and drop support
    const dropZone = document.getElementById('csv-drop-zone');
    const fileInput = document.getElementById('csv_file');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-myunila', 'bg-myunila-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-myunila', 'bg-myunila-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        updateCsvLabel(fileInput);
    }

    function closeModal() {
        document.getElementById('modal-tambah-unit').classList.add('hidden');
        // Reset form
        document.getElementById('form-unit').reset();
        document.getElementById('form-unit').action = "{{ route('admin.units.store') }}";
        document.getElementById('form-method').value = "POST";
        document.getElementById('modal-title').textContent = "Tambah Unit Baru";
    }

    function editUnit(uuid, nm_lmbg, kode_unit, kategori_uuid, a_aktif) {
        document.getElementById('modal-title').textContent = "Edit Unit";
        
        // Update action route for edit
        document.getElementById('form-unit').action = `/admin/units/${uuid}`;
        document.getElementById('form-method').value = "PUT";
        
        // Fill data
        document.getElementById('nm_lmbg').value = nm_lmbg;
        document.getElementById('kode_unit').value = kode_unit || '';
        document.getElementById('kategori_uuid').value = kategori_uuid || '';
        document.getElementById('a_aktif').checked = a_aktif;
        
        // Show modal
        document.getElementById('modal-tambah-unit').classList.remove('hidden');
    }
</script>
@endpush
@endsection
