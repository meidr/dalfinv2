<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SK Tugas Pembimbing</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
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
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10pt;
        }
        .title {
            text-align: center;
            margin: 30px 0;
        }
        .title h3 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .title p {
            font-size: 11pt;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .content p {
            margin-bottom: 10px;
            text-indent: 40px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table.data td {
            padding: 5px;
            vertical-align: top;
        }
        table.data td:first-child {
            width: 150px;
        }
        table.data td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        table.pembimbing {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table.pembimbing th,
        table.pembimbing td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table.pembimbing th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .signature {
            margin-top: 40px;
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature .date {
            margin-bottom: 60px;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature .title-pos {
            font-size: 11pt;
        }
        .footer {
            clear: both;
            margin-top: 100px;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
        <h2>UNIVERSITAS NEGERI</h2>
        <h1>FAKULTAS TEKNIK</h1>
        <p>Jl. Pendidikan No. 1, Kota Pendidikan 12345</p>
        <p>Telp: (021) 1234567, Email: ft@univ.ac.id</p>
    </div>

    <div class="title">
        <h3>SURAT KEPUTUSAN</h3>
        <p>Nomor: {{ $skTugas->nomor }}</p>
        <p style="margin-top: 10px;">Tentang</p>
        <p><strong>PENUNJUKAN DOSEN PEMBIMBING SKRIPSI</strong></p>
    </div>

    <div class="content">
        <p>Dekan Fakultas Teknik Universitas Negeri, berdasarkan:</p>

        <ol style="margin-left: 60px; margin-top: 10px; margin-bottom: 15px;">
            <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
            <li>Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi;</li>
            <li>Peraturan Akademik Universitas tentang Penulisan Skripsi;</li>
        </ol>

        <p style="text-align: center; font-weight: bold; margin: 20px 0;">MEMUTUSKAN</p>

        <p>Menunjuk Dosen Pembimbing Skripsi untuk mahasiswa:</p>

        <table class="data">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $skripsi->mahasiswa->nama }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $skripsi->mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>:</td>
                <td>{{ $skripsi->judul }}</td>
            </tr>
        </table>

        <p>Dengan susunan Dosen Pembimbing sebagai berikut:</p>

        <table class="pembimbing">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Dosen</th>
                    <th style="width: 150px;">Jabatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($skripsi->pembimbing as $index => $p)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $p->dosen->nama_lengkap }}</td>
                    <td>{{ $p->jenis === 'pembimbing_1' ? 'Pembimbing I' : 'Pembimbing II' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p>Surat Keputusan ini berlaku sejak tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan akan diadakan perbaikan sebagaimana mestinya.</p>
    </div>

    <div class="signature">
        <p class="date">Ditetapkan di: Kota Pendidikan</p>
        <p>Pada tanggal: {{ $tanggal }}</p>
        <br><br>
        <p>Dekan,</p>
        <br><br><br>
        <p class="name">Prof. Dr. Nama Dekan, M.T.</p>
        <p class="title-pos">NIP. 196001011990011001</p>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh Sistem Informasi Skripsi</p>
    </div>
</body>
</html>
