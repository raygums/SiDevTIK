<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Domain TIK - Tailwind Test</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center justify-center">

        <div class="max-w-4xl w-full p-6">
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white text-center">
                    <h1 class="text-4xl font-extrabold tracking-tight mb-2">
                        Domain TIK Project
                    </h1>
                    <p class="text-blue-100 text-lg font-medium">
                        Docker • Laravel • Tailwind CSS
                    </p>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="flex items-start space-x-4 p-4 rounded-lg hover:bg-gray-50 transition duration-300">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-xl">
                                ✓
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Tailwind Berfungsi!</h3>
                            <p class="text-gray-500 mt-1">
                                Jika Anda melihat ikon hijau dan layout yang rapi, artinya Tailwind CSS berhasil di-build.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4 p-4 rounded-lg hover:bg-gray-50 transition duration-300">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xl">
                                ⚙
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Build Production</h3>
                            <p class="text-gray-500 mt-1">
                                Saat ini berjalan dalam mode dev (dinamis), sehingga tidak perlu melakukan refresh browser kembali. Gunakan <code class="bg-gray-200 px-1 py-0.5 rounded text-sm text-red-500">npm run dev</code> jika mengubah tampilan.
                            </p>
                        </div>
                    </div>

                </div>

                <div class="bg-gray-50 p-6 border-t border-gray-100 text-center">
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-full shadow-lg transform transition hover:-translate-y-1 hover:scale-105 duration-200">
                        Tombol Interaktif
                    </button>
                    <p class="mt-4 text-xs text-gray-400">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </p>
                </div>

            </div>
        </div>

    </body>
</html>