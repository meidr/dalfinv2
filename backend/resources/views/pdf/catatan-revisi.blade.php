<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Catatan Revisi {{ $jenisLabel }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            padding: 10px 55px 25px 55px;
        }

        /* kop */
        .kop {
            text-align: center;
            margin-bottom: 6px;
        }

        .kop img {
            width: 100%;
        }

        /* judul */
        .judul {
            text-align: center;
            margin: 6px 0 12px 0;
        }

        .judul h3 {
            font-size: 16pt;
            letter-spacing: 4px;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .judul p {
            font-size: 11pt;
            margin: 0;
        }

        /* info table */
        table.info {
            width: 100%;
            margin: 15px 0;
        }

        table.info td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 12pt;
        }

        table.info td:first-child {
            width: 160px;
        }

        table.info td:nth-child(2) {
            width: 15px;
        }

        /* catatan table */
        table.catatan {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table.catatan th,
        table.catatan td {
            border: 1px solid #000;
            padding: 7px 10px;
            text-align: left;
            font-size: 11pt;
        }

        table.catatan th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        /* signatures */
        .signatures {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .signatures .name {
            font-weight: bold;
            text-decoration: underline;
            display: inline-block;
        }

        .nip {
            font-size: 10pt;
        }

        .ttd-cap {
            position: relative;
            height: 80px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    {{-- Kop Surat --}}
    <div class="kop">
        <img src="{{ public_path('images/kop surat.jpg') }}" alt="Kop Surat">
    </div>

    {{-- Judul --}}
    <div class="judul">
        <h3>CATATAN REVISI</h3>
        <p><strong>{{ strtoupper($jenisLabel) }}</strong></p>
    </div>

    <p style="text-indent: 30px; margin-bottom: 15px;">
        Berdasarkan pelaksanaan <strong>{{ $jenisLabel }}</strong> pada tanggal
        {{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('d F Y') }},
        dengan hasil <strong>Lulus Bersyarat (Revisi)</strong>,
        berikut adalah catatan revisi yang harus dipenuhi oleh mahasiswa:
    </p>

    {{-- Mahasiswa Info --}}
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
            <td>Fakultas</td>
            <td>:</td>
            <td>{{ $seminar->skripsi->mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</td>
        </tr>
        <tr>
            <td>Judul Skripsi</td>
            <td>:</td>
            <td>{{ $seminar->skripsi->judul }}</td>
        </tr>
        <tr>
            <td>Tanggal {{ $jenisLabel }}</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    {{-- Catatan dari Berita Acara --}}
    @if (!empty($catatanUmum))
        <p style="margin: 15px 0 5px 0;"><strong>Catatan Umum:</strong></p>
        <div style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 15px;">
            {{ $catatanUmum }}
        </div>
    @endif

    {{-- Catatan Per Penguji --}}
    @if ($catatanPenguji->count() > 0)
        <p style="margin: 15px 0 5px 0;"><strong>Catatan Revisi dari Penguji:</strong></p>
        <table class="catatan">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Dosen Penguji</th>
                    <th style="width: 100px;">Jabatan</th>
                    <th>Catatan Revisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($catatanPenguji as $index => $p)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $p->dosen->full_name ?? ($p->dosen->nama ?? '-') }}</td>
                        <td style="text-align: center;">
                            @php
                                $jabLabel = $p->peran;
                                if ($jabLabel === 'penguji_1') {
                                    $jabLabel = 'Penguji 1';
                                } elseif ($jabLabel === 'penguji_2') {
                                    $jabLabel = 'Penguji 2';
                                } else {
                                    $jabLabel = ucfirst(str_replace('_', ' ', $jabLabel));
                                }
                            @endphp
                            {{ $jabLabel }}
                        </td>
                        <td>{{ $p->catatan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Perbaikan Proposal (khusus sempro) --}}
    @if (isset($perbaikan) && $perbaikan->count() > 0)
        <p style="margin: 15px 0 5px 0;"><strong>Detail Perbaikan Proposal:</strong></p>
        <table class="catatan" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th style="width: 140px;">TOPIK</th>
                    <th style="width: 80px;">HALAMAN</th>
                    <th>URAIAN PERBAIKAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perbaikan as $item)
                    <tr>
                        <td style="text-align: center; vertical-align: top;">{{ $item->no }}</td>
                        <td style="vertical-align: top;">{{ $item->topik }}</td>
                        <td style="text-align: center; vertical-align: top;">{{ $item->halaman ?? '-' }}</td>
                        <td style="vertical-align: top;">{{ $item->uraian ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin: 20px 0 5px 0;">
        Demikian catatan revisi ini dibuat untuk ditindaklanjuti oleh mahasiswa yang bersangkutan.
    </p>

    {{-- Tanda Tangan Ketua Penguji --}}
    @if (isset($ketuaPenguji))
        <div class="signatures" style="text-align: right; padding-right: 40px;">
            <p>Pasuruan, {{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('d F Y') }}</p>
            <p style="margin-top: 5px;">Ketua Penguji {{ $jenisLabel }}</p>
            @php
                $ketuaTtdPath = null;
                if (isset($ketuaPenguji) && $ketuaPenguji->dosen) {
                    $kttdModel = \App\Models\TandaTangan::where('dosen_id', $ketuaPenguji->dosen->id)->first();
                    if ($kttdModel && $kttdModel->ttd) {
                        $kpath = storage_path('app/public/' . $kttdModel->ttd);
                        if (file_exists($kpath)) {
                            $ketuaTtdPath = $kpath;
                        }
                    }
                }
            @endphp
            @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                <div style="height: 100px; display: flex; align-items: center; justify-content: center; margin-top: 5px;">
                    <img src="{{ $qrData['qr_base64'] }}" style="width: 90px; height: 90px;" alt="QR">
                </div>
                <p style="font-size: 7pt; color: #666; margin: 2px 0 4px 0;">Scan QR untuk verifikasi</p>
            @else
                <div class="ttd-cap" style="display: inline-block; position: relative; width: 150px; height: 80px; margin-top: 5px;">
                    @if ($ketuaTtdPath)
                        <img src="{{ $ketuaTtdPath }}" style="position: absolute; left: 50%; transform: translateX(-50%); top: 5px; width: 90px; z-index: 3;" alt="TTD">
                    @endif
                    <img class="cap" src="{{ public_path('images/capori.png') }}" style="position: absolute; left: 50%; transform: translateX(-50%); top: -10px; width: 110px; opacity: 0.85; z-index: 2;">
                </div>
            @endif
            <p class="name" style="margin-top: {{ (isset($signatureMode) && $signatureMode === 'qr') ? '0' : '20px' }};">{{ $ketuaPenguji->dosen->full_name ?? ($ketuaPenguji->dosen->nama ?? '-') }}</p>
            <p class="nip">NIP/NIY. {{ $ketuaPenguji->dosen->nip ?? '-' }}</p>
        </div>
    @endif

    <div style="clear: both; margin-top: 30px; font-size: 9pt; color: #666; text-align: center;">
        <p>Catatan revisi ini dibuat sebagai tindak lanjut pelaksanaan {{ $jenisLabel }}</p>
        <p>Dicetak pada: {{ $tanggal }}</p>
    </div>

</body>

</html>
