<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekap SK Yudisium</title>

    <style>
        @page {
            margin: 20px 30px;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.3;
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
            margin-bottom: 12px;
        }

        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }

        .header h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10pt;
            margin: 0;
        }

        /* main table */
        table.jadwal {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            page-break-inside: auto;
        }

        table.jadwal thead {
            display: table-header-group;
        }

        table.jadwal th {
            background-color: #333;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #333;
            font-size: 9pt;
        }

        table.jadwal td {
            border: 1px solid #444;
            padding: 5px 6px;
            vertical-align: top;
        }

        table.jadwal tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.jadwal td.center {
            text-align: center;
        }

        table.jadwal td.nama-nim {
            line-height: 1.4;
        }

        table.jadwal td.nama-nim .nama {
            font-weight: bold;
            font-size: 9pt;
        }

        table.jadwal td.nama-nim .nim {
            font-size: 8pt;
            color: #555;
        }

        .predikat-badge {
            font-size: 8.5pt;
            font-weight: bold;
        }

        /* dual tanda tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 25px;
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
            padding: 0 40px;
        }

        .ttd-space {
            height: 85px;
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
            font-size: 10pt;
        }

        .niy-ttd {
            font-size: 9pt;
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
        <h2>REKAP SK YUDISIUM</h2>
        <h3>TAHUN AKADEMIK {{ $tahun_ajaran }}</h3>
    </div>

    {{-- Main Table --}}
    <table class="jadwal">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 100px;">NIM</th>
                <th style="width: 140px;">NAMA MAHASISWA</th>
                <th style="width: 100px;">PROGRAM STUDI</th>
                <th>JUDUL SKRIPSI</th>
                <th style="width: 80px;">TANGGAL UJIAN</th>
                <th style="width: 80px;">NOMOR SK</th>
                <th style="width: 60px;">IPK</th>
                <th style="width: 80px;">PREDIKAT</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $sk = $item->skripsi?->skYudisium;
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->skripsi->mahasiswa->nim ?? '-' }}</td>
                    <td class="nama-nim">
                        <div class="nama">{{ $item->skripsi->mahasiswa->nama ?? '-' }}</div>
                    </td>
                    <td>{{ $item->skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
                    <td style="font-size: 8.5pt; font-style: italic;">{{ $item->skripsi->judul ?? '-' }}</td>
                    <td class="center">
                        @if ($item->tanggal)
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">{{ $sk->nomor_sk ?? '-' }}</td>
                    <td class="center">{{ $sk->ipk_akhir ?? '-' }}</td>
                    <td class="center">
                        @if ($sk?->predikat)
                            <span class="predikat-badge">{{ ucwords(str_replace('_', ' ', $sk->predikat)) }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center" style="padding: 20px; font-style: italic;">
                        Tidak ada data SK Yudisium.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Dual Tanda Tangan --}}
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
