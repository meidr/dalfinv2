<?php

namespace App\Services;

use App\Models\NomorSuratTemplate;
use App\Models\Prodi;
use App\Models\Skripsi;
use App\Models\Seminar;
use App\Models\BeritaAcara;
use App\Models\NotaBimbingan;
use App\Models\SKTugas;
use App\Models\SKYudisium;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class NomorSuratService
{
    public const ALLOWED_TOKENS = [
        '{nomor_urut}',
        '{Fakultas_kode}',
        '{prodi_kode}',
        '{prodi_alias}',
        '{bulan}',
        '{tahun}',
    ];

    public static function keyForSkPenguji(string $jenis): string
    {
        return match ($jenis) {
            'sempro' => 'sk_penguji_sempro',
            'semhas' => 'sk_penguji_semhas',
            'sidang' => 'sk_penguji_sidang',
            default => throw new InvalidArgumentException("Jenis seminar tidak dikenal: {$jenis}"),
        };
    }

    public static function keyForBeritaAcara(string $jenis): string
    {
        return match ($jenis) {
            'sempro' => 'ba_sempro',
            'semhas' => 'ba_semhas',
            'sidang' => 'ba_sidang',
            default => throw new InvalidArgumentException("Jenis seminar tidak dikenal: {$jenis}"),
        };
    }

    public function generateForSkripsi(string $key, Skripsi $skripsi, ?Carbon $date = null): string
    {
        $skripsi->loadMissing('mahasiswa.prodi.fakultas');

        return $this->render(
            $this->getTemplate($key),
            $skripsi->mahasiswa?->prodi,
            $date
        );
    }

    public function generateForSeminar(string $key, Seminar $seminar, ?Carbon $date = null): string
    {
        $seminar->loadMissing('skripsi.mahasiswa.prodi.fakultas');

        return $this->render(
            $this->getTemplate($key),
            $seminar->skripsi?->mahasiswa?->prodi,
            $date
        );
    }

    public function generateForProdi(string $key, ?Prodi $prodi, ?Carbon $date = null): string
    {
        $prodi?->loadMissing('fakultas');

        return $this->render(
            $this->getTemplate($key),
            $prodi,
            $date
        );
    }

    public function ensureSkTugasNumber(SKTugas $skTugas, Skripsi $skripsi, ?Carbon $date = null): string
    {
        if (filled($skTugas->nomor_sk)) {
            return $skTugas->nomor_sk;
        }

        $nomor = $this->generateForSkripsi('sk_tugas', $skripsi, $date);
        $skTugas->forceFill(['nomor_sk' => $nomor])->save();

        return $nomor;
    }

    public function ensureNotaBimbinganNumber(NotaBimbingan $nota, Skripsi $skripsi, ?Carbon $date = null): string
    {
        if (filled($nota->nomor)) {
            return $nota->nomor;
        }

        $nomor = $this->generateForSkripsi('nota_bimbingan', $skripsi, $date);
        $nota->forceFill(['nomor' => $nomor])->save();

        return $nomor;
    }

    public function ensureSeminarSkPengujiNumber(Seminar $seminar, ?Carbon $date = null): string
    {
        if (filled($seminar->nomor_sk_penguji)) {
            return $seminar->nomor_sk_penguji;
        }

        $nomor = $this->generateForSeminar(self::keyForSkPenguji($seminar->jenis), $seminar, $date);
        $seminar->forceFill(['nomor_sk_penguji' => $nomor])->save();

        return $nomor;
    }

    public function ensureBeritaAcaraNumber(BeritaAcara $beritaAcara, Seminar $seminar, ?Carbon $date = null): string
    {
        if (filled($beritaAcara->nomor)) {
            return $beritaAcara->nomor;
        }

        $nomor = $this->generateForSeminar(self::keyForBeritaAcara($seminar->jenis), $seminar, $date);
        $beritaAcara->forceFill(['nomor' => $nomor])->save();

        return $nomor;
    }

    public function ensureSkYudisiumNumber(SKYudisium $skYudisium, Skripsi $skripsi, ?Carbon $date = null): string
    {
        if (filled($skYudisium->nomor_sk)) {
            return $skYudisium->nomor_sk;
        }

        $nomor = $this->generateForSkripsi('sk_yudisium', $skripsi, $date);
        $skYudisium->forceFill(['nomor_sk' => $nomor])->save();

        return $nomor;
    }

    public function getTemplate(string $key): NomorSuratTemplate
    {
        return NomorSuratTemplate::where('key', $key)->firstOrFail();
    }

    public function render(NomorSuratTemplate $template, $prodi, ?Carbon $date = null): string
    {
        $date ??= now();
        $fakultas = $prodi?->fakultas;
        $nomorUrut = str_pad((string) NomorSK::getNomorSk(), $template->digit_urut, '0', STR_PAD_LEFT);

        return strtr($template->template, [
            '{nomor_urut}' => $nomorUrut,
            '{Fakultas_kode}' => $fakultas?->kode ?? '',
            '{prodi_kode}' => $prodi?->kode ?? '',
            '{prodi_alias}' => $prodi?->alias ?: ($prodi?->kode ?? ''),
            '{bulan}' => $date->format('m'),
            '{tahun}' => $date->format('Y'),
        ]);
    }

    public static function validateTemplate(string $template): array
    {
        preg_match_all('/\{[^}]+\}/', $template, $matches);
        $tokens = array_values(array_unique($matches[0] ?? []));
        $invalid = array_values(array_diff($tokens, self::ALLOWED_TOKENS));

        if (!empty($invalid)) {
            return ['Token tidak dikenal: ' . implode(', ', $invalid)];
        }

        $required = ['{nomor_urut}', '{tahun}'];
        $missing = array_values(array_filter($required, fn ($token) => !str_contains($template, $token)));
        if (!empty($missing)) {
            return ['Template wajib memuat token: ' . implode(', ', $missing)];
        }

        return [];
    }
}
