<x-guest-layout>
    <div class="text-center py-10 px-4">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-6 animate-pulse">
            <svg class="h-10 w-10 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Pendaftaran Berhasil!</h2>
        
        @if(session('user_name'))
            <p class="text-lg text-indigo-600 font-semibold mb-4">Halo, {{ session('user_name') }}</p>
        @endif

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 text-left rounded shadow-sm">
            <p class="text-yellow-800">
                Akun Anda telah terhubung dengan SSO, namun saat ini status akun Anda 
                <strong>MENUNGGU PERSETUJUAN (PENDING)</strong>.
            </p>
        </div>

        <p class="text-gray-500 mb-8 leading-relaxed">
            Admin sistem kami perlu memverifikasi data Anda sebelum memberikan akses penuh 
            ke layanan Domain TIK. Proses ini biasanya memakan waktu 1x24 jam kerja.
        </p>

        <div class="flex justify-center gap-4">
            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition">
                &larr; Kembali ke Login
            </a>
            
            <a href="https://wa.me/628123456789" target="_blank" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition shadow-md">
                Hubungi Admin
            </a>
        </div>
    </div>
</x-guest-layout>