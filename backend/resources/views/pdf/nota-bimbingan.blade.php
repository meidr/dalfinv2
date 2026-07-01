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
            margin-top: 10px;
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
                            @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                                <img src="{{ $qrData['qr_base64'] }}" style="width: 35px; height: 35px;" alt="QR">
                            @else
                                <img src="{{ public_path('images/cap.jpg') }}" style="width: 40px; opacity: 0.8;">
                            @endif
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
                    @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                        <div style="height: 130px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ $qrData['qr_base64'] }}" style="width: 120px; height: 120px;" alt="QR">
                        </div>
                    @else
                        <div style="position: relative; height: 80px; margin-top: 5px;">
                            <img src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); margin-left: -40px; top: -15px; width: 100px; opacity: 0.8;">
                        </div>
                    @endif
                    <p class="name">{{ $pembimbing1?->dosen->full_name ?? '________________' }}</p>
                    <p class="nip">NIP/NIY. {{ $pembimbing1?->dosen->nip ?? '-' }}</p>
                </td>
                @if ($pembimbing2)
                    <td>
                        <p>Pembimbing II,</p>
                        @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                            <div style="height: 130px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $qrData['qr_base64'] }}" style="width: 120px; height: 120px;" alt="QR">
                            </div>
                        @else
                            <div style="position: relative; height: 80px; margin-top: 5px;">
                                <img src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); margin-left: -40px; top: -15px; width: 100px; opacity: 0.8;">
                            </div>
                        @endif
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

    {{-- ==================== HALAMAN NOTA PEMBIMBING ==================== --}}
    <div style="page-break-before: always;"></div>

    @php
        $prodiNama = strtolower($skripsi->mahasiswa->prodi->nama ?? '');
        $isArabicProdi = str_contains($prodiNama, 'bahasa arab') || str_contains($prodiNama, 'sastra arab');
    @endphp

    {{-- Kop Surat Nota Pembimbing --}}
    <div class="kop">
        <img src="{{ public_path('images/kop surat.jpg') }}">
    </div>

    @if ($isArabicProdi)
        {{-- ==================== VERSI BAHASA ARAB ==================== --}}

        {{-- Judul --}}
        <div style="text-align: center; margin: 10px 0 20px 0;">
            <h3 style="font-size: 16pt; letter-spacing: 2px; text-decoration: underline; margin-bottom: 2px; font-family: 'Traditional Arabic', 'Amiri', 'Times New Roman', serif;">مذكرة المشرف</h3>
        </div>

        {{-- Isi Surat Bahasa Arab --}}
        <div style="font-size: 12pt; line-height: 2; direction: rtl; text-align: right; font-family: 'Traditional Arabic', 'Amiri', 'Times New Roman', serif;">
            <table style="margin-bottom: 8px; border-collapse: collapse; direction: rtl; width: 100%;">
                <tr>
                    <td style="padding: 1px 5px; vertical-align: top; width: 100px; text-align: right;">الموضوع</td>
                    <td style="padding: 1px 5px; vertical-align: top; width: 15px; text-align: center;">:</td>
                    <td style="padding: 1px 5px; vertical-align: top; text-align: right;">الموافقة على مناقشة البحث العلمي</td>
                </tr>
            </table>

            <p style="margin-bottom: 5px;">
                إلى سعادة مدير جامعة دار اللغة والدعوة الإسلامية العالمية بانقيل، باسوروان، جاوى الشرقية.
            </p>

            <p style="margin-bottom: 10px;">
                السلام عليكم ورحمة الله وبركاته
            </p>

            <p style="margin-bottom: 10px; text-align: justify;">
                بعد الاطلاع والمراجعة الدقيقة وإجراء التصحيحات والتحسينات اللازمة وفقاً لتوجيهاتنا وإرشاداتنا، فإننا نرى أن البحث العلمي للطالب/ة:
            </p>

            {{-- Data Mahasiswa --}}
            <table style="margin: 5px 30px 10px 0; border-collapse: collapse; direction: rtl; width: 90%;">
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top; width: 120px; text-align: right;">الاسم</td>
                    <td style="padding: 2px 5px; vertical-align: top; width: 15px; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: right;">{{ $skripsi->mahasiswa->nama }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: right;">رقم القيد</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: right; direction: ltr;">{{ $skripsi->mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: right;">القسم</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: right;">{{ $skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: right;">عنوان البحث</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: justify;">"{{ $skripsi->judul }}"</td>
                </tr>
            </table>

            <p style="margin-bottom: 10px; text-align: justify;">
                قد استوفى الشروط المطلوبة لتقديمه في امتحان مناقشة البحث العلمي
                {{ $skripsi->mahasiswa->prodi->fakultas->nama_fakultas ?? '' }}
                قسم {{ $skripsi->mahasiswa->prodi->nama ?? '' }}
                بجامعة دار اللغة والدعوة الإسلامية العالمية بانقيل، باسوروان، جاوى الشرقية.
                لذا نرجو أن يُناقَش هذا البحث العلمي. وتفضلوا بقبول فائق الاحترام والتقدير.
            </p>

            <p style="margin-bottom: 15px;">
                والسلام عليكم ورحمة الله وبركاته
            </p>
        </div>

        {{-- Tanggal --}}
        <div style="text-align: left; margin-bottom: 20px; font-size: 11pt;">
            <p>Bangil, {{ $tanggal }}</p>
        </div>

        {{-- Tanda Tangan Pembimbing I & II --}}
        <div class="signatures">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <p style="font-family: 'Traditional Arabic', 'Amiri', 'Times New Roman', serif; font-size: 12pt;">المشرف الأول</p>
                        @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                            <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $qrData['qr_base64'] }}" style="width: 70px; height: 70px;" alt="QR">
                            </div>
                        @else
                            <div style="position: relative; height: 70px; margin-top: 5px;">
                                <img src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); margin-left: -40px; top: -10px; width: 90px; opacity: 0.8;">
                            </div>
                        @endif
                        <p class="name">{{ $pembimbing1?->dosen->full_name ?? '________________' }}</p>
                        @if ($pembimbing1?->dosen->nip)
                            <p class="nip">NIP/NIY. {{ $pembimbing1->dosen->nip }}</p>
                        @endif
                    </td>
                    @if ($pembimbing2)
                        <td style="width: 50%;">
                            <p style="font-family: 'Traditional Arabic', 'Amiri', 'Times New Roman', serif; font-size: 12pt;">المشرف الثاني</p>
                            @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                                <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $qrData['qr_base64'] }}" style="width: 70px; height: 70px;" alt="QR">
                                </div>
                            @else
                                <div style="position: relative; height: 70px; margin-top: 5px;">
                                    <img src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); margin-left: -40px; top: -10px; width: 90px; opacity: 0.8;">
                                </div>
                            @endif
                            <p class="name">{{ $pembimbing2->dosen->full_name ?? '________________' }}</p>
                            @if ($pembimbing2->dosen->nip)
                                <p class="nip">NIP/NIY. {{ $pembimbing2->dosen->nip }}</p>
                            @endif
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        {{-- Tanda Tangan Ketua Program Studi --}}
        <div style="text-align: center; margin-top: 15px; font-size: 11pt;">
            <p style="font-family: 'Traditional Arabic', 'Amiri', 'Times New Roman', serif; font-size: 12pt;">رئيس قسم {{ $skripsi->mahasiswa->prodi->nama ?? '' }}</p>
            @if (isset($kaprodi) && $kaprodi['signature'])
                <div style="height: 70px; margin-top: 5px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ $kaprodi['signature'] }}" style="height: 60px; opacity: 0.9;">
                </div>
            @elseif (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                <div style="height: 70px; margin-top: 5px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ $qrData['qr_base64'] }}" style="width: 60px; height: 60px;" alt="QR">
                </div>
            @else
                <div style="height: 70px; margin-top: 5px;">
                    <img src="{{ public_path('images/cap.jpg') }}" style="width: 90px; opacity: 0.8;">
                </div>
            @endif
            <p style="font-weight: bold; text-decoration: underline; display: inline-block; margin-top: 5px;">
                {{ $kaprodi['name'] ?? '________________' }}
            </p>
            @if (isset($kaprodi['nip']) && $kaprodi['nip'] !== '-')
                <p style="font-size: 10pt;">NIP/NIY. {{ $kaprodi['nip'] }}</p>
            @endif
        </div>

    @else
        {{-- ==================== VERSI BAHASA INDONESIA ==================== --}}

        {{-- Judul Nota Pembimbing --}}
        <div style="text-align: center; margin: 10px 0 20px 0;">
            <h3 style="font-size: 14pt; letter-spacing: 2px; text-decoration: underline; margin-bottom: 2px;">NOTA PEMBIMBING</h3>
        </div>

        {{-- Isi Surat --}}
        <div style="font-size: 11pt; line-height: 1.6;">
            <table style="margin-bottom: 8px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 1px 5px; vertical-align: top; width: 60px;">Hal</td>
                    <td style="padding: 1px 5px; vertical-align: top; width: 15px; text-align: center;">:</td>
                    <td style="padding: 1px 5px; vertical-align: top;">Persetujuan Munaqosyah Skripsi</td>
                </tr>
            </table>

            <p style="margin-bottom: 5px; text-indent: 30px;">
                Yth. Rektor Universitas Islam Internasional Darullughah Wadda'wah Bangil, Pasuruan, Jawa Timur.
            </p>

            <p style="margin-bottom: 10px; text-indent: 30px; font-style: italic;">
                Assalamu'alaikum Warahmatullahi Wabarakatuh
            </p>

            <p style="margin-bottom: 10px; text-indent: 30px; text-align: justify;">
                Setelah secermat kami baca/teliti kembali dan telah diadakan perbaikan penyempurnaan sesuai dengan petunjuk dan pengarahan kami, maka kami berpendapat bahwa skripsi saudara/i:
            </p>

            {{-- Data Mahasiswa --}}
            <table style="margin: 5px 0 10px 30px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top; width: 120px;">Nama</td>
                    <td style="padding: 2px 5px; vertical-align: top; width: 15px; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top;">{{ $skripsi->mahasiswa->nama }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top;">NIM</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top;">{{ $skripsi->mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top;">Jurusan/prodi</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top;">{{ $skripsi->mahasiswa->prodi->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px 5px; vertical-align: top;">Judul Skripsi</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: center;">:</td>
                    <td style="padding: 2px 5px; vertical-align: top; text-align: justify;">"{{ $skripsi->judul }}"</td>
                </tr>
            </table>

            <p style="margin-bottom: 10px; text-align: justify;">
                Telah memenuhi syarat untuk diajukan dalam sidang ujian munaqosyah skripsi
                {{ $skripsi->mahasiswa->prodi->fakultas->nama_fakultas ?? '' }}
                prodi {{ $skripsi->mahasiswa->prodi->nama ?? '' }}
                di Universitas Islam Internasional Darullughah Wadda'wah Bangil, Pasuruan, Jawa Timur.
                Untuk itu kami harapkan agar skripsi ini dapat dimunaqosyahkan. Demikian kami sampaikan terimakasih.
            </p>

            <p style="margin-bottom: 15px; font-style: italic;">
                Wassalamu'alaikum Warahmatullahi Wabarakatuh
            </p>
        </div>

        {{-- Tanggal --}}
        <div style="text-align: right; margin-bottom: 20px; font-size: 11pt;">
            <p>Bangil, {{ $tanggal }}</p>
        </div>

        {{-- Tanda Tangan Pembimbing I & II --}}
        <div class="signatures">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <p>Pembimbing I</p>
                        @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                            <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $qrData['qr_base64'] }}" style="width: 70px; height: 70px;" alt="QR">
                            </div>
                        @else
                            <div style="position: relative; height: 70px; margin-top: 5px;">
                                <img src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); margin-left: -40px; top: -10px; width: 90px; opacity: 0.8;">
                            </div>
                        @endif
                        <p class="name">{{ $pembimbing1?->dosen->full_name ?? '________________' }}</p>
                        @if ($pembimbing1?->dosen->nip)
                            <p class="nip">NIP/NIY. {{ $pembimbing1->dosen->nip }}</p>
                        @endif
                    </td>
                    @if ($pembimbing2)
                        <td style="width: 50%;">
                            <p>Pembimbing II</p>
                            @if (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                                <div style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $qrData['qr_base64'] }}" style="width: 70px; height: 70px;" alt="QR">
                                </div>
                            @else
                                <div style="position: relative; height: 70px; margin-top: 5px;">
                                    <img src="{{ public_path('images/cap.jpg') }}" style="position: absolute; left: 50%; transform: translateX(-50%); margin-left: -40px; top: -10px; width: 90px; opacity: 0.8;">
                                </div>
                            @endif
                            <p class="name">{{ $pembimbing2->dosen->full_name ?? '________________' }}</p>
                            @if ($pembimbing2->dosen->nip)
                                <p class="nip">NIP/NIY. {{ $pembimbing2->dosen->nip }}</p>
                            @endif
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        {{-- Tanda Tangan Ketua Program Studi --}}
        <div style="text-align: center; margin-top: 15px; font-size: 11pt;">
            <p>Ketua Program Studi {{ $skripsi->mahasiswa->prodi->nama ?? '' }}</p>
            @if (isset($kaprodi) && $kaprodi['signature'])
                <div style="height: 70px; margin-top: 5px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ $kaprodi['signature'] }}" style="height: 60px; opacity: 0.9;">
                </div>
            @elseif (isset($signatureMode) && $signatureMode === 'qr' && !empty($qrData))
                <div style="height: 70px; margin-top: 5px; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ $qrData['qr_base64'] }}" style="width: 60px; height: 60px;" alt="QR">
                </div>
            @else
                <div style="height: 70px; margin-top: 5px;">
                    <img src="{{ public_path('images/cap.jpg') }}" style="width: 90px; opacity: 0.8;">
                </div>
            @endif
            <p style="font-weight: bold; text-decoration: underline; display: inline-block; margin-top: 5px;">
                {{ $kaprodi['name'] ?? '________________' }}
            </p>
            @if (isset($kaprodi['nip']) && $kaprodi['nip'] !== '-')
                <p style="font-size: 10pt;">NIP/NIY. {{ $kaprodi['nip'] }}</p>
            @endif
        </div>

    @endif

</body>

</html>
