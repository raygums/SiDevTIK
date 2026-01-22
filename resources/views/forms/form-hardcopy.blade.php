<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan {{ ucfirst($submission->service_type) }} - {{ $submission->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 15mm 20mm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }
        
        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0;
        }
        
        /* Header Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding-bottom: 10px;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
        }
        
        .kop-surat img {
            height: 70px;
            width: 70px;
            object-fit: contain;
        }
        
        .kop-surat .text-center {
            text-align: center;
        }
        
        .kop-surat h1 {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        
        .kop-surat h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .kop-surat p {
            font-size: 10pt;
        }
        
        /* Title */
        .form-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .form-title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .form-title p {
            font-size: 10pt;
            margin-top: 5px;
        }
        
        /* Info Tiket */
        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        
        .ticket-info .left {
            text-align: left;
        }
        
        .ticket-info .right {
            text-align: right;
        }
        
        .ticket-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 12pt;
        }
        
        /* Sections */
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            background: #f0f0f0;
            padding: 6px 10px;
            border-left: 4px solid #333;
            margin-bottom: 10px;
        }
        
        .section-title span {
            display: inline-block;
            width: 22px;
            height: 22px;
            background: #333;
            color: #fff;
            text-align: center;
            line-height: 22px;
            border-radius: 50%;
            font-size: 10pt;
            margin-right: 8px;
        }
        
        /* Table Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 6px 10px;
            vertical-align: top;
            border: 1px solid #ddd;
        }
        
        .data-table .label {
            width: 35%;
            background: #fafafa;
            font-weight: 500;
        }
        
        .data-table .value {
            width: 65%;
        }
        
        .data-table .highlight {
            font-weight: bold;
            color: #006837;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-row {
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .signature-box .space {
            height: 70px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        
        .signature-box .name {
            font-weight: bold;
        }
        
        .signature-box .nip {
            font-size: 10pt;
        }
        
        /* Notes */
        .notes {
            margin-top: 20px;
            padding: 10px;
            background: #fff9e6;
            border: 1px solid #ffd700;
            font-size: 9pt;
        }
        
        .notes h4 {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .notes ul {
            margin-left: 15px;
        }
        
        .notes li {
            margin-bottom: 3px;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            text-align: center;
            color: #666;
        }
        
        /* Print optimization */
        @media print {
            .page {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- Kop Surat --}}
        <div class="kop-surat">
            @if(file_exists(public_path('images/logo-unila.png')))
                <img src="{{ public_path('images/logo-unila.png') }}" alt="Logo Unila">
            @else
                <div style="width: 70px; height: 70px; background: #006837; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">UNILA</div>
            @endif
            <div class="text-center">
                <p>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</p>
                <h1>UNIVERSITAS LAMPUNG</h1>
                <h2>UPT TEKNOLOGI INFORMASI DAN KOMUNIKASI</h2>
                <p>Jl. Prof. Dr. Ir. Sumantri Brojonegoro No. 1, Bandar Lampung 35145</p>
                <p>Telp. (0721) 701609 | Email: tik@unila.ac.id | Web: tik.unila.ac.id</p>
            </div>
            @if(file_exists(public_path('images/logo-tik.png')))
                <img src="{{ public_path('images/logo-tik.png') }}" alt="Logo TIK">
            @else
                <div style="width: 70px; height: 70px; background: #1a73e8; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">TIK</div>
            @endif
        </div>

        {{-- Form Title --}}
        <div class="form-title">
            <h3>
                @if($submission->service_type === 'vps')
                    Formulir Permohonan Layanan VPS
                @elseif($submission->service_type === 'hosting')
                    Formulir Permohonan Layanan Hosting
                @else
                    Formulir Permohonan Layanan Sub Domain
                @endif
            </h3>
            <p>*.unila.ac.id</p>
        </div>

        {{-- Ticket Info --}}
        <div class="ticket-info">
            <div class="left">
                <strong>No. Tiket:</strong> <span class="ticket-number">{{ $submission->ticket_number }}</span>
            </div>
            <div class="right">
                <strong>Tanggal:</strong> {{ $submission->created_at->format('d F Y') }}
            </div>
        </div>

        {{-- Section 1: Data Pemohon --}}
        <div class="section">
            <div class="section-title">
                <span>1</span> DATA PEMOHON
            </div>
            <table class="data-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ $submission->applicant_name }}</td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="value">{{ $submission->applicant_nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="value">{{ $submission->applicant_position }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $submission->applicant_email }}</td>
                </tr>
                <tr>
                    <td class="label">No. Telepon/HP</td>
                    <td class="value">{{ $submission->applicant_phone }}</td>
                </tr>
            </table>
        </div>

        {{-- Section 2: Data Unit --}}
        <div class="section">
            <div class="section-title">
                <span>2</span> DATA UNIT KERJA
            </div>
            <table class="data-table">
                <tr>
                    <td class="label">Nama Unit</td>
                    <td class="value">{{ $submission->unit->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori Unit</td>
                    <td class="value">{{ $submission->unit->category->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Section 3: Data PIC --}}
        <div class="section">
            <div class="section-title">
                <span>3</span> DATA PENANGGUNG JAWAB TEKNIS
            </div>
            <table class="data-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ $submission->pic_name }}</td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="value">{{ $submission->pic_nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $submission->pic_email }}</td>
                </tr>
                <tr>
                    <td class="label">No. Telepon/HP</td>
                    <td class="value">{{ $submission->pic_phone }}</td>
                </tr>
            </table>
        </div>

        {{-- Section 4: Data Layanan --}}
        <div class="section">
            <div class="section-title">
                <span>4</span>
                @if($submission->service_type === 'vps')
                    SPESIFIKASI VPS YANG DIMINTA
                @elseif($submission->service_type === 'hosting')
                    DATA HOSTING YANG DIMINTA
                @else
                    NAMA SUB DOMAIN YANG DIMINTA
                @endif
            </div>
            <table class="data-table">
                @if($submission->service_type === 'domain')
                    <tr>
                        <td class="label">Sub Domain</td>
                        <td class="value highlight">{{ $submission->details->requested_domain }}.unila.ac.id</td>
                    </tr>
                @elseif($submission->service_type === 'hosting')
                    <tr>
                        <td class="label">Nama Akun Hosting</td>
                        <td class="value highlight">{{ $submission->details->requested_domain }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kuota Storage</td>
                        <td class="value">{{ $submission->details->hosting_quota }} MB</td>
                    </tr>
                @elseif($submission->service_type === 'vps')
                    <tr>
                        <td class="label">Hostname VPS</td>
                        <td class="value highlight">{{ $submission->details->requested_domain }}</td>
                    </tr>
                    <tr>
                        <td class="label">Sistem Operasi</td>
                        <td class="value">{{ $submission->details->vps_os }}</td>
                    </tr>
                    <tr>
                        <td class="label">CPU Core</td>
                        <td class="value">{{ $submission->details->vps_cpu }} Core</td>
                    </tr>
                    <tr>
                        <td class="label">RAM</td>
                        <td class="value">{{ $submission->details->vps_ram }} GB</td>
                    </tr>
                    <tr>
                        <td class="label">Storage</td>
                        <td class="value">{{ $submission->details->vps_storage }} GB</td>
                    </tr>
                    <tr>
                        <td class="label">Tujuan Penggunaan</td>
                        <td class="value">{{ $submission->details->vps_purpose }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <p style="margin-bottom: 10px;">Demikian permohonan ini kami ajukan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
            
            <div style="text-align: right; margin-bottom: 10px;">
                Bandar Lampung, {{ $submission->created_at->format('d F Y') }}
            </div>
            
            <div class="signature-row">
                <div class="signature-box">
                    <div class="title">Mengetahui,<br>Pimpinan Unit</div>
                    <div class="space"></div>
                    <div class="name">................................</div>
                    <div class="nip">NIP. ................................</div>
                </div>
                <div class="signature-box">
                    <div class="title">Pemohon,</div>
                    <div class="space"></div>
                    <div class="name">{{ $submission->applicant_name }}</div>
                    <div class="nip">NIP. {{ $submission->applicant_nip ?? '................................' }}</div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="notes">
            <h4>Catatan:</h4>
            <ul>
                <li>Form ini harus ditandatangani oleh pimpinan unit dan pemohon</li>
                <li>Serahkan form yang sudah ditandatangani ke UPT TIK atau scan dan kirim ke tik@unila.ac.id</li>
                <li>Proses verifikasi membutuhkan waktu 1-3 hari kerja</li>
                <li>Status pengajuan dapat dipantau melalui website domaintik.unila.ac.id dengan nomor tiket di atas</li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh sistem Domaintik - UPT TIK Universitas Lampung</p>
            <p>{{ config('app.url') }} | Tiket: {{ $submission->ticket_number }}</p>
        </div>
    </div>
</body>
</html>
