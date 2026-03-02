<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara {{ $jenisLabel }}</title>
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

        /* penguji table */
        table.penguji {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table.penguji th,
        table.penguji td {
            border: 1px solid #000;
            padding: 7px 10px;
            text-align: left;
            font-size: 11pt;
        }

        table.penguji th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        /* result box */
        .result-box {
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #000;
            text-align: center;
        }

        .result-box h4 {
            font-size: 13pt;
            margin-bottom: 8px;
        }

        .result-box .result {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .result-box .nilai {
            margin-top: 8px;
            font-size: 12pt;
        }

        /* signatures */
        .signatures {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .signatures table {
            width: 100%;
        }

        .signatures td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 8px;
            position: relative;
        }

        .signatures .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 70px;
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

        .ttd-cap img.cap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin-left: -15px;
            top: -15px;
            width: 120px;
            opacity: 0.8;
        }

        .catatan {
            margin: 15px 0;
        }

        .catatan-content {
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
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
        <h3>BERITA ACARA</h3>
        <p><strong>{{ strtoupper($jenisLabel) }}</strong></p>
        <p>Nomor: {{ $beritaAcara->nomor }}</p>
    </div>

    <p style="text-indent: 30px; margin-bottom: 15px;">
        Pada hari ini, {{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('l') }},
        tanggal {{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('d F Y') }},
        telah dilaksanakan <strong>{{ $jenisLabel }}</strong> untuk:
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
            <td>Tempat / Ruangan</td>
            <td>:</td>
            <td>{{ $seminar->ruangan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>{{ $seminar->waktu ? \Carbon\Carbon::parse($seminar->waktu)->format('H:i') : '-' }} WIB</td>
        </tr>
    </table>

    {{-- Dosen Pembimbing --}}
    <p style="margin: 10px 0 5px 0;">Dosen Pembimbing:</p>
    <table class="info">
        @foreach ($seminar->skripsi->pembimbing ?? [] as $pb)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $pb->jenis ?? 'Pembimbing')) }}</td>
                <td>:</td>
                <td>{{ $pb->dosen->full_name ?? ($pb->dosen->nama ?? '-') }} (NIP/NIY: {{ $pb->dosen->nip ?? '-' }})
                </td>
            </tr>
        @endforeach
    </table>

    <p style="margin: 15px 0 5px 0;">Dengan susunan Tim Penguji sebagai berikut:</p>

    {{-- Penguji Table --}}
    <table class="penguji">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Dosen</th>
                <th style="width: 120px;">NIP/NIY</th>
                <th style="width: 100px;">Jabatan</th>
                <th style="width: 70px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($seminar->penguji as $index => $p)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $p->dosen->full_name ?? ($p->dosen->nama ?? '-') }}</td>
                    <td style="text-align: center;">{{ $p->dosen->nip ?? '-' }}</td>
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
                    <td style="text-align: center;">{{ $p->nilai ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Hasil --}}
    <div class="result-box">
        <h4>HASIL {{ strtoupper($jenisLabel) }}</h4>
        <p class="result">{{ str_replace('_', ' ', strtoupper($beritaAcara->hasil ?? ($seminar->hasil ?? '-'))) }}</p>
        @if ($seminar->nilai)
            @php
                $nilaiNum = floatval($seminar->nilai);
                if ($nilaiNum >= 85) {
                    $grade = 'A';
                } elseif ($nilaiNum >= 80) {
                    $grade = 'B+';
                } elseif ($nilaiNum >= 70) {
                    $grade = 'B';
                } elseif ($nilaiNum >= 65) {
                    $grade = 'C+';
                } elseif ($nilaiNum >= 55) {
                    $grade = 'C';
                } else {
                    $grade = 'D';
                }
            @endphp
            <p class="nilai">Nilai: <strong>{{ $seminar->nilai }}</strong> &nbsp;|&nbsp; Grade:
                <strong>{{ $grade }}</strong>
            </p>
        @endif
    </div>

    @if ($beritaAcara->catatan || $seminar->catatan)
        <div class="catatan">
            <p><strong>Catatan:</strong></p>
            <p class="catatan-content">{{ $beritaAcara->catatan ?? $seminar->catatan }}</p>
        </div>
    @endif

    {{-- Tanda Tangan Penguji --}}
    <div class="signatures">
        <table>
            <tr>
                @foreach ($seminar->penguji->take(3) as $p)
                    <td>
                        @php
                            $peranLabel = $p->peran;
                            if ($peranLabel === 'penguji_1') {
                                $peranLabel = 'Penguji 1';
                            } elseif ($peranLabel === 'penguji_2') {
                                $peranLabel = 'Penguji 2';
                            } else {
                                $peranLabel = ucfirst(str_replace('_', ' ', $peranLabel));
                            }

                            $ttdPath = null;
                            if ($p->dosen) {
                                $ttdModel = \App\Models\TandaTangan::where('dosen_id', $p->dosen->id)->first();
                                if ($ttdModel && $ttdModel->ttd) {
                                    $path = storage_path('app/public/' . $ttdModel->ttd);
                                    if (file_exists($path)) {
                                        $ttdPath = $path;
                                    }
                                }
                            }
                        @endphp
                        <p>{{ $peranLabel }},</p>
                        @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                            <div class="ttd-cap" style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $qrData['qr_base64'] }}" style="width: 80px; height: 80px;" alt="QR">
                            </div>
                        @else
                            <div class="ttd-cap" style="position: relative; height: 80px;">
                                @if ($ttdPath)
                                    <img src="{{ $ttdPath }}" style="position: absolute; left: 50%; transform: translateX(-50%); top: -5px; width: 80px; z-index: 3;" alt="TTD">
                                @endif
                                <img class="cap" src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); top: -15px; margin-left: -15px; width: 120px; opacity: 0.8; z-index: 2;">
                            </div>
                        @endif
                        <p class="name" style="margin-top: {{ (isset($signatureMode) && $signatureMode === 'qr') ? '0' : '40px' }};">{{ $p->dosen->full_name ?? ($p->dosen->nama ?? '-') }}</p>
                        <p class="nip">NIP/NIY. {{ $p->dosen->nip ?? '-' }}</p>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div style="clear: both; margin-top: 30px; font-size: 9pt; color: #666; text-align: center;">
        <p>Berita Acara ini dibuat sebagai bukti pelaksanaan {{ $jenisLabel }}</p>
        <p>Dicetak pada: {{ $tanggal }}</p>
    </div>

    {{-- ========== PAGE 2: LEMBAR PERBAIKAN PROPOSAL (only for sempro) ========== --}}
    @if ($seminar->jenis === 'sempro' && isset($perbaikan) && $perbaikan->count() > 0)
        <div style="page-break-before: always;"></div>

        {{-- Kop Surat --}}
        <div class="kop">
            <img src="{{ public_path('images/kop surat.jpg') }}" alt="Kop Surat">
        </div>

        {{-- Title --}}
        <div class="judul">
            <h3>LEMBAR PERBAIKAN PROPOSAL</h3>
        </div>

        {{-- Mahasiswa Info --}}
        <table class="info">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $seminar->skripsi->mahasiswa->nama }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $seminar->skripsi->mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Fakultas/Prodi</td>
                <td>:</td>
                <td>{{ $seminar->skripsi->mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }} / {{ $seminar->skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $seminar->skripsi->semester_daftar ?? '-' }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>:</td>
                <td>{{ $seminar->skripsi->judul }}</td>
            </tr>
        </table>

        <p style="margin: 15px 0 10px 0; text-indent: 30px;">
            Setelah diadakan seminar atas proposal saudara tersebut di atas, maka kami menyarankan diadakan
            perbaikan proposal tersebut sebagaimana di bawah ini :
        </p>

        {{-- Perbaikan Table --}}
        <table class="penguji" style="margin-top: 10px;">
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

        {{-- Signature: Ketua Penguji --}}
        @if (isset($ketuaPenguji))
            <div style="margin-top: 40px; text-align: right; padding-right: 40px;">
                <p>Pasuruan, {{ \Carbon\Carbon::parse($seminar->tanggal)->translatedFormat('d F Y') }}</p>
                <p style="margin-top: 5px;">Ketua Penguji Seminar Proposal</p>
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
    @endif

</body>

</html>
