@extends('layouts.dashboard')

@section('title', 'Detail Aktivitas Pengguna')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Back Button & Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.audit.aktivitas') }}" 
           class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-myunila hover:underline">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Kembali ke Log Login
        </a>
        
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    Detail Aktivitas Pengguna
                </h1>
                <p class="mt-2 text-gray-600">
                    Timeline aktivitas lengkap untuk {{ $user->nm }}
                </p>
            </div>
        </div>
    </div>

    {{-- User Profile Card --}}
    <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-6">
            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-full bg-gradient-unila text-2xl font-bold text-white shadow-lg">
                {{ strtoupper(substr($user->nm, 0, 2)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">{{ $user->nm }}</h2>
                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <x-icon name="mail" class="h-4 w-4 text-gray-400" />
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <x-icon name="user" class="h-4 w-4 text-gray-400" />
                        <span>{{ $user->usn }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <x-icon name="shield-check" class="h-4 w-4 text-gray-400" />
                        <span>{{ $user->peran?->nm_peran ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        @if($user->a_aktif)
                        <span class="inline-flex items-center rounded-full bg-success-light px-2.5 py-0.5 text-xs font-medium text-success">
                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-success"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                            Non-Aktif
                        </span>
                        @endif
                    </div>
                </div>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @if($user->last_login_at)
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <x-icon name="clock" class="h-4 w-4" />
                        <span>Login Terakhir: {{ $user->last_login_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    @if($user->last_login_ip)
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <x-icon name="globe" class="h-4 w-4" />
                        <span class="font-mono">IP: {{ $user->last_login_ip }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Timeline --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Timeline Aktivitas</h2>
                <span class="text-sm text-gray-500">{{ $timeline->total() }} aktivitas</span>
            </div>
        </div>

        @if($timeline->isEmpty())
        <div class="p-12 text-center">
            <x-icon name="exclamation-circle" class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Aktivitas</h3>
            <p class="mt-2 text-sm text-gray-500">User belum memiliki riwayat aktivitas</p>
        </div>
        @else
        <div class="p-6">
            <div class="relative space-y-6">
                {{-- Timeline Line --}}
                <div class="absolute left-[21px] top-0 h-full w-0.5 bg-gray-200"></div>

                @foreach($timeline as $activity)
                {{-- Activity Item --}}
                <div class="relative flex gap-4">
                    {{-- Timeline Dot --}}
                    <div class="relative z-10 flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full border-4 border-white 
                        {{ $activity['type'] === 'login' ? 'bg-success' : 'bg-info' }} shadow-sm">
                        @if($activity['type'] === 'login')
                            <x-icon name="login" class="h-5 w-5 text-white" />
                        @else
                            <x-icon name="document-text" class="h-5 w-5 text-white" />
                        @endif
                    </div>

                    {{-- Activity Content --}}
                    <div class="flex-1 rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                {{-- Activity Type --}}
                                <div class="mb-2 flex items-center gap-2">
                                    @if($activity['type'] === 'login')
                                    <span class="inline-flex items-center rounded-full bg-success-light px-2.5 py-0.5 text-xs font-medium text-success">
                                        Login Activity
                                    </span>
                                    @else
                                    <span class="inline-flex items-center rounded-full bg-info-light px-2.5 py-0.5 text-xs font-medium text-info">
                                        Status Change
                                    </span>
                                    @endif
                                </div>

                                {{-- Activity Details --}}
                                @if($activity['type'] === 'login')
                                <p class="text-sm text-gray-900">
                                    <span class="font-semibold">{{ $activity['data']['user_name'] }}</span> 
                                    melakukan login ke sistem
                                </p>
                                @if($activity['ip_address'])
                                <p class="mt-1 text-xs text-gray-500">
                                    <x-icon name="globe" class="inline h-3 w-3" />
                                    IP Address: <span class="font-mono">{{ $activity['ip_address'] }}</span>
                                </p>
                                @endif
                                @else
                                <p class="text-sm text-gray-900">
                                    Pengajuan 
                                    <span class="font-mono font-semibold text-myunila">{{ $activity['data']['ticket_number'] }}</span>
                                    diubah statusnya
                                </p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                        {{ $activity['data']['status_old'] }}
                                    </span>
                                    <x-icon name="arrow-right" class="h-3 w-3 text-gray-400" />
                                    @php
                                        $newStatus = $activity['data']['status_new'];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        @if($newStatus === 'Selesai') bg-success-light text-success
                                        @elseif(str_contains($newStatus, 'Ditolak')) bg-danger-light text-danger
                                        @elseif($newStatus === 'Sedang Dikerjakan') bg-info-light text-info
                                        @elseif($newStatus === 'Diajukan') bg-warning-light text-warning
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        {{ $newStatus }}
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Layanan: 
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        @if($activity['data']['service_type'] === 'vps') bg-purple-100 text-purple-800
                                        @elseif($activity['data']['service_type'] === 'hosting') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($activity['data']['service_type']) }}
                                    </span>
                                </div>
                                @if($activity['data']['notes'])
                                <p class="mt-2 text-xs text-gray-600">
                                    <strong>Catatan:</strong> {{ $activity['data']['notes'] }}
                                </p>
                                @endif
                                @endif
                            </div>

                            {{-- Timestamp --}}
                            <div class="ml-4 flex-shrink-0 text-right">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $activity['timestamp']->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $activity['timestamp']->format('H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        @if($timeline->hasPages())
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            {{ $timeline->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
