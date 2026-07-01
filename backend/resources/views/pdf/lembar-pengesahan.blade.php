<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Pengesahan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 1.5cm 2cm 1.5cm 2cm; /* Reduced top/bottom margin to fit on 1 page */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .mb-2 { margin-bottom: 15px; }
        
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .skripsi-title {
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
            margin: 15px 0; /* Reduced margin */
            line-height: 1.4;
        }

        .table-ttd {
            width: 100%;
            margin-top: 15px; /* Reduced margin */
            page-break-inside: avoid;
        }
        .table-ttd td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            padding-bottom: 10px;
        }
        .signature-space {
            height: 70px; /* Reduced space */
            position: relative;
        }
        .qr-image {
            width: 70px;
            height: 70px;
        }
        .cap-image {
            position: absolute;
            width: 80px;
            height: 80px;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.7;
            z-index: -1;
        }
        .nama-dosen {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .kaprodi-wrapper {
            margin-top: 20px; /* Reduced margin */
            text-align: center;
            width: 100%;
            page-break-inside: avoid;
        }
        .kaprodi-inner {
            display: inline-block;
            text-align: center;
            width: 300px;
        }
    </style>
</head>
<body>

    <div class="text-center mb-2">
        <div class="header-title">LEMBAR PENGESAHAN</div>
    </div>

    <div class="text-center skripsi-title">
        "{{ strtoupper($skripsi->judul) }}"
    </div>

    <div class="text-center mb-2">
        <div class="header-title" style="margin-bottom: 20px;">SKRIPSI</div>
    </div>

    <div class="text-justify mb-2">
        Telah dipertahankan di depan Dewan Penguji Skripsi Program Studi {{ $mahasiswa->prodi->nama ?? '' }} {{ $mahasiswa->prodi->fakultas->nama_fakultas ?? '' }} Universitas Islam Internasional Darullughah Wadda’wah, dan diterima untuk memenuhi syarat guna memperoleh gelar Sarjana.
    </div>

    <div class="mb-2">
        <table>
            <tr>
                <td width="200">Pada Tanggal</td>
                <td width="10">:</td>
                <td>{{ $tanggal }}</td>
            </tr>
        </table>
    </div>

    <!-- TTD Ketua & Penguji I -->
    <table class="table-ttd">
        <tr>
            @if($ketua)
            <td>
                Mengetahui dan Mengesahkan,<br>
                <strong>KETUA:</strong>
                <div class="signature-space">
                    @if($signatureMode === 'qr' && $qrKetua)
                        <img src="{{ $qrKetua['qr_base64'] }}" class="qr-image" alt="QR Code">
                    @endif
                </div>
                <div class="nama-dosen">{{ $ketua->nama ?? '(Nama Ketua Penguji)' }}</div>
                <div>NIP/NIY. {{ $ketua->nip ?? '-' }}</div>
            </td>
            @endif

            @if($penguji1)
            <td>
                @if(!$ketua)<br>@else<br>@endif
                <strong>PENGUJI I:</strong>
                <div class="signature-space">
                    @if($signatureMode === 'qr' && $qrPenguji1)
                        <img src="{{ $qrPenguji1['qr_base64'] }}" class="qr-image" alt="QR Code">
                    @endif
                </div>
                <div class="nama-dosen">{{ $penguji1->nama ?? '(Nama Penguji I)' }}</div>
                <div>NIP/NIY. {{ $penguji1->nip ?? '-' }}</div>
            </td>
            @endif
        </tr>
    </table>

    <!-- TTD Penguji II -->
    @if($penguji2)
    <table class="table-ttd" style="margin-top: 5px;">
        <tr>
            <td style="width: 25%;"></td>
            <td style="width: 50%;">
                <br>
                <strong>PENGUJI II:</strong>
                <div class="signature-space">
                    @if($signatureMode === 'qr' && $qrPenguji2)
                        <img src="{{ $qrPenguji2['qr_base64'] }}" class="qr-image" alt="QR Code">
                    @endif
                </div>
                <div class="nama-dosen">{{ $penguji2->nama ?? '(Nama Penguji II)' }}</div>
                <div>NIP/NIY. {{ $penguji2->nip ?? '-' }}</div>
            </td>
            <td style="width: 25%;"></td>
        </tr>
    </table>
    @endif

    <!-- TTD Kaprodi -->
    <div class="kaprodi-wrapper">
        <div class="kaprodi-inner">
            Mengetahui,<br>
            Ketua Program Studi {{ $mahasiswa->prodi->nama ?? '' }}
            <div class="signature-space">
                @if($signatureMode === 'qr' && $qrKaprodi)
                    @if(file_exists($cap_path))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($cap_path)) }}" class="cap-image" alt="Cap Prodi">
                    @endif
                    <img src="{{ $qrKaprodi['qr_base64'] }}" class="qr-image" alt="QR Code" style="position: relative; z-index: 1;">
                @endif
            </div>
            <div class="nama-dosen">{{ $kaprodi['name'] ?? '(Nama Kaprodi)' }}</div>
            <div>NIP/NIY. {{ $kaprodi['nip'] ?? '-' }}</div>
        </div>
    </div>

</body>
</html>
