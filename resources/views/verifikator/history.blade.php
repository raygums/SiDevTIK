@extends('layouts.app')

@section('title', 'Riwayat Verifikasi')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Verifikasi</h1>
                <p class="text-gray-600">Pengajuan yang sudah Anda verifikasi</p>
            </div>
            <a href="{{ route('verifikator.index') }}" class="btn-secondary inline-flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            @if($submissions->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada riwayat</h3>
                <p class="mt-2 text-gray-500">Riwayat verifikasi akan muncul di sini.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No. Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pemohon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Update</th>
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
                            <td class="whitespace-nowrap px-6 py-4">
                                @php $statusName = $submission->status?->nm_status ?? '-'; @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if(str_contains($statusName, 'Disetujui')) bg-success-light text-success
                                    @elseif(str_contains($statusName, 'Ditolak')) bg-danger-light text-danger
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $submission->last_update?->format('d M Y, H:i') ?? '-' }}
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
