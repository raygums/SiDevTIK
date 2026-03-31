@extends('layouts.dashboard')

@section('title', 'Timeline - ' . $submission->no_tiket)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-myunila">
            <x-icon name="arrow-left" class="h-5 w-5" />
            Kembali
        </a>
    </div>

    {{-- Header Card --}}
    <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $submission->no_tiket }}</h1>
                    @php $statusName = $submission->status?->nm_status ?? '-'; @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                        @if(str_contains($statusName, 'Disetujui')) bg-success-light text-success
                        @elseif(str_contains($statusName, 'Ditolak')) bg-danger-light text-danger
                        @elseif($statusName === 'Sedang Dikerjakan') bg-info-light text-info
                        @elseif($statusName === 'Selesai') bg-success-light text-success
                        @elseif($statusName === 'Diajukan') bg-warning-light text-warning
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $statusName }}
                    </span>
                </div>
                <p class="mt-2 text-gray-600">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
            </div>
            <div class="text-right">
                @php $serviceType = strtolower($submission->jenisLayanan?->nm_layanan ?? 'domain'); @endphp
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium
                    @if($serviceType === 'vps') badge-service-vps
                    @elseif($serviceType === 'hosting') badge-service-hosting
                    @else badge-service-domain
                    @endif">
                    @if($serviceType === 'vps')
                    <x-icon name="server" class="h-5 w-5" />
                    @elseif($serviceType === 'hosting')
                    <x-icon name="server" class="h-5 w-5" />
                    @else
                    <x-icon name="globe-alt" class="h-5 w-5" />
                    @endif
                    {{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}
                </span>
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="mt-6 grid gap-4 border-t border-gray-100 pt-6 sm:grid-cols-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Pemohon</p>
                <p class="mt-1 text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Unit Kerja</p>
                <p class="mt-1 text-gray-900">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Tanggal Pengajuan</p>
                <p class="mt-1 text-gray-900">{{ $submission->create_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h2 class="font-semibold text-gray-900">Timeline Perubahan Status</h2>
        </div>

        @if($logs->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="clock" class="mx-auto h-16 w-16 text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada riwayat</h3>
            <p class="mt-2 text-gray-500">Timeline perubahan status akan muncul di sini.</p>
        </div>
        @else
        <div class="p-6">
            <div class="relative space-y-0">
                @foreach($logs as $index => $log)
                <div class="relative flex gap-4 pb-8 last:pb-0">
                    {{-- Timeline Line --}}
                    @if(!$loop->last)
                    <div class="absolute left-4 top-8 -bottom-0 w-0.5 bg-gray-200"></div>
                    @endif

                    {{-- Icon --}}
                    <div class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full 
                        @if($log->statusBaru?->nm_status === 'Selesai') bg-success-light text-success
                        @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Ditolak')) bg-danger-light text-danger
                        @elseif($log->statusBaru?->nm_status === 'Sedang Dikerjakan') bg-info-light text-info
                        @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Disetujui')) bg-success-light text-success
                        @elseif($log->statusBaru?->nm_status === 'Diajukan') bg-warning-light text-warning
                        @else bg-gray-100 text-gray-600
                        @endif">
                        @if($log->statusBaru?->nm_status === 'Selesai')
                        <x-icon name="check" class="h-4 w-4" />
                        @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Ditolak'))
                        <x-icon name="x-mark" class="h-4 w-4" />
                        @elseif($log->statusBaru?->nm_status === 'Sedang Dikerjakan')
                        <x-icon name="cog" class="h-4 w-4" />
                        @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Disetujui'))
                        <x-icon name="check-circle" class="h-4 w-4" />
                        @else
                        <x-icon name="clock" class="h-4 w-4" />
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition">
                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">
                                    {{ $log->statusBaru?->nm_status ?? 'Status Diperbarui' }}
                                </h4>
                                @if($log->statusLama)
                                <p class="text-sm text-gray-500 mt-1">
                                    Dari: <span class="font-medium">{{ $log->statusLama->nm_status }}</span>
                                </p>
                                @endif
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($log->statusBaru?->nm_status === 'Selesai') bg-success-light text-success
                                @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Ditolak')) bg-danger-light text-danger
                                @elseif($log->statusBaru?->nm_status === 'Sedang Dikerjakan') bg-info-light text-info
                                @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Disetujui')) bg-success-light text-success
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $log->statusBaru?->nm_status ?? '-' }}
                            </span>
                        </div>

                        {{-- Catatan --}}
                        @if($log->catatan_log)
                        <div class="mt-3 rounded-lg bg-gray-50 p-3">
                            <p class="text-sm text-gray-700">{{ $log->catatan_log }}</p>
                        </div>
                        @endif

                        {{-- Footer: Diubah Oleh & Time --}}
                        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-gray-500">Diubah oleh:</span>
                                <span class="font-medium text-gray-900">{{ $log->creator?->nm ?? 'System' }}</span>
                                @if($log->creator?->peran)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                    {{ $log->creator?->peran?->nm_peran ?? 'Unknown' }}
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <x-icon name="clock" class="h-4 w-4" />
                                <span>{{ $log->create_at?->format('d M Y, H:i') }} WIB</span>
                                <span class="text-gray-400">•</span>
                                <span>{{ $log->create_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
