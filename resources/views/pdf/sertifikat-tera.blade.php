<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Tera - {{ $hasilTera->nomor_sertifikat }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .subtitle {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #374151;
        }
        
        .institution {
            font-size: 14px;
            margin: 5px 0;
            color: #6b7280;
        }
        
        .certificate-number {
            background: #1e40af;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .content {
            margin: 30px 0;
            text-align: justify;
        }
        
        .intro-text {
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
            font-style: italic;
        }
        
        .main-content {
            font-size: 14px;
            line-height: 1.6;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #d1d5db;
            padding: 8px 12px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        
        .data-table .label {
            background-color: #f9fafb;
            font-weight: bold;
            width: 30%;
        }
        
        .validity {
            background: #dcfce7;
            border: 2px solid #16a34a;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .validity-title {
            font-weight: bold;
            color: #16a34a;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .validity-date {
            font-size: 18px;
            font-weight: bold;
            color: #15803d;
        }
        
        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            font-size: 14px;
        }
        
        .signature-name {
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }
        
        .signature-position {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .footer {
            position: fixed;
            bottom: 20mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .qr-code {
            float: right;
            margin: 20px 0;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(30, 64, 175, 0.05);
            z-index: -1;
            font-weight: bold;
        }
        
        .status-badge {
            background: #16a34a;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .warning-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
            font-size: 11px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">SERTIFIKAT TERA</div>
    
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <!-- Logo UPTD - bisa diganti dengan logo sebenarnya -->
            <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" fill="#1e40af" stroke="#1e40af" stroke-width="2"/>
                <text x="50" y="35" text-anchor="middle" fill="white" font-size="12" font-weight="bold">UPTD</text>
                <text x="50" y="50" text-anchor="middle" fill="white" font-size="8">METROLOGI</text>
                <text x="50" y="65" text-anchor="middle" fill="white" font-size="8">LEGAL</text>
            </svg>
        </div>
        
        <div class="title">Sertifikat Tera</div>
        <div class="subtitle">UPTD Metrologi Legal</div>
        <div class="institution">Kabupaten Muna Barat</div>
        <div class="institution">Provinsi Sulawesi Tenggara</div>
        
        <div class="certificate-number">
            No. {{ $hasilTera->nomor_sertifikat }}
        </div>
    </div>
    
    <!-- Content -->
    <div class="content">
        <div class="intro-text">
            Kepala Unit Pelaksana Teknis Daerah (UPTD) Metrologi Legal Kabupaten Muna Barat<br>
            dengan ini menerangkan bahwa:
        </div>
        
        <div class="main-content">
            <!-- Data UTTP -->
            <table class="data-table">
                <tr>
                    <td class="label">Nama Pemilik</td>
                    <td>{{ $hasilTera->uttp->nama_pemilik }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td>{{ $hasilTera->uttp->alamat_lengkap }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis UTTP</td>
                    <td>{{ $hasilTera->uttp->jenisUttp->nama }}</td>
                </tr>
                <tr>
                    <td class="label">Merk/Type</td>
                    <td>{{ $hasilTera->uttp->merk }} / {{ $hasilTera->uttp->tipe }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor Seri</td>
                    <td>{{ $hasilTera->uttp->nomor_seri }}</td>
                </tr>
                <tr>
                    <td class="label">Kapasitas Maksimum</td>
                    <td>{{ $hasilTera->uttp->kapasitas_maksimum }} {{ $hasilTera->uttp->jenisUttp->satuan }}</td>
                </tr>
                <tr>
                    <td class="label">Daya Baca</td>
                    <td>{{ $hasilTera->uttp->daya_baca }} {{ $hasilTera->uttp->jenisUttp->satuan }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Tera</td>
                    <td>{{ $hasilTera->tanggal_tera->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Petugas Tera</td>
                    <td>{{ $hasilTera->petugas_tera }}</td>
                </tr>
                <tr>
                    <td class="label">Hasil Pemeriksaan</td>
                    <td><span class="status-badge">{{ $hasilTera->hasil }}</span></td>
                </tr>
            </table>
            
            <p style="margin: 20px 0; text-align: center; font-weight: bold; font-size: 16px;">
                Telah memenuhi persyaratan teknis dan dinyatakan <strong style="color: #16a34a;">LULUS TERA</strong>
            </p>
            
            <!-- Validity Period -->
            <div class="validity">
                <div class="validity-title">MASA BERLAKU SERTIFIKAT</div>
                <div class="validity-date">
                    {{ $hasilTera->tanggal_tera->format('d F Y') }} s/d {{ $hasilTera->tanggal_expired->format('d F Y') }}
                </div>
            </div>
            
            <!-- Warning Box -->
            <div class="warning-box">
                <strong>PERHATIAN:</strong><br>
                1. Sertifikat ini berlaku selama 1 (satu) tahun sejak tanggal penerbitan<br>
                2. UTTP wajib ditera ulang sebelum masa berlaku habis<br>
                3. Sertifikat ini tidak dapat dipindahtangankan<br>
                4. Apabila terjadi kerusakan pada UTTP, sertifikat ini dinyatakan tidak berlaku
            </div>
        </div>
    </div>
    
    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-left">
            <div class="signature-title">Mengetahui,<br>Kepala UPTD Metrologi Legal</div>
            <div class="signature-name">Dr. Ahmad Syarifuddin, M.Si</div>
            <div class="signature-position">NIP. 196801011990031001</div>
        </div>
        
        <div class="signature-right">
            <div class="signature-title">Raha, {{ $hasilTera->tanggal_tera->format('d F Y') }}<br>Petugas Tera</div>
            <div class="signature-name">{{ $hasilTera->petugas_tera }}</div>
            <div class="signature-position">Petugas Tera Bersertifikat</div>
        </div>
    </div>
    
    <!-- QR Code untuk verifikasi -->
    <div class="qr-code">
        <!-- QR Code bisa ditambahkan dengan library seperti Simple QrCode -->
        <div style="width: 80px; height: 80px; border: 1px solid #ccc; text-align: center; line-height: 80px; font-size: 10px;">
            QR Code<br>Verifikasi
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        Dokumen ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah<br>
        UPTD Metrologi Legal Kabupaten Muna Barat - {{ now()->format('Y') }}
    </div>
</body>
</html>
