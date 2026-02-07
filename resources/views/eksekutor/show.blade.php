@extends('layouts.dashboard')

@section('title', 'Review Pengajuan - ' . $submission->no_tiket)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('eksekutor.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-myunila">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali ke Daftar Tugas
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
                    @php $statusName = $submission->status?->nm_status ?? '-'; @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                        @if($statusName === 'Disetujui Verifikator') bg-warning-light text-warning
                        @elseif($statusName === 'Sedang Dikerjakan') bg-info-light text-info
                        @elseif($statusName === 'Selesai') bg-success-light text-success
                        @elseif($statusName === 'Ditolak Eksekutor') bg-danger-light text-danger
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $statusName }}
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

            {{-- Catatan Verifikasi --}}
            @if($verificationLog && $verificationLog->catatan_log)
            <div>
                <h3 class="mb-3 font-semibold text-gray-900">Catatan Verifikasi</h3>
                <div class="rounded-lg border-l-4 border-teal-500 bg-teal-50 p-4">
                    <div class="flex items-start gap-3">
                        <x-icon name="information-circle" class="h-5 w-5 text-teal-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">{{ $verificationLog->catatan_log }}</p>
                            <p class="mt-2 text-xs text-gray-500">
                                oleh {{ $verificationLog->creator?->nm ?? 'Verifikator' }} • 
                                {{ $verificationLog->create_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Riwayat Status Timeline --}}
            @if($logs->isNotEmpty())
            <div>
                <h3 class="mb-4 font-semibold text-gray-900">Riwayat Status</h3>
                <div class="relative space-y-0">
                    @foreach($logs as $log)
                    <div class="relative flex gap-3 pb-6 last:pb-0">
                        {{-- Timeline Line --}}
                        @if(!$loop->last)
                        <div class="absolute left-3 top-6 -bottom-0 w-0.5 bg-gray-200"></div>
                        @endif

                        {{-- Icon --}}
                        <div class="relative z-10 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full 
                            @if($log->statusBaru?->nm_status === 'Selesai') bg-success text-white
                            @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Ditolak')) bg-danger text-white
                            @elseif($log->statusBaru?->nm_status === 'Sedang Dikerjakan') bg-info text-white
                            @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Disetujui')) bg-warning text-white
                            @else bg-myunila text-white
                            @endif">
                            @if($log->statusBaru?->nm_status === 'Selesai')
                            <x-icon name="check" class="h-3 w-3" />
                            @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Ditolak'))
                            <x-icon name="x-mark" class="h-3 w-3" />
                            @elseif($log->statusBaru?->nm_status === 'Sedang Dikerjakan')
                            <x-icon name="cog" class="h-3 w-3" />
                            @elseif(str_contains($log->statusBaru?->nm_status ?? '', 'Disetujui'))
                            <x-icon name="check-circle" class="h-3 w-3" />
                            @else
                            <x-icon name="clock" class="h-3 w-3" />
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 pt-0.5">
                            <p class="text-sm font-semibold text-gray-900">{{ $log->statusBaru?->nm_status ?? '-' }}</p>
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

    {{-- Keputusan Eksekusi --}}
    @if($statusName === 'Disetujui Verifikator')
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 font-semibold text-gray-900">Keputusan Eksekusi</h3>
        
        <div class="grid gap-6 sm:grid-cols-2">
            {{-- Mulai Kerjakan Card --}}
            <div class="decision-card rounded-xl border-2 border-gray-200 bg-white p-4 cursor-pointer transition-all hover:shadow-md" data-type="accept">
                <div class="mb-4 flex items-center gap-3" onclick="selectDecisionEksekutor('accept')">
                    <div class="card-icon flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="card-title font-semibold text-gray-700 transition-colors">Mulai Kerjakan</p>
                        <p class="text-xs text-gray-500">Terima dan mulai eksekusi</p>
                    </div>
                </div>
                <form action="{{ route('eksekutor.accept', $submission) }}" method="POST" class="card-form" style="display: none;" onclick="event.stopPropagation()">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-info focus:ring-info resize-none" placeholder="Catatan pekerjaan..."></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-info px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-info/90 hover:shadow-md">
                        Mulai Kerjakan
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-4 rounded-lg bg-blue-50 p-4">
            <div class="flex gap-3">
                <x-icon name="information-circle" class="h-5 w-5 text-blue-600 flex-shrink-0" />
                <div class="text-sm text-blue-800">
                    <p class="font-medium">Informasi:</p>
                    <ul class="mt-1 list-disc list-inside space-y-1">
                        <li>Klik "Mulai Kerjakan" untuk mengambil tugas ini</li>
                        <li>Status akan berubah menjadi "Sedang Dikerjakan"</li>
                        <li>Setelah selesai, gunakan tombol "Selesai" untuk menyelesaikan tugas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Selesaikan Pekerjaan --}}
    @if($statusName === 'Sedang Dikerjakan')
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 font-semibold text-gray-900">Selesaikan Pekerjaan</h3>
        
        <div class="grid gap-6 sm:grid-cols-2">
            {{-- Complete Card --}}
            <div class="decision-card rounded-xl border-2 border-gray-200 bg-white p-4 cursor-pointer transition-all hover:shadow-md" data-type="complete">
                <div class="mb-4 flex items-center gap-3" onclick="selectDecisionEksekutor('complete')">
                    <div class="card-icon flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="card-title font-semibold text-gray-700 transition-colors">Tandai Selesai</p>
                        <p class="text-xs text-gray-500">Pekerjaan telah diselesaikan</p>
                    </div>
                </div>
                <form action="{{ route('eksekutor.complete', $submission) }}" method="POST" class="card-form" style="display: none;" onclick="event.stopPropagation()">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasil Eksekusi <span class="text-error">*</span></label>
                        <textarea name="hasil_eksekusi" rows="4" required class="block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-success focus:ring-success resize-none @error('hasil_eksekusi') border-error @enderror" placeholder="Jelaskan hasil pekerjaan, konfigurasi yang diterapkan, informasi akses, dll..."></textarea>
                        @error('hasil_eksekusi')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" rows="2" class="block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm transition focus:border-success focus:ring-success resize-none" placeholder="Catatan tambahan..."></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-success px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-success/90 hover:shadow-md">
                        Tandai Selesai
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-4 rounded-lg bg-green-50 p-4">
            <div class="flex gap-3">
                <x-icon name="check-circle" class="h-5 w-5 text-green-600 flex-shrink-0" />
                <p class="text-sm text-green-800">
                    <span class="font-medium">Pastikan hasil eksekusi diisi dengan lengkap</span> agar pemohon mendapat informasi yang jelas tentang layanan yang telah dikonfigurasi.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Info Selesai/Ditolak --}}
    @if(in_array($statusName, ['Selesai', 'Ditolak Eksekutor']))
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            @if($statusName === 'Selesai')
            <x-icon name="check-circle" class="h-8 w-8 text-success" />
            <div>
                <h3 class="font-semibold text-gray-900">Pekerjaan Selesai</h3>
                <p class="text-sm text-gray-600">Pengajuan telah diselesaikan</p>
            </div>
            @else
            <x-icon name="x-circle" class="h-8 w-8 text-danger" />
            <div>
                <h3 class="font-semibold text-gray-900">Pekerjaan Ditolak</h3>
                <p class="text-sm text-gray-600">Pengajuan tidak dapat diselesaikan</p>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

<script>
function selectDecisionEksekutor(type) {
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
                if (type === 'accept') {
                    card.classList.remove('border-info', 'bg-info-light/50');
                } else if (type === 'complete') {
                    card.classList.remove('border-success', 'bg-success-light/50');
                }
                card.classList.add('border-gray-200', 'bg-white');
                
                cardIcon.classList.remove(type === 'accept' ? 'bg-info' : 'bg-success');
                cardIcon.classList.remove('text-white');
                cardIcon.classList.add('bg-gray-200', 'text-gray-500');
                
                cardTitle.classList.remove(type === 'accept' ? 'text-info' : 'text-success');
                cardTitle.classList.add('text-gray-700');
                
                cardForm.style.display = 'none';
            } else {
                // Select
                card.classList.add('selected');
                card.classList.remove('border-gray-200', 'bg-white');
                if (type === 'accept') {
                    card.classList.add('border-info', 'bg-info-light/50');
                } else if (type === 'complete') {
                    card.classList.add('border-success', 'bg-success-light/50');
                }
                
                cardIcon.classList.remove('bg-gray-200', 'text-gray-500');
                cardIcon.classList.add(type === 'accept' ? 'bg-info' : 'bg-success');
                cardIcon.classList.add('text-white');
                
                cardTitle.classList.remove('text-gray-700');
                cardTitle.classList.add(type === 'accept' ? 'text-info' : 'text-success');
                
                cardForm.style.display = 'block';
            }
        } else {
            // Deselect other card
            card.classList.remove('selected');
            card.classList.remove('border-info', 'border-success');
            card.classList.remove('bg-info-light/50', 'bg-success-light/50');
            card.classList.add('border-gray-200', 'bg-white');
            
            cardIcon.classList.remove('bg-info', 'bg-success', 'text-white');
            cardIcon.classList.add('bg-gray-200', 'text-gray-500');
            
            cardTitle.classList.remove('text-info', 'text-success');
            cardTitle.classList.add('text-gray-700');
            
            cardForm.style.display = 'none';
        }
    });
}
</script>

@endsection
