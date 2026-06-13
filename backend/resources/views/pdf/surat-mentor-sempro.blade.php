<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Mentor Seminar Proposal</title>

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
            margin: 6px 0 20px 0;
        }

        .judul h3 {
            font-size: 16pt;
            letter-spacing: 2px;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* tabel surat */
        .surat {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .surat td {
            vertical-align: top;
            padding: 4px 0;
        }

        /* kolom tetap */
        .c1 {
            width: 35px;
        }
        .c2 {
            width: 150px;
        }
        .c3 {
            width: 15px;
            text-align: center;
        }
        .c4 {
            width: auto;
        }

        /* tanda tangan */
        .ttd {
            width: 300px;
            margin-left: auto;
            text-align: center;
            margin-top: 50px;
            position: relative;
        }

        .ttd-date,
        .ttd-title {
            position: relative;
            z-index: 3;
        }

        /* nama di atas layer cap/ttd */
        .nama {
            font-weight: bold;
            text-decoration: underline;
            position: relative;
            z-index: 3;
        }

        .niy {
            font-size: 11pt;
            position: relative;
            z-index: 3;
        }

        .ttd-space {
            position: relative;
            height: 80px;
            margin-top: 5px;
        }

        /* CAP */
        .ttd-space img.cap {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin-left: -120px;
            top: -85px;
            width: 250px;
            opacity: .8;
            z-index: 2;
        }

        /* TTD di belakang semua teks */
        .ttd-space img.ttd-img {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 5px;
            width: 210px;
            z-index: 1;
        }
    </style>
</head>

<body>

    <div class="kop">
        <img src="{{ public_path('images/kop surat.jpg') }}">
    </div>

    <div class="judul">
        <h3 style="text-align: center;margin-top: 7px;">SURAT MENTOR SEMINAR PROPOSAL</h3>
    </div>

    <p style="text-indent: 30px; text-align: justify;">
        Ketua Program Studi {{ $prodi_nama ?? '' }} Universitas Islam Internasional Darullughah Wadda'wah, dengan ini menugaskan:
    </p>

    <table class="surat" style="margin-left: 30px; width: 90%;">
        @foreach ($skripsi->mentorSempro as $index => $m)
            <tr>
                <td class="c2">{{ $index + 1 }}. Nama</td>
                <td class="c3">:</td>
                <td class="c4"><strong>{{ $m->dosen->full_name ?? ($m->dosen->nama ?? '-') }}</strong></td>
            </tr>
            <tr>
                <td class="c2">&nbsp;&nbsp;&nbsp;&nbsp;NIP/NIY</td>
                <td class="c3">:</td>
                <td class="c4">{{ $m->dosen->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="c2">&nbsp;&nbsp;&nbsp;&nbsp;Sebagai</td>
                <td class="c3">:</td>
                <td class="c4">Mentor Seminar Proposal {{ $m->jenis == 'mentor_1' ? 'Utama' : 'Pendamping' }}</td>
            </tr>
        @endforeach
    </table>

    <p style="text-align: justify;">
        Untuk memberikan mentoring dalam persiapan penyusunan dan seminar proposal skripsi kepada mahasiswa:
    </p>

    <table class="surat" style="margin-left: 30px; width: 90%;">
        <tr>
            <td class="c2">Nama</td>
            <td class="c3">:</td>
            <td class="c4"><strong>{{ $skripsi->mahasiswa->nama ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="c2">NIM / NIK</td>
            <td class="c3">:</td>
            <td class="c4">{{ $skripsi->mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="c2">Fakultas / Prodi</td>
            <td class="c3">:</td>
            <td class="c4">{{ $skripsi->mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }} / {{ $skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="c2">Judul Skripsi</td>
            <td class="c3">:</td>
            <td class="c4" style="text-align: justify;">"{{ $skripsi->judul ?? '-' }}"</td>
        </tr>
    </table>

    <p style="text-indent: 30px; text-align: justify;">
        Demikian surat tugas mentoring ini diberikan untuk dapat dilaksanakan dengan sebaik-baiknya dan penuh tanggung jawab.
    </p>

    <div class="ttd">
        <div class="ttd-date">Bangil, {{ $tanggal }}</div>
        <div class="ttd-title">Kepala Program Studi {{ $prodi_nama ?? '' }}</div>

        <div class="ttd-space">
            @if (!empty($signer['signature']))
                <img class="ttd-img" src="{{ $signer['signature'] }}">
            @endif
            <img class="cap" src="{{ public_path('images/capori.png') }}">
        </div>

        <div class="nama">{{ $signer['name'] ?? '-' }}</div>
        <div class="niy">NIY: {{ $signer['nip'] ?? '-' }}</div>
    </div>

</body>

</html>
