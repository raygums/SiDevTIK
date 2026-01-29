@extends('layouts.app')

@section('title', 'Riwayat Eksekusi')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('eksekutor.index') }}" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Riwayat Eksekusi</h1>
                    <p class="text-gray-600">Daftar pengajuan yang telah Anda proses</p>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="mb-6 flex gap-2">
            <a href="{{ route('eksekutor.history') }}" 
               class="rounded-lg px-4 py-2 text-sm font-medium {{ !request('status') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('eksekutor.history', ['status' => 'selesai']) }}" 
               class="rounded-lg px-4 py-2 text-sm font-medium {{ request('status') === 'selesai' ? 'bg-success text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Selesai
            </a>
            <a href="{{ route('eksekutor.history', ['status' => 'ditolak']) }}" 
               class="rounded-lg px-4 py-2 text-sm font-medium {{ request('status') === 'ditolak' ? 'bg-danger text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Ditolak/Kendala
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            @if($submissions->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada riwayat</h3>
                <p class="mt-2 text-gray-500">Anda belum memproses pengajuan apapun.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No. Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pemohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @php $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain'; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($serviceType === 'vps') bg-purple-100 text-purple-800
                                    @elseif($serviceType === 'hosting') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($serviceType) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $submission->rincian?->nm_domain ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($submission->status?->nm_status === 'Selesai') bg-success-light text-success
                                    @elseif(str_contains($submission->status?->nm_status ?? '', 'Ditolak')) bg-danger-light text-danger
                                    @elseif($submission->status?->nm_status === 'Sedang Dikerjakan') bg-info-light text-info
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $submission->status?->nm_status ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $submission->last_update?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <a href="{{ route('eksekutor.show', $submission) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                {{ $submissions->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
