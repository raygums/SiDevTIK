@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $submission->ticket_number)

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('submissions.index') }}" class="mb-4 inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-myunila">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Pengajuan
            </a>
            
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Detail Pengajuan</h1>
                    <p class="mt-1 font-mono text-lg text-myunila">{{ $submission->ticket_number }}</p>
                </div>
                
                {{-- Status Badge --}}
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-700',
                        'submitted' => 'bg-info-light text-info',
                        'in_review' => 'bg-warning-light text-warning',
                        'approved_admin' => 'bg-myunila-100 text-myunila',
                        'processing' => 'bg-myunila-200 text-myunila-700',
                        'completed' => 'bg-success-light text-success',
                        'rejected' => 'bg-error-light text-error',
                    ];
                    $statusDots = [
                        'draft' => 'bg-gray-500',
                        'submitted' => 'bg-info',
                        'in_review' => 'bg-warning',
                        'approved_admin' => 'bg-myunila',
                        'processing' => 'bg-myunila-700',
                        'completed' => 'bg-success',
                        'rejected' => 'bg-error',
                    ];
                @endphp
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium {{ $statusColors[$submission->status] ?? 'bg-gray-100 text-gray-700' }}">
                    <span class="h-2 w-2 rounded-full {{ $statusDots[$submission->status] ?? 'bg-gray-500' }}"></span>
                    {{ $submission->status_label }}
                </div>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Layanan Info --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Informasi Layanan</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @php $detail = $submission->getMainDetail(); @endphp
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Jenis Layanan</span>
                            <span class="font-medium text-gray-900">{{ $submission->request_type_label }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Nama Aplikasi</span>
                            <span class="font-medium text-gray-900">{{ $submission->application_name }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Domain Diminta</span>
                            <span class="font-mono font-medium text-myunila">{{ $detail?->requested_domain }}.unila.ac.id</span>
                        </div>
                        @if($detail?->requested_quota_gb)
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Kuota Storage</span>
                            <span class="font-medium text-gray-900">{{ $detail->requested_quota_gb }} GB</span>
                        </div>
                        @endif
                        <div class="px-6 py-4">
                            <span class="text-gray-600">Deskripsi/Keperluan</span>
                            <p class="mt-2 text-gray-900">{{ $submission->description }}</p>
                        </div>
                    </div>
                </div>

                {{-- Unit & Atasan --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Unit Kerja & Penanggung Jawab</h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Unit Kerja</span>
                            <span class="font-medium text-gray-900">{{ $submission->unit->name }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Kategori</span>
                            <span class="font-medium text-gray-900">{{ $submission->unit->category->name }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Nama Atasan</span>
                            <span class="font-medium text-gray-900">{{ $submission->admin_responsible_name }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">Jabatan</span>
                            <span class="font-medium text-gray-900">{{ $submission->admin_responsible_position }}</span>
                        </div>
                        <div class="flex justify-between px-6 py-4">
                            <span class="text-gray-600">No. HP</span>
                            <span class="font-medium text-gray-900">{{ $submission->admin_responsible_phone }}</span>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Dokumen</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            {{-- Signed Form --}}
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $submission->signed_form_path ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-400' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Formulir Bertanda Tangan</p>
                                        @if($submission->signed_form_path)
                                            <a href="{{ Storage::url($submission->signed_form_path) }}" target="_blank" class="text-sm text-myunila hover:underline">
                                                Lihat Dokumen →
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-500">Belum diupload</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Identity --}}
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $submission->attachment_identity_path ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-400' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Identitas (KTM/Karpeg)</p>
                                        @if($submission->attachment_identity_path)
                                            <a href="{{ Storage::url($submission->attachment_identity_path) }}" target="_blank" class="text-sm text-myunila hover:underline">
                                                Lihat Dokumen →
                                            </a>
                                        @else
                                            <p class="text-sm text-gray-500">Belum diupload</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($submission->status === 'draft')
                            <div class="mt-4">
                                <a href="{{ route('submissions.upload', $submission) }}" class="btn-primary inline-flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload Dokumen
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Applicant Info --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Pemohon</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-unila text-lg font-bold text-white">
                                {{ strtoupper(substr($submission->applicant->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $submission->applicant->name }}</p>
                                <p class="text-sm text-gray-500">{{ $submission->applicant->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                        <h2 class="font-semibold text-gray-900">Riwayat Aktivitas</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($submission->logs->sortByDesc('created_at') as $log)
                                <div class="flex gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-myunila-100 text-xs font-medium text-myunila">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm">
                                            <span class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</span>
                                            <span class="text-gray-600">{{ $log->note }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="p-6">
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dibuat</span>
                                <span class="font-medium text-gray-900">{{ $submission->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir Update</span>
                                <span class="font-medium text-gray-900">{{ $submission->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
