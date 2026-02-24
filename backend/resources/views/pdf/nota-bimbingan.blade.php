<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Nota Bimbingan Skripsi</title>
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
            padding: 10px 55px 25px 55px;
        }

        /* Kop Surat */
        .kop {
            text-align: center;
            margin-bottom: 6px;
        }

        .kop img {
            width: 100%;
        }

        /* Judul */
        .judul {
            text-align: center;
            margin: 6px 0 15px 0;
        }

        .judul h3 {
            font-size: 14pt;
            letter-spacing: 2px;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .judul p {
            font-size: 11pt;
        }

        /* Info Mahasiswa */
        table.info {
            width: 100%;
            margin: 10px 0 15px 0;
            border-collapse: collapse;
        }

        table.info td {
            padding: 2px 5px;
            vertical-align: top;
            font-size: 11pt;
        }

        table.info td.label {
            width: 140px;
        }

        table.info td.sep {
            width: 15px;
            text-align: center;
        }

        /* Tabel Bimbingan */
        table.bimbingan {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10pt;
        }

        table.bimbingan th,
        table.bimbingan td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }

        table.bimbingan th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 10pt;
        }

        table.bimbingan td.center {
            text-align: center;
        }

        /* Summary */
        .summary {
            margin: 15px 0;
            font-size: 11pt;
        }

        /* Tanda Tangan */
        .signatures {
            margin-top: 25px;
            width: 100%;
        }

        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures td {
            text-align: center;
            vertical-align: top;
            padding: 5px 10px;
            font-size: 11pt;
        }

        .signatures .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 70px;
            display: inline-block;
        }

        .signatures .nip {
            font-size: 10pt;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- Kop Surat --}}
    <div class="kop">
        <img src="{{ public_path('images/kop surat.jpg') }}">
    </div>

    {{-- Judul --}}
    <div class="judul">
        <h3>KARTU / NOTA BIMBINGAN SKRIPSI</h3>
        <p>Nomor: {{ $nota->nomor }}</p>
    </div>

    {{-- Info Mahasiswa --}}
    <table class="info">
        <tr>
            <td class="label">Nama Mahasiswa</td>
            <td class="sep">:</td>
            <td><strong>{{ $skripsi->mahasiswa->nama }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td class="sep">:</td>
            <td>{{ $skripsi->mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi</td>
            <td class="sep">:</td>
            <td>{{ $skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Fakultas</td>
            <td class="sep">:</td>
            <td>{{ $skripsi->mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Judul Skripsi</td>
            <td class="sep">:</td>
            <td>{{ $skripsi->judul }}</td>
        </tr>
        @php
            $pembimbing1 = $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first();
            $pembimbing2 = $skripsi->pembimbing->where('jenis', 'pembimbing_2')->first();
        @endphp
        <tr>
            <td class="label">Pembimbing I</td>
            <td class="sep">:</td>
            <td>{{ $pembimbing1?->dosen->full_name ?? '-' }}</td>
        </tr>
        @if ($pembimbing2)
            <tr>
                <td class="label">Pembimbing II</td>
                <td class="sep">:</td>
                <td>{{ $pembimbing2->dosen->full_name ?? '-' }}</td>
            </tr>
        @endif
    </table>

    {{-- Tabel Bimbingan --}}
    <table class="bimbingan">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Topik / Materi Bimbingan</th>
                <th style="width: 90px;">Pembimbing</th>
                <th style="width: 60px;">Status</th>
                <th style="width: 80px;">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bimbingan as $index => $b)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($b->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $b->topik }}</strong>
                        @if ($b->deskripsi)
                            <br><span style="font-size: 9pt;">{{ $b->deskripsi }}</span>
                        @endif
                        @if ($b->catatan_dosen)
                            <br><em style="font-size: 9pt; color: #333;">Catatan: {{ $b->catatan_dosen }}</em>
                        @endif
                    </td>
                    <td class="center" style="font-size: 9pt;">{{ $b->dosen->full_name ?? ($b->dosen->nama ?? '-') }}
                    </td>
                    <td class="center">
                        @if ($b->status === 'approved')
                            Disetujui
                        @elseif($b->status === 'revision')
                            Revisi
                        @elseif($b->status === 'rejected')
                            Ditolak
                        @else
                            Menunggu
                        @endif
                    </td>
                    <td class="center">
                        @if ($b->status === 'approved')
                            ✓
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Belum ada catatan bimbingan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
        <p><strong>Total Bimbingan:</strong> {{ $bimbingan->count() }} kali</p>
        <p><strong>Tanggal Cetak:</strong> {{ $tanggal }}</p>
    </div>

    {{-- Tanda Tangan Pembimbing --}}
    <div class="signatures">
        <table>
            <tr>
                <td>
                    <p>Pembimbing I,</p>
                    <p class="name">{{ $pembimbing1?->dosen->full_name ?? '________________' }}</p>
                    <p class="nip">NIP/NIY. {{ $pembimbing1?->dosen->nip ?? '-' }}</p>
                </td>
                @if ($pembimbing2)
                    <td>
                        <p>Pembimbing II,</p>
                        <p class="name">{{ $pembimbing2->dosen->full_name ?? '________________' }}</p>
                        <p class="nip">NIP/NIY. {{ $pembimbing2->dosen->nip ?? '-' }}</p>
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh Sistem Informasi Skripsi</p>
    </div>
</body>

</html>
