<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Tugas Pembimbing</title>

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

        /* a,b,c */
        .sub2 {
            padding-left: 50px;
        }

        /* isi dalam a */

        /* tanda tangan */
        /* tanda tangan */
        .ttd {
            width: 300px;
            margin-left: auto;
            text-align: center;
            margin-top: 35px;
            position: relative;
        }

        /* nama jadi dasar layer */
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

        /* area ttd */
        .ttd-space {
            height: 90px;
            position: relative;
            margin-top: 5px;
        }

        /* CAP di atas nama */
        .ttd-space img.cap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin-left: -80px;
            top: -25px;
            /* turun menimpa nama */
            width: 150px;
            opacity: .8;
            z-index: 2;
        }

        /* TTD paling atas */
        .ttd-space img.ttd-img {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: -5px;
            width: 85px;
            z-index: 3;
        }
    </style>
</head>

<body>

    <div class="kop">
        <img src="{{ public_path('images/kop surat.jpg') }}">
    </div>

    <div class="judul">
        <h3 style="text-align: center;margin-top: 7px;">SURAT &nbsp; TUGAS</h3>
        Nomor : {{ $skTugas->nomor_sk ?? ($skTugas->nomor ?? '-') }}
    </div>


    <table class="surat">

        <tr class="mt-2">
            <td class="c1">1.</td>
            <td class="c2">Lembaga yang memberi tugas</td>
            <td class="c3">:</td>
            <td class="c4">
                {{ $signer['position'] ?? 'Kepala Prodi' }} {{ $prodi_lengkap ?? '' }}
                {{ $signer['institution'] ?? "Universitas Islam Internasional Darullughah Wadda'wah" }},
                {{ $signer['city'] ?? 'Bangil' }}.
            </td>
        </tr>

        <tr class="mt-2">
            <td class="c1">2.</td>
            <td class="c2">Dosen yang diberi tugas</td>
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
                <td class="c2 sub2">&nbsp;&nbsp;&nbsp;&nbsp;Tugas</td>
                <td class="c3">:</td>
                <td class="c4">{{ $p->jenis == 'pembimbing_1' ? 'Pembimbing I (Utama)' : 'Pembimbing II' }}</td>
            </tr>
        @endforeach

        <tr class="mt-2">
            <td class="c1">3.</td>
            <td class="c2">Diberi tugas untuk</td>
            <td class="c3">:</td>
            <td class="c4">Membimbing Skripsi</td>
        </tr>

        <tr class="mt-2">
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
            <td class="c4">{{ $skripsi->mahasiswa->prodi->fakultas ?? '-' }} /
                {{ $skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
        </tr>

        <tr>
            <td class="c1 sub"></td>
            <td class="c2">d. Judul Skripsi</td>
            <td class="c3">:</td>
            <td class="c4">"{{ $skripsi->judul ?? '-' }}"</td>
        </tr>

        <tr class="mt-2">
            <td class="c1">4.</td>
            <td class="c2">Masa penugasan</td>
            <td class="c3">:</td>
            <td class="c4">{{ $tanggal }} s/d selesai</td>
        </tr>

        <tr class="mt-2">
            <td class="c1">5.</td>
            <td class="c2">Catatan penting</td>
            <td class="c3">:</td>
            <td class="c4">
                Harap dilaksanakan dengan penuh tanggung jawab dan terstandar (Sesuai buku pedoman penulisan skripsi).
            </td>
        </tr>

    </table>


    <div class="ttd">
        <div>{{ $signer['city'] ?? 'Bangil' }}, {{ $tanggal }}</div>
        <div>{{ $signer['position'] ?? 'Kepala Prodi' }}</div>

        <div class="ttd-space">
            @if (!empty($signer['signature']))
                <img class="ttd-img" src="{{ $signer['signature'] }}">
            @endif
            <img class="cap" src="{{ public_path('images/cap.jpg') }}">
        </div>

        <div class="nama">{{ $signer['name'] ?? '-' }}</div>
        <div class="niy">NIY: {{ $signer['nip'] ?? '-' }}</div>
    </div>

</body>

</html>
