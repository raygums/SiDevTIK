@extends('layouts.dashboard')

@section('title', 'Ganti Password')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Ganti Password</h1>
        <p class="mt-2 text-gray-500">Perbarui password akun Anda untuk menjaga keamanan.</p>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div id="success-alert" class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4">
        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-myunila-100 text-myunila">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Keamanan Akun</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->nm }} · {{ Auth::user()->usn }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('password.update') }}" method="POST" id="changePasswordForm" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Current Password --}}
            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Password Saat Ini <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        autocomplete="current-password"
                        placeholder="Masukkan password saat ini"
                        class="block w-full rounded-lg border pr-10 py-2.5 pl-4 text-sm shadow-sm transition
                            focus:outline-none focus:ring-2
                            @error('current_password')
                                border-red-400 bg-red-50 text-red-900 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 text-gray-900 focus:border-myunila focus:ring-myunila/20
                            @enderror">
                    <button type="button" onclick="togglePassword('current_password', 'eye-current')"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                        <svg id="eye-current" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="border-gray-100">

            {{-- New Password --}}
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Password Baru <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        autocomplete="new-password"
                        placeholder="Masukkan password baru"
                        oninput="checkStrength(this.value)"
                        class="block w-full rounded-lg border pr-10 py-2.5 pl-4 text-sm shadow-sm transition
                            focus:outline-none focus:ring-2
                            @error('password')
                                border-red-400 bg-red-50 text-red-900 focus:border-red-500 focus:ring-red-200
                            @else
                                border-gray-300 text-gray-900 focus:border-myunila focus:ring-myunila/20
                            @enderror">
                    <button type="button" onclick="togglePassword('password', 'eye-new')"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                        <svg id="eye-new" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Password Strength Meter --}}
                <div id="strength-wrapper" class="mt-3 hidden">
                    <div class="mb-1.5 flex items-center justify-between">
                        <span class="text-xs text-gray-500">Kekuatan Password</span>
                        <span id="strength-label" class="text-xs font-semibold"></span>
                    </div>
                    <div class="flex gap-1">
                        <div id="bar-1" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        <div id="bar-2" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        <div id="bar-3" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        <div id="bar-4" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                    </div>

                    {{-- Checklist Persyaratan --}}
                    <ul class="mt-3 space-y-1.5">
                        <li class="flex items-center gap-2 text-xs" id="req-length">
                            <svg class="req-icon h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            </svg>
                            <span class="text-gray-500">Minimal 8 karakter</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs" id="req-upper">
                            <svg class="req-icon h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            </svg>
                            <span class="text-gray-500">Mengandung huruf kapital (A–Z)</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs" id="req-lower">
                            <svg class="req-icon h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            </svg>
                            <span class="text-gray-500">Mengandung huruf kecil (a–z)</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs" id="req-number">
                            <svg class="req-icon h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            </svg>
                            <span class="text-gray-500">Mengandung angka (0–9)</span>
                        </li>
                        <li class="flex items-center gap-2 text-xs" id="req-symbol">
                            <svg class="req-icon h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                            </svg>
                            <span class="text-gray-500">Mengandung simbol (!@#$%^&amp;*...)</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Ulangi password baru"
                        oninput="checkConfirm()"
                        class="block w-full rounded-lg border pr-10 py-2.5 pl-4 text-sm shadow-sm transition
                            focus:outline-none focus:ring-2 border-gray-300 text-gray-900 focus:border-myunila focus:ring-myunila/20"
                        id="password_confirmation">
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                        <svg id="eye-confirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p id="confirm-msg" class="mt-1.5 hidden text-xs"></p>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-400">
                    <span class="text-red-500">*</span> Wajib diisi
                </p>
                <button
                    type="submit"
                    id="submit-btn"
                    class="inline-flex items-center gap-2 rounded-lg bg-myunila px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-myunila/90 focus:outline-none focus:ring-2 focus:ring-myunila focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Password
                </button>
            </div>
        </form>
    </div>

    {{-- Security Tips --}}
    <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4">
        <div class="flex gap-3">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-blue-800">Tips Keamanan Password</p>
                <ul class="mt-1.5 list-inside list-disc space-y-1 text-xs text-blue-700">
                    <li>Gunakan kombinasi huruf, angka, dan simbol</li>
                    <li>Hindari menggunakan nama, tanggal lahir, atau kata umum</li>
                    <li>Jangan gunakan password yang sama di layanan lain</li>
                    <li>Ganti password secara berkala, minimal setiap 3 bulan</li>
                </ul>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // ─── Toggle Show/Hide Password ───────────────────────────────────
    const EYE_OPEN = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    const EYE_CLOSED = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = EYE_CLOSED;
        } else {
            input.type = 'password';
            icon.innerHTML = EYE_OPEN;
        }
    }

    // ─── Password Strength Checker ───────────────────────────────────
    const CHECKS = {
        length:  v => v.length >= 8,
        upper:   v => /[A-Z]/.test(v),
        lower:   v => /[a-z]/.test(v),
        number:  v => /[0-9]/.test(v),
        symbol:  v => /[^A-Za-z0-9]/.test(v),
    };

    const COLORS = ['', 'bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
    const LABELS = ['', 'Lemah', 'Cukup', 'Baik', 'Kuat'];
    const LABEL_COLORS = ['', 'text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600'];

    function checkStrength(val) {
        const wrapper = document.getElementById('strength-wrapper');
        if (!val) { wrapper.classList.add('hidden'); return; }
        wrapper.classList.remove('hidden');

        const results = {
            length: CHECKS.length(val),
            upper:  CHECKS.upper(val),
            lower:  CHECKS.lower(val),
            number: CHECKS.number(val),
            symbol: CHECKS.symbol(val),
        };

        // Update requirement icons
        Object.keys(results).forEach(key => {
            const li   = document.getElementById('req-' + key);
            const icon = li.querySelector('.req-icon');
            if (results[key]) {
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" class="text-green-500"/>`;
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-green-500');
                li.querySelector('span').classList.replace('text-gray-500', 'text-green-700');
            } else {
                icon.innerHTML = `<circle cx="12" cy="12" r="10" stroke-width="2"/>`;
                icon.classList.remove('text-green-500');
                icon.classList.add('text-gray-300');
                li.querySelector('span').classList.replace('text-green-700', 'text-gray-500');
            }
        });

        // Calculate score
        const score = Object.values(results).filter(Boolean).length;
        // Map score 0-5 to level 0-4
        const level = score <= 1 ? 1 : score <= 2 ? 2 : score <= 3 ? 3 : 4;
        const displayLevel = val.length === 0 ? 0 : level;

        // Update bars
        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar-' + i);
            bar.className = 'h-1.5 flex-1 rounded-full transition-colors duration-300 ' +
                (i <= displayLevel ? COLORS[displayLevel] : 'bg-gray-200');
        }

        // Update label
        const label = document.getElementById('strength-label');
        label.textContent = val.length ? LABELS[displayLevel] : '';
        label.className   = 'text-xs font-semibold ' + (val.length ? LABEL_COLORS[displayLevel] : '');

        checkConfirm();
    }

    // ─── Confirm Password Checker ────────────────────────────────────
    function checkConfirm() {
        const pw      = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        const msg     = document.getElementById('confirm-msg');

        if (!confirm) { msg.classList.add('hidden'); return; }

        msg.classList.remove('hidden');
        if (pw === confirm) {
            msg.textContent = '✓ Password cocok';
            msg.className   = 'mt-1.5 text-xs text-green-600';
        } else {
            msg.textContent = '✗ Password tidak cocok';
            msg.className   = 'mt-1.5 text-xs text-red-500';
        }
    }

    // ─── Auto-dismiss Success Alert ──────────────────────────────────
    const alert = document.getElementById('success-alert');
    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    }
</script>
@endpush
@endsection
