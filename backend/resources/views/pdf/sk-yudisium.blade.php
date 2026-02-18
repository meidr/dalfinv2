<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SK Yudisium</title>

    <style>
        @page {
            margin: 20px 40px 30px 40px;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
        }

        /* kop surat */
        .kop-surat {
            width: 100%;
            margin-bottom: 0;
        }

        .kop-surat img {
            width: 100%;
            height: auto;
        }

        /* header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header .nomor-sk {
            font-size: 12pt;
            font-weight: normal;
            margin: 5px 0 0;
        }

        /* content */
        .content {
            margin: 20px 0;
        }

        .content p {
            text-align: justify;
            margin: 5px 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .info-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 12pt;
        }

        .info-table td:first-child {
            width: 170px;
            font-weight: normal;
        }

        .info-table td:nth-child(2) {
            width: 15px;
            text-align: center;
        }

        .predikat-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 8px 25px;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 0;
        }

        .center-text {
            text-align: center;
        }

        /* tanda tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .ttd-wrapper table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-wrapper td {
            vertical-align: top;
            width: 50%;
            text-align: center;
            padding: 0 30px;
        }

        .ttd-space {
            height: 90px;
            position: relative;
            margin-top: 4px;
        }

        .ttd-space img.cap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin-left: -80px;
            top: -25px;
            width: 150px;
            opacity: .8;
            z-index: 2;
        }

        .ttd-space img.ttd-img {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: -5px;
            width: 85px;
            z-index: 3;
        }

        .nama-ttd {
            font-weight: bold;
            text-decoration: underline;
            position: relative;
            z-index: 1;
            font-size: 12pt;
        }

        .niy-ttd {
            font-size: 10pt;
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>

    {{-- Kop Surat --}}
    <div class="kop-surat">
        <img src="{{ public_path('images/kop surat.jpg') }}" alt="Kop Surat">
    </div>

    {{-- Header --}}
    <div class="header">
        <h2>SURAT KETERANGAN YUDISIUM</h2>
        <p class="nomor-sk">Nomor : {{ $sk_yudisium->nomor_sk }}</p>
    </div>

    {{-- Content --}}
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, menerangkan bahwa :</p>

        <table class="info-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $mahasiswa->nama ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $mahasiswa->nim ?? '-' }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $mahasiswa->prodi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>:</td>
                <td><em>{{ $skripsi->judul ?? '-' }}</em></td>
            </tr>
            <tr>
                <td>Tanggal Ujian</td>
                <td>:</td>
                <td>{{ $tanggal_ujian ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Yudisium</td>
                <td>:</td>
                <td>{{ $tanggal_yudisium ?? '-' }}</td>
            </tr>
            <tr>
                <td>IPK</td>
                <td>:</td>
                <td><strong>{{ $sk_yudisium->ipk_akhir ?? '-' }}</strong></td>
            </tr>
        </table>

        <p>Telah dinyatakan <strong>LULUS</strong> dalam ujian skripsi dan berhak menyandang gelar Sarjana dengan predikat :</p>

        <div class="center-text">
            <div class="predikat-box">
                {{ ucwords(str_replace('_', ' ', $sk_yudisium->predikat ?? '-')) }}
            </div>
        </div>

        <p style="margin-top: 15px;">Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd-wrapper">
        <table>
            <tr>
                <td>
                    <div>Mengetahui,</div>
                    <div>{{ $dekan['position'] ?? 'Dekan Fakultas' }}</div>

                    <div class="ttd-space">
                        @if (!empty($dekan['signature']))
                            <img class="ttd-img" src="{{ $dekan['signature'] }}">
                        @endif
                        <img class="cap" src="{{ public_path('images/cap.jpg') }}">
                    </div>

                    <div class="nama-ttd">{{ $dekan['name'] ?? 'Nama Dekan' }}</div>
                    <div class="niy-ttd">NIDN/NIY : {{ $dekan['nip'] ?? '-' }}</div>
                </td>
                <td>
                    <div>{{ $city ?? 'Bangil' }}, {{ $tanggal }}</div>
                    <div>{{ $kaprodi['position'] ?? 'Kepala Program Studi' }}</div>

                    <div class="ttd-space">
                        @if (!empty($kaprodi['signature']))
                            <img class="ttd-img" src="{{ $kaprodi['signature'] }}">
                        @endif
                        <img class="cap" src="{{ public_path('images/cap.jpg') }}">
                    </div>

                    <div class="nama-ttd">{{ $kaprodi['name'] ?? 'Nama Kaprodi' }}</div>
                    <div class="niy-ttd">NIDN/NIY : {{ $kaprodi['nip'] ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
