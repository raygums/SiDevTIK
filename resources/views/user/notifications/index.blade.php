@extends('layouts.dashboard')

@section('title', 'Notifikasi Aktivitas')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifikasi & Aktivitas</h1>
            <p class="mt-2 text-gray-600">Lihat aktivitas dan notifikasi pengajuan Anda</p>
        </div>
        @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-myunila px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila-dark">
                <x-icon name="check" class="h-4 w-4" />
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Stats --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Total Notifikasi</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $notifications->total() }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Belum Dibaca</p>
            <p class="mt-2 text-2xl font-bold text-warning">{{ $unreadCount }}</p>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @if($notifications->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="bell" class="mx-auto h-12 w-12 text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Notifikasi</h3>
            <p class="mt-2 text-sm text-gray-500">Belum ada aktivitas atau notifikasi untuk pengajuan Anda</p>
        </div>
        @else
        <ul class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
            <li class="hover:bg-gray-50 transition">
                <a href="{{ route('notifications.show', $notification) }}" class="block p-4 sm:p-6">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full 
                                @if($notification->type === 'user_registered') bg-blue-100
                                @elseif($notification->type === 'user_activated') bg-green-100
                                @elseif($notification->type === 'submission_status_changed') bg-purple-100
                                @else bg-gray-100
                                @endif">
                                @if($notification->type === 'user_registered')
                                <x-icon name="user-plus" class="h-5 w-5 text-blue-600" />
                                @elseif($notification->type === 'user_activated')
                                <x-icon name="check-circle" class="h-5 w-5 text-green-600" />
                                @elseif($notification->type === 'submission_status_changed')
                                <x-icon name="document-check" class="h-5 w-5 text-purple-600" />
                                @else
                                <x-icon name="bell" class="h-5 w-5 text-gray-600" />
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        {{ $notification->title }}
                                        @if(!$notification->is_read)
                                        <span class="ml-2 inline-flex items-center rounded-full bg-myunila-100 px-2 py-1 text-xs font-medium text-myunila">
                                            Baru
                                        </span>
                                        @endif
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                                    
                                    @if($notification->relatedSubmission)
                                    <div class="mt-2 text-xs text-gray-500">
                                        📋 Tiket: <span class="font-mono font-medium">{{ $notification->relatedSubmission->no_tiket }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-xs text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Unread indicator --}}
                        @if(!$notification->is_read)
                        <div class="flex-shrink-0">
                            <span class="inline-block h-2.5 w-2.5 rounded-full bg-myunila"></span>
                        </div>
                        @endif
                    </div>
                </a>
            </li>
            @empty
            <li class="p-6 text-center text-gray-500">
                Tidak ada notifikasi
            </li>
            @endforelse
        </ul>

        {{-- Pagination --}}
        @if($notifications->hasPages())
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $notifications->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
