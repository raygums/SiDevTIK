@extends('layouts.dashboard')

@section('title', 'Dashboard Eksekutor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Eksekutor</h1>
            <p class="mt-2 text-sm text-gray-600">Selamat datang kembali, {{ Auth::user()->nm }}</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Tugas Baru -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="clipboard-list" class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Tugas Baru</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['tugas_baru'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('eksekutor.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat daftar tugas →
                    </a>
                </div>
            </div>

            <!-- Sedang Dikerjakan -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="clock" class="h-8 w-8 text-orange-600" />
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Sedang Dikerjakan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['sedang_dikerjakan'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('eksekutor.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                        Lanjutkan pekerjaan →
                    </a>
                </div>
            </div>

            <!-- Selesai Hari Ini -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon name="check-circle" class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Selesai Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['selesai_hari_ini'] }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('eksekutor.my-history') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                        Lihat riwayat →
                    </a>
                </div>
            </div>
        </div>

        <!-- Tugas Terbaru -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Tugas Terbaru</h2>
                    <a href="{{ route('eksekutor.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua
                    </a>
                </div>
            </div>

            @if($tasks->isEmpty())
                <div class="px-6 py-12 text-center">
                    <x-icon name="inbox" class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada tugas</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada pengajuan yang perlu dikerjakan saat ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Tiket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tasks as $task)
                                @php
                                    $serviceType = strtolower($task->jenisLayanan->nm_layanan ?? 'unknown');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $task->no_tiket }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $task->pengguna->nm ?? '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $task->unitKerja->nm_lmbg ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($serviceType === 'vps') badge-service-vps
                                            @elseif($serviceType === 'domain') badge-service-domain
                                            @elseif($serviceType === 'hosting') badge-service-hosting
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($task->status->nm_status === 'Sedang Dikerjakan') bg-orange-100 text-orange-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ $task->status->nm_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($task->tgl_pengajuan)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('eksekutor.show', $task->UUID) }}" class="text-blue-600 hover:text-blue-900">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
