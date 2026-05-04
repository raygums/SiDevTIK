@component('mail::message')
# Selamat! Akun Anda Telah Diaktifkan

Halo {{ $user->nm }},

Akun Anda telah berhasil diaktifkan oleh administrator sistem SiDevTIK.

Anda sekarang dapat login ke sistem menggunakan kredensial Anda:
- **Email**: {{ $user->email }}
- **URL**: {{ config('app.url') }}

@component('mail::button', ['url' => config('app.url') . '/login'])
Login Sekarang
@endcomponent

Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi tim support kami.

Terima kasih,
{{ config('app.name') }}
@endcomponent
