@extends('layouts.app')

@section('title', 'Formulir Pengajuan Sub Domain')

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
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-unila text-white shadow-lg shadow-myunila/30">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Formulir Permohonan Sub Domain</h1>
                    <p class="text-gray-600">Layanan Hosting Universitas Lampung</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('submissions.store') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="request_type" value="{{ $type }}">
            
            {{-- Section 1: Data Sub Domain --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">1</span>
                        Data Sub Domain
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        {{-- Jenis Domain --}}
                        <div>
                            <label class="mb-3 block text-sm font-medium text-gray-700">
                                Jenis Domain <span class="text-error">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                                @php
                                    $jenisOptions = [
                                        'lembaga_fakultas' => 'Lembaga / Fakultas / Jurusan',
                                        'kegiatan_lembaga' => 'Kegiatan Lembaga / Fakultas / Jurusan',
                                        'organisasi_mahasiswa' => 'Organisasi Mahasiswa',
                                        'kegiatan_mahasiswa' => 'Kegiatan Mahasiswa',
                                        'lainnya' => 'Lain-lain',
                                    ];
                                @endphp
                                @foreach($jenisOptions as $value => $label)
                                    <label class="relative flex cursor-pointer items-center justify-center rounded-xl border-2 border-gray-200 bg-white p-4 text-center transition hover:border-myunila-300 hover:bg-myunila-50 has-checked:border-myunila has-checked:bg-myunila-50">
                                        <input type="radio" name="jenis_domain" value="{{ $value }}" class="peer sr-only" {{ old('jenis_domain') == $value ? 'checked' : '' }} required>
                                        <span class="text-xs font-medium text-gray-700 peer-checked:text-myunila">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('jenis_domain')
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

            {{-- Section 2: Penanggung Jawab Administratif --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">2</span>
                        Penanggung Jawab Administratif
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Pejabat yang akan menandatangani formulir permohonan</p>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
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
                                No. Identitas (NIP/NPM)
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

            {{-- Section 3: Penanggung Jawab Teknis --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">3</span>
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
                                data-user-name="{{ $user->name }}"
                                class="form-input @error('tech_name') form-input-error @enderror"
                            >
                            @error('tech_name')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tech_nip" class="mb-1 block text-sm font-medium text-gray-700">
                                No. Identitas (NIP/NIM) <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="tech_nip" 
                                id="tech_nip"
                                value="{{ old('tech_nip') }}"
                                placeholder="NIP atau NIM"
                                required
                                data-user-nip="{{ $user->nomor_identitas }}"
                                class="form-input @error('tech_nip') form-input-error @enderror"
                            >
                            @error('tech_nip')
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
                                data-user-email="{{ $user->email }}"
                                class="form-input @error('tech_email') form-input-error @enderror"
                            >
                            @error('tech_email')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Nama Sub Domain yang Diminta --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-myunila text-xs font-bold text-white">4</span>
                        Nama Sub Domain yang Diminta
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="requested_domain" class="mb-1 block text-sm font-medium text-gray-700">
                                Sub Domain <span class="text-error">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    name="requested_domain" 
                                    id="requested_domain"
                                    value="{{ old('requested_domain') }}"
                                    placeholder="namadomain"
                                    minlength="2"
                                    maxlength="12"
                                    pattern="[a-z0-9\-]+"
                                    required
                                    class="form-input max-w-xs @error('requested_domain') form-input-error @enderror"
                                >
                                <span class="whitespace-nowrap text-lg font-semibold text-myunila">.unila.ac.id</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                <span class="font-medium">Ketentuan:</span> Minimal 2 karakter, maksimal 12 karakter. Hanya huruf kecil, angka, dan tanda hubung (-).
                            </p>
                            @error('requested_domain')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
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
            <input type="hidden" name="unit_id" value="{{ $categories->first()?->units->first()?->id }}">
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
                            Setelah submit, Anda akan diminta untuk:
                        </p>
                        <ol class="mt-2 list-inside list-decimal space-y-1 text-sm text-gray-700">
                            <li>Download formulir PDF yang sudah terisi otomatis</li>
                            <li>Cetak formulir dan minta <strong>tanda tangan basah</strong> dari Kepala Divisi Pusat Infrastruktur TIK (Mengetahui) dan Pelanggan</li>
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
document.addEventListener('DOMContentLoaded', function() {
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

    // Auto-fill hidden fields before submit
    document.querySelector('form').addEventListener('submit', function() {
        const namaOrg = document.getElementById('nama_organisasi').value;
        const domain = document.getElementById('requested_domain').value;
        document.getElementById('hidden_application_name').value = namaOrg || domain;
        document.getElementById('hidden_description').value = 'Permohonan Sub Domain untuk ' + namaOrg;
    });

    // Format domain input to lowercase
    document.getElementById('requested_domain')?.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '');
    });
});
</script>
@endpush
@endsection
