<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SK Yudisium</title>

    <style>
        @page {
            margin: 20px 30px;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
        }

        .kop-surat {
            width: 100%;
            margin-bottom: 0;
        }

        .kop-surat img {
            width: 100%;
            height: auto;
        }

        .kop-line {
            border: none;
            border-top: 3px double #000;
            margin: 0 30px 10px 30px;
        }

        .page-content {
            margin: 5px 30px 20px 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .header h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11pt;
            margin: 0;
        }

        .bismillah {
            text-align: center;
            font-style: italic;
            font-size: 12pt;
            margin: 12px 0 8px 0;
            font-weight: bold;
        }

        .sk-body {
            text-align: justify;
            margin-bottom: 8px;
        }

        .sk-body p {
            margin: 0 0 4px 0;
            text-indent: 0;
        }

        .menimbang-list,
        .mengingat-list {
            margin: 0;
            padding: 0 0 0 10px;
        }

        .menimbang-list li,
        .mengingat-list li {
            margin-bottom: 3px;
            text-align: justify;
        }

        .label-col {
            width: 90px;
            vertical-align: top;
            font-weight: normal;
            padding: 2px 0;
        }

        .colon-col {
            width: 10px;
            vertical-align: top;
            padding: 2px 0;
        }

        .content-col {
            vertical-align: top;
            padding: 2px 0;
        }

        .memutuskan {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 12px 0 5px 0;
            text-transform: uppercase;
        }

        .memutuskan-sub {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .menetapkan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .menetapkan-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        /* Lampiran page */
        .lampiran-header {
            margin-bottom: 10px;
        }

        .lampiran-header p {
            margin: 0;
            font-size: 11pt;
        }

        .lampiran-title {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
        }

        .lampiran-subtitle {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            margin: 0 0 10px 0;
        }

        table.jadwal {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            page-break-inside: auto;
        }

        table.jadwal thead {
            display: table-header-group;
        }

        table.jadwal th {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #000;
            font-size: 10pt;
        }

        table.jadwal td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.jadwal td.center {
            text-align: center;
        }

        /* Signature */
        .ttd-wrapper {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .ttd-wrapper table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-wrapper td {
            vertical-align: top;
            text-align: center;
            padding: 0 20px;
        }

        .ttd-right {
            text-align: right;
        }

        .ttd-right-inner {
            display: inline-block;
            text-align: center;
        }

        .ttd-space {
            height: 80px;
            position: relative;
            margin-top: 4px;
        }

        .ttd-space img.cap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin-left: -75px;
            top: -25px;
            width: 140px;
            opacity: .75;
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

        .ttd-space img.qr-img {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 0;
            width: 75px;
            height: 75px;
            z-index: 3;
        }

        .nama-ttd {
            font-weight: bold;
            text-decoration: underline;
            position: relative;
            z-index: 1;
            font-size: 11pt;
        }

        .niy-ttd {
            font-size: 10pt;
            position: relative;
            z-index: 1;
        }

        .tembusan {
            margin-top: 15px;
            font-size: 10pt;
        }

        .tembusan p {
            margin: 0;
        }

        .tembusan ol {
            margin: 0;
            padding-left: 20px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    {{-- ====== PAGE 1: Surat Keputusan ====== --}}

    {{-- Kop Surat --}}
    @if (file_exists($kop_path ?? ''))
        <div class="kop-surat">
            <img src="{{ $kop_path }}">
        </div>
        <hr class="kop-line">
    @endif

    <div class="page-content">
        {{-- Header SK --}}
        <div class="header">
            <h2>SURAT KEPUTUSAN DEKAN {{ strtoupper($fakultas_name ?: 'FAKULTAS') }}</h2>
            <h3>UNIVERSITAS ISLAM INTERNASIONAL DARULLUGHAH WADDA'WAH BANGIL</h3>
            <p>Nomor : {{ $nomor_sk_batch ?? '-' }}</p>
            <p>Tentang</p>
            <h3>YUDISIUM MAHASISWA PROGRAM STUDI {{ strtoupper($prodi_name ?: 'SEMUA PRODI') }}</h3>
            <h3>{{ strtoupper($fakultas_name ?: 'FAKULTAS') }}</h3>
            <h3>UNIVERSITAS ISLAM INTERNASIONAL DARULLUGHAH WADDA'WAH BANGIL</h3>
        </div>

        {{-- Bismillah --}}
        <div class="bismillah">Bismillaahirrahmaanirrahim</div>

        {{-- Body SK --}}
        <div class="sk-body">
            <p>Dekan {{ $fakultas_name ?: 'Fakultas' }}, Universitas Islam Internasional Darullughah Wadda'wah Bangil :</p>

            {{-- Menimbang --}}
            <table class="menetapkan-table">
                <tr>
                    <td class="label-col">Menimbang</td>
                    <td class="colon-col">:</td>
                    <td class="content-col">
                        <ol class="menimbang-list">
                            <li>Bahwa untuk ketertiban dan kelancaran pelaksanaan Yudisium Mahasiswa Program Studi {{ $prodi_name ?: '-' }}, {{ $fakultas_name ?: 'Fakultas' }}, Universitas Islam Internasional Darullughah Wadda'wah Bangil, maka perlu diterbitkan Surat Keputusan Dekan tentang Yudisium;</li>
                            <li>Bahwa nama-nama yang disebut dalam lampiran Surat Keputusan ini dinyatakan lulus dan berhak mengikuti agenda Yudisium Mahasiswa Program Studi {{ $prodi_name ?: '-' }} Tahun {{ $tahun_ajaran ?? date('Y') }};</li>
                        </ol>
                    </td>
                </tr>
            </table>

            {{-- Mengingat --}}
            <table class="menetapkan-table">
                <tr>
                    <td class="label-col">Mengingat</td>
                    <td class="colon-col">:</td>
                    <td class="content-col">
                        <ol class="mengingat-list">
                            <li>Undang-Undang Nomor 20 Tahun 2003 Tentang Sistem Pendidikan Nasional;</li>
                            <li>Undang-undang Nomor 12 Tahun 2012 Tentang Pendidikan Tinggi;</li>
                            <li>Peraturan Pemerintah Nomor 4 Tahun 2014 Tentang Penyelenggaraan Pendidikan Tinggi Dan Pengelolaan Perguruan Tinggi;</li>
                            <li>Rencana Induk Pengembangan (RIP) Universitas Islam Internasional Darullughah Wadda'wah Bangil Pasuruan Jawa Timur;</li>
                            <li>Statuta Universitas Islam Internasional Darullughah Wadda'wah Bangil, Pasuruan, Jawa timur Pasal 35 dan 36.</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>

        {{-- MEMUTUSKAN --}}
        <div class="memutuskan">MEMUTUSKAN</div>
        <div class="memutuskan-sub">YUDISIUM MAHASISWA PROGRAM STUDI {{ strtoupper($prodi_name ?: '-') }}<br>{{ strtoupper($fakultas_name ?: 'FAKULTAS') }}<br>UNIVERSITAS ISLAM INTERNASIONAL DARULLUGHAH WADDA'WAH BANGIL</div>

        {{-- Menetapkan --}}
        <div class="sk-body">
            <p>Menetapkan :</p>
            <table class="menetapkan-table">
                <tr>
                    <td class="label-col">Pertama</td>
                    <td class="colon-col">:</td>
                    <td class="content-col">Surat Keputusan Dekan {{ $fakultas_name ?: 'Fakultas' }}, Universitas Islam Internasional Darullughah Wadda'wah Bangil, tentang Yudisium Mahasiswa Program Studi {{ $prodi_name ?: '-' }}, {{ $fakultas_name ?: 'Fakultas' }}, Universitas Islam Internasional Darullughah Wadda'wah Bangil Tahun {{ $tahun_ajaran ?? date('Y') }}.</td>
                </tr>
                <tr>
                    <td class="label-col">Kedua</td>
                    <td class="colon-col">:</td>
                    <td class="content-col">Yudisium diberikan kepada mahasiswa sebagaimana terlampir.</td>
                </tr>
                <tr>
                    <td class="label-col">Ketiga</td>
                    <td class="colon-col">:</td>
                    <td class="content-col">Surat keputusan ini berlaku sejak tanggal ditetapkan dengan ketentuan apabila dikemudian hari terdapat kekeliruan dalam penetapannya akan diadakan perbaikan sebagaimana mestinya.</td>
                </tr>
            </table>
        </div>

        {{-- TTD Dekan --}}
        <div class="ttd-wrapper">
            <table>
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%; text-align: center;">
                        <div>Ditetapkan di : {{ $city ?? 'Bangil' }}</div>
                        <div>Pada tanggal : {{ $tanggal }}</div>
                        <div style="margin-top: 2px;">{{ $dekan['position'] ?? 'Dekan Fakultas' }},</div>

                        <div class="ttd-space">
                            @if (($signature_mode ?? 'biasa') === 'qr' && !empty($qr_dekan))
                                <img class="qr-img" src="{{ $qr_dekan['qr_base64'] }}">
                            @else
                                @if (!empty($dekan['signature']))
                                    <img class="ttd-img" src="{{ $dekan['signature'] }}">
                                @endif
                                @if (file_exists($cap_path ?? ''))
                                    <img class="cap" src="{{ $cap_path }}">
                                @endif
                            @endif
                        </div>

                        <div class="nama-ttd">{{ $dekan['name'] ?? 'Nama Dekan' }}</div>
                        <div class="niy-ttd">NIDN/NIY : {{ $dekan['nip'] ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Tembusan --}}
        <div class="tembusan">
            <p>Tembusan Yth.</p>
            <ol>
                <li>Kaprodi {{ $prodi_name ?: '-' }}</li>
                <li>Para Kepala Biro</li>
            </ol>
        </div>
    </div>

    {{-- ====== PAGE 2: Lampiran (Daftar Mahasiswa) ====== --}}
    <div class="page-break"></div>

    @if (file_exists($kop_path ?? ''))
        <div class="kop-surat">
            <img src="{{ $kop_path }}">
        </div>
        <hr class="kop-line">
    @endif

    <div class="page-content">
        <div class="lampiran-header">
            <p>Lampiran I: Surat Keputusan Dekan {{ $fakultas_name ?: 'Fakultas' }}</p>
            <p>Universitas Islam Internasional Darullughah Wadda'wah</p>
            <p>Nomor : {{ $nomor_sk_batch ?? '-' }}</p>
            <p>Tanggal : {{ $tanggal }}</p>
        </div>

        <div class="lampiran-title">
            YUDISIUM MAHASISWA PROGRAM STUDI {{ strtoupper($prodi_name ?: '-') }}
        </div>
        <div class="lampiran-subtitle">
            {{ strtoupper($fakultas_name ?: 'FAKULTAS') }}<br>
            UNIVERSITAS ISLAM INTERNASIONAL DARULLUGHAH WADDA'WAH BANGIL
        </div>

        {{-- Tabel Mahasiswa --}}
        <table class="jadwal">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 110px;">NIM</th>
                    <th>NAMA</th>
                    <th style="width: 130px;">PRODI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $item->skripsi->mahasiswa->nim ?? '-' }}</td>
                        <td>{{ $item->skripsi->mahasiswa->nama ?? '-' }}</td>
                        <td>{{ $item->skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center" style="padding: 20px; font-style: italic;">
                            Tidak ada data mahasiswa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TTD Dekan on Lampiran --}}
        <div class="ttd-wrapper">
            <table>
                <tr>
                    <td style="width: 50%;"></td>
                    <td style="width: 50%; text-align: center;">
                        <div>Ditetapkan di : {{ $city ?? 'Bangil' }}</div>
                        <div>Pada tanggal : {{ $tanggal }}</div>
                        <div style="margin-top: 2px;">{{ $dekan['position'] ?? 'Dekan Fakultas' }},</div>

                        <div class="ttd-space">
                            @if (($signature_mode ?? 'biasa') === 'qr' && !empty($qr_dekan))
                                <img class="qr-img" src="{{ $qr_dekan['qr_base64'] }}">
                            @else
                                @if (!empty($dekan['signature']))
                                    <img class="ttd-img" src="{{ $dekan['signature'] }}">
                                @endif
                                @if (file_exists($cap_path ?? ''))
                                    <img class="cap" src="{{ $cap_path }}">
                                @endif
                            @endif
                        </div>

                        <div class="nama-ttd">{{ $dekan['name'] ?? 'Nama Dekan' }}</div>
                        <div class="niy-ttd">NIDN/NIY : {{ $dekan['nip'] ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
