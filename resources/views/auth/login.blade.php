<x-guest-layout>
    <div class="lg:hidden mb-8 text-center">
        <img src="{{ asset('images/be-strong.png') }}" alt="Logo" class="h-16 w-auto mx-auto mb-4">
    </div>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-900">Selamat Datang</h2>
        <p class="mt-2 text-sm text-slate-500">
            Silakan login untuk melanjutkan
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center gap-3 text-sm text-green-700">
            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                Username / Email <span class="text-red-500">*</span>
            </label>
            <input id="email" name="email" type="email" autocomplete="email" required autofocus
                class="block w-full rounded-md border border-slate-300 py-2.5 px-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition"
                placeholder="Masukkan Akun Pengguna" 
                value="{{ old('email') }}">
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                Password <span class="text-red-500">*</span>
            </label>
            <input id="password" name="password" type="password" autocomplete="current-password" required
                class="block w-full rounded-md border border-slate-300 py-2.5 px-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition"
                placeholder="Masukkan Kata Sandi">
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember-me" name="remember" type="checkbox" 
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="remember-me" class="ml-2 block text-sm text-slate-600">Ingat Saya</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                    Lupa Password?
                </a>
            @endif
        </div>

        <div>
            <button type="submit" 
                class="w-full rounded-md bg-blue-600 py-3 px-4 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                Masuk
            </button>
        </div>
    </form>

    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-slate-500">ATAU</span>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('sso.login') }}" 
                class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 bg-white py-2.5 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <img src="{{ asset('images/logo-unila.png') }}" alt="SSO" class="h-5 w-5">
                Login dengan SSO Unila
            </a>
        </div>
    </div>
</x-guest-layout>