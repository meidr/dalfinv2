<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nota Bimbingan Skripsi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            padding: 20px 30px;
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
            font-size: 15pt;
            font-weight: bold;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
        }
        table.info {
            width: 100%;
            margin: 15px 0;
        }
        table.info td {
            padding: 3px 5px;
            vertical-align: top;
        }
        table.info td:first-child {
            width: 140px;
        }
        table.info td:nth-child(2) {
            width: 15px;
        }
        table.bimbingan {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }
        table.bimbingan th,
        table.bimbingan td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        table.bimbingan th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table.bimbingan td.center {
            text-align: center;
        }
        .summary {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures table {
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signatures .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 60px;
            display: inline-block;
        }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UNIVERSITAS NEGERI</h1>
        <h2>FAKULTAS TEKNIK</h2>
        <p style="font-size: 10pt;">Jl. Pendidikan No. 1, Kota Pendidikan 12345</p>
    </div>

    <div class="title">
        <h3>KARTU/NOTA BIMBINGAN SKRIPSI</h3>
        <p style="font-size: 10pt; margin-top: 5px;">Nomor: {{ $nota->nomor }}</p>
    </div>

    <table class="info">
        <tr>
            <td>Nama Mahasiswa</td>
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
        <tr>
            <td>Pembimbing I</td>
            <td>:</td>
            <td>{{ $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first()?->dosen->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pembimbing II</td>
            <td>:</td>
            <td>{{ $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first()?->dosen->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <table class="bimbingan">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th>Topik/Materi Bimbingan</th>
                <th style="width: 100px;">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bimbingan as $index => $b)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($b->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $b->topik }}@if($b->deskripsi)<br><small>{{ Str::limit($b->deskripsi, 100) }}</small>@endif</td>
                <td class="center">✓</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="center">Belum ada catatan bimbingan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Bimbingan:</strong> {{ $bimbingan->count() }} kali</p>
        <p><strong>Tanggal Cetak:</strong> {{ $tanggal }}</p>
    </div>

    <div class="signatures">
        <table>
            <tr>
                <td>
                    <p>Pembimbing I,</p>
                    <p class="name">{{ $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first()?->dosen->nama_lengkap ?? '________________' }}</p>
                    <p style="font-size: 10pt;">NIP. {{ $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first()?->dosen->nip ?? '-' }}</p>
                </td>
                <td>
                    <p>Pembimbing II,</p>
                    <p class="name">{{ $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first()?->dosen->nama_lengkap ?? '________________' }}</p>
                    <p style="font-size: 10pt;">NIP. {{ $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first()?->dosen->nip ?? '-' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh Sistem Informasi Skripsi</p>
    </div>
</body>
</html>
