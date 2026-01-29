{{-- Printable Form Template --}}
<div class="mx-auto max-w-[210mm] bg-white p-8 font-sans text-sm" style="font-family: Arial, sans-serif;">
    
    {{-- Header --}}
    <div class="mb-6 border-b-2 border-black pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                {{-- Logo Placeholder --}}
                <div class="flex h-16 w-16 items-center justify-center rounded border border-gray-300 text-xs text-gray-500">
                    LOGO<br>UNILA
                </div>
                <div>
                    <p class="text-xs">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</p>
                    <p class="font-bold">UNIVERSITAS LAMPUNG</p>
                    <p class="text-xs">UPA TEKNOLOGI INFORMASI DAN KOMUNIKASI</p>
                    <p class="text-xs">Jl. Prof. Dr. Ir. Sumantri Brojonegoro No.1, Bandar Lampung 35145</p>
                </div>
            </div>
            <div class="text-right text-xs">
                <p>No. Tiket:</p>
                <p class="font-mono font-bold">{{ $submission->ticket_number }}</p>
                <p class="mt-1 text-gray-500">{{ $submission->tgl_pengajuan?->format('d/m/Y') ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Title --}}
    <div class="mb-6 text-center">
        <h1 class="text-lg font-bold uppercase">Formulir Permohonan</h1>
        <h2 class="font-bold">Layanan {{ ucfirst($submission->jenisLayanan?->nm_layanan ?? 'Domain') }} (.unila.ac.id)</h2>
    </div>

    {{-- Section A: Data Pemohon --}}
    <div class="mb-6">
        <h3 class="mb-2 font-bold uppercase">A. Data Pemohon</h3>
        <table class="w-full border-collapse">
            <tr>
                <td class="w-1/3 border border-gray-400 px-2 py-1.5 font-medium">Nama Lengkap</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->applicant->name }}</td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">NIP / NPM</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->applicant->nomor_identitas ?? '-' }}</td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">Email</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->applicant->email }}</td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">Unit Kerja</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->unit->name }} ({{ $submission->unit->category->name }})</td>
            </tr>
        </table>
    </div>

    {{-- Section B: Data Atasan/Penanggung Jawab --}}
    <div class="mb-6">
        <h3 class="mb-2 font-bold uppercase">B. Data Atasan / Penanggung Jawab Administratif</h3>
        <table class="w-full border-collapse">
            <tr>
                <td class="w-1/3 border border-gray-400 px-2 py-1.5 font-medium">Nama Lengkap</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->admin_responsible_name }}</td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">NIP</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->admin_responsible_nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">Jabatan</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->admin_responsible_position }}</td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">No. HP/WA</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->admin_responsible_phone }}</td>
            </tr>
        </table>
    </div>

    {{-- Section C: Data Permohonan --}}
    <div class="mb-6">
        <h3 class="mb-2 font-bold uppercase">C. Data Permohonan Layanan</h3>
        <table class="w-full border-collapse">
            <tr>
                <td class="w-1/3 border border-gray-400 px-2 py-1.5 font-medium">Jenis Layanan</td>
                <td class="border border-gray-400 px-2 py-1.5">
                    {{ ucfirst($submission->jenisLayanan?->nm_layanan ?? 'Domain') }}
                </td>
            </tr>
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">Nama Domain Diminta</td>
                <td class="border border-gray-400 px-2 py-1.5 font-mono">{{ $submission->rincian?->nm_domain ?? '-' }}</td>
            </tr>
            @if($submission->rincian?->kapasitas_penyimpanan)
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">Kapasitas Storage</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->rincian->kapasitas_penyimpanan }}</td>
            </tr>
            @endif
            @if($submission->rincian?->alamat_ip)
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium">Alamat IP</td>
                <td class="border border-gray-400 px-2 py-1.5 font-mono">{{ $submission->rincian->alamat_ip }}</td>
            </tr>
            @endif
            <tr>
                <td class="border border-gray-400 px-2 py-1.5 font-medium align-top">Keterangan/Keperluan</td>
                <td class="border border-gray-400 px-2 py-1.5">{{ $submission->rincian?->keterangan_keperluan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Section D: Pernyataan --}}
    <div class="mb-6">
        <h3 class="mb-2 font-bold uppercase">D. Pernyataan</h3>
        <div class="border border-gray-400 p-3 text-xs">
            <p class="mb-2">Dengan ini saya menyatakan bahwa:</p>
            <ol class="list-inside list-decimal space-y-1">
                <li>Data yang saya isikan di atas adalah benar dan dapat dipertanggungjawabkan.</li>
                <li>Layanan domain/hosting ini akan digunakan untuk kepentingan resmi unit kerja/instansi.</li>
                <li>Saya bersedia mematuhi ketentuan penggunaan layanan TIK Universitas Lampung.</li>
                <li>Saya bertanggung jawab atas konten yang dipublikasikan pada layanan ini.</li>
            </ol>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="mt-8">
        <div class="flex justify-between">
            {{-- Pemohon --}}
            <div class="w-2/5 text-center">
                <p>Bandar Lampung, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-medium">Pemohon,</p>
                <div class="my-12"></div>
                <p class="font-medium underline">{{ $submission->applicant->name }}</p>
                <p class="text-xs">{{ $submission->applicant->nomor_identitas ?? 'NIP/NPM: ................' }}</p>
            </div>
            
            {{-- Atasan --}}
            <div class="w-2/5 text-center">
                <p>&nbsp;</p>
                <p class="font-medium">Mengetahui,</p>
                <p class="text-xs">{{ $submission->admin_responsible_position }}</p>
                <div class="my-10"></div>
                <p class="font-medium underline">{{ $submission->admin_responsible_name }}</p>
                <p class="text-xs">NIP: {{ $submission->admin_responsible_nip ?? '................................' }}</p>
            </div>
        </div>
    </div>

    {{-- Footer Note --}}
    <div class="mt-8 border-t border-gray-300 pt-4 text-xs text-gray-500">
        <p><strong>Catatan:</strong></p>
        <ul class="list-inside list-disc">
            <li>Formulir ini harus ditandatangani oleh Pemohon dan Atasan/Penanggung Jawab.</li>
            <li>Scan formulir yang sudah ditandatangani beserta identitas (KTM/Karpeg) dan upload ke sistem.</li>
            <li>Pertanyaan? Hubungi helpdesk@tik.unila.ac.id atau kunjungi tik.unila.ac.id</li>
        </ul>
    </div>
</div>
