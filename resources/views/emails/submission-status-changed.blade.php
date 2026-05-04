@component('mail::message')
# Pembaruan Status Pengajuan

Halo {{ $user->nm }},

Status pengajuan layanan Anda telah mengalami perubahan.

**Detail Pengajuan:**
- **Nomor Tiket**: {{ $submission->no_tiket }}
- **Layanan**: {{ $submission->jenisLayanan?->nm_layanan ?? 'Domain' }}
- **Status Lama**: {{ $oldStatus }}
- **Status Baru**: {{ $newStatus }}
- **Tanggal Perubahan**: {{ now()->format('d M Y, H:i') }}

@if($notes)
**Catatan:**
{{ $notes }}
@endif

Untuk melihat detail lengkap pengajuan Anda, silakan login ke sistem:

@component('mail::button', ['url' => config('app.url') . '/login'])
Lihat Detail Pengajuan
@endcomponent

Jika Anda memiliki pertanyaan, silakan hubungi tim support kami.

Terima kasih,
{{ config('app.name') }}
@endcomponent
