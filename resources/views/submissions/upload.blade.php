@extends('layouts.app')

@section('title', 'Upload Dokumen - ' . $submission->ticket_number)

@section('content')
<div class="py-8 lg:py-12">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('submissions.download-form', $submission) }}" class="mb-4 inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-myunila">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
            
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Upload Dokumen</h1>
            <p class="mt-2 text-gray-600">
                Tiket: <span class="font-mono font-medium text-myunila">{{ $submission->ticket_number }}</span>
            </p>
        </div>

        {{-- Upload Form --}}
        <form action="{{ route('submissions.upload.store', $submission) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            {{-- Signed Form Upload --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="font-semibold text-gray-900">1. Scan Formulir Bertanda Tangan</h2>
                    <p class="mt-1 text-sm text-gray-500">Upload scan formulir yang sudah ditandatangani atasan (PDF)</p>
                </div>
                <div class="p-6">
                    <label class="relative block rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center transition cursor-pointer hover:border-myunila-300 hover:bg-myunila-50/50">
                        <input 
                            type="file" 
                            name="signed_form" 
                            id="signed_form"
                            accept=".pdf"
                            required
                            class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                            onchange="updateFileName(this, 'signed_form_label')"
                        >
                        <div class="pointer-events-none">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p id="signed_form_label" class="mt-4 text-sm font-medium text-gray-700">
                                Klik untuk memilih file atau drag & drop
                            </p>
                            <p class="mt-1 text-xs text-gray-500">Format: PDF, Maks. 5MB</p>
                        </div>
                    </label>
                    @error('signed_form')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Identity Upload --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
                    <h2 class="font-semibold text-gray-900">2. Scan/Foto Identitas</h2>
                    <p class="mt-1 text-sm text-gray-500">Upload KTM (mahasiswa) atau Karpeg (PNS/pegawai)</p>
                </div>
                <div class="p-6">
                    <label class="relative block rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center transition cursor-pointer hover:border-myunila-300 hover:bg-myunila-50/50">
                        <input 
                            type="file" 
                            name="identity_attachment" 
                            id="identity_attachment"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                            class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                            onchange="updateFileName(this, 'identity_label')"
                        >
                        <div class="pointer-events-none">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            <p id="identity_label" class="mt-4 text-sm font-medium text-gray-700">
                                Klik untuk memilih file atau drag & drop
                            </p>
                            <p class="mt-1 text-xs text-gray-500">Format: PDF, JPG, PNG, Maks. 5MB</p>
                        </div>
                    </label>
                    @error('identity_attachment')
                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Info Box --}}
            <div class="rounded-xl border border-info/30 bg-info-light p-4">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 shrink-0 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-gray-800">
                        <p class="font-medium">Pastikan dokumen sudah benar!</p>
                        <p class="mt-1">Setelah upload, pengajuan akan dikirim ke tim verifikator TIK untuk ditinjau. Anda tidak dapat mengubah dokumen setelah submit.</p>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('submissions.download-form', $submission) }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Upload & Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.classList.add('text-myunila');
    }
}
</script>
@endpush
@endsection
