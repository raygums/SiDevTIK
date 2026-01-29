<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Permohonan Sub Domain - {{ $submission->ticket_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .logo {
            width: 70px;
            height: auto;
        }
        
        .header-text h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header-text h2 {
            font-size: 12pt;
            font-weight: bold;
        }
        
        .header-text p {
            font-size: 9pt;
        }
        
        .form-title {
            text-align: center;
            margin: 20px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-weight: bold;
            background: #f0f0f0;
            padding: 5px 10px;
            margin-bottom: 8px;
            border: 1px solid #000;
        }
        
        .form-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .form-table td {
            padding: 5px 8px;
            vertical-align: top;
            border: 1px solid #000;
        }
        
        .form-table .label {
            width: 35%;
            background: #fafafa;
        }
        
        .form-table .value {
            width: 65%;
        }
        
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .checkbox {
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .checkbox.checked::before {
            content: '✓';
        }
        
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-table td {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
            border: 1px solid #000;
        }
        
        .signature-box {
            height: 80px;
            border-bottom: 1px dotted #999;
            margin: 10px 20px;
        }
        
        .helpdesk-section {
            margin-top: 20px;
            border: 2px solid #000;
            padding: 10px;
        }
        
        .helpdesk-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            background: #e0e0e0;
            padding: 5px;
        }
        
        .domain-highlight {
            font-family: monospace;
            font-size: 12pt;
            font-weight: bold;
            background: #fff3cd;
            padding: 2px 5px;
        }
        
        .ticket-info {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 9pt;
            text-align: right;
        }
        
        .note {
            font-size: 9pt;
            font-style: italic;
            color: #666;
        }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @php
        $metadata = is_array($submission->metadata) ? $submission->metadata : json_decode($submission->metadata ?? '{}', true);
        $detail = $submission->details->first();
        
        $jenisLabels = [
            'lembaga_fakultas' => 'Lembaga / Fakultas / Jurusan',
            'kegiatan_lembaga' => 'Kegiatan Lembaga / Fakultas / Jurusan',
            'organisasi_mahasiswa' => 'Organisasi Mahasiswa',
            'kegiatan_mahasiswa' => 'Kegiatan Mahasiswa',
            'lainnya' => 'Lain-lain',
        ];
        
        $selectedJenis = $metadata['jenis_domain'] ?? '';
    @endphp

    {{-- Header --}}
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 80px; text-align: center; vertical-align: middle;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-unila.png'))) }}" class="logo" alt="Logo Unila" onerror="this.style.display='none'">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <h1>UNIVERSITAS LAMPUNG</h1>
                    <h2>UPA TEKNOLOGI INFORMASI DAN KOMUNIKASI</h2>
                    <p>Jl. Prof. Dr. Ir. Sumantri Brojonegoro No. 1, Gedong Meneng, Bandar Lampung 35145</p>
                    <p>Telp: (0721) 701609 | Email: helpdesk@tik.unila.ac.id | Website: tik.unila.ac.id</p>
                </td>
                <td style="width: 80px;"></td>
            </tr>
        </table>
    </div>

    {{-- Form Title --}}
    <h2 class="form-title">FORMULIR PERMOHONAN SUB DOMAIN</h2>
    
    <p style="text-align: center; margin-bottom: 15px; font-size: 10pt;">
        No. Tiket: <strong>{{ $submission->ticket_number }}</strong> | 
        Tanggal: <strong>{{ $submission->created_at->format('d F Y') }}</strong>
    </p>

    {{-- Section: Data Sub Domain --}}
    <div class="section">
        <div class="section-title">DATA SUB DOMAIN</div>
        <table class="form-table">
            <tr>
                <td class="label">Jenis Domain</td>
                <td class="value">
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($jenisLabels as $key => $label)
                            <span class="checkbox-item">
                                <span class="checkbox {{ $selectedJenis === $key ? 'checked' : '' }}"></span>
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label">Nama Lembaga/Organisasi/Kegiatan</td>
                <td class="value"><strong>{{ $metadata['nama_organisasi'] ?? $submission->application_name }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Section: Penanggung Jawab Administratif --}}
    <div class="section">
        <div class="section-title">PENANGGUNG JAWAB ADMINISTRATIF</div>
        <table class="form-table">
            <tr>
                <td class="label">Nama</td>
                <td class="value">{{ $metadata['admin']['name'] ?? $submission->admin_responsible_name }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="value">{{ $metadata['admin']['position'] ?? $submission->admin_responsible_position }}</td>
            </tr>
            <tr>
                <td class="label">NIP / NPM</td>
                <td class="value">{{ $metadata['admin']['nip'] ?? $submission->admin_responsible_nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Kantor</td>
                <td class="value">{{ $metadata['admin']['alamat_kantor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Rumah</td>
                <td class="value">{{ $metadata['admin']['alamat_rumah'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon Kantor</td>
                <td class="value">{{ $metadata['admin']['telepon_kantor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon Rumah / HP</td>
                <td class="value">{{ $metadata['admin']['telepon_rumah'] ?? $submission->admin_responsible_phone }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="value">{{ $metadata['admin']['email'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Section: Penanggung Jawab Teknis --}}
    <div class="section">
        <div class="section-title">PENANGGUNG JAWAB TEKNIS</div>
        <table class="form-table">
            <tr>
                <td class="label">Nama</td>
                <td class="value">{{ $metadata['tech']['name'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIP / NIM</td>
                <td class="value">{{ $metadata['tech']['nip'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Kantor</td>
                <td class="value">{{ $metadata['tech']['alamat_kantor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Rumah</td>
                <td class="value">{{ $metadata['tech']['alamat_rumah'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="value">{{ $metadata['tech']['email'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon</td>
                <td class="value">{{ $metadata['tech']['phone'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Section: Nama Sub Domain yang Diminta --}}
    <div class="section">
        <div class="section-title">NAMA SUB DOMAIN YANG DIMINTA</div>
        <table class="form-table">
            <tr>
                <td class="label">Sub Domain</td>
                <td class="value">
                    <span class="domain-highlight">{{ $detail->requested_domain ?? '________' }}.unila.ac.id</span>
                    <br><span class="note">(min. 2 karakter, maks. 12 karakter)</span>
                </td>
            </tr>
            <tr>
                <td class="label">Admin Password (Hint)</td>
                <td class="value">
                    {{ $detail->initial_password_hint ?? '________' }}
                    <br><span class="note">(min. 6 karakter, maks. 8 karakter)</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Section: Persetujuan --}}
    <div class="section">
        <div class="section-title">PERSETUJUAN</div>
        <p style="margin: 10px 0; text-align: justify;">
            Dengan ini saya menyatakan bahwa data di atas adalah benar. Saya bertindak atas nama institusi 
            yang saya wakili dan saya mematuhi semua aturan yang ditentukan dan berlaku bagi seluruh 
            pengguna fasilitas layanan Hosting Universitas Lampung.
        </p>
        
        <table class="signature-table">
            <tr>
                <td>
                    <strong>Mengetahui,</strong><br>
                    Kepala Divisi Pusat Infrastruktur TIK
                    <div class="signature-box"></div>
                    <p>(...........................................)</p>
                    <p>NIP: ..........................................</p>
                </td>
                <td>
                    <strong>Bandar Lampung, {{ now()->format('d F Y') }}</strong><br>
                    Pelanggan
                    <div class="signature-box"></div>
                    <p><strong>{{ $metadata['admin']['name'] ?? $submission->admin_responsible_name }}</strong></p>
                    <p>NIP: {{ $metadata['admin']['nip'] ?? $submission->admin_responsible_nip ?? '..........................................' }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Section: Diisi oleh Helpdesk --}}
    <div class="helpdesk-section">
        <div class="helpdesk-title">DIISI OLEH HELPDESK</div>
        <table class="form-table" style="border: none;">
            <tr>
                <td class="label" style="border: none; background: none;">Diterima Tanggal</td>
                <td class="value" style="border-bottom: 1px dotted #000; border-top: none; border-left: none; border-right: none;">...........................</td>
                <td class="label" style="border: none; background: none; width: 20%;">Oleh</td>
                <td class="value" style="border-bottom: 1px dotted #000; border-top: none; border-left: none; border-right: none;">...........................</td>
            </tr>
            <tr>
                <td class="label" style="border: none; background: none;">Diproses Tanggal</td>
                <td class="value" style="border-bottom: 1px dotted #000; border-top: none; border-left: none; border-right: none;">...........................</td>
                <td class="label" style="border: none; background: none;">Oleh</td>
                <td class="value" style="border-bottom: 1px dotted #000; border-top: none; border-left: none; border-right: none;">...........................</td>
            </tr>
            <tr>
                <td class="label" style="border: none; background: none;">Catatan</td>
                <td colspan="3" class="value" style="border-bottom: 1px dotted #000; border-top: none; border-left: none; border-right: none;">...........................................................................................</td>
            </tr>
        </table>
    </div>

</body>
</html>
