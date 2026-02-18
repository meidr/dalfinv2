<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SK Penguji {{ $seminar->jenis === 'sempro' ? 'Seminar Proposal' : ($seminar->jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang Skripsi') }}</title>

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

        /* tabel surat */
        .surat {
            width: 100%;
            border-collapse: collapse;
        }

        .surat td {
            vertical-align: top;
            padding: 2px 0;
        }

        /* kolom tetap */
        .c1 {
            width: 35px;
        }

        /* nomor */
        .c2 {
            width: 250px;
        }

        /* label */
        .c3 {
            width: 15px;
            text-align: center;
        }

        /* : */
        .c4 {
            width: auto;
        }

        /* isi */

        /* sub poin */
        .sub {
            padding-left: 25px;
        }

        .sub2 {
            padding-left: 50px;
        }

        /* tabel penguji */
        table.penguji-tbl {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        table.penguji-tbl th,
        table.penguji-tbl td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
            font-size: 11pt;
        }

        table.penguji-tbl th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
        }

        table.penguji-tbl td.center {
            text-align: center;
        }

        /* dual tanda tangan */
        .ttd-wrapper {
            width: 100%;
            margin-top: 30px;
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
        }

        .ttd-box {
            text-align: center;
            position: relative;
            display: inline-block;
        }

        .ttd-space {
            height: 90px;
            position: relative;
            margin-top: 5px;
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

        .nama {
            font-weight: bold;
            text-decoration: underline;
            position: relative;
            z-index: 1;
        }

        .niy {
            font-size: 11pt;
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>

    <div class="kop">
        <img src="{{ public_path('images/kop surat.jpg') }}">
    </div>

    <div class="judul">
        <h3 style="text-align: center;margin-top: 7px;">SURAT &nbsp; KEPUTUSAN</h3>
        <p style="margin-top: 2px;">TENTANG</p>
        <p style="margin-top: 2px;"><strong>PENUNJUKAN TIM PENGUJI {{ $seminar->jenis === 'sempro' ? 'SEMINAR PROPOSAL' : ($seminar->jenis === 'semhas' ? 'SEMINAR HASIL' : 'SIDANG SKRIPSI') }}</strong></p>
        <p style="margin-top: 2px;">TAHUN AKADEMIK {{ $tahun_ajaran ?? '-' }}</p>
    </div>


    <table class="surat">

        <tr class="mt-2">
            <td class="c1">1.</td>
            <td class="c2">Dasar</td>
            <td class="c3">:</td>
            <td class="c4">
                Surat Keputusan {{ $dekan['position'] ?? 'Dekan Fakultas' }}
                {{ $institution ?? "Universitas Islam Internasional Darullughah Wadda'wah" }}.
            </td>
        </tr>

        <tr class="mt-2">
            <td class="c1">2.</td>
            <td class="c2">Mahasiswa yang diuji</td>
            <td class="c3">:</td>
            <td class="c4"></td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">a. Nama</td>
            <td class="c3">:</td>
            <td class="c4">{{ $skripsi->mahasiswa->nama ?? '-' }}</td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">b. NIM / NIK</td>
            <td class="c3">:</td>
            <td class="c4">{{ $skripsi->mahasiswa->nim ?? '-' }}</td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">c. Fakultas / Prodi</td>
            <td class="c3">:</td>
            <td class="c4">{{ $fakultas ?? '-' }} / {{ $prodi_lengkap ?? '-' }}</td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">d. Judul Skripsi</td>
            <td class="c3">:</td>
            <td class="c4">"{{ $skripsi->judul ?? '-' }}"</td>
        </tr>

        <tr class="mt-2">
            <td class="c1">3.</td>
            <td class="c2">Dosen Pembimbing</td>
            <td class="c3">:</td>
            <td class="c4"></td>
        </tr>

        @foreach ($skripsi->pembimbing as $index => $p)
            <tr>
                <td class="c1 sub"></td>
                <td class="c2">{{ chr(97 + $index) }}. Nama</td>
                <td class="c3">:</td>
                <td class="c4">{{ $p->dosen->full_name ?? ($p->dosen->nama ?? '-') }}</td>
            </tr>

            <tr>
                <td class="c1"></td>
                <td class="c2 sub2">&nbsp;&nbsp;&nbsp;&nbsp;NIP/NIY</td>
                <td class="c3">:</td>
                <td class="c4">{{ $p->dosen->nip ?? '-' }}</td>
            </tr>

            <tr>
                <td class="c1"></td>
                <td class="c2 sub2">&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td class="c3">:</td>
                <td class="c4">{{ $p->jenis == 'pembimbing_1' ? 'Pembimbing I (Utama)' : 'Pembimbing II' }}</td>
            </tr>
        @endforeach

        <tr class="mt-2">
            <td class="c1">4.</td>
            <td class="c2">Jadwal {{ $seminar->jenis === 'sempro' ? 'Seminar Proposal' : ($seminar->jenis === 'semhas' ? 'Seminar Hasil' : 'Sidang') }}</td>
            <td class="c3">:</td>
            <td class="c4"></td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">a. Hari / Tanggal</td>
            <td class="c3">:</td>
            <td class="c4">{{ $seminar->tanggal ? $seminar->tanggal->translatedFormat('l, d F Y') : '-' }}</td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">b. Waktu</td>
            <td class="c3">:</td>
            <td class="c4">{{ $seminar->waktu ? \Carbon\Carbon::parse($seminar->waktu)->format('H:i') : '-' }} WIB
            </td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">c. Tempat / Ruangan</td>
            <td class="c3">:</td>
            <td class="c4">{{ $seminar->ruangan ?? '-' }}</td>
        </tr>

        <tr class="mt-2">
            <td class="c1">5.</td>
            <td class="c2">Tim Penguji yang ditunjuk</td>
            <td class="c3">:</td>
            <td class="c4"></td>
        </tr>

    </table>


    {{-- Tabel Tim Penguji --}}
    <table class="penguji-tbl" style="margin-left: 35px; width: calc(100% - 35px);">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Dosen Penguji</th>
                <th style="width: 120px;">NIP/NIY</th>
                <th style="width: 110px;">Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($seminar->penguji as $index => $p)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $p->dosen->full_name ?? ($p->dosen->nama ?? '-') }}</td>
                    <td class="center">{{ $p->dosen->nip ?? '-' }}</td>
                    <td class="center">
                        @if ($p->peran === 'ketua')
                            Ketua Penguji
                        @elseif($p->peran === 'sekretaris')
                            Sekretaris
                        @else
                            Anggota Penguji
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center"><em>Belum ada penguji ditunjuk</em></td>
                </tr>
            @endforelse
        </tbody>
    </table>


    <table class="surat" style="margin-top: 8px;">
        <tr>
            <td class="c1">6.</td>
            <td class="c2">Catatan penting</td>
            <td class="c3">:</td>
            <td class="c4">
                Harap dilaksanakan dengan penuh tanggung jawab dan sesuai ketentuan yang berlaku.
            </td>
        </tr>
    </table>


    {{-- Dual Tanda Tangan: Mengetahui (Dekan) + Menetapkan (Kaprodi) --}}
    <div class="ttd-wrapper">
        <table>
            <tr>
                <td style="text-align: center;">
                    <div>Mengetahui,</div>
                    <div>{{ $dekan['position'] ?? 'Dekan Fakultas' }}</div>

                    <div class="ttd-space">
                        @if (!empty($dekan['signature']))
                            <img class="ttd-img" src="{{ $dekan['signature'] }}">
                        @endif
                        <img class="cap" src="{{ public_path('images/cap.jpg') }}">
                    </div>

                    <div class="nama">{{ $dekan['name'] ?? 'Nama Dekan' }}</div>
                    <div class="niy">NIP/NIY: {{ $dekan['nip'] ?? '-' }}</div>
                </td>
                <td style="text-align: center;">
                    <div>{{ $city ?? 'Bangil' }}, {{ $tanggal }}</div>
                    <div>{{ $kaprodi['position'] ?? 'Kepala Program Studi' }}</div>

                    <div class="ttd-space">
                        @if (!empty($kaprodi['signature']))
                            <img class="ttd-img" src="{{ $kaprodi['signature'] }}">
                        @endif
                        <img class="cap" src="{{ public_path('images/cap.jpg') }}">
                    </div>

                    <div class="nama">{{ $kaprodi['name'] ?? 'Nama Kaprodi' }}</div>
                    <div class="niy">NIP/NIY: {{ $kaprodi['nip'] ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
