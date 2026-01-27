<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi SSO Unila</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-blue-900">SSO Simulation</h1>
            <p class="text-gray-500 text-sm">Gerbang Masuk Sementara (Development)</p>
        </div>

        <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Login Sebagai (Group SSO)</label>
                <select name="sso_group" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
                    <option value="mahasiswa">Mahasiswa (Default)</option>
                    <option value="dosen">Dosen</option>
                    <option value="tendik">Tendik / Staff</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">*Ini simulasi data yang dikirim server SSO</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">NIP / NPM</label>
                <input type="text" name="nip" required placeholder="Contoh: 2215061001" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Nama User" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border">
            </div>

            <div class="bg-yellow-50 p-3 rounded text-xs text-yellow-800">
                <strong>Cheat Sheet Login:</strong>
                <ul class="list-disc ml-4 mt-1">
                    <li><b>Mahasiswa:</b> Sembarang NPM</li>
                    <li><b>Verifikator (Siti):</b> 198702152011012002</li>
                    <li><b>Eksekutor (Andi):</b> 199003202015011003</li>
                    <li><b>Super Admin:</b> 198501012010011001</li>
                </ul>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Masuk via SSO Dummy
            </button>
        </form>
    </div>

</body>
</html>