@extends('layouts.dashboard')

@section('title', 'Manajemen Unit & Domain')

@push('styles')
<style>
/* ===== Modal Overlay ===== */
#modal-unit-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(3px);
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
#modal-unit-overlay.is-open {
    display: flex;
}

/* ===== Modal Box ===== */
#modal-unit-box {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 25px 60px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 520px;
    animation: modalSlideIn 0.25s ease;
    overflow: hidden;
}
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ===== Modal Header ===== */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #0B5EA8 0%, #073864 100%);
}
.modal-header h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
    margin: 0;
}
.modal-header-icon {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}
.modal-header-icon svg {
    color: rgba(255,255,255,0.85);
    width: 1.25rem;
    height: 1.25rem;
}
.modal-close-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    padding: 0.375rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.modal-close-btn:hover { background: rgba(255,255,255,0.25); }
.modal-close-btn svg { width: 1.1rem; height: 1.1rem; }

/* ===== Modal Body ===== */
.modal-body {
    padding: 1.5rem;
}
.modal-field + .modal-field { margin-top: 1rem; }
.modal-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.375rem;
}
.modal-label .req { color: #ef4444; }
.modal-input,
.modal-select {
    display: block;
    width: 100%;
    padding: 0.6rem 0.875rem;
    border: 1.5px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #111827;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}
.modal-input:focus,
.modal-select:focus {
    outline: none;
    border-color: #0B5EA8;
    box-shadow: 0 0 0 3px rgba(11,94,168,0.12);
}
.modal-input-group {
    display: flex;
    border: 1.5px solid #d1d5db;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.modal-input-group:focus-within {
    border-color: #0B5EA8;
    box-shadow: 0 0 0 3px rgba(11,94,168,0.12);
}
.modal-input-group input {
    flex: 1;
    padding: 0.6rem 0.875rem;
    border: none;
    font-size: 0.875rem;
    color: #111827;
    background: #fff;
}
.modal-input-group input:focus { outline: none; }
.modal-input-suffix {
    display: flex;
    align-items: center;
    padding: 0 0.75rem;
    background: #f3f4f6;
    font-size: 0.8rem;
    color: #6b7280;
    border-left: 1.5px solid #d1d5db;
    white-space: nowrap;
}
.modal-checkbox-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}
.modal-checkbox-row input[type="checkbox"] {
    width: 1rem; height: 1rem;
    accent-color: #0B5EA8;
    cursor: pointer;
}
.modal-checkbox-row label {
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
}

/* ===== Modal Footer ===== */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.625rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
}
.modal-btn-cancel {
    padding: 0.55rem 1.25rem;
    border: 1.5px solid #d1d5db;
    border-radius: 0.5rem;
    background: #fff;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.modal-btn-cancel:hover { background: #f3f4f6; border-color: #9ca3af; }
.modal-btn-save {
    padding: 0.55rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #0B5EA8 0%, #073864 100%);
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.1s;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.modal-btn-save:hover { opacity: 0.92; }
.modal-btn-save:active { transform: scale(0.98); }
</style>
@endpush

@section('content')
<div class="px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8 sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Unit &amp; Domain</h1>
            <p class="mt-2 text-sm text-gray-600">
                Kelola daftar unit kerja dan sub unit, serta sinkronisasi data melalui file CSV.
            </p>
        </div>
        <div class="mt-4 flex gap-3 sm:mt-0">
            <button type="button" onclick="openModal()"
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

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        {{-- Tabel Daftar Unit --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Unit Kerja</h2>
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
                                    <div class="mt-1">
                                        <a href="https://{{ $unit->kode_unit }}.unila.ac.id" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 hover:underline">
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
                                    <button type="button"
                                        onclick="editUnit('{{ $unit->UUID }}', '{{ addslashes($unit->nm_lmbg) }}', '{{ $unit->kode_unit }}', '{{ $unit->kategori_uuid }}', {{ $unit->a_aktif ? 'true' : 'false' }})"
                                        class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <x-icon name="pencil-square" class="h-5 w-5" />
                                    </button>
                                    <form action="{{ route('admin.units.destroy', $unit->UUID) }}" method="POST"
                                          class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
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

                <form method="POST" action="{{ route('admin.units.sync') }}" enctype="multipart/form-data"
                      class="space-y-4" id="form-csv-unit">
                    @csrf
                    <div id="csv-drop-zone"
                        class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-7 text-center transition hover:border-myunila hover:bg-myunila-50"
                        onclick="document.getElementById('csv_file').click()">
                        <x-icon name="document-arrow-up" class="mb-2 h-9 w-9 text-gray-400" />
                        <p class="text-sm font-medium text-gray-600" id="csv-drop-label">Klik atau drag &amp; drop file CSV di sini</p>
                        <p class="mt-1 text-xs text-gray-400">Format: .csv &nbsp;|&nbsp; Maks. 5 MB</p>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" class="hidden"
                               onchange="updateCsvLabel(this)">
                    </div>

                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-gray-700">Mode Sinkronisasi:</span>
                        <div class="flex flex-col gap-2 rounded-lg border border-gray-200 p-3">
                            <label class="flex cursor-pointer items-start gap-2">
                                <input type="radio" name="mode" value="upsert" checked class="mt-0.5 h-4 w-4 text-myunila focus:ring-myunila">
                                <span class="text-sm text-gray-700 leading-tight">
                                    <span class="font-medium block">Upsert</span>
                                    <span class="text-gray-500 text-xs">Tambah data baru &amp; perbarui data lama</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-2">
                                <input type="radio" name="mode" value="insert" class="mt-0.5 h-4 w-4 text-myunila focus:ring-myunila">
                                <span class="text-sm text-gray-700 leading-tight">
                                    <span class="font-medium block">Insert Only</span>
                                    <span class="text-gray-500 text-xs">Hanya tambah data baru (lewati data lama)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-lg bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        <span class="font-semibold block mb-1">📋 Format Kolom CSV:</span>
                        <code class="font-mono block bg-white/50 p-1 rounded border border-amber-200 text-[11px] mb-2">nm_lmbg, kode_unit, nm_kategori, a_aktif</code>
                        <div class="flex justify-between items-center">
                            <span class="text-amber-600">Kolom <code class="font-bold">nm_lmbg</code> wajib diisi.</span>
                            <a href="{{ route('admin.units.csv-template') }}"
                               class="font-medium text-myunila hover:underline flex items-center gap-1">
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

{{-- ====================================================== --}}
{{-- MODAL TAMBAH / EDIT UNIT (inline CSS - tidak butuh Tailwind) --}}
{{-- ====================================================== --}}
<div id="modal-unit-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-unit-title">
    <div id="modal-unit-box">
        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 6 0m-6 0H2.25m16.5 0a3 3 0 0 0 3-3m-3 3a3 3 0 1 1-6 0m6 0h1.875M5.25 14.25a3 3 0 0 1-3-3m0 0V6a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3v5.25m-18 0h18" />
                </svg>
                <h3 id="modal-unit-title">Tambah Unit Baru</h3>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal()" title="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body">
            <form id="form-unit" method="POST" action="{{ route('admin.units.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                {{-- Nama Lembaga --}}
                <div class="modal-field">
                    <label for="nm_lmbg" class="modal-label">
                        Nama Lembaga <span class="req">*</span>
                    </label>
                    <input type="text" name="nm_lmbg" id="nm_lmbg" required
                           placeholder="Contoh: Fakultas Teknik"
                           class="modal-input">
                </div>

                {{-- Kode Unit --}}
                <div class="modal-field">
                    <label for="kode_unit" class="modal-label">Kode Unit / Domain Utama</label>
                    <div class="modal-input-group">
                        <input type="text" name="kode_unit" id="kode_unit"
                               placeholder="Contoh: ft">
                        <span class="modal-input-suffix">.unila.ac.id</span>
                    </div>
                </div>

                {{-- Kategori --}}
                <div class="modal-field">
                    <label for="kategori_uuid" class="modal-label">
                        Kategori <span class="req">*</span>
                    </label>
                    <select name="kategori_uuid" id="kategori_uuid" required class="modal-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->UUID }}">{{ $cat->nm_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Aktif --}}
                <div class="modal-field">
                    <div class="modal-checkbox-row">
                        <input type="checkbox" name="a_aktif" id="a_aktif" value="1" checked>
                        <label for="a_aktif">Unit Aktif</label>
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="modal-footer">
            <button type="button" class="modal-btn-cancel" onclick="closeModal()">Batal</button>
            <button type="button" class="modal-btn-save" onclick="submitModalForm()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span id="modal-btn-label">Simpan Unit</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    /* ---- CSV Drop Zone ---- */
    function updateCsvLabel(input) {
        const label = document.getElementById('csv-drop-label');
        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
            label.style.color = '#0B5EA8';
            label.style.fontWeight = '600';
        } else {
            label.textContent = 'Klik atau drag & drop file CSV di sini';
            label.style.color = '';
            label.style.fontWeight = '';
        }
    }

    const dropZone = document.getElementById('csv-drop-zone');
    const fileInput = document.getElementById('csv_file');
    ['dragenter','dragover','dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); ev.stopPropagation(); }));
    ['dragenter','dragover'].forEach(e => dropZone.addEventListener(e, () => dropZone.style.borderColor = '#0B5EA8'));
    ['dragleave','drop'].forEach(e => dropZone.addEventListener(e, () => dropZone.style.borderColor = ''));
    dropZone.addEventListener('drop', ev => { fileInput.files = ev.dataTransfer.files; updateCsvLabel(fileInput); });

    /* ---- Modal Functions ---- */
    const overlay  = document.getElementById('modal-unit-overlay');
    const formEl   = document.getElementById('form-unit');
    const titleEl  = document.getElementById('modal-unit-title');
    const methodEl = document.getElementById('form-method');
    const btnLabel = document.getElementById('modal-btn-label');

    function openModal() {
        // Reset ke mode Tambah
        titleEl.textContent  = 'Tambah Unit Baru';
        btnLabel.textContent = 'Simpan Unit';
        formEl.action        = "{{ route('admin.units.store') }}";
        methodEl.value       = 'POST';
        formEl.reset();
        document.getElementById('a_aktif').checked = true;
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('nm_lmbg').focus(), 100);
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function editUnit(uuid, nm_lmbg, kode_unit, kategori_uuid, a_aktif) {
        titleEl.textContent  = 'Edit Unit';
        btnLabel.textContent = 'Perbarui Unit';
        formEl.action        = `/admin/units/${uuid}`;
        methodEl.value       = 'PUT';

        document.getElementById('nm_lmbg').value      = nm_lmbg;
        document.getElementById('kode_unit').value    = kode_unit || '';
        document.getElementById('kategori_uuid').value = kategori_uuid || '';
        document.getElementById('a_aktif').checked    = a_aktif;

        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('nm_lmbg').focus(), 100);
    }

    function submitModalForm() {
        formEl.submit();
    }

    // Tutup modal jika klik overlay (bukan box)
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });

    // Tutup modal dengan Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeModal();
    });

    // Jika ada error validasi & sebelumnya sedang tambah, buka modal lagi
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() { openModal(); });
    @endif
</script>
@endpush
@endsection
