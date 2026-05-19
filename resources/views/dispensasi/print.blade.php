<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dispensasi - {{ $dispensasi->pengajuanBudget->nama_aksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: 40px 60px;
            line-height: 1.6;
            font-size: 13px;
        }
        
        .header {
            margin-bottom: 20px;
        }
        
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
        }
        
        .header-logo {
            width: 150px;
            height: auto;
        }
        
        .header-logo img {
            width: 100%;
            height: auto;
        }
        
        .header-center {
            flex: 1;
            text-align: center;
            padding: 0 20px;
        }
        
        .header-center h1 {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 2px;
        }
        
        .header-center h2 {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 2px;
        }
        
        .header-center h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 0;
        }
        
        /* Double line border - garis atas */
        .header-border {
            border-top: 3px solid #000;
            margin-top: -10px;
        }
        
        .header-border-inner {
            border-top: 1px solid #000;
            margin-top: 2px;
        }
        
        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 11px;
            padding-top: 10px;
            /* garis bawah dihilangkan */
        }
        
        .header-address {
            text-align: left;
        }
        
        .header-contact {
            text-align: right;
        }
        
        /* Nomor Surat Section */
        .letter-info {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            margin-bottom: 20px;
        }
        
        .letter-info-left {
            display: table;
        }
        
        .letter-info-row {
            display: table-row;
        }
        
        .letter-info-label {
            display: table-cell;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .letter-info-colon {
            display: table-cell;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .letter-info-value {
            display: table-cell;
            vertical-align: top;
        }
        
        .letter-info-right {
            text-align: right;
        }
        
        /* Tujuan Surat */
        .letter-recipient {
            margin-bottom: 20px;
        }
        
        /* Isi Surat */
        .letter-body {
            text-align: justify;
        }
        
        .letter-body p {
            margin-bottom: 15px;
        }
        
        .event-details {
            margin: 15px 0 15px 40px;
        }
        
        .event-details table {
            border: none;
        }
        
        .event-details td {
            border: none;
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }
        
        .event-details td:first-child {
            font-weight: bold;
            width: 80px;
        }
        
        .participant-list {
            margin: 15px 0;
            padding-left: 20px;
        }
        
        .participant-list li {
            margin-bottom: 5px;
        }
        
        .letter-closing {
            margin-top: 25px;
            text-align: justify;
        }
        
        .regards {
            text-align: center;
            margin-top: 20px;
        }
        
        /* Footer Tanda Tangan */
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-name {
            font-weight: bold;
            color: #000000;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        
        .signature-title {
            font-weight: normal;
        }
        
        .signature-space {
            height: 70px;
        }
        
        @media print {
            body {
                padding: 20px 40px;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #000;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .print-button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    

    <!-- Header Kop Surat -->
    <div class="header">
        <div class="header-top">
            <div class="header-logo">
                <img src="{{ asset('images/logo-spamk.png') }}" alt="Logo SPAMK">
            </div>
            <div class="header-center">
                <h1>PIMPINAN UNIT KERJA</h1>
                <h2>SERIKAT PEKERJA AUTOMOTIF MESIN DAN KOMPONEN</h2>
                <h2>FEDERASI SERIKAT PEKERJA METAL INDONESIA</h2>
                <h3>PT. SARI TAKAGI ELOK PRODUK</h3>
            </div>
            <div class="header-logo">
                <img src="{{ asset('images/logo-fspmi.png') }}" alt="Logo FSPMI">
            </div>
        </div>
        
        <!-- Double line border -->
        <div class="header-border"></div>
        <div class="header-border-inner"></div>
        
        <div class="header-info">
            <div class="header-address">
                Kawasan Jababeka V Blok A1-B<br>
                Cikarang Industrial Estate Bekasi 17530
            </div>
            <div class="header-contact">
                Telp. ( 021 ) 8934 211-12-14<br>
                Fax. ( 021 ) 8934 213
            </div>
        </div>
    </div>

    <!-- Nomor Surat -->
    <div class="letter-info">
        <div class="letter-info-left">
            <div class="letter-info-row">
                <div class="letter-info-label">Nomor</div>
                <div class="letter-info-colon">:</div>
                <div class="letter-info-value">{{ str_pad($dispensasi->id, 3, '0', STR_PAD_LEFT) }}/PUK SPAMK FSPMI/PT.STEP/{{ $dispensasi->created_at->format('m') }}/{{ $dispensasi->created_at->format('Y') }}</div>
            </div>
            <div class="letter-info-row">
                <div class="letter-info-label">Lampiran</div>
                <div class="letter-info-colon">:</div>
                <div class="letter-info-value">-</div>
            </div>
            <div class="letter-info-row">
                <div class="letter-info-label">Perihal</div>
                <div class="letter-info-colon">:</div>
                <div class="letter-info-value"><strong>PERMOHONAN DISPENSASI</strong></div>
            </div>
        </div>
        <div class="letter-info-right">
            Cikarang, {{ $dispensasi->created_at->translatedFormat('d F Y') }}
        </div>
    </div>

    <!-- Tujuan Surat -->
    <div class="letter-recipient">
        Kepada Yth,<br>
        Management PT. STEP<br>
        Di Tempat
    </div>

    <!-- Isi Surat -->
    <div class="letter-body">
        <p>Dengan hormat,</p>
        
        <p>
            Menindak lanjuti surat instruksi dari Konsulat Cabang (KC) terkait aksi 
            <strong>{{ $dispensasi->pengajuanBudget->nama_aksi }}</strong> yang akan di laksanakan pada :
        </p>
        
        <div class="event-details">
            <table>
                <tr>
                    <td>Hari</td>
                    <td>:</td>
                    <td>{{ $dispensasi->pengajuanBudget->tanggal ? $dispensasi->pengajuanBudget->tanggal->translatedFormat('l') : '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ $dispensasi->pengajuanBudget->tanggal ? $dispensasi->pengajuanBudget->tanggal->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Jam</td>
                    <td>:</td>
                    <td>{{ $dispensasi->pengajuanBudget->jam_aksi ?? '08.00 s/d selesai' }}</td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td><strong>{{ $dispensasi->pengajuanBudget->tempat_aksi }}</strong></td>
                </tr>
            </table>
        </div>
        
        <p>
            Maka dari itu kami menginformasikan agar nama karyawan yang tertera dibawah ini diberikan 
            dispensasi pada hari {{ $dispensasi->pengajuanBudget->tanggal ? $dispensasi->pengajuanBudget->tanggal->translatedFormat('l') : '-' }} 
            tanggal {{ $dispensasi->pengajuanBudget->tanggal ? $dispensasi->pengajuanBudget->tanggal->translatedFormat('d F Y') : '-' }} 
            untuk mengikuti/menghadiri agenda tersebut.
        </p>
        
        <ol class="participant-list">
            @foreach($users as $user)
            <li><u>{{ $user->name }}</u> - {{ $user->jabatan ?? $user->bidang->nama ?? '-' }}</li>
            @endforeach
        </ol>
    </div>

    <!-- Penutup -->
    <div class="letter-closing">
        <p>Demikian yang dapat kami <u>sampaikan</u>, atas perhatiannya kami ucapkan terima kasih</p>
    </div>
    
    <div class="regards">
        Hormat Kami
    </div>

    <!-- Tanda Tangan -->
    <div class="footer">
        <div class="signature-box">
            <div class="signature-space"></div>
            <div class="signature-name">Sukirno</div>
            <div class="signature-title">Ketua</div>
        </div>
        <div class="signature-box">
            <div class="signature-space"></div>
            <div class="signature-name">Wibowo Bagus K</div>
            <div class="signature-title">Sekretaris</div>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>