@extends('layouts.app')

@section('title', 'Download Formulir - ' . $submission->ticket_number)

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        
        {{-- Success Card --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-success/30 bg-success-light p-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-success/20">
                <svg class="h-8 w-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="mb-2 text-2xl font-bold text-gray-900">Formulir Berhasil Dibuat!</h1>
            <p class="text-gray-600">Nomor Tiket: <strong class="font-mono text-success">{{ $submission->ticket_number }}</strong></p>
        </div>

        {{-- Steps Card --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Langkah Selanjutnya</h2>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-myunila text-sm font-bold text-white">
                            1
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Download & Cetak Formulir</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Klik tombol di bawah untuk mengunduh formulir PDF yang sudah terisi otomatis dengan data Anda.
                            </p>
                        </div>
                    </div>
                    
                    {{-- Step 2 --}}
                    <div class="flex gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-600">
                            2
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Minta Tanda Tangan</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Cetak formulir dan minta <strong>tanda tangan basah</strong> dari:
                            </p>
                            <ul class="mt-2 list-inside list-disc text-sm text-gray-600 space-y-1">
                                <li><strong>Kepala Divisi Pusat Infrastruktur TIK</strong> (Mengetahui)</li>
                                <li><strong>{{ $submission->admin_responsible_name }}</strong> - {{ $submission->admin_responsible_position }} (Pelanggan)</li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-600">
                            3
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Scan Dokumen</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Scan formulir yang sudah ditandatangani dalam format <strong>PDF</strong>. 
                                Siapkan juga foto/scan identitas Anda (KTM untuk mahasiswa, Karpeg untuk PNS/pegawai).
                            </p>
                        </div>
                    </div>
                    
                    {{-- Step 4 --}}
                    <div class="flex gap-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-600">
                            4
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Upload & Kirim</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Kembali ke halaman ini atau klik tombol "Upload Dokumen" untuk mengunggah scan formulir dan identitas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $metadata = is_array($submission->metadata) ? $submission->metadata : json_decode($submission->metadata ?? '{}', true);
            $jenisLabels = [
                'lembaga_fakultas' => 'Lembaga / Fakultas / Jurusan',
                'kegiatan_lembaga' => 'Kegiatan Lembaga / Fakultas / Jurusan',
                'organisasi_mahasiswa' => 'Organisasi Mahasiswa',
                'kegiatan_mahasiswa' => 'Kegiatan Mahasiswa',
                'lainnya' => 'Lain-lain',
            ];
        @endphp

        {{-- Preview Data --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Ringkasan Pengajuan</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="flex justify-between px-6 py-4">
                    <span class="text-gray-600">Jenis Domain</span>
                    <span class="font-medium text-gray-900">{{ $jenisLabels[$metadata['jenis_domain'] ?? ''] ?? 'Domain' }}</span>
                </div>
                <div class="flex justify-between px-6 py-4">
                    <span class="text-gray-600">Nama Lembaga/Organisasi</span>
                    <span class="font-medium text-gray-900">{{ $metadata['nama_organisasi'] ?? $submission->application_name }}</span>
                </div>
                <div class="flex justify-between px-6 py-4">
                    <span class="text-gray-600">Sub Domain Diminta</span>
                    <span class="font-mono font-medium text-myunila">{{ $submission->details->first()?->requested_domain }}.unila.ac.id</span>
                </div>
                <div class="flex justify-between px-6 py-4">
                    <span class="text-gray-600">Penanggung Jawab Administratif</span>
                    <span class="font-medium text-gray-900">{{ $submission->admin_responsible_name }}</span>
                </div>
                <div class="flex justify-between px-6 py-4">
                    <span class="text-gray-600">Penanggung Jawab Teknis</span>
                    <span class="font-medium text-gray-900">{{ $metadata['tech']['name'] ?? '-' }}</span>
                </div>
                <div class="flex justify-between px-6 py-4">
                    <span class="text-gray-600">Status</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700">
                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                        {{ $submission->status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('submissions.index') }}" class="btn-secondary inline-flex items-center justify-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Lihat Semua Pengajuan
            </a>
            
            <div class="flex flex-col gap-3 sm:flex-row">
                {{-- Download PDF Button --}}
                <a 
                    href="{{ route('submissions.print-form', $submission) }}" 
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-myunila-300 bg-myunila-50 px-6 py-3 font-semibold text-myunila transition hover:bg-myunila-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download/Print Formulir
                </a>
                
                {{-- Upload Button --}}
                <a href="{{ route('submissions.upload', $submission) }}" class="btn-primary inline-flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Dokumen
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Print Styles --}}
@push('styles')
<style>
@media print {
    nav, footer, .no-print { display: none !important; }
    body { background: white !important; }
    .print-only { display: block !important; }
}
</style>
@endpush

{{-- Printable Form (Hidden, shown only when printing) --}}
<div class="print-only hidden">
    @include('submissions.partials.printable-form', ['submission' => $submission])
</div>
@endsection
