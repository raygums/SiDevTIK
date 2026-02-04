<x-register-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Registrasi Akun</h2>
        <p class="mt-1 text-sm text-gray-500">Daftar untuk mengajukan layanan domain dan hosting</p>
    </div>

    {{-- Error Message --}}
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="ml-3 text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                    <ul class="mt-1 list-inside list-disc text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Registration Form with 2 Columns --}}
    <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Grid 2 Columns --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
            
            {{-- Kolom Kiri --}}
            <div class="space-y-5">
                {{-- Nama Lengkap --}}
                <div class="h-[88px]">
                    <label for="nm" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="nm" 
                        name="nm" 
                        type="text" 
                        required 
                        autofocus
                        value="{{ old('nm') }}"
                        class="block w-full h-[44px] px-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm"
                        placeholder="Nama Lengkap">
                    @error('nm')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Peran --}}
                <div class="h-[88px]">
                    <label for="peran" class="block text-sm font-medium text-gray-700 mb-2">
                        Peran <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="peran" 
                        name="peran" 
                        required
                        class="block w-full h-[44px] px-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm">
                        <option value="">Pilih Peran</option>
                        <option value="mahasiswa" {{ old('peran') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('peran') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="karyawan" {{ old('peran') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                    @error('peran')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat Email --}}
                <div class="h-[88px]">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required
                        value="{{ old('email') }}"
                        class="block w-full h-[44px] px-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kata Sandi --}}
                <div class="h-[88px]">
                    <label for="kata_sandi" class="block text-sm font-medium text-gray-700 mb-2">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            id="kata_sandi" 
                            name="kata_sandi" 
                            type="password" 
                            required
                            autocomplete="new-password"
                            class="block w-full h-[44px] px-4 pr-12 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm"
                            placeholder="Masukkan Kata Sandi">
                        <button 
                            type="button" 
                            onclick="togglePassword('kata_sandi', 'eye_kata_sandi', 'eye_slash_kata_sandi')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg id="eye_kata_sandi" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye_slash_kata_sandi" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('kata_sandi')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @else
                        <p class="mt-1.5 text-xs text-gray-500">Min. 8 karakter, huruf besar/kecil, angka & simbol</p>
                    @enderror
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-5">
                {{-- Username --}}
                <div class="h-[88px]">
                    <label for="usn" class="block text-sm font-medium text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="usn" 
                        name="usn" 
                        type="text" 
                        required
                        value="{{ old('usn') }}"
                        class="block w-full h-[44px] px-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm"
                        placeholder="username">
                    @error('usn')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @else
                        <p class="mt-1.5 text-xs text-gray-500">Huruf kecil, angka, titik, underscore</p>
                    @enderror
                </div>

                {{-- Nomor Identitas --}}
                <div class="h-[88px]">
                    <label for="nomor_identitas" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor (NIK/NPM/NIP/NIDN) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="nomor_identitas" 
                        name="nomor_identitas" 
                        type="text" 
                        required
                        maxlength="50"
                        value="{{ old('nomor_identitas') }}"
                        class="block w-full h-[44px] px-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm"
                        placeholder="Masukkan Nomor">
                    @error('nomor_identitas')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Lahir --}}
                <div class="h-[88px]">
                    <label for="tgl_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="tgl_lahir" 
                        name="tgl_lahir" 
                        type="date" 
                        required
                        max="{{ date('Y-m-d') }}"
                        value="{{ old('tgl_lahir') }}"
                        class="block w-full h-[44px] px-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm">
                    @error('tgl_lahir')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div class="h-[88px]">
                    <label for="kata_sandi_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            id="kata_sandi_confirmation" 
                            name="kata_sandi_confirmation" 
                            type="password" 
                            required
                            autocomplete="new-password"
                            class="block w-full h-[44px] px-4 pr-12 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-myunila focus:ring-myunila text-sm"
                            placeholder="Konfirmasi Kata Sandi">
                        <button 
                            type="button" 
                            onclick="togglePassword('kata_sandi_confirmation', 'eye_confirmation', 'eye_slash_confirmation')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg id="eye_confirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye_slash_confirmation" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upload File KTP/KTM (Full Width Below) --}}
        <div class="pt-2">
            <label for="file_ktp_ktm" class="block text-sm font-medium text-gray-700 mb-1.5 text-center">
                Upload KTP/KTM <span class="text-red-500">*</span>
            </label>
            <div class="max-w-md mx-auto">
                <input 
                    id="file_ktp_ktm" 
                    name="file_ktp_ktm" 
                    type="file" 
                    required
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-myunila file:text-white hover:file:bg-myunila-700">
                @error('file_ktp_ktm')
                    <p class="mt-1 text-xs text-red-600 text-center">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 text-center">JPG, PNG, WEBP. Max: 2MB</p>
            </div>
        </div>

        {{-- Register Button --}}
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full rounded-xl bg-myunila px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-myunila/30 transition hover:bg-myunila-700 hover:shadow-xl hover:shadow-myunila/40 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                Daftar Sekarang
            </button>
        </div>
    </form>

    {{-- Bottom Actions --}}
    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
        <p class="text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-semibold text-myunila transition hover:text-myunila-700 hover:underline">
                Login di sini
            </a>
        </p>
        
        <div class="rounded-lg bg-amber-50 px-3 py-2 border border-amber-200">
            <p class="text-xs text-amber-800 flex items-center">
                <svg class="h-4 w-4 mr-1 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">Akun menunggu verifikasi admin</span>
            </p>
        </div>
    </div>

    {{-- Password Toggle Script --}}
    <script>
        function togglePassword(inputId, eyeId, eyeSlashId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeSlash = document.getElementById(eyeSlashId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.add('hidden');
                eyeSlash.classList.remove('hidden');
            } else {
                input.type = 'password';
                eye.classList.remove('hidden');
                eyeSlash.classList.add('hidden');
            }
        }
    </script>
</x-register-layout>
