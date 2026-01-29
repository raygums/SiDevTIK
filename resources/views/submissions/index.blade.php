@extends('layouts.app')

@section('title', 'Daftar Pengajuan Saya')

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Pengajuan Saya</h1>
                <p class="mt-1 text-gray-600">Daftar semua permohonan layanan yang Anda ajukan</p>
            </div>
            
            <a href="{{ url('/') }}#layanan" class="btn-primary inline-flex items-center justify-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pengajuan Baru
            </a>
        </div>

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

        {{-- Submissions List --}}
        @if($submissions->count() > 0)
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-myunila-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">No. Tiket</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Layanan</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Domain</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($submissions as $submission)
                                <tr class="transition hover:bg-myunila-50/50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="font-mono text-sm font-medium text-myunila">{{ $submission->ticket_number }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @php $detail = $submission->getMainDetail(); @endphp
                                            @if($detail?->request_type === 'domain')
                                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-myunila-100 text-myunila">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-myunila-200 text-myunila-700">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                                    </svg>
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-900">{{ $submission->request_type_label }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="font-mono text-sm text-gray-700">{{ $detail?->requested_domain }}.unila.ac.id</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $statusColors[$submission->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $statusDots[$submission->status] ?? 'bg-gray-500' }}"></span>
                                            {{ $submission->status_label }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $submission->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($submission->status === 'draft')
                                                <a href="{{ route('submissions.upload', $submission) }}" class="rounded-lg bg-myunila-100 px-3 py-1.5 text-xs font-medium text-myunila transition hover:bg-myunila-200">
                                                    Upload
                                                </a>
                                            @endif
                                            <a href="{{ route('submissions.show', $submission) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-200">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($submissions->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        @else
            {{-- Empty State --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-myunila-100">
                    <svg class="h-8 w-8 text-myunila" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900">Belum Ada Pengajuan</h3>
                <p class="mb-6 text-gray-600">Anda belum memiliki pengajuan layanan. Mulai dengan membuat pengajuan baru.</p>
                <a href="{{ url('/') }}#layanan" class="btn-primary inline-flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Pengajuan Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
