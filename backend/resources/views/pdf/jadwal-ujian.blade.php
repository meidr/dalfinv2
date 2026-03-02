<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Jadwal Ujian Skripsi</title>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-content {
            margin: 10px 30px 20px 30px;
        }

        /* Kop Surat */
        .kop-surat {
            text-align: center;
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

        /* header */
        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h2 {
            font-size: 13pt;
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

        /* info bar */
        .info-bar {
            width: 100%;
            margin-bottom: 8px;
        }

        .info-bar td {
            font-size: 10pt;
            vertical-align: top;
            padding: 2px 0;
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

        .penguji-item {
            margin-bottom: 2px;
            font-size: 8.5pt;
        }

        .penguji-item .peran {
            font-weight: bold;
            font-size: 8pt;
        }

        .pembimbing-item {
            margin-bottom: 2px;
            font-size: 8.5pt;
        }

        .judul-skripsi {
            font-size: 8.5pt;
            font-style: italic;
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
            height: 90px;
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
    @if (file_exists($kop_path))
        <div class="kop-surat">
            <img src="{{ $kop_path }}">
        </div>
        <hr class="kop-line">
    @endif

    <div class="page-content">
        {{-- Header --}}
        <div class="header">
            <h2>JADWAL UJIAN SKRIPSI</h2>
            <h3>
                @if ($semester_label)
                    {{ strtoupper($semester_label) }}
                @else
                    TAHUN AKADEMIK {{ $tahun_ajaran }}
                @endif
            </h3>
            <p style="font-size: 11pt; font-weight: bold; margin-top: 2px;">
                {{ $fakultas_name ? 'FAKULTAS ' . strtoupper($fakultas_name) : 'SEMUA FAKULTAS' }}
                &mdash;
                {{ $prodi_name ? 'PRODI ' . strtoupper($prodi_name) : 'SEMUA PRODI' }}
            </p>
        </div>

        {{-- Info Bar --}}
        @php
            $firstDate = $ujianList->first()?->tanggal;
            $lastDate = $ujianList->last()?->tanggal;
            $firstRoom = $ujianList->first()?->ruangan;
        @endphp

        <table class="info-bar">
            <tr>
                <td style="width: 60%; text-align: left;">
                    <strong>Hari, Tanggal :</strong>
                    @if ($firstDate)
                        {{ $firstDate->translatedFormat('l, d F Y') }}
                        @if ($lastDate && $firstDate->ne($lastDate))
                            s/d {{ $lastDate->translatedFormat('l, d F Y') }}
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td style="width: 40%; text-align: right;">
                    <strong>Tempat :</strong> UII DALWA
                </td>
            </tr>
        </table>

        {{-- Main Table --}}
        <table class="jadwal">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th style="width: 70px;">JAM</th>
                    <th style="width: 60px;">RUANGAN</th>
                    <th style="width: 140px;">NAMA / NIM / PRODI</th>
                    <th style="width: 130px;">PEMBIMBING SKRIPSI</th>
                    <th style="width: 150px;">TIM PENGUJI SKRIPSI</th>
                    <th>JUDUL SKRIPSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ujianList as $index => $u)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">
                            @if ($u->waktu)
                                {{ \Carbon\Carbon::parse($u->waktu)->format('H.i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="center">{{ $u->ruangan ?? '-' }}</td>
                        <td class="nama-nim">
                            <div class="nama">{{ $u->skripsi->mahasiswa->nama ?? '-' }}</div>
                            <div class="nim">{{ $u->skripsi->mahasiswa->nim ?? '-' }}</div>
                            <div class="nim">{{ $u->skripsi->mahasiswa->prodi->nama ?? '-' }}</div>
                        </td>
                        <td>
                            @foreach ($u->skripsi->pembimbing ?? [] as $pb)
                                <div class="pembimbing-item">
                                    {{ $pb->dosen->full_name ?? ($pb->dosen->nama ?? '-') }}
                                </div>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($u->penguji ?? [] as $pg)
                                <div class="penguji-item">
                                    <span class="peran">
                                        @if ($pg->peran === 'ketua')
                                            Ketua :
                                        @elseif ($pg->peran === 'penguji_1')
                                            Penguji I :
                                        @elseif ($pg->peran === 'penguji_2')
                                            Penguji II :
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $pg->peran)) }} :
                                        @endif
                                    </span>
                                    {{ $pg->dosen->full_name ?? ($pg->dosen->nama ?? '-') }}
                                </div>
                            @endforeach
                        </td>
                        <td class="judul-skripsi">
                            {{ $u->skripsi->judul ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center" style="padding: 20px; font-style: italic;">
                            Tidak ada data ujian skripsi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        {{-- Dual Tanda Tangan --}}
        <div class="ttd-wrapper">
            <table>
                <tr>
                    {{-- Dekan (left) --}}
                    <td>
                        <div>Mengetahui,</div>
                        <div>{{ $dekan['position'] ?? 'Dekan Fakultas' }}</div>

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

                    {{-- Kaprodi (right) --}}
                    <td>
                        <div>{{ $city ?? 'Pasuruan' }}, {{ $tanggal }}</div>
                        <div>{{ $kaprodi['position'] ?? 'Kepala Program Studi' }}</div>

                        <div class="ttd-space">
                            @if (($signature_mode ?? 'biasa') === 'qr' && !empty($qr_kaprodi))
                                <img class="qr-img" src="{{ $qr_kaprodi['qr_base64'] }}">
                            @else
                                @if (!empty($kaprodi['signature']))
                                    <img class="ttd-img" src="{{ $kaprodi['signature'] }}">
                                @endif
                                @if (file_exists($cap_path ?? ''))
                                    <img class="cap" src="{{ $cap_path }}">
                                @endif
                            @endif
                        </div>

                        <div class="nama-ttd">{{ $kaprodi['name'] ?? 'Nama Kaprodi' }}</div>
                        <div class="niy-ttd">NIDN/NIY : {{ $kaprodi['nip'] ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
