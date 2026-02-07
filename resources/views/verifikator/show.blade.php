@extends('layouts.dashboard')

@section('title', 'Review Pengajuan - ' . $submission->no_tiket)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('verifikator.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-myunila">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali ke Daftar Pengajuan
        </a>
    </div>

        {{-- Header --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Tiket</p>
                        <p class="font-mono text-xl font-bold text-myunila">{{ $submission->no_tiket }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-flex items-center rounded-full bg-warning-light px-3 py-1 text-sm font-medium text-warning">
                            {{ $submission->status?->nm_status ?? 'Menunggu Verifikasi' }}
                        </span>
                    </div>
                </div>
            </div>

            @php
                $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain';
                $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);
            @endphp

            <div class="p-6 space-y-6">
                {{-- Info Pemohon --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Informasi Pemohon</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Nama</p>
                            <p class="font-medium text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Unit Kerja</p>
                            <p class="font-medium text-gray-900">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Organisasi</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['nama_organisasi'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Tanggal Pengajuan</p>
                            <p class="font-medium text-gray-900">{{ $submission->create_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Info Layanan --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Detail Layanan</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-myunila-50 p-4">
                            <p class="text-xs text-myunila-700">Jenis Layanan</p>
                            <p class="font-semibold text-myunila">{{ $serviceType === 'vps' ? 'VPS' : ucfirst($serviceType) }}</p>
                        </div>
                        <div class="rounded-lg bg-myunila-50 p-4">
                            <p class="text-xs text-myunila-700">Domain/Hostname</p>
                            <p class="font-mono font-semibold text-myunila">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                        </div>
                    </div>

                    @if($serviceType === 'vps')
                    <div class="mt-4 grid gap-4 sm:grid-cols-4">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">OS</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['VPS']['os'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">CPU</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['VPS']['cpu'] ?? '-' }} Core</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">RAM</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['VPS']['ram'] ?? '-' }} GB</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Storage</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['VPS']['storage'] ?? '-' }} GB</p>
                        </div>
                    </div>
                    @elseif($serviceType === 'hosting')
                    <div class="mt-4">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Kuota Storage</p>
                            <p class="font-medium text-gray-900">{{ $keterangan['hosting']['quota'] ?? '-' }} MB</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Kontak --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Kontak</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs font-medium text-gray-500">Penanggung Jawab Administratif</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $keterangan['admin']['name'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['admin']['email'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['admin']['phone'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="text-xs font-medium text-gray-500">Penanggung Jawab Teknis</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $keterangan['teknis']['name'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['teknis']['email'] ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $keterangan['teknis']['phone'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Lihat Dokumen Formulir --}}
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Dokumen</h3>
                    <a href="{{ route('forms.hardcopy.preview', $submission->no_tiket) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg border border-myunila bg-myunila-50 px-4 py-2.5 text-sm font-medium text-myunila transition hover:bg-myunila hover:text-white">
                        <x-icon name="document-text" class="h-5 w-5" />
                        Lihat Formulir Lengkap
                        <x-icon name="external-link" class="h-4 w-4" />
                    </a>
                </div>

                {{-- Tujuan Penggunaan --}}
                @if(!empty($keterangan['tujuan_penggunaan']))
                <div>
                    <h3 class="mb-3 font-semibold text-gray-900">Tujuan Penggunaan</h3>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="whitespace-pre-line text-sm text-gray-700">{{ $keterangan['tujuan_penggunaan'] }}</p>
                    </div>
                </div>
                @endif

                {{-- Riwayat Status Timeline --}}
                @if($submission->riwayat->isNotEmpty())
                <div>
                    <h3 class="mb-4 font-semibold text-gray-900">Riwayat Status</h3>
                    <div class="relative space-y-0">
                        @foreach($submission->riwayat as $log)
                        <div class="relative flex gap-3 pb-6 last:pb-0">
                            {{-- Timeline Line --}}
                            @if(!$loop->last)
                            <div class="absolute left-3 top-6 -bottom-0 w-0.5 bg-gray-200"></div>
                            @endif

                            {{-- Icon --}}
                            <div class="relative z-10 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full 
                                @if($log->status?->nm_status === 'Selesai') bg-success text-white
                                @elseif(str_contains($log->status?->nm_status ?? '', 'Ditolak')) bg-danger text-white
                                @elseif($log->status?->nm_status === 'Sedang Dikerjakan') bg-info text-white
                                @elseif(str_contains($log->status?->nm_status ?? '', 'Disetujui')) bg-warning text-white
                                @else bg-myunila text-white
                                @endif">
                                @if($log->status?->nm_status === 'Selesai')
                                <x-icon name="check" class="h-3 w-3" />
                                @elseif(str_contains($log->status?->nm_status ?? '', 'Ditolak'))
                                <x-icon name="x-mark" class="h-3 w-3" />
                                @elseif($log->status?->nm_status === 'Sedang Dikerjakan')
                                <x-icon name="cog" class="h-3 w-3" />
                                @elseif(str_contains($log->status?->nm_status ?? '', 'Disetujui'))
                                <x-icon name="check-circle" class="h-3 w-3" />
                                @else
                                <x-icon name="clock" class="h-3 w-3" />
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 pt-0.5">
                                <p class="text-sm font-semibold text-gray-900">{{ $log->status?->nm_status ?? '-' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $log->create_at?->format('d M Y, H:i') }}
                                    @if($log->creator)
                                    • {{ $log->creator->nm }}
                                    @endif
                                </p>
                                @if($log->catatan_log)
                                <p class="mt-2 text-sm text-gray-600 rounded bg-gray-50 p-2">{{ $log->catatan_log }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-900">Keputusan Verifikasi</h3>
            
            <div class="grid gap-6 sm:grid-cols-2">
                {{-- Approve Card --}}
                <div class="decision-card rounded-xl border-2 border-gray-200 bg-white p-4 cursor-pointer transition-all hover:shadow-md" data-type="approve">
                    <div class="mb-4 flex items-center gap-3" onclick="selectDecision('approve')">
                        <div class="card-icon flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="card-title font-semibold text-gray-700 transition-colors">Setujui Pengajuan</p>
                            <p class="text-xs text-gray-500">Teruskan ke Eksekutor</p>
                        </div>
                    </div>
                    <form action="{{ route('verifikator.approve', $submission) }}" method="POST" class="card-form" style="display: none;" onclick="event.stopPropagation()">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="4" class="block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-success focus:ring-success resize-none" placeholder="Catatan untuk eksekutor..."></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-success px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-success/90 hover:shadow-md">
                            Setujui & Teruskan
                        </button>
                    </form>
                </div>

                {{-- Reject Card --}}
                <div class="decision-card rounded-xl border-2 border-gray-200 bg-white p-4 cursor-pointer transition-all hover:shadow-md" data-type="reject">
                    <div class="mb-4 flex items-center gap-3" onclick="selectDecision('reject')">
                        <div class="card-icon flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="card-title font-semibold text-gray-700 transition-colors">Tolak Pengajuan</p>
                            <p class="text-xs text-gray-500">Kembalikan ke pemohon</p>
                        </div>
                    </div>
                    <form action="{{ route('verifikator.reject', $submission) }}" method="POST" class="card-form" style="display: none;" onclick="event.stopPropagation()">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-error">*</span></label>
                            <textarea name="alasan_penolakan" rows="4" required class="block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-error focus:ring-error resize-none @error('alasan_penolakan') border-error @enderror" placeholder="Jelaskan alasan penolakan..."></textarea>
                            @error('alasan_penolakan')
                            <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-error px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-error/90 hover:shadow-md">
                            Tolak Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectDecision(type) {
    // Get all decision cards
    const cards = document.querySelectorAll('.decision-card');
    
    cards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        const cardIcon = card.querySelector('.card-icon');
        const cardTitle = card.querySelector('.card-title');
        const cardForm = card.querySelector('.card-form');
        
        if (cardType === type) {
            // Selected card - toggle selection
            const isSelected = card.classList.contains('selected');
            
            if (isSelected) {
                // Deselect
                card.classList.remove('selected');
                card.classList.remove(type === 'approve' ? 'border-success' : 'border-error');
                card.classList.remove(type === 'approve' ? 'bg-success-light/50' : 'bg-error-light/50');
                card.classList.add('border-gray-200', 'bg-white');
                
                cardIcon.classList.remove(type === 'approve' ? 'bg-success' : 'bg-error');
                cardIcon.classList.remove('text-white');
                cardIcon.classList.add('bg-gray-200', 'text-gray-500');
                
                cardTitle.classList.remove(type === 'approve' ? 'text-success' : 'text-error');
                cardTitle.classList.add('text-gray-700');
                
                cardForm.style.display = 'none';
            } else {
                // Select
                card.classList.add('selected');
                card.classList.remove('border-gray-200', 'bg-white');
                card.classList.add(type === 'approve' ? 'border-success' : 'border-error');
                card.classList.add(type === 'approve' ? 'bg-success-light/50' : 'bg-error-light/50');
                
                cardIcon.classList.remove('bg-gray-200', 'text-gray-500');
                cardIcon.classList.add(type === 'approve' ? 'bg-success' : 'bg-error');
                cardIcon.classList.add('text-white');
                
                cardTitle.classList.remove('text-gray-700');
                cardTitle.classList.add(type === 'approve' ? 'text-success' : 'text-error');
                
                cardForm.style.display = 'block';
            }
        } else {
            // Deselect other card
            card.classList.remove('selected');
            card.classList.remove('border-success', 'border-error');
            card.classList.remove('bg-success-light/50', 'bg-error-light/50');
            card.classList.add('border-gray-200', 'bg-white');
            
            cardIcon.classList.remove('bg-success', 'bg-error', 'text-white');
            cardIcon.classList.add('bg-gray-200', 'text-gray-500');
            
            cardTitle.classList.remove('text-success', 'text-error');
            cardTitle.classList.add('text-gray-700');
            
            cardForm.style.display = 'none';
        }
    });
}
</script>

@endsection
