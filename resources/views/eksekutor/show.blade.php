@extends('layouts.app')

@section('title', 'Detail Pengajuan - Eksekutor')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('eksekutor.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-myunila">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

        {{-- Header --}}
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $submission->no_tiket }}</h1>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                            @if($submission->status?->nm_status === 'Disetujui Verifikator') bg-warning-light text-warning
                            @elseif($submission->status?->nm_status === 'Sedang Dikerjakan') bg-info-light text-info
                            @elseif($submission->status?->nm_status === 'Selesai') bg-success-light text-success
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $submission->status?->nm_status ?? 'Unknown' }}
                        </span>
                    </div>
                    <p class="mt-2 text-gray-600">Diajukan pada {{ $submission->tgl_pengajuan?->format('d F Y H:i') }}</p>
                </div>
                <div class="text-right">
                    @php $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain'; @endphp
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium
                        @if($serviceType === 'vps') bg-purple-100 text-purple-800
                        @elseif($serviceType === 'hosting') bg-blue-100 text-blue-800
                        @else bg-green-100 text-green-800
                        @endif">
                        @if($serviceType === 'vps')
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                        @else
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        @endif
                        {{ ucfirst($serviceType) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main Content --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Applicant Info --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Informasi Pemohon</h2>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-gray-900">{{ $submission->pengguna?->nm ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIP</dt>
                            <dd class="mt-1 font-mono text-gray-900">{{ $submission->pengguna?->no_ident ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unit Kerja</dt>
                            <dd class="mt-1 text-gray-900">{{ $submission->unitKerja?->nm_lmbg ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-gray-900">{{ $submission->pengguna?->email ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Request Details --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Detail Permintaan</h2>
                    
                    @if($submission->rincian)
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Domain/Subdomain</dt>
                            <dd class="mt-1 font-mono text-lg font-semibold text-myunila">{{ $submission->rincian->nm_domain ?? '-' }}</dd>
                        </div>
                        @if($submission->rincian->keterangan)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Keterangan/Deskripsi</dt>
                            <dd class="mt-1 text-gray-900">{{ $submission->rincian->keterangan }}</dd>
                        </div>
                        @endif
                        @if($submission->rincian->tujuan)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tujuan Penggunaan</dt>
                            <dd class="mt-1 text-gray-900">{{ $submission->rincian->tujuan }}</dd>
                        </div>
                        @endif
                        @if($submission->rincian->ip_tujuan)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP Tujuan</dt>
                            <dd class="mt-1 font-mono text-gray-900">{{ $submission->rincian->ip_tujuan }}</dd>
                        </div>
                        @endif
                    </dl>
                    @else
                    <p class="text-gray-500">Tidak ada detail yang tersedia.</p>
                    @endif
                </div>

                {{-- Verification Note --}}
                @if($verificationLog)
                <div class="rounded-2xl border border-teal-200 bg-teal-50 p-6">
                    <h3 class="mb-3 flex items-center gap-2 font-semibold text-teal-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Catatan Verifikasi
                    </h3>
                    <div class="text-sm text-teal-700">
                        <p>Diverifikasi oleh <strong>{{ $verificationLog->admin?->nm ?? 'Unknown' }}</strong></p>
                        <p class="text-xs text-teal-600">{{ $verificationLog->create_at?->format('d M Y H:i') }}</p>
                        @if($verificationLog->catatan)
                        <p class="mt-2 rounded-lg bg-white/50 p-3">{{ $verificationLog->catatan }}</p>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Action Forms --}}
                @if($submission->status?->nm_status === 'Disetujui Verifikator')
                <div class="rounded-2xl border-2 border-purple-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900">Aksi Eksekutor</h2>
                    
                    <div class="grid gap-6 sm:grid-cols-2">
                        {{-- Accept Form --}}
                        <div class="rounded-xl border border-info/30 bg-info-light p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-medium text-info">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Mulai Kerjakan
                            </h3>
                            <p class="mb-4 text-sm text-info/80">Pengajuan akan masuk ke daftar pekerjaan Anda.</p>
                            <form action="{{ route('eksekutor.accept', $submission) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-info px-4 py-2 text-sm font-medium text-white hover:bg-info/90 focus:outline-none focus:ring-2 focus:ring-info focus:ring-offset-2">
                                    Terima & Kerjakan
                                </button>
                            </form>
                        </div>

                        {{-- Reject Form --}}
                        <div class="rounded-xl border border-danger/30 bg-danger-light p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-medium text-danger">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tolak Pengajuan
                            </h3>
                            <form action="{{ route('eksekutor.reject', $submission) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="alasan_penolakan" class="mb-1 block text-sm font-medium text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea 
                                        name="alasan_penolakan" 
                                        id="alasan_penolakan" 
                                        rows="3"
                                        required
                                        placeholder="Jelaskan kendala atau alasan mengapa pengajuan tidak bisa dieksekusi..."
                                        class="w-full rounded-lg border border-danger/30 bg-white p-3 text-sm focus:border-danger focus:outline-none focus:ring-2 focus:ring-danger/20"
                                    ></textarea>
                                </div>
                                <button type="submit" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-medium text-white hover:bg-danger/90 focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2">
                                    Tolak Pengajuan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Complete/Reject for In Progress --}}
                @if($submission->status?->nm_status === 'Sedang Dikerjakan')
                <div class="rounded-2xl border-2 border-info/30 bg-white p-6 shadow-sm">
                    <h2 class="mb-6 text-lg font-semibold text-gray-900">Selesaikan Pekerjaan</h2>
                    
                    <div class="grid gap-6 sm:grid-cols-2">
                        {{-- Complete Form --}}
                        <div class="rounded-xl border border-success/30 bg-success-light p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-medium text-success">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tandai Selesai
                            </h3>
                            <p class="mb-4 text-sm text-success/80">Tandai pengajuan sebagai selesai dikerjakan.</p>
                            <form action="{{ route('eksekutor.complete', $submission) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="hasil_eksekusi" class="mb-1 block text-sm font-medium text-success">Hasil Eksekusi</label>
                                    <textarea 
                                        name="hasil_eksekusi" 
                                        id="hasil_eksekusi" 
                                        rows="3"
                                        placeholder="Jelaskan hasil pekerjaan, konfigurasi yang diterapkan, dsb..."
                                        class="w-full rounded-lg border border-success/30 bg-white p-3 text-sm focus:border-success focus:outline-none focus:ring-2 focus:ring-success/20"
                                    ></textarea>
                                </div>
                                <button type="submit" class="w-full rounded-lg bg-success px-4 py-2 text-sm font-medium text-white hover:bg-success/90 focus:outline-none focus:ring-2 focus:ring-success focus:ring-offset-2">
                                    Tandai Selesai
                                </button>
                            </form>
                        </div>

                        {{-- Reject Form (Kendala) --}}
                        <div class="rounded-xl border border-danger/30 bg-danger-light p-4">
                            <h3 class="mb-3 flex items-center gap-2 font-medium text-danger">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Kendala/Tidak Bisa Dilanjutkan
                            </h3>
                            <form action="{{ route('eksekutor.reject', $submission) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="alasan_kendala" class="mb-1 block text-sm font-medium text-danger">Keterangan Kendala <span class="text-danger">*</span></label>
                                    <textarea 
                                        name="alasan_penolakan" 
                                        id="alasan_kendala" 
                                        rows="3"
                                        required
                                        placeholder="Jelaskan kendala teknis yang menghalangi penyelesaian..."
                                        class="w-full rounded-lg border border-danger/30 bg-white p-3 text-sm focus:border-danger focus:outline-none focus:ring-2 focus:ring-danger/20"
                                    ></textarea>
                                </div>
                                <button type="submit" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-medium text-white hover:bg-danger/90 focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2">
                                    Laporkan Kendala
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status Timeline --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-gray-900">Riwayat Status</h3>
                    <div class="space-y-4">
                        @forelse($logs as $log)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full 
                                    @if(str_contains($log->status?->nm_status ?? '', 'Ditolak')) bg-danger-light text-danger
                                    @elseif(str_contains($log->status?->nm_status ?? '', 'Selesai')) bg-success-light text-success
                                    @elseif(str_contains($log->status?->nm_status ?? '', 'Dikerjakan')) bg-info-light text-info
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        @if(str_contains($log->status?->nm_status ?? '', 'Ditolak'))
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        @elseif(str_contains($log->status?->nm_status ?? '', 'Selesai'))
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                </div>
                                @if(!$loop->last)
                                <div class="h-full w-px bg-gray-200"></div>
                                @endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-medium text-gray-900">{{ $log->status?->nm_status ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $log->create_at?->format('d M Y H:i') }}</p>
                                @if($log->catatan)
                                <p class="mt-1 text-xs text-gray-600">{{ Str::limit($log->catatan, 100) }}</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Belum ada riwayat.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Info --}}
                <div class="rounded-2xl border border-purple-200 bg-purple-50 p-6">
                    <h3 class="mb-3 font-semibold text-purple-900">Info Eksekutor</h3>
                    <ul class="space-y-2 text-sm text-purple-800">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Terima untuk mulai mengerjakan
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Setelah selesai, tandai sebagai "Selesai"
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Jika ada kendala, laporkan dengan alasan jelas
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
