@extends('layouts.dashboard')

@section('title', 'Detail Pengguna - ' . $user->nm)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('pimpinan.users') }}" class="text-gray-400 transition hover:text-gray-600">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Detail Pengguna</h1>
                <p class="mt-1 text-gray-600">Informasi lengkap dan riwayat aktivitas pengguna.</p>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 rounded-xl border border-success/30 bg-success-light p-4">
        <div class="flex items-center gap-3">
            <x-icon name="check-circle" class="h-5 w-5 text-success" />
            <p class="text-sm font-medium text-success">{{ session('success') }}</p>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 rounded-xl border border-danger/30 bg-danger-light p-4">
        <div class="flex items-center gap-3">
            <x-icon name="x-circle" class="h-5 w-5 text-danger" />
            <p class="text-sm font-medium text-danger">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- User Profile Card --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gradient-to-r from-myunila to-myunila-700 p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-2xl font-bold text-white">
                    {{ strtoupper(substr($user->nm, 0, 2)) }}
                </div>
                <div class="flex-1 text-white">
                    <h2 class="text-xl font-bold">{{ $user->nm }}</h2>
                    <p class="text-white/80">{{ $user->email }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        @php
                            $roleName = $user->peran?->nm_peran ?? 'Pengguna';
                            $roleClass = match($roleName) {
                                'Administrator' => 'bg-purple-500 text-white',
                                'Verifikator' => 'bg-yellow-500 text-white',
                                'Eksekutor' => 'bg-green-500 text-white',
                                'Pimpinan' => 'bg-white text-myunila',
                                default => 'bg-blue-500 text-white',
                            };
                        @endphp
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleClass }}">
                            {{ $roleName }}
                        </span>
                        @if($user->a_aktif)
                        <span class="rounded-full bg-success px-2.5 py-0.5 text-xs font-medium text-white">Aktif</span>
                        @else
                        <span class="rounded-full bg-danger px-2.5 py-0.5 text-xs font-medium text-white">Non-Aktif</span>
                        @endif
                        @if($user->sso_id)
                        <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-medium text-white">SSO</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100">
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Username</span>
                <span class="font-medium text-gray-900">{{ $user->usn ?? '-' }}</span>
            </div>
            @if($user->ktp)
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">KTP</span>
                <span class="font-medium text-gray-900">{{ $user->ktp }}</span>
            </div>
            @endif
            @if($user->tgl_lahir)
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Tanggal Lahir</span>
                <span class="font-medium text-gray-900">{{ date('d M Y', strtotime($user->tgl_lahir)) }}</span>
            </div>
            @endif
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Terdaftar</span>
                <span class="text-gray-900">{{ $user->create_at?->format('d M Y, H:i') }} WIB</span>
            </div>
            @if($user->update_at)
            <div class="flex items-center justify-between px-6 py-4">
                <span class="text-sm text-gray-500">Terakhir Diperbarui</span>
                <span class="text-gray-900">{{ $user->update_at?->format('d M Y, H:i') }} WIB</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    @if($user->UUID !== auth()->user()->UUID)
    <div class="mb-6 space-y-4">
        {{-- Toggle Status Card --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Status Akun</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('pimpinan.users.toggle-status', $user->UUID) }}">
                    @csrf
                    @if($user->a_aktif)
                    <button type="submit" 
                            class="inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold text-white bg-red-600 hover:bg-red-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            onclick="return confirm('Nonaktifkan pengguna ini?')">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Nonaktifkan Akun
                    </button>
                    @else
                    <button type="submit" 
                            class="inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold text-white bg-green-600 hover:bg-green-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            onclick="return confirm('Aktifkan pengguna ini?')">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aktifkan Akun
                    </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- Change Role Card --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Ubah Role</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('pimpinan.users.change-role', $user->UUID) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="role_uuid" class="block text-sm font-medium text-gray-700 mb-2">Pilih Role Baru</label>
                        <select name="role_uuid" id="role_uuid" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-700 focus:border-myunila-500 focus:outline-none focus:ring-2 focus:ring-myunila-500/20">
                            @foreach($roles as $role)
                            <option value="{{ $role->UUID }}" {{ $user->peran_uuid === $role->UUID ? 'selected' : '' }}>
                                {{ $role->nm_peran }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold text-white bg-myunila hover:bg-myunila-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-myunila-500 focus:ring-offset-2" onclick="return confirm('Ubah role pengguna ini?')">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- User's Submissions --}}
    @if($submissions->count() > 0)
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Pengajuan Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($submissions as $submission)
            <a href="{{ route('submissions.show', $submission->UUID) }}" 
               class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50" target="_blank">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-myunila-50 text-myunila">
                        @if($submission->jenisLayanan?->nm_layanan === 'vps')
                        <x-icon name="server" class="h-5 w-5" />
                        @elseif($submission->jenisLayanan?->nm_layanan === 'hosting')
                        <x-icon name="server-stack" class="h-5 w-5" />
                        @else
                        <x-icon name="globe-alt" class="h-5 w-5" />
                        @endif
                    </div>
                    <div>
                        <p class="font-mono text-sm font-semibold text-myunila">{{ $submission->no_tiket }}</p>
                        <p class="text-sm text-gray-500">{{ $submission->rincian?->nm_domain ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $statusName = $submission->status?->nm_status ?? '';
                        $statusClass = match(true) {
                            str_contains($statusName, 'Ditolak') => 'bg-danger-light text-danger',
                            str_contains($statusName, 'Selesai') => 'bg-success-light text-success',
                            str_contains($statusName, 'Disetujui') || str_contains($statusName, 'Dikerjakan') => 'bg-info-light text-info',
                            default => 'bg-warning-light text-warning',
                        };
                    @endphp
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClass }}">
                        {{ $statusName }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $submission->create_at?->format('d M Y') }}</span>
                    <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- User's Actions (as performer) --}}
    @if($actions->count() > 0)
    <div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Aksi Terbaru (sebagai {{ $user->peran?->nm_peran ?? 'Pengguna' }})</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($actions as $action)
            <a href="{{ route('pimpinan.activity-detail', $action->UUID) }}" 
               class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    @php
                        $statusName = $action->statusBaru?->nm_status ?? '';
                        $iconData = match(true) {
                            str_contains($statusName, 'Ditolak') => ['class' => 'bg-danger-light text-danger', 'icon' => 'x-circle'],
                            str_contains($statusName, 'Selesai') => ['class' => 'bg-success-light text-success', 'icon' => 'check-badge'],
                            str_contains($statusName, 'Disetujui') => ['class' => 'bg-info-light text-info', 'icon' => 'check-circle'],
                            str_contains($statusName, 'Dikerjakan') => ['class' => 'bg-warning-light text-warning', 'icon' => 'cog'],
                            default => ['class' => 'bg-gray-100 text-gray-600', 'icon' => 'arrow-right'],
                        };
                    @endphp
                    <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $iconData['class'] }}">
                        <x-icon :name="$iconData['icon']" class="h-4 w-4" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-900">
                            Mengubah status ke <span class="font-medium">{{ $statusName }}</span>
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $action->pengajuan?->no_tiket ?? '-' }} • {{ $action->pengajuan?->rincian?->nm_domain ?? '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">{{ $action->create_at?->diffForHumans() }}</span>
                    <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Back Button --}}
    <div>
        <a href="{{ route('pimpinan.users') }}" class="inline-flex items-center justify-center rounded-xl px-6 py-3 font-semibold text-myunila-700 bg-white border border-myunila-200 hover:bg-myunila-50 hover:border-myunila-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-myunila-500 focus:ring-offset-2">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Pengguna
        </a>
    </div>
</div>
@endsection
