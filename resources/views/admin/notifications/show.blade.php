@extends('layouts.dashboard')

@section('title', 'Detail Notifikasi - Admin')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Back Button --}}
    <a href="{{ route('admin.notifications.index') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-myunila">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali ke Notifikasi
    </a>

    {{-- Notification Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="border-b border-gray-200 bg-gradient-to-r from-myunila-50 to-myunila-100 px-6 py-6">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full 
                    @if($notification->type === 'user_registered') bg-blue-100
                    @elseif($notification->type === 'user_activated') bg-green-100
                    @elseif($notification->type === 'submission_status_changed') bg-purple-100
                    @else bg-gray-100
                    @endif">
                    @if($notification->type === 'user_registered')
                    <x-icon name="user-plus" class="h-6 w-6 text-blue-600" />
                    @elseif($notification->type === 'user_activated')
                    <x-icon name="check-circle" class="h-6 w-6 text-green-600" />
                    @elseif($notification->type === 'submission_status_changed')
                    <x-icon name="document-check" class="h-6 w-6 text-purple-600" />
                    @else
                    <x-icon name="bell" class="h-6 w-6 text-gray-600" />
                    @endif
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $notification->title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $notification->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6 space-y-6">
            {{-- Message --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Pesan</h3>
                <div class="rounded-lg bg-gray-50 p-4 text-gray-700 whitespace-pre-line">
                    {{ $notification->message }}
                </div>
            </div>

            {{-- Related User --}}
            @if($notification->relatedUser)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Pengguna Terkait</h3>
                <div class="rounded-lg bg-blue-50 p-4 border border-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                            <x-icon name="user" class="h-5 w-5 text-blue-600" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $notification->relatedUser->nm }}</p>
                            <p class="text-sm text-gray-600">{{ $notification->relatedUser->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Related Submission --}}
            @if($notification->relatedSubmission)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Pengajuan Terkait</h3>
                <div class="rounded-lg bg-purple-50 p-4 border border-purple-100 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">No. Tiket:</span>
                        <span class="font-mono font-semibold text-gray-900">{{ $notification->relatedSubmission->no_tiket }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Jenis Layanan:</span>
                        <span class="font-semibold text-gray-900">{{ $notification->relatedSubmission->jenisLayanan?->nm_layanan ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                            {{ $notification->relatedSubmission->status?->nm_status ?? '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Tanggal Pengajuan:</span>
                        <span class="text-gray-900">{{ $notification->relatedSubmission->create_at?->format('d M Y, H:i') ?? '-' }}</span>
                    </div>
                    <div class="pt-2 border-t border-purple-200">
                        <a href="{{ route('verifikator.show', $notification->relatedSubmission) }}" 
                           class="inline-flex items-center gap-2 rounded-lg bg-purple-100 px-3 py-2 text-sm font-semibold text-purple-600 transition hover:bg-purple-200">
                            <x-icon name="arrow-top-right" class="h-4 w-4" />
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Type Badge --}}
            <div class="border-t border-gray-200 pt-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700">
                    <x-icon name="tag" class="h-3.5 w-3.5" />
                    {{ $notification->type }}
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex gap-3">
            @if(!$notification->is_read)
            <form action="{{ route('admin.notifications.mark-read', $notification) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-success px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-success/90">
                    <x-icon name="check" class="h-4 w-4" />
                    Tandai Dibaca
                </button>
            </form>
            @endif
            <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <x-icon name="trash" class="h-4 w-4" />
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
