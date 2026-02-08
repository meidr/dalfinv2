<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Berita Acara {{ $jenisLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            padding: 20px 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
        }
        .header h2 {
            font-size: 16pt;
            font-weight: bold;
        }
        .title {
            text-align: center;
            margin: 25px 0;
        }
        .title h3 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
        }
        table.info {
            width: 100%;
            margin: 15px 0;
        }
        table.info td {
            padding: 4px 5px;
            vertical-align: top;
        }
        table.info td:first-child {
            width: 160px;
        }
        table.info td:nth-child(2) {
            width: 15px;
        }
        table.penguji {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table.penguji th,
        table.penguji td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table.penguji th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .result-box {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #000;
            text-align: center;
        }
        .result-box h4 {
            font-size: 14pt;
            margin-bottom: 10px;
        }
        .result-box .result {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .signatures {
            margin-top: 40px;
        }
        .signatures table {
            width: 100%;
        }
        .signatures td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signatures .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 60px;
            display: inline-block;
        }
        .footer {
            clear: both;
            margin-top: 40px;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS NEGERI</h1>
        <h2>FAKULTAS TEKNIK</h2>
        <p style="font-size: 10pt;">Jl. Pendidikan No. 1, Kota Pendidikan 12345</p>
    </div>

    <div class="title">
        <h3>BERITA ACARA {{ strtoupper($jenisLabel) }}</h3>
        <p style="margin-top: 5px;">Nomor: {{ $beritaAcara->nomor }}</p>
    </div>

    <p style="margin-bottom: 15px;">Pada hari ini, {{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('l, d F Y') }}, telah dilaksanakan {{ $jenisLabel }} untuk:</p>

    <table class="info">
        <tr>
            <td>Nama Mahasiswa</td>
            <td>:</td>
            <td><strong>{{ $seminar->skripsi->mahasiswa->nama }}</strong></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $seminar->skripsi->mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $seminar->skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Judul Skripsi</td>
            <td>:</td>
            <td>{{ $seminar->skripsi->judul }}</td>
        </tr>
        <tr>
            <td>Tempat/Ruangan</td>
            <td>:</td>
            <td>{{ $seminar->ruangan }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>{{ $seminar->waktu }} WIB</td>
        </tr>
    </table>

    <p style="margin: 15px 0;">Dengan susunan Tim Penguji sebagai berikut:</p>

    <table class="penguji">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Dosen</th>
                <th style="width: 120px;">Jabatan</th>
                <th style="width: 80px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seminar->penguji as $index => $p)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $p->dosen->nama_lengkap }}</td>
                <td style="text-align: center;">{{ ucfirst($p->peran) }}</td>
                <td style="text-align: center;">{{ $p->nilai ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="result-box">
        <h4>HASIL {{ strtoupper($jenisLabel) }}</h4>
        <p class="result">{{ str_replace('_', ' ', strtoupper($beritaAcara->hasil)) }}</p>
        @if($seminar->nilai)
        <p style="margin-top: 10px;">Nilai: <strong>{{ $seminar->nilai }}</strong></p>
        @endif
    </div>

    @if($beritaAcara->catatan)
    <div style="margin: 15px 0;">
        <p><strong>Catatan:</strong></p>
        <p style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">{{ $beritaAcara->catatan }}</p>
    </div>
    @endif

    <div class="signatures">
        <table>
            <tr>
                @foreach($seminar->penguji->take(3) as $p)
                <td>
                    <p>{{ ucfirst($p->peran) }},</p>
                    <p class="name">{{ $p->dosen->nama_lengkap }}</p>
                    <p style="font-size: 10pt;">NIP. {{ $p->dosen->nip }}</p>
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Berita Acara ini dibuat sebagai bukti pelaksanaan {{ $jenisLabel }}</p>
        <p>Dicetak pada: {{ $tanggal }}</p>
    </div>
</body>
</html>
