<x-app-layout title="Dashboard Mahasiswa">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Pengajuan</h1>
        
        <x-primary-button type="button" onclick="alert('Buka Modal!')">
            + Buat Pengajuan Baru
        </x-primary-button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-status-card title="Total Pengajuan" count="12" color="blue">
             <x-slot name="icon">📂</x-slot>
        </x-status-card>
        
        <x-status-card title="Disetujui" count="8" color="green">
             <x-slot name="icon">✅</x-slot>
        </x-status-card>

        <x-status-card title="Ditolak" count="1" color="red">
             <x-slot name="icon">❌</x-slot>
        </x-status-card>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">hima-if.unila.ac.id</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Disetujui
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                    </td>
                </tr>
                </tbody>
        </table>
    </div>

</x-app-layout>