@extends('layouts.app')

@section('title', 'Formulir Pengajuan ' . ucfirst($type))

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ url('/') }}" class="mb-4 inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-myunila">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Beranda
            </a>
            
            <div class="flex items-center gap-4">
                @php
                    $iconBg = match($type) {
                        'hosting' => 'bg-gradient-ocean',
                        'vps' => 'bg-info',
                        default => 'bg-gradient-unila'
                    };
                    $iconSvg = match($type) {
                        'hosting' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>',
                        'vps' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>',
                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>'
                    };
                    $formTitle = match($type) {
                        'hosting' => 'Formulir Permohonan Hosting',
                        'vps' => 'Formulir Permohonan VPS',
                        default => 'Formulir Permohonan Sub Domain'
                    };
                    $formSubtitle = match($type) {
                        'hosting' => 'Layanan Hosting Universitas Lampung',
                        'vps' => 'Layanan Virtual Private Server Universitas Lampung',
                        default => 'Layanan Domain Universitas Lampung'
                    };
                @endphp
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $iconBg }} text-white shadow-lg shadow-myunila/30">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">{{ $formTitle }}</h1>
                    <p class="text-gray-600">{{ $formSubtitle }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('submissions.store') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="request_type" value="{{ $type }}">
            
            {{-- Section 1: Tipe Pengajuan --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">1</span>
                        Tipe Pengajuan
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Pilih jenis permohonan yang ingin Anda ajukan</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <label class="mb-3 block text-sm font-medium text-gray-700">
                            Tipe Pengajuan <span class="text-error">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                            @php
                                $tipePengajuanOptions = match($type) {
                                    'hosting' => [
                                        'pengajuan_baru' => ['label' => 'Pengajuan Baru', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>'],
                                        'perpanjangan' => ['label' => 'Perpanjangan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'],
                                        'upgrade_downgrade' => ['label' => 'Upgrade / Downgrade', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>'],
                                        'penonaktifan' => ['label' => 'Penonaktifan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
                                        'laporan_masalah' => ['label' => 'Laporan Masalah', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                                    ],
                                    'vps' => [
                                        'pengajuan_baru' => ['label' => 'Pengajuan Baru', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>'],
                                        'perpanjangan' => ['label' => 'Perpanjangan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'],
                                        'upgrade_downgrade' => ['label' => 'Upgrade / Downgrade', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>'],
                                        'penonaktifan' => ['label' => 'Penonaktifan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
                                        'laporan_masalah' => ['label' => 'Laporan Masalah', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                                    ],
                                    default => [ // domain
                                        'pengajuan_baru' => ['label' => 'Pengajuan Baru', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>'],
                                        'perpanjangan' => ['label' => 'Perpanjangan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>'],
                                        'perubahan_data' => ['label' => 'Perubahan Data / Pointing', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                                        'penonaktifan' => ['label' => 'Penonaktifan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>'],
                                        'laporan_masalah' => ['label' => 'Laporan Masalah', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                                    ],
                                };
                            @endphp
                            @foreach($tipePengajuanOptions as $value => $option)
                                <label class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-gray-200 bg-white p-4 text-center transition hover:border-myunila-300 hover:bg-myunila-50 has-[:checked]:border-myunila has-[:checked]:bg-myunila-50">
                                    <input type="radio" name="tipe_pengajuan" value="{{ $value }}" class="peer sr-only" {{ old('tipe_pengajuan', 'pengajuan_baru') == $value ? 'checked' : '' }} required>
                                    <svg class="mb-2 h-6 w-6 text-gray-400 peer-checked:text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $option['icon'] !!}</svg>
                                    <span class="text-xs font-medium text-gray-700 peer-checked:text-myunila">{{ $option['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('tipe_pengajuan')
                            <p class="mt-2 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Conditional: Existing Service Info (untuk perpanjangan, perubahan, penonaktifan, laporan masalah) --}}
                    <div id="existing_service_section" class="mt-6 hidden rounded-lg border border-warning/30 bg-warning-light p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Informasi Layanan Existing</h4>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="existing_domain" class="mb-1 block text-sm font-medium text-gray-700">
                                    @if($type === 'vps')
                                        Hostname VPS Existing <span class="text-error">*</span>
                                    @elseif($type === 'hosting')
                                        Akun Hosting Existing <span class="text-error">*</span>
                                    @else
                                        Domain Existing <span class="text-error">*</span>
                                    @endif
                                </label>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="text" 
                                        name="existing_domain" 
                                        id="existing_domain"
                                        value="{{ old('existing_domain') }}"
                                        placeholder="{{ $type === 'vps' ? 'vps-example' : ($type === 'hosting' ? 'akun-hosting' : 'contoh') }}"
                                        class="form-input {{ $type !== 'vps' ? 'max-w-xs' : '' }}"
                                    >
                                    @if($type !== 'vps')
                                        <span class="whitespace-nowrap text-lg font-semibold text-myunila">.unila.ac.id</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Masukkan domain/hosting/VPS yang sudah Anda miliki</p>
                            </div>
                            <div>
                                <label for="existing_ticket" class="mb-1 block text-sm font-medium text-gray-700">
                                    No. Tiket Sebelumnya <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="existing_ticket" 
                                    id="existing_ticket"
                                    value="{{ old('existing_ticket') }}"
                                    placeholder="Contoh: TIK-20260101-XXXX"
                                    class="form-input"
                                >
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika layanan dibuat sebelum sistem ini ada</p>
                                <div id="ticket_loading" class="mt-2 hidden">
                                    <div class="flex items-center gap-2 text-sm text-blue-600">
                                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Memuat data dari tiket...</span>
                                    </div>
                                </div>
                                <div id="ticket_success" class="mt-2 hidden">
                                    <div class="flex items-center gap-2 text-sm text-green-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Data berhasil dimuat. Silakan periksa dan sesuaikan jika ada perubahan.</span>
                                    </div>
                                </div>
                                <div id="ticket_error" class="mt-2 hidden">
                                    <div class="flex items-center gap-2 text-sm text-red-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span id="ticket_error_message">Tiket tidak ditemukan atau tidak valid.</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="existing_expired" class="mb-1 block text-sm font-medium text-gray-700">
                                    Tanggal Expired <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="existing_expired" 
                                    id="existing_expired"
                                    value="{{ old('existing_expired') }}"
                                    class="form-input"
                                >
                            </div>
                        </div>

                        {{-- Detail Keterangan (lebih prominent untuk Laporan Masalah) --}}
                        <div id="detail_keterangan_section" class="mt-4">
                            <label for="existing_notes" class="mb-1 block text-sm font-medium text-gray-700">
                                <span id="keterangan_label">Keterangan Permohonan</span> <span class="text-error">*</span>
                            </label>
                            <textarea 
                                name="existing_notes" 
                                id="existing_notes"
                                rows="3"
                                placeholder="Jelaskan detail permohonan Anda..."
                                class="form-input"
                            >{{ old('existing_notes') }}</textarea>
                            <p id="keterangan_hint" class="mt-1 text-sm text-gray-500">Jelaskan perubahan yang diminta atau alasan permohonan</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Data Sub Domain / Kategori Pemohon --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">2</span>
                        @if($type === 'vps')
                            Data Pemohon VPS
                        @elseif($type === 'hosting')
                            Data Pemohon Hosting
                        @else
                            Data Sub Domain
                        @endif
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        {{-- Kategori Pemohon --}}
                        <div>
                            <label class="mb-3 block text-sm font-medium text-gray-700">
                                Kategori Pemohon <span class="text-error">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                                @php
                                    $kategoriOptions = [
                                        'lembaga_fakultas' => 'Lembaga / Fakultas / Jurusan',
                                        'kegiatan_lembaga' => 'Kegiatan Lembaga / Fakultas / Jurusan',
                                        'organisasi_mahasiswa' => 'Organisasi Mahasiswa',
                                        'kegiatan_mahasiswa' => 'Kegiatan Mahasiswa',
                                        'lainnya' => 'Lain-lain',
                                    ];
                                @endphp
                                @foreach($kategoriOptions as $value => $label)
                                    <label class="relative flex cursor-pointer items-center justify-center rounded-xl border-2 border-gray-200 bg-white p-4 text-center transition hover:border-myunila-300 hover:bg-myunila-50 has-[:checked]:border-myunila has-[:checked]:bg-myunila-50">
                                        <input type="radio" name="kategori_pemohon" value="{{ $value }}" class="peer sr-only" {{ old('kategori_pemohon') == $value ? 'checked' : '' }} required>
                                        <span class="text-xs font-medium text-gray-700 peer-checked:text-myunila">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('kategori_pemohon')
                                <p class="mt-2 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Lembaga/Organisasi/Kegiatan --}}
                        <div>
                            <label for="nama_organisasi" class="mb-1 block text-sm font-medium text-gray-700">
                                Nama Lembaga / Organisasi / Kegiatan <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nama_organisasi" 
                                id="nama_organisasi"
                                value="{{ old('nama_organisasi') }}"
                                placeholder="Contoh: Fakultas Teknik / BEM Universitas / Seminar Nasional IT"
                                required
                                class="form-input @error('nama_organisasi') form-input-error @enderror"
                            >
                            @error('nama_organisasi')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Penanggung Jawab Administratif --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">3</span>
                        Penanggung Jawab Administratif
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Pejabat yang akan menandatangani formulir permohonan</p>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Kategori Admin --}}
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Kategori Penanggung Jawab <span class="text-error">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori_admin" value="dosen" required class="h-4 w-4 border-gray-300 text-myunila focus:ring-myunila" checked>
                                    <span class="text-sm text-gray-700">Dosen</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori_admin" value="tendik" required class="h-4 w-4 border-gray-300 text-myunila focus:ring-myunila">
                                    <span class="text-sm text-gray-700">Tendik</span>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="admin_responsible_name" class="mb-1 block text-sm font-medium text-gray-700">
                                Nama <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="admin_responsible_name" 
                                id="admin_responsible_name"
                                value="{{ old('admin_responsible_name') }}"
                                placeholder="Contoh: Dr. Ir. Ahmad Sudrajat, M.T."
                                required
                                class="form-input @error('admin_responsible_name') form-input-error @enderror"
                            >
                            @error('admin_responsible_name')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="admin_responsible_position" class="mb-1 block text-sm font-medium text-gray-700">
                                Jabatan <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="admin_responsible_position" 
                                id="admin_responsible_position"
                                value="{{ old('admin_responsible_position') }}"
                                placeholder="Contoh: Dekan / Ketua Jurusan / Kepala UPT"
                                required
                                class="form-input @error('admin_responsible_position') form-input-error @enderror"
                            >
                            @error('admin_responsible_position')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_responsible_nip" class="mb-1 block text-sm font-medium text-gray-700">
                                No. Identitas (NIP/NIDN)
                            </label>
                            <input 
                                type="text" 
                                name="admin_responsible_nip" 
                                id="admin_responsible_nip"
                                value="{{ old('admin_responsible_nip') }}"
                                placeholder="Contoh: 198501012010011001"
                                class="form-input"
                            >
                        </div>
                        
                        <div>
                            <label for="admin_alamat_kantor" class="mb-1 block text-sm font-medium text-gray-700">
                                Alamat Kantor
                            </label>
                            <input 
                                type="text" 
                                name="admin_alamat_kantor" 
                                id="admin_alamat_kantor"
                                value="{{ old('admin_alamat_kantor') }}"
                                placeholder="Gedung A Lt. 2, Fakultas Teknik Unila"
                                class="form-input"
                            >
                        </div>
                        
                        <div>
                            <label for="admin_alamat_rumah" class="mb-1 block text-sm font-medium text-gray-700">
                                Alamat Rumah
                            </label>
                            <input 
                                type="text" 
                                name="admin_alamat_rumah" 
                                id="admin_alamat_rumah"
                                value="{{ old('admin_alamat_rumah') }}"
                                placeholder="Jl. Contoh No. 123, Bandar Lampung"
                                class="form-input"
                            >
                        </div>
                        
                        <div>
                            <label for="admin_telepon_kantor" class="mb-1 block text-sm font-medium text-gray-700">
                                No. Telepon Kantor
                            </label>
                            <input 
                                type="tel" 
                                name="admin_telepon_kantor" 
                                id="admin_telepon_kantor"
                                value="{{ old('admin_telepon_kantor') }}"
                                placeholder="(0721) 123456"
                                class="form-input"
                            >
                        </div>
                        
                        <div>
                            <label for="admin_responsible_phone" class="mb-1 block text-sm font-medium text-gray-700">
                                No. Telepon Rumah / HP <span class="text-error">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="admin_responsible_phone" 
                                id="admin_responsible_phone"
                                value="{{ old('admin_responsible_phone') }}"
                                placeholder="081234567890"
                                required
                                class="form-input @error('admin_responsible_phone') form-input-error @enderror"
                            >
                            @error('admin_responsible_phone')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="admin_email" class="mb-1 block text-sm font-medium text-gray-700">
                                Email <span class="text-error">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="admin_email" 
                                id="admin_email"
                                value="{{ old('admin_email') }}"
                                placeholder="email@unila.ac.id"
                                required
                                class="form-input @error('admin_email') form-input-error @enderror"
                            >
                            @error('admin_email')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Penanggung Jawab Teknis --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">4</span>
                        Penanggung Jawab Teknis
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Orang yang akan mengelola akun hosting (bisa sama dengan pemohon)</p>
                </div>
                <div class="p-6">
                    {{-- Quick fill from logged in user --}}
                    <div class="mb-6 rounded-lg border border-info/30 bg-info-light p-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="fill_from_user" class="h-4 w-4 rounded border-gray-300 text-myunila focus:ring-myunila">
                            <span class="text-sm text-gray-700">Gunakan data saya sebagai Penanggung Jawab Teknis</span>
                        </label>
                    </div>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Kategori Teknis --}}
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                Kategori Penanggung Jawab Teknis <span class="text-error">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori_teknis" value="mahasiswa" required class="h-4 w-4 border-gray-300 text-myunila focus:ring-myunila" onchange="updateTechIdentityLabel()" checked>
                                    <span class="text-sm text-gray-700">Mahasiswa</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori_teknis" value="dosen" required class="h-4 w-4 border-gray-300 text-myunila focus:ring-myunila" onchange="updateTechIdentityLabel()">
                                    <span class="text-sm text-gray-700">Dosen</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="kategori_teknis" value="tendik" required class="h-4 w-4 border-gray-300 text-myunila focus:ring-myunila" onchange="updateTechIdentityLabel()">
                                    <span class="text-sm text-gray-700">Tendik</span>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="tech_name" class="mb-1 block text-sm font-medium text-gray-700">
                                Nama <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="tech_name" 
                                id="tech_name"
                                value="{{ old('tech_name') }}"
                                placeholder="Nama lengkap pengelola teknis"
                                required
                                data-user-name="{{ $user->name ?? '' }}"
                                class="form-input @error('tech_name') form-input-error @enderror"
                            >
                            @error('tech_name')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tech_nip" id="tech_nip_label" class="mb-1 block text-sm font-medium text-gray-700">
                                NPM <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="tech_nip" 
                                id="tech_nip"
                                value="{{ old('tech_nip') }}"
                                placeholder="NPM"
                                required
                                data-user-nip="{{ $user->nomor_identitas ?? '' }}"
                                class="form-input @error('tech_nip') form-input-error @enderror"
                            >
                            @error('tech_nip')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="tech_nik" class="mb-1 block text-sm font-medium text-gray-700">
                                NIK / Passport <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="tech_nik" 
                                id="tech_nik"
                                value="{{ old('tech_nik') }}"
                                placeholder="Nomor NIK atau Passport"
                                required
                                class="form-input @error('tech_nik') form-input-error @enderror"
                            >
                            @error('tech_nik')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="tech_phone" class="mb-1 block text-sm font-medium text-gray-700">
                                No. Telepon <span class="text-error">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="tech_phone" 
                                id="tech_phone"
                                value="{{ old('tech_phone') }}"
                                placeholder="081234567890"
                                required
                                class="form-input @error('tech_phone') form-input-error @enderror"
                            >
                            @error('tech_phone')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="tech_alamat_kantor" class="mb-1 block text-sm font-medium text-gray-700">
                                Alamat Kantor
                            </label>
                            <input 
                                type="text" 
                                name="tech_alamat_kantor" 
                                id="tech_alamat_kantor"
                                value="{{ old('tech_alamat_kantor') }}"
                                placeholder="Alamat kantor/kampus"
                                class="form-input"
                            >
                        </div>
                        
                        <div>
                            <label for="tech_alamat_rumah" class="mb-1 block text-sm font-medium text-gray-700">
                                Alamat Rumah
                            </label>
                            <input 
                                type="text" 
                                name="tech_alamat_rumah" 
                                id="tech_alamat_rumah"
                                value="{{ old('tech_alamat_rumah') }}"
                                placeholder="Alamat rumah"
                                class="form-input"
                            >
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="tech_email" class="mb-1 block text-sm font-medium text-gray-700">
                                Email <span class="text-error">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="tech_email" 
                                id="tech_email"
                                value="{{ old('tech_email') }}"
                                placeholder="email@students.unila.ac.id"
                                required
                                data-user-email="{{ $user->email ?? '' }}"
                                class="form-input @error('tech_email') form-input-error @enderror"
                            >
                            @error('tech_email')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 5: Data Layanan yang Diminta (Hanya untuk Pengajuan Baru) --}}
            <div id="section_layanan_baru" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">5</span>
                        <span id="section5_title">
                            @if($type === 'vps')
                                Spesifikasi VPS yang Diminta
                            @elseif($type === 'hosting')
                                Data Hosting yang Diminta
                            @else
                                Nama Sub Domain yang Diminta
                            @endif
                        </span>
                    </h2>
                    <p id="section5_subtitle" class="mt-1 text-sm text-gray-500">Isi data layanan baru yang ingin Anda ajukan</p>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Domain/Hostname Field (untuk pengajuan baru) --}}
                        <div id="requested_domain_wrapper" class="md:col-span-2">
                            <label for="requested_domain" class="mb-1 block text-sm font-medium text-gray-700">
                                @if($type === 'vps')
                                    Hostname VPS <span class="text-error">*</span>
                                @elseif($type === 'hosting')
                                    Nama Akun Hosting <span class="text-error">*</span>
                                @else
                                    Sub Domain <span class="text-error">*</span>
                                @endif
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1 max-w-xs">
                                    <input 
                                        type="text" 
                                        name="requested_domain" 
                                        id="requested_domain"
                                        value="{{ old('requested_domain') }}"
                                        placeholder="{{ $type === 'vps' ? 'vps-namamu' : ($type === 'hosting' ? 'hosting-namamu' : 'namadomain') }}"
                                        minlength="2"
                                        maxlength="12"
                                        pattern="[a-z0-9\-]+"
                                        class="form-input w-full pr-10 @error('requested_domain') form-input-error @enderror"
                                    >
                                    <div id="domain_check_icon" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                        <!-- Loading spinner -->
                                        <svg class="checking h-5 w-5 animate-spin text-gray-400 hidden" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <!-- Available -->
                                        <svg class="available h-5 w-5 text-success hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <!-- Taken -->
                                        <svg class="taken h-5 w-5 text-error hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                @if($type !== 'vps')
                                    <span class="whitespace-nowrap text-lg font-semibold text-myunila">.unila.ac.id</span>
                                @endif
                            </div>
                            <div id="domain_availability_message" class="mt-2 text-sm hidden">
                                <!-- Messages will be inserted here -->
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                <span class="font-medium">Ketentuan:</span> Minimal 2 karakter, maksimal 12 karakter. Hanya huruf kecil, angka, dan tanda hubung (-).
                            </p>
                            @error('requested_domain')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- VPS-specific fields --}}
                        @if($type === 'vps')
                            <div>
                                <label for="vps_cpu" class="mb-1 block text-sm font-medium text-gray-700">
                                    Jumlah CPU Core <span class="text-error">*</span>
                                </label>
                                <select name="vps_cpu" id="vps_cpu" class="form-input @error('vps_cpu') form-input-error @enderror">
                                    <option value="">Pilih jumlah CPU</option>
                                    <option value="1" {{ old('vps_cpu') == '1' ? 'selected' : '' }}>1 Core</option>
                                    <option value="2" {{ old('vps_cpu') == '2' ? 'selected' : '' }}>2 Core</option>
                                    <option value="4" {{ old('vps_cpu') == '4' ? 'selected' : '' }}>4 Core</option>
                                </select>
                                @error('vps_cpu')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vps_ram" class="mb-1 block text-sm font-medium text-gray-700">
                                    RAM <span class="text-error">*</span>
                                </label>
                                <select name="vps_ram" id="vps_ram" class="form-input @error('vps_ram') form-input-error @enderror">
                                    <option value="">Pilih kapasitas RAM</option>
                                    <option value="1" {{ old('vps_ram') == '1' ? 'selected' : '' }}>1 GB</option>
                                    <option value="2" {{ old('vps_ram') == '2' ? 'selected' : '' }}>2 GB</option>
                                    <option value="4" {{ old('vps_ram') == '4' ? 'selected' : '' }}>4 GB</option>
                                    <option value="8" {{ old('vps_ram') == '8' ? 'selected' : '' }}>8 GB</option>
                                </select>
                                @error('vps_ram')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vps_storage" class="mb-1 block text-sm font-medium text-gray-700">
                                    Storage <span class="text-error">*</span>
                                </label>
                                <select name="vps_storage" id="vps_storage" class="form-input @error('vps_storage') form-input-error @enderror">
                                    <option value="">Pilih kapasitas storage</option>
                                    <option value="20" {{ old('vps_storage') == '20' ? 'selected' : '' }}>20 GB</option>
                                    <option value="40" {{ old('vps_storage') == '40' ? 'selected' : '' }}>40 GB</option>
                                    <option value="80" {{ old('vps_storage') == '80' ? 'selected' : '' }}>80 GB</option>
                                    <option value="100" {{ old('vps_storage') == '100' ? 'selected' : '' }}>100 GB</option>
                                </select>
                                @error('vps_storage')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vps_os" class="mb-1 block text-sm font-medium text-gray-700">
                                    Sistem Operasi <span class="text-error">*</span>
                                </label>
                                <select name="vps_os" id="vps_os" class="form-input @error('vps_os') form-input-error @enderror">
                                    <option value="">Pilih OS</option>
                                    <option value="ubuntu-22.04" {{ old('vps_os') == 'ubuntu-22.04' ? 'selected' : '' }}>Ubuntu 22.04 LTS</option>
                                    <option value="ubuntu-20.04" {{ old('vps_os') == 'ubuntu-20.04' ? 'selected' : '' }}>Ubuntu 20.04 LTS</option>
                                    <option value="centos-8" {{ old('vps_os') == 'centos-8' ? 'selected' : '' }}>CentOS 8</option>
                                    <option value="debian-11" {{ old('vps_os') == 'debian-11' ? 'selected' : '' }}>Debian 11</option>
                                </select>
                                @error('vps_os')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="vps_purpose" class="mb-1 block text-sm font-medium text-gray-700">
                                    Tujuan Penggunaan VPS <span class="text-error">*</span>
                                </label>
                                <textarea 
                                    name="vps_purpose" 
                                    id="vps_purpose"
                                    rows="3"
                                    placeholder="Jelaskan tujuan penggunaan VPS, misalnya: untuk hosting aplikasi SIAKAD, web service API, dll."
                                    class="form-input @error('vps_purpose') form-input-error @enderror"
                                >{{ old('vps_purpose') }}</textarea>
                                @error('vps_purpose')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Hosting-specific fields --}}
                        @if($type === 'hosting')
                            <div>
                                <label for="hosting_quota" class="mb-1 block text-sm font-medium text-gray-700">
                                    Kuota Storage <span class="text-error">*</span>
                                </label>
                                <select name="hosting_quota" id="hosting_quota" class="form-input @error('hosting_quota') form-input-error @enderror">
                                    <option value="">Pilih kuota</option>
                                    <option value="500" {{ old('hosting_quota') == '500' ? 'selected' : '' }}>500 MB</option>
                                    <option value="1000" {{ old('hosting_quota') == '1000' ? 'selected' : '' }}>1 GB</option>
                                    <option value="2000" {{ old('hosting_quota') == '2000' ? 'selected' : '' }}>2 GB</option>
                                    <option value="5000" {{ old('hosting_quota') == '5000' ? 'selected' : '' }}>5 GB</option>
                                </select>
                                @error('hosting_quota')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Password field (semua tipe) --}}
                        <div class="{{ $type === 'hosting' ? '' : 'md:col-span-2' }}">
                            <label for="admin_password" class="mb-1 block text-sm font-medium text-gray-700">
                                Admin Password (Hint) <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="admin_password" 
                                id="admin_password"
                                value="{{ old('admin_password') }}"
                                placeholder="Kata kunci password (6-8 karakter)"
                                minlength="6"
                                maxlength="8"
                                required
                                class="form-input max-w-xs @error('admin_password') form-input-error @enderror"
                            >
                            <p class="mt-2 text-sm text-gray-500">
                                <span class="font-medium">Ketentuan:</span> Minimal 6 karakter, maksimal 8 karakter. Password final akan digenerate oleh tim TIK.
                            </p>
                            @error('admin_password')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hidden fields for DB compatibility --}}
            <input type="hidden" name="unit_id" value="{{ $categories->first()?->units->first()?->id ?? '' }}">
            <input type="hidden" name="application_name" id="hidden_application_name" value="">
            <input type="hidden" name="description" id="hidden_description" value="">

            {{-- Persetujuan --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-warning-light px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <svg class="h-5 w-5 text-warning" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Persetujuan
                    </h2>
                </div>
                <div class="p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="agreement" required class="mt-1 h-4 w-4 rounded border-gray-300 text-myunila focus:ring-myunila">
                        <span class="text-sm text-gray-700">
                            Dengan ini saya menyatakan bahwa data di atas adalah benar. Saya bertindak atas nama institusi yang saya wakili dan saya mematuhi semua aturan yang ditentukan dan berlaku bagi seluruh pengguna fasilitas layanan Hosting Universitas Lampung.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="rounded-2xl border border-info/30 bg-info-light p-6">
                <div class="flex gap-4">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Langkah Selanjutnya</h3>
                        <p class="mt-1 text-sm text-gray-700">
                            Setelah submit, sistem akan otomatis membuat <strong>2 formulir</strong>:
                        </p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg border border-myunila/20 bg-white p-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="h-5 w-5 text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium text-gray-900">Paperless (Digital)</span>
                                </div>
                                <p class="text-xs text-gray-600">Untuk administrasi internal TIK. Tersimpan otomatis di sistem.</p>
                            </div>
                            <div class="rounded-lg border border-myunila/20 bg-white p-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="h-5 w-5 text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium text-gray-900">Hardcopy (PDF)</span>
                                </div>
                                <p class="text-xs text-gray-600">Untuk dicetak & ditandatangani atasan (Kajur/Dekan/Wakil Rektor).</p>
                            </div>
                        </div>
                        <ol class="mt-4 list-inside list-decimal space-y-1 text-sm text-gray-700">
                            <li>Download formulir PDF yang sudah terisi otomatis</li>
                            <li>Cetak formulir dan minta <strong>tanda tangan basah</strong> dari atasan</li>
                            <li>Scan formulir yang sudah ditandatangani</li>
                            <li>Upload scan formulir beserta foto/scan identitas (KTM/Karpeg)</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ url('/') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Buat Formulir Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Function to update tech identity label based on kategori_teknis selection
function updateTechIdentityLabel() {
    const kategoriTech = document.querySelector('input[name="kategori_teknis"]:checked')?.value;
    const techNipLabel = document.getElementById('tech_nip_label');
    const techNipInput = document.getElementById('tech_nip');
    
    if (kategoriTech === 'mahasiswa') {
        techNipLabel.innerHTML = 'NPM <span class="text-error">*</span>';
        techNipInput.placeholder = 'NPM';
    } else if (kategoriTech === 'dosen') {
        techNipLabel.innerHTML = 'NIP/NIDN <span class="text-error">*</span>';
        techNipInput.placeholder = 'NIP/NIDN';
    } else if (kategoriTech === 'tendik') {
        techNipLabel.innerHTML = 'NIP/NIDN <span class="text-error">*</span>';
        techNipInput.placeholder = 'NIP/NIDN';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize label on page load
    updateTechIdentityLabel();
    
    const fillCheckbox = document.getElementById('fill_from_user');
    const techName = document.getElementById('tech_name');
    const techNip = document.getElementById('tech_nip');
    const techEmail = document.getElementById('tech_email');
    
    // Auto-fill from user data
    fillCheckbox?.addEventListener('change', function() {
        if (this.checked) {
            techName.value = techName.dataset.userName || '';
            techNip.value = techNip.dataset.userNip || '';
            techEmail.value = techEmail.dataset.userEmail || '';
        } else {
            techName.value = '';
            techNip.value = '';
            techEmail.value = '';
        }
    });

    // Elements
    const tipePengajuanRadios = document.querySelectorAll('input[name="tipe_pengajuan"]');
    const existingServiceSection = document.getElementById('existing_service_section');
    const existingTicket = document.getElementById('existing_ticket');
    const existingDomain = document.getElementById('existing_domain');
    const existingNotes = document.getElementById('existing_notes');
    const existingExpired = document.getElementById('existing_expired');
    const sectionLayananBaru = document.getElementById('section_layanan_baru');
    const requestedDomainWrapper = document.getElementById('requested_domain_wrapper');
    const requestedDomain = document.getElementById('requested_domain');
    const adminPassword = document.getElementById('admin_password');
    const keterlangaLabel = document.getElementById('keterangan_label');
    const keteranganHint = document.getElementById('keterangan_hint');
    
    // VPS fields (may not exist)
    const vpsCpu = document.getElementById('vps_cpu');
    const vpsRam = document.getElementById('vps_ram');
    const vpsStorage = document.getElementById('vps_storage');
    const vpsOs = document.getElementById('vps_os');
    const vpsPurpose = document.getElementById('vps_purpose');
    
    // Hosting fields (may not exist)
    const hostingQuota = document.getElementById('hosting_quota');

    function toggleFormSections() {
        const selectedTipe = document.querySelector('input[name="tipe_pengajuan"]:checked')?.value;
        const isPengajuanBaru = selectedTipe === 'pengajuan_baru';
        const isLaporanMasalah = selectedTipe === 'laporan_masalah';
        const needsExistingInfo = ['perpanjangan', 'perubahan_data', 'upgrade_downgrade', 'penonaktifan', 'laporan_masalah'].includes(selectedTipe);
        
        // Toggle existing service section
        if (needsExistingInfo) {
            existingServiceSection.classList.remove('hidden');
            existingDomain.setAttribute('required', 'required');
            existingNotes.setAttribute('required', 'required');
            
            // Update keterangan label based on tipe
            if (isLaporanMasalah) {
                keterlangaLabel.textContent = 'Detail Masalah';
                keteranganHint.textContent = 'Jelaskan masalah yang Anda alami secara detail (error, tidak bisa diakses, dll.)';
                existingNotes.placeholder = 'Jelaskan masalah yang Anda alami secara detail...';
            } else if (selectedTipe === 'perpanjangan') {
                keterlangaLabel.textContent = 'Keterangan Perpanjangan';
                keteranganHint.textContent = 'Jelaskan alasan perpanjangan layanan';
                existingNotes.placeholder = 'Contoh: Layanan masih aktif digunakan untuk kegiatan akademik...';
            } else if (selectedTipe === 'perubahan_data') {
                keterlangaLabel.textContent = 'Detail Perubahan';
                keteranganHint.textContent = 'Jelaskan perubahan data yang diminta (pointing, nama, dll.)';
                existingNotes.placeholder = 'Contoh: Ubah pointing domain ke IP xxx.xxx.xxx.xxx...';
            } else if (selectedTipe === 'upgrade_downgrade') {
                keterlangaLabel.textContent = 'Detail Upgrade/Downgrade';
                keteranganHint.textContent = 'Jelaskan perubahan spesifikasi yang diminta';
                existingNotes.placeholder = 'Contoh: Upgrade kapasitas dari 500MB ke 2GB...';
            } else if (selectedTipe === 'penonaktifan') {
                keterlangaLabel.textContent = 'Alasan Penonaktifan';
                keteranganHint.textContent = 'Jelaskan alasan penonaktifan layanan';
                existingNotes.placeholder = 'Contoh: Kegiatan sudah selesai, tidak diperlukan lagi...';
            }
        } else {
            existingServiceSection.classList.add('hidden');
            existingDomain.removeAttribute('required');
            existingNotes.removeAttribute('required');
            // Clear values when hidden
            existingTicket.value = '';
            existingDomain.value = '';
            existingNotes.value = '';
            if (existingExpired) existingExpired.value = '';
        }
        
        // Toggle Section 5 (Data Layanan Baru)
        if (isPengajuanBaru) {
            // Show full section for new submissions
            sectionLayananBaru.classList.remove('hidden');
            requestedDomain.setAttribute('required', 'required');
            adminPassword.setAttribute('required', 'required');
            
            // Set required for VPS fields
            if (vpsCpu) vpsCpu.setAttribute('required', 'required');
            if (vpsRam) vpsRam.setAttribute('required', 'required');
            if (vpsStorage) vpsStorage.setAttribute('required', 'required');
            if (vpsOs) vpsOs.setAttribute('required', 'required');
            if (vpsPurpose) vpsPurpose.setAttribute('required', 'required');
            if (hostingQuota) hostingQuota.setAttribute('required', 'required');
        } else if (selectedTipe === 'upgrade_downgrade') {
            // For upgrade/downgrade, show section but hide requested_domain
            sectionLayananBaru.classList.remove('hidden');
            requestedDomainWrapper.classList.add('hidden');
            requestedDomain.removeAttribute('required');
            adminPassword.removeAttribute('required');
            
            // Keep VPS/Hosting specs visible for upgrade
            if (vpsCpu) vpsCpu.setAttribute('required', 'required');
            if (vpsRam) vpsRam.setAttribute('required', 'required');
            if (vpsStorage) vpsStorage.setAttribute('required', 'required');
            if (vpsOs) vpsOs.removeAttribute('required'); // OS usually doesn't change
            if (vpsPurpose) vpsPurpose.removeAttribute('required');
            if (hostingQuota) hostingQuota.setAttribute('required', 'required');
        } else {
            // Hide section completely for perpanjangan, perubahan_data, penonaktifan, laporan_masalah
            sectionLayananBaru.classList.add('hidden');
            requestedDomain.removeAttribute('required');
            adminPassword.removeAttribute('required');
            
            // Remove required from all VPS/Hosting fields
            if (vpsCpu) vpsCpu.removeAttribute('required');
            if (vpsRam) vpsRam.removeAttribute('required');
            if (vpsStorage) vpsStorage.removeAttribute('required');
            if (vpsOs) vpsOs.removeAttribute('required');
            if (vpsPurpose) vpsPurpose.removeAttribute('required');
            if (hostingQuota) hostingQuota.removeAttribute('required');
        }
        
        // Reset requested_domain visibility when showing section
        if (isPengajuanBaru) {
            requestedDomainWrapper.classList.remove('hidden');
        }
    }

    tipePengajuanRadios.forEach(radio => {
        radio.addEventListener('change', toggleFormSections);
    });

    // Initial check on page load
    toggleFormSections();

    // Auto-fill hidden fields before submit
    document.querySelector('form').addEventListener('submit', function() {
        const namaOrg = document.getElementById('nama_organisasi').value;
        const domain = requestedDomain.value || existingDomain.value;
        document.getElementById('hidden_application_name').value = namaOrg || domain;
        
        const selectedTipe = document.querySelector('input[name="tipe_pengajuan"]:checked')?.value;
        const tipeLabel = {
            'pengajuan_baru': 'Pengajuan Baru',
            'perpanjangan': 'Perpanjangan',
            'perubahan_data': 'Perubahan Data',
            'upgrade_downgrade': 'Upgrade/Downgrade',
            'penonaktifan': 'Penonaktifan',
            'laporan_masalah': 'Laporan Masalah'
        }[selectedTipe] || 'Permohonan';
        
        document.getElementById('hidden_description').value = tipeLabel + ' layanan untuk ' + namaOrg;
    });

    // Format domain input to lowercase
    requestedDomain?.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '');
        checkDomainAvailability();
    });
    
    existingDomain?.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9\-\.]/g, '');
    });

    // Auto-fill form when existing ticket number is entered
    let ticketFetchTimeout;
    const ticketLoadingEl = document.getElementById('ticket_loading');
    const ticketSuccessEl = document.getElementById('ticket_success');
    const ticketErrorEl = document.getElementById('ticket_error');
    const ticketErrorMessage = document.getElementById('ticket_error_message');
    
    existingTicket?.addEventListener('input', function() {
        clearTimeout(ticketFetchTimeout);
        
        // Hide all feedback messages
        ticketLoadingEl?.classList.add('hidden');
        ticketSuccessEl?.classList.add('hidden');
        ticketErrorEl?.classList.add('hidden');
        
        const ticketNumber = this.value.trim().toUpperCase();
        
        // Update input value to uppercase
        this.value = ticketNumber;
        
        // Validate ticket format (TIK-YYYYMMDD-XXXX)
        if (ticketNumber.length < 10) {
            return;
        }
        
        // Show loading indicator
        ticketLoadingEl?.classList.remove('hidden');
        
        // Debounce API call
        ticketFetchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/api/submission-by-ticket/${encodeURIComponent(ticketNumber)}`);
                const result = await response.json();
                
                // Hide loading
                ticketLoadingEl?.classList.add('hidden');
                
                if (result.success && result.data) {
                    const data = result.data;
                    
                    // Auto-fill existing service info
                    if (existingDomain) existingDomain.value = data.domain || '';
                    if (existingExpired) existingExpired.value = data.expired_date || '';
                    
                    // Auto-fill organization info
                    const kategoriPemohonRadio = document.querySelector(`input[name="kategori_pemohon"][value="${data.kategori_pemohon}"]`);
                    if (kategoriPemohonRadio) kategoriPemohonRadio.checked = true;
                    
                    const namaOrganisasi = document.getElementById('nama_organisasi');
                    if (namaOrganisasi) namaOrganisasi.value = data.nama_organisasi || '';
                    
                    // Auto-fill admin contact
                    const adminName = document.getElementById('admin_responsible_name');
                    const adminPosition = document.getElementById('admin_responsible_position');
                    const adminNip = document.getElementById('admin_responsible_nip');
                    const adminEmail = document.getElementById('admin_email');
                    const adminPhone = document.getElementById('admin_responsible_phone');
                    const adminTeleponKantor = document.getElementById('admin_telepon_kantor');
                    const adminAlamatKantor = document.getElementById('admin_alamat_kantor');
                    const adminAlamatRumah = document.getElementById('admin_alamat_rumah');
                    
                    if (adminName) adminName.value = data.admin_name || '';
                    if (adminPosition) adminPosition.value = data.admin_position || '';
                    if (adminNip) adminNip.value = data.admin_nip || '';
                    if (adminEmail) adminEmail.value = data.admin_email || '';
                    if (adminPhone) adminPhone.value = data.admin_phone || '';
                    if (adminTeleponKantor) adminTeleponKantor.value = data.admin_telepon_kantor || '';
                    if (adminAlamatKantor) adminAlamatKantor.value = data.admin_alamat_kantor || '';
                    if (adminAlamatRumah) adminAlamatRumah.value = data.admin_alamat_rumah || '';
                    
                    // Auto-fill kategori admin
                    if (data.kategori_admin) {
                        const kategoriAdminRadio = document.querySelector(`input[name="kategori_admin"][value="${data.kategori_admin}"]`);
                        if (kategoriAdminRadio) kategoriAdminRadio.checked = true;
                    }
                    
                    // Auto-fill tech contact
                    const techName = document.getElementById('tech_name');
                    const techNip = document.getElementById('tech_nip');
                    const techNik = document.getElementById('tech_nik');
                    const techEmail = document.getElementById('tech_email');
                    const techPhone = document.getElementById('tech_phone');
                    const techAlamatKantor = document.getElementById('tech_alamat_kantor');
                    const techAlamatRumah = document.getElementById('tech_alamat_rumah');
                    
                    if (techName) techName.value = data.tech_name || '';
                    if (techNip) techNip.value = data.tech_nip || '';
                    if (techNik) techNik.value = data.tech_nik || '';
                    if (techEmail) techEmail.value = data.tech_email || '';
                    if (techPhone) techPhone.value = data.tech_phone || '';
                    if (techAlamatKantor) techAlamatKantor.value = data.tech_alamat_kantor || '';
                    if (techAlamatRumah) techAlamatRumah.value = data.tech_alamat_rumah || '';
                    
                    // Auto-fill kategori tech
                    if (data.kategori_tech) {
                        const kategoriTechRadio = document.querySelector(`input[name="kategori_teknis"][value="${data.kategori_tech}"]`);
                        if (kategoriTechRadio) {
                            kategoriTechRadio.checked = true;
                            // Update label dinamis
                            updateTechIdentityLabel();
                        }
                    }
                    
                    // Auto-fill VPS specs (if applicable)
                    if (data.service_type === 'vps') {
                        if (vpsCpu) vpsCpu.value = data.vps_cpu || '';
                        if (vpsRam) vpsRam.value = data.vps_ram || '';
                        if (vpsStorage) vpsStorage.value = data.vps_storage || '';
                        if (vpsOs) vpsOs.value = data.vps_os || '';
                        if (vpsPurpose) vpsPurpose.value = data.vps_purpose || '';
                    }
                    
                    // Auto-fill Hosting specs (if applicable)
                    if (data.service_type === 'hosting') {
                        if (hostingQuota) hostingQuota.value = data.hosting_quota || '';
                    }
                    
                    // Show success message
                    ticketSuccessEl?.classList.remove('hidden');
                    
                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        ticketSuccessEl?.classList.add('hidden');
                    }, 5000);
                    
                } else {
                    // Show error message
                    ticketErrorMessage.textContent = result.message || 'Tiket tidak ditemukan atau tidak valid.';
                    ticketErrorEl?.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error fetching ticket data:', error);
                
                // Hide loading
                ticketLoadingEl?.classList.add('hidden');
                
                // Show error message
                ticketErrorMessage.textContent = 'Terjadi kesalahan saat memuat data. Silakan coba lagi.';
                ticketErrorEl?.classList.remove('hidden');
            }
        }, 800); // Wait 800ms after user stops typing
    });

    // Domain availability checker
    let domainCheckTimeout;
    function checkDomainAvailability() {
        clearTimeout(domainCheckTimeout);
        
        const domainInput = requestedDomain.value.trim();
        const iconContainer = document.getElementById('domain_check_icon');
        const messageContainer = document.getElementById('domain_availability_message');
        const checkingIcon = iconContainer.querySelector('.checking');
        const availableIcon = iconContainer.querySelector('.available');
        const takenIcon = iconContainer.querySelector('.taken');
        
        // Hide all icons
        iconContainer.classList.add('hidden');
        checkingIcon.classList.add('hidden');
        availableIcon.classList.add('hidden');
        takenIcon.classList.add('hidden');
        messageContainer.classList.add('hidden');
        
        // Validate input
        if (domainInput.length < 2) {
            return;
        }
        
        // Show checking state
        iconContainer.classList.remove('hidden');
        checkingIcon.classList.remove('hidden');
        
        // Debounce API call
        domainCheckTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/api/check-domain?domain=${encodeURIComponent(domainInput)}`);
                const data = await response.json();
                
                checkingIcon.classList.add('hidden');
                
                if (data.available) {
                    // Domain available
                    availableIcon.classList.remove('hidden');
                    messageContainer.innerHTML = '<span class="text-success font-medium">✓ Domain tersedia</span>';
                    messageContainer.classList.remove('hidden');
                    requestedDomain.classList.remove('border-error', 'focus:border-error', 'focus:ring-error');
                    requestedDomain.classList.add('border-success', 'focus:border-success', 'focus:ring-success');
                } else {
                    // Domain taken
                    takenIcon.classList.remove('hidden');
                    messageContainer.innerHTML = '<span class="text-error font-medium">✗ Domain sudah digunakan</span>';
                    messageContainer.classList.remove('hidden');
                    requestedDomain.classList.remove('border-success', 'focus:border-success', 'focus:ring-success');
                    requestedDomain.classList.add('border-error', 'focus:border-error', 'focus:ring-error');
                }
            } catch (error) {
                console.error('Error checking domain:', error);
                checkingIcon.classList.add('hidden');
                iconContainer.classList.add('hidden');
            }
        }, 500); // Wait 500ms after user stops typing
    }
});
</script>
@endpush
@endsection
