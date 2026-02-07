<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan {{ ucfirst($submission->jenisLayanan?->nm_layanan ?? 'domain') }} - {{ $submission->no_tiket }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 14mm 17mm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 10px 25px;
        }
        
        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header Kop Surat - Style seperti contoh */
        .kop-surat {
            display: table;
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
            margin-bottom: 18px;
        }
        
        .kop-logo {
            display: table-cell;
            width: 85px;
            vertical-align: middle;
            padding-right: 15px;
        }
        
        .kop-logo img {
            height: 70px;
            width: 70px;
        }
        
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-left: 5px;
            padding-right: 85px; /* Equal to logo width to center text */
        }
        
        .kop-text .kementerian {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .kop-text .universitas {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .kop-text .unit {
            font-size: 12pt;
            font-weight: bold;
        }
        
        .kop-text .alamat {
            font-size: 9pt;
            margin-top: 2px;
        }
        
        .kop-text .kontak {
            font-size: 9pt;
        }
        
        /* Title */
        .form-title {
            text-align: center;
            margin: 18px 10px;
            padding: 10px 20px;
            border: 1px solid #000;
        }
        
        .form-title h3 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-title p {
            font-size: 10pt;
            margin-top: 3px;
        }
        
        /* Info Tiket */
        .ticket-info {
            width: 100%;
            margin-bottom: 15px;
            padding: 0 10px;
            font-size: 10pt;
        }
        
        .ticket-info::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .ticket-left {
            float: left;
        }
        
        .ticket-right {
            float: right;
            margin-right: 15px;
        }
        
        .ticket-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 11pt;
        }
        
        /* Sections */
        .section {
            margin-bottom: 15px;
            padding: 0 10px;
        }
        
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            background: #e8e8e8;
            padding: 6px 12px;
            border-left: 3px solid #000;
            margin-bottom: 10px;
        }
        
        .section-number {
            display: inline-block;
            width: 18px;
            height: 18px;
            background: #000;
            color: #fff;
            text-align: center;
            line-height: 18px;
            border-radius: 50%;
            font-size: 9pt;
            margin-right: 8px;
        }
        
        /* Table Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 6px 15px;
            vertical-align: top;
            border: 1px solid #999;
            font-size: 10pt;
        }
        
        .data-table .label {
            width: 32%;
            background: #f5f5f5;
            font-weight: 600;
        }
        
        .data-table .value {
            width: 68%;
        }
        
        .data-table .highlight {
            font-weight: bold;
            color: #006837;
        }
        
        /* Page Break */
        .page-break {
            page-break-before: always;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            padding: 0 10px;
        }
        
        .signature-intro {
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .signature-date {
            text-align: right;
            margin-bottom: 25px;
        }
        
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 25px;
        }
        
        .signature-box .title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 3px;
        }
        
        .signature-box .subtitle {
            font-size: 9pt;
            margin-bottom: 5px;
        }
        
        .signature-box .space {
            height: 70px;
            margin-bottom: 5px;
        }
        
        .signature-box .line {
            border-bottom: 1px solid #000;
            margin-bottom: 3px;
        }
        
        .signature-box .name {
            font-weight: bold;
            font-size: 10pt;
        }
        
        .signature-box .nip {
            font-size: 9pt;
        }
        
        /* TIK Verification Section */
        .tik-section {
            margin: 30px 10px;
            border: 2px solid #000;
            padding: 20px;
        }
        
        .tik-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        
        .tik-row {
            display: table;
            width: 100%;
        }
        
        .tik-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }
        
        .tik-box-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 10px;
        }
        
        .tik-field {
            margin: 6px 0;
            font-size: 10pt;
        }
        
        .tik-sign-space {
            height: 55px;
            border-bottom: 1px solid #000;
            margin-top: 12px;
        }
        
        /* Notes */
        .notes {
            margin: 25px 10px;
            padding: 12px 15px;
            background: #fffbe6;
            border: 1px solid #d4b106;
            font-size: 9pt;
        }
        
        .notes h4 {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .notes ul {
            margin-left: 20px;
        }
        
        .notes li {
            margin-bottom: 3px;
        }
        
        /* Footer */
        .footer {
            margin: 20px 10px 0;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    @php
        $serviceType = $submission->jenisLayanan?->nm_layanan ?? 'domain';
        $keterangan = json_decode($submission->rincian?->keterangan_keperluan ?? '{}', true);
        $tipePengajuan = $keterangan['tipe_pengajuan'] ?? 'pengajuan_baru';
        $tipePengajuanLabel = match($tipePengajuan) {
            'pengajuan_baru' => 'Permohonan Baru',
            'perpanjangan' => 'Perpanjangan',
            'perubahan_data' => 'Perubahan Data',
            'upgrade_downgrade' => 'Upgrade/Downgrade',
            'penonaktifan' => 'Penonaktifan',
            'laporan_masalah' => 'Laporan Masalah',
            default => 'Permohonan'
        };
    @endphp

    <div class="page">
        {{-- ==================== HALAMAN 1 ==================== --}}
        
        {{-- Kop Surat --}}
        <div class="kop-surat">
            <div class="kop-logo">
                @if(file_exists(public_path('images/logo-unila.png')))
                    <img src="{{ public_path('images/logo-unila.png') }}" alt="Logo Unila">
                @else
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/Logo_of_Lampung_University.svg/1200px-Logo_of_Lampung_University.svg.png" alt="Logo Unila" style="height: 65px; width: 65px;">
                @endif
            </div>
            <div class="kop-text">
                <div class="kementerian">KEMENTERIAN PENDIDIKAN TINGGI, SAINS DAN TEKNOLOGI</div>
                <div class="universitas">UNIVERSITAS LAMPUNG</div>
                <div class="unit">UPA. TEKNOLOGI INFORMASI DAN KOMUNIKASI</div>
                <div class="alamat">Jalan Prof. Dr. Sumantri Brojonegoro No. 1 Gedungmeneng Bandarlampung 35145</div>
                <div class="kontak">Website:tik.unila.ac.id email:tik@kpa.unila.ac.id VOIP 127, 137, 146, 147, dan 148</div>
            </div>
        </div>

        {{-- Form Title --}}
        <div class="form-title">
            <h3>
                @if($serviceType === 'vps')
                    Formulir {{ $tipePengajuanLabel }} Layanan VPS
                @elseif($serviceType === 'hosting')
                    Formulir {{ $tipePengajuanLabel }} Layanan Hosting
                @else
                    Formulir {{ $tipePengajuanLabel }} Layanan Sub Domain
                @endif
            </h3>
            <p>*.unila.ac.id</p>
        </div>

        {{-- Ticket Info --}}
        <div class="ticket-info">
            <div class="ticket-left">
                <strong>No. Tiket:</strong> <span class="ticket-number">{{ $submission->no_tiket }}</span>
            </div>
            <div class="ticket-right">
                <strong>Tanggal:</strong> {{ $submission->create_at?->format('d F Y') ?? now()->format('d F Y') }}
            </div>
        </div>

        {{-- Section 1: Data Pemohon --}}
        <div class="section">
            <div class="section-title">
                <span class="section-number">1</span> DATA PEMOHON / ORGANISASI
            </div>
            <table class="data-table">
                <tr>
                    <td class="label">Tipe Pengajuan</td>
                    <td class="value highlight">{{ $tipePengajuanLabel }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Organisasi/Unit</td>
                    <td class="value">{{ $keterangan['nama_organisasi'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori Pemohon</td>
                    <td class="value">{{ ucfirst(str_replace('_', ' ', $keterangan['kategori_pemohon'] ?? '-')) }}</td>
                </tr>
                <tr>
                    <td class="label">Unit Kerja</td>
                    <td class="value">{{ $submission->unitKerja?->nm_unit ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori Unit</td>
                    <td class="value">{{ $submission->unitKerja?->category?->nm_kategori_unit ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Diajukan Oleh</td>
                    <td class="value">{{ $submission->pengguna?->nm ?? '-' }} ({{ $submission->pengguna?->email ?? '-' }})</td>
                </tr>
            </table>
        </div>

        {{-- Section 2: Kontak Admin --}}
        <div class="section">
            <div class="section-title">
                <span class="section-number">2</span> KONTAK ADMIN (PENGELOLA)
            </div>
            <table class="data-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ $keterangan['admin']['name'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori</td>
                    <td class="value">{{ isset($keterangan['kategori_admin']) ? ucfirst($keterangan['kategori_admin']) : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="value">{{ $keterangan['admin']['position'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="value">{{ $keterangan['admin']['nip'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $keterangan['admin']['email'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No. HP/WA</td>
                    <td class="value">{{ $keterangan['admin']['phone'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Telepon Kantor</td>
                    <td class="value">{{ $keterangan['admin']['telepon_kantor'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Kantor</td>
                    <td class="value">{{ $keterangan['admin']['alamat_kantor'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Rumah</td>
                    <td class="value">{{ $keterangan['admin']['alamat_rumah'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Section 3: Kontak Teknis --}}
        <div class="section">
            <div class="section-title">
                <span class="section-number">3</span> KONTAK TEKNIS (PENGELOLA TEKNIS)
            </div>
            <table class="data-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ $keterangan['teknis']['name'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori</td>
                    <td class="value">{{ isset($keterangan['kategori_teknis']) ? ucfirst($keterangan['kategori_teknis']) : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NIP/NPM</td>
                    <td class="value">{{ $keterangan['teknis']['nip'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NIK/Passport</td>
                    <td class="value">{{ $keterangan['teknis']['nik'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $keterangan['teknis']['email'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No. HP/WA</td>
                    <td class="value">{{ $keterangan['teknis']['phone'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Kantor</td>
                    <td class="value">{{ $keterangan['teknis']['alamat_kantor'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat Rumah</td>
                    <td class="value">{{ $keterangan['teknis']['alamat_rumah'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Section 4: Data Layanan --}}
        <div class="section">
            <div class="section-title">
                <span class="section-number">4</span>
                @if($serviceType === 'vps')
                    SPESIFIKASI VPS YANG DIMINTA
                @elseif($serviceType === 'hosting')
                    DATA HOSTING YANG DIMINTA
                @else
                    NAMA SUB DOMAIN YANG DIMINTA
                @endif
            </div>
            <table class="data-table">
                @if($tipePengajuan !== 'pengajuan_baru' && !empty($keterangan['existing']))
                    <tr>
                        <td class="label">Layanan Existing</td>
                        <td class="value highlight">{{ $keterangan['existing']['domain'] ?? '-' }}</td>
                    </tr>
                    @if(!empty($keterangan['existing']['ticket']))
                    <tr>
                        <td class="label">No. Tiket Sebelumnya</td>
                        <td class="value">{{ $keterangan['existing']['ticket'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($keterangan['existing']['expired']))
                    <tr>
                        <td class="label">Tanggal Expired</td>
                        <td class="value">{{ $keterangan['existing']['expired'] }}</td>
                    </tr>
                    @endif
                @endif

                @if($serviceType === 'domain')
                    <tr>
                        <td class="label">Sub Domain</td>
                        <td class="value highlight">{{ $submission->rincian?->nm_domain ?? '-' }}.unila.ac.id</td>
                    </tr>
                @elseif($serviceType === 'hosting')
                    <tr>
                        <td class="label">Nama Akun Hosting</td>
                        <td class="value highlight">{{ $submission->rincian?->nm_domain ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kuota Storage</td>
                        <td class="value">{{ $keterangan['hosting']['quota'] ?? $submission->rincian?->kapasitas_penyimpanan ?? '-' }} MB</td>
                    </tr>
                @elseif($serviceType === 'vps')
                    <tr>
                        <td class="label">Hostname VPS</td>
                        <td class="value highlight">{{ $submission->rincian?->nm_domain ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Sistem Operasi</td>
                        <td class="value">{{ $keterangan['vps']['os'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">CPU Core</td>
                        <td class="value">{{ $keterangan['vps']['cpu'] ?? '-' }} Core</td>
                    </tr>
                    <tr>
                        <td class="label">RAM</td>
                        <td class="value">{{ $keterangan['vps']['ram'] ?? '-' }} GB</td>
                    </tr>
                    <tr>
                        <td class="label">Storage</td>
                        <td class="value">{{ $keterangan['vps']['storage'] ?? $submission->rincian?->kapasitas_penyimpanan ?? '-' }} GB</td>
                    </tr>
                    @if(!empty($keterangan['vps']['purpose']))
                    <tr>
                        <td class="label">Tujuan Penggunaan VPS</td>
                        <td class="value">{{ $keterangan['vps']['purpose'] }}</td>
                    </tr>
                    @endif
                @endif

                @if(!empty($keterangan['tujuan_penggunaan']))
                <tr>
                    <td class="label">Tujuan Penggunaan</td>
                    <td class="value">{{ $keterangan['tujuan_penggunaan'] }}</td>
                </tr>
                @endif

                @if(!empty($keterangan['detail_masalah']))
                <tr>
                    <td class="label">
                        @if($tipePengajuan === 'laporan_masalah')
                            Deskripsi Masalah
                        @else
                            Keterangan Tambahan
                        @endif
                    </td>
                    <td class="value">{{ $keterangan['detail_masalah'] }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- Signature Section - Di Halaman 2 setelah Section 4 --}}
        <div class="signature-section">
            <p class="signature-intro">Demikian permohonan ini kami ajukan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
            
            <p class="signature-date">Bandar Lampung, {{ $submission->create_at?->format('d F Y') ?? now()->format('d F Y') }}</p>
            
            <div class="signature-row">
                {{-- Kolom Kiri: Atasan Pemohon --}}
                <div class="signature-box">
                    <div class="title">Mengetahui,</div>
                    <div class="title">Atasan Pemohon</div>
                    <div class="subtitle">(Kajur/Kaprodi/Dekan/Wakil Rektor)</div>
                    <div class="space"></div>
                    <div class="line"></div>
                    <div class="name"> ........................................ </div>
                    <div class="nip">NIP. ........................................</div>
                </div>
                
                {{-- Kolom Kanan: Pemohon --}}
                <div class="signature-box">
                    <div class="title">Pemohon,</div>
                    <div class="title">(Admin Pengelola)</div>
                    <div class="subtitle">&nbsp;</div>
                    <div class="space"></div>
                    <div class="line"></div>
                    <div class="name">( {{ $keterangan['admin']['name'] ?? '........................................' }} )</div>
                    <div class="nip">{{ !empty($keterangan['admin']['nip']) ? 'NIP. ' . $keterangan['admin']['nip'] : 'NIP. ........................................' }}</div>
                </div>
            </div>
        </div>

        {{-- ==================== HALAMAN 3 ==================== --}}
        <div class="page-break"></div>

        {{-- Section TIK - Halaman 3 --}}
        <div class="tik-section">
            <div class="tik-title">UNTUK DIISI OLEH TIM TIK</div>
            
            <div class="tik-row">
                {{-- Kolom Kiri: Verifikator --}}
                <div class="tik-box">
                    <div class="tik-box-title">Diverifikasi oleh:</div>
                    <div class="tik-field">Nama: ............................................</div>
                    <div class="tik-field">Tanggal: ............................................</div>
                    <div class="tik-field">Catatan: ............................................</div>
                    <div class="tik-field">............................................</div>
                    <div class="tik-field" style="margin-top: 10px;">Tanda Tangan:</div>
                    <div class="tik-sign-space"></div>
                </div>
                
                {{-- Kolom Kanan: Eksekutor --}}
                <div class="tik-box">
                    <div class="tik-box-title">Dieksekusi oleh:</div>
                    <div class="tik-field">Nama: ............................................</div>
                    <div class="tik-field">Tanggal: ............................................</div>
                    <div class="tik-field">Catatan: ............................................</div>
                    <div class="tik-field">............................................</div>
                    <div class="tik-field" style="margin-top: 10px;">Tanda Tangan:</div>
                    <div class="tik-sign-space"></div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="notes">
            <h4>Catatan Penting:</h4>
            <ul>
                <li><strong>Form ini WAJIB ditandatangani</strong> oleh atasan pemohon (minimal Kajur/Kaprodi untuk unit akademik atau Kabag untuk unit non-akademik)</li>
                <li>Scan form yang sudah ditandatangani dengan format <strong>PDF</strong> dan upload ke sistem melalui website</li>
                <li>Proses verifikasi dilakukan oleh tim TIK dalam waktu <strong>1-3 hari kerja</strong></li>
                <li>Status pengajuan dapat dipantau secara real-time melalui <strong>domaintik.unila.ac.id</strong> menggunakan nomor tiket</li>
                <li>Untuk pengajuan VPS dan Hosting, mohon sertakan <strong>surat resmi</strong> dari unit kerja sebagai lampiran pendukung</li>
                <li>Perpanjangan layanan mohon diajukan minimal <strong>7 hari sebelum expired</strong></li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh sistem DomainTIK - UPA TIK Universitas Lampung</p>
            <p>{{ config('app.url') }} | Tiket: {{ $submission->no_tiket }}</p>
        </div>
    </div>
</body>
</html>
