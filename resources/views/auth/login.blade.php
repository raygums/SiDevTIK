<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Image & Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="{{ asset('images/unila.jpg') }}" alt="Gedung Rektorat" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900/70 to-gray-900/50"></div>
            </div>
            
            <!-- Content Overlay -->
            <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                <!-- Top Logos -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/be-strong.png') }}" alt="Be Strong" class="h-16 w-auto object-contain">
                </div>
                
                <!-- Bottom Text -->
                <div>
                    <h1 class="text-4xl font-bold mb-2">SISTEM PENGAJUAN LAYANAN DOMAIN DAN HOSTING UNIVERSITAS LAMPUNG</h1>
                    <p class="text-lg font-light">Sistem Informasi Layanan Digital Terpadu Universitas Lampung</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
                    <p class="text-gray-500">Silakan login untuk melanjutkan</p>
                </div>

                <!-- Error Messages -->
                @if(session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-lg text-center">
                        {{ session('status') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded-lg text-center border border-red-200">
                        Login Gagal. Silakan coba lagi atau hubungi admin.
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="Masukkan Akun Pengguna" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               required>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan Kata Sandi" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               required>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lupa Password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" 
                            class="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        Masuk
                    </button>
                </form>

                <!-- SSO Login -->
                <div class="mt-6">
                    <p class="text-center text-sm text-gray-500 mb-3">Khusus pengguna dosen/tendik</p>
                    <a href="{{ route('auth.sso.redirect') }}" 
                       class="w-full flex items-center justify-center gap-2 py-3 px-4 border-2 border-cyan-500 text-cyan-600 font-semibold rounded-lg hover:bg-cyan-50 transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        LOGIN SSO UNILA
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>