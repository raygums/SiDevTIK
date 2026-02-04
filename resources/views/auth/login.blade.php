<x-guest-layout>
    {{-- Mobile Logo (visible on small screens only) --}}
    <div class="mb-8 text-center lg:hidden">
        <img src="{{ asset('images/be-strong.png') }}" alt="Be Strong Unila" class="mx-auto mb-4 h-16 w-auto" onerror="this.style.display='none'">
        <h1 class="text-2xl font-bold text-myunila">DOMAINTIK</h1>
        <p class="mt-1 text-sm text-gray-500">Sistem Layanan Domain & Hosting</p>
        <p class="text-xs text-gray-400">Universitas Lampung</p>
    </div>

    {{-- Success Message (After Registration) --}}
    @if (session('success') && session('registered'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Registrasi Berhasil!</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>{{ session('success') }} Silakan login setelah akun Anda diverifikasi oleh admin.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- General Success/Error Messages --}}
    @if (session('success') && !session('registered'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Welcome Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Selamat Datang</h2>
        <p class="mt-2 text-sm text-gray-500">
            Silakan login untuk melanjutkan
        </p>
    </div>

    {{-- Local Login Form --}}
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf

        {{-- Username Field --}}
        <div>
            <label for="username" class="mb-2 block text-sm font-medium text-gray-700">
                Username <span class="text-error">*</span>
            </label>
            <input 
                id="username" 
                name="username" 
                type="text" 
                autocomplete="username" 
                required 
                autofocus
                value="{{ old('username') }}"
                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-myunila focus:outline-none focus:ring-2 focus:ring-myunila/20 sm:text-sm"
                placeholder="Masukkan Akun Pengguna">
            @error('username')
                <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Field --}}
        <div>
            <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                Password <span class="text-error">*</span>
            </label>
            <input 
                id="password" 
                name="password" 
                type="password" 
                autocomplete="current-password" 
                required
                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-myunila focus:outline-none focus:ring-2 focus:ring-myunila/20 sm:text-sm"
                placeholder="Masukkan Kata Sandi">
            @error('password')
                <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input 
                    id="remember-me" 
                    name="remember" 
                    type="checkbox" 
                    class="h-4 w-4 rounded border-gray-300 text-myunila transition focus:ring-2 focus:ring-myunila/20">
                <label for="remember-me" class="ml-2 block text-sm text-gray-600">
                    Ingat Saya
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-myunila transition hover:text-myunila-700">
                    Lupa Password?
                </a>
            @endif
        </div>

        {{-- Login Button --}}
        <div>
            <button 
                type="submit" 
                class="w-full rounded-xl bg-myunila px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-myunila/30 transition hover:bg-myunila-700 hover:shadow-xl hover:shadow-myunila/40 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
                Masuk
            </button>
        </div>
    </form>

    {{-- Divider --}}
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm text-center">
                <span class="bg-white px-4 text-gray-500">Khusus pengguna SSO UNILA</span>
            </div>
        </div>
    </div>

    {{-- SSO Login Button --}}
    <div class="mt-6">
        <a 
            href="{{ route('sso.login') }}" 
            class="group flex w-full items-center justify-center gap-3 rounded-xl border-2 border-myunila-200 bg-white px-4 py-3 text-sm font-semibold text-myunila shadow-sm transition hover:border-myunila hover:bg-myunila-50 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2">
            <svg class="h-5 w-5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>LOGIN SSO UNILA</span>
        </a>
    </div>

    {{-- Register Link --}}
    <div class="mt-6 rounded-lg bg-gray-50 p-4 text-center">
        <p class="text-sm text-gray-600">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="font-semibold text-myunila transition hover:text-myunila-700 hover:underline">
                Daftar di sini
            </a>
        </p>
    </div>
</x-guest-layout>