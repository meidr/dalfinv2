<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\Skripsi;
use App\Models\Seminar;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\SKYudisium;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Log write requests (POST, PUT, DELETE) automatically
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log write operations
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $request->user()) {
            $action = match ($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'unknown',
            };

            $routePath = $request->path();
            $method = strtoupper($request->method());
            $description = "{$method} /{$routePath}";

            // Generate full narrative detail
            $detail = $this->generateNarrativeDetail($request, $method, $routePath);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => $action,
                'description' => $description,
                'detail' => $detail,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    /**
     * Generate a fully narrative, human-readable detail string.
     */
    private function generateNarrativeDetail(Request $request, string $method, string $routePath): string
    {
        $userName = $request->user()->name ?? 'Unknown';
        $userRole = $this->translateRole($request->user()->role ?? 'unknown');

        try {
            $narrative = $this->buildNarrative($request, $method, $routePath, $userName, $userRole);
            if ($narrative) {
                return $narrative;
            }
        } catch (\Throwable $e) {
            // Silently fail — don't break the request because of logging
        }

        // Generic fallback
        return "{$userName} ({$userRole}) melakukan aksi {$method} pada endpoint /{$routePath}.";
    }

    /**
     * Build a narrative sentence based on the specific route.
     */
    private function buildNarrative(Request $request, string $method, string $routePath, string $userName, string $userRole): ?string
    {
        $actor = "{$userName} ({$userRole})";

        // Normalize path: replace numeric/UUID-ish segments for matching
        $normalizedPath = preg_replace('#/[0-9a-f\-]{8,}#i', '/{id}', $routePath);
        $normalizedPath = preg_replace('#/\d+#', '/{id}', $normalizedPath);

        $key = "{$method} {$normalizedPath}";

        // ──────────────────────────────────────────
        //  SKRIPSI
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/skripsi') {
            $mhsInfo = $this->getMahasiswaInfo($request->input('mahasiswa_id'));
            $judul = $request->input('judul', '-');
            return "{$actor} menambahkan data skripsi baru untuk {$mhsInfo} dengan judul \"{$judul}\".";
        }

        if ($key === 'PUT api/admin/skripsi/{id}') {
            $skripsiInfo = $this->getSkripsiInfo($this->extractId($routePath, 'skripsi'));
            $parts = ["{$actor} mengubah data skripsi {$skripsiInfo}."];
            if ($request->filled('status')) {
                $parts[] = "Status diubah menjadi: {$this->translateStatus($request->input('status'))}.";
            }
            if ($request->filled('judul')) {
                $parts[] = "Judul diubah menjadi: \"{$request->input('judul')}\".";
            }
            if ($request->filled('catatan_admin')) {
                $parts[] = "Catatan admin: \"{$request->input('catatan_admin')}\".";
            }
            return implode(' ', $parts);
        }

        if ($key === 'DELETE api/admin/skripsi/{id}') {
            $skripsiInfo = $this->getSkripsiInfo($this->extractId($routePath, 'skripsi'));
            return "{$actor} menghapus data skripsi {$skripsiInfo}.";
        }

        // ──────────────────────────────────────────
        //  PEMBIMBING
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/pembimbing') {
            $skripsiInfo = $this->getSkripsiInfo($request->input('skripsi_id'));
            $parts = ["{$actor} menetapkan dosen pembimbing untuk skripsi {$skripsiInfo}."];
            if ($request->filled('pembimbing_1_id')) {
                $dosen = Dosen::find($request->input('pembimbing_1_id'));
                if ($dosen) $parts[] = "Pembimbing 1: {$dosen->nama} (NIP: {$dosen->nip}).";
            }
            if ($request->filled('pembimbing_2_id')) {
                $dosen = Dosen::find($request->input('pembimbing_2_id'));
                if ($dosen) $parts[] = "Pembimbing 2: {$dosen->nama} (NIP: {$dosen->nip}).";
            }
            return implode(' ', $parts);
        }

        if ($key === 'PUT api/admin/pembimbing/{id}') {
            return "{$actor} mengubah data dosen pembimbing (ID: {$this->extractId($routePath, 'pembimbing')}).";
        }

        if ($key === 'DELETE api/admin/pembimbing/{id}') {
            return "{$actor} menghapus dosen pembimbing (ID: {$this->extractId($routePath, 'pembimbing')}).";
        }

        // ──────────────────────────────────────────
        //  SEMINAR (Sempro)
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/seminar') {
            $skripsiInfo = $this->getSkripsiInfo($request->input('skripsi_id'));
            $tanggal = $request->input('tanggal', '-');
            $ruangan = $request->input('ruangan', '-');
            return "{$actor} menjadwalkan seminar proposal (sempro) untuk skripsi {$skripsiInfo}. Tanggal: {$tanggal}, Ruangan: {$ruangan}.";
        }

        if ($key === 'PUT api/admin/seminar/{id}') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            $parts = ["{$actor} mengubah data seminar proposal {$seminarInfo}."];
            if ($request->filled('hasil')) {
                $parts[] = "Hasil sempro: {$this->translateHasil($request->input('hasil'))}.";
            }
            if ($request->filled('status')) {
                $parts[] = "Status seminar: {$this->translateSeminarStatus($request->input('status'))}.";
            }
            if ($request->filled('nilai')) {
                $parts[] = "Nilai: {$request->input('nilai')}.";
            }
            return implode(' ', $parts);
        }

        if ($key === 'DELETE api/admin/seminar/{id}') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            return "{$actor} menghapus jadwal seminar proposal {$seminarInfo}.";
        }

        if ($key === 'POST api/admin/seminar/{id}/berita-acara') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            return "{$actor} membuat berita acara untuk seminar proposal {$seminarInfo}.";
        }

        if ($key === 'POST api/admin/seminar/{id}/penguji' || $key === 'POST api/admin/seminar/{id}/penguji/{id2}') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            $dosenNama = $this->getDosenNama($request->input('dosen_id'));
            $peran = ucfirst(str_replace('_', ' ', $request->input('peran', '-')));
            return "{$actor} menambahkan {$dosenNama} sebagai penguji ({$peran}) pada seminar proposal {$seminarInfo}.";
        }

        if (preg_match('#PUT api/admin/seminar/\{id\}/penguji/\{id#', $key)) {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            return "{$actor} mengubah data penguji pada seminar proposal {$seminarInfo}.";
        }

        if (preg_match('#DELETE api/admin/seminar/\{id\}/penguji/\{id#', $key)) {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            return "{$actor} menghapus penguji dari seminar proposal {$seminarInfo}.";
        }

        // ──────────────────────────────────────────
        //  SEMINAR HASIL (Semhas)
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/seminar-hasil') {
            $skripsiInfo = $this->getSkripsiInfo($request->input('skripsi_id'));
            $tanggal = $request->input('tanggal', '-');
            $ruangan = $request->input('ruangan', '-');
            return "{$actor} menjadwalkan seminar hasil (semhas) untuk skripsi {$skripsiInfo}. Tanggal: {$tanggal}, Ruangan: {$ruangan}.";
        }

        if ($key === 'PUT api/admin/seminar-hasil/{id}') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar-hasil'));
            $parts = ["{$actor} mengubah data seminar hasil {$seminarInfo}."];
            if ($request->filled('hasil')) {
                $parts[] = "Hasil semhas: {$this->translateHasil($request->input('hasil'))}.";
            }
            if ($request->filled('status')) {
                $parts[] = "Status seminar: {$this->translateSeminarStatus($request->input('status'))}.";
            }
            if ($request->filled('nilai')) {
                $parts[] = "Nilai: {$request->input('nilai')}.";
            }
            return implode(' ', $parts);
        }

        if ($key === 'DELETE api/admin/seminar-hasil/{id}') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar-hasil'));
            return "{$actor} menghapus jadwal seminar hasil {$seminarInfo}.";
        }

        if ($key === 'POST api/admin/seminar-hasil/{id}/berita-acara') {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar-hasil'));
            return "{$actor} membuat berita acara untuk seminar hasil {$seminarInfo}.";
        }

        // ──────────────────────────────────────────
        //  UJIAN (Sidang)
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/ujian') {
            $skripsiInfo = $this->getSkripsiInfo($request->input('skripsi_id'));
            $tanggal = $request->input('tanggal', '-');
            $ruangan = $request->input('ruangan', '-');
            return "{$actor} menjadwalkan ujian sidang skripsi untuk {$skripsiInfo}. Tanggal: {$tanggal}, Ruangan: {$ruangan}.";
        }

        if ($key === 'PUT api/admin/ujian/{id}') {
            $ujianInfo = $this->getUjianInfo($this->extractId($routePath, 'ujian'));
            $parts = ["{$actor} mengubah data ujian sidang {$ujianInfo}."];
            if ($request->filled('hasil')) {
                $parts[] = "Hasil sidang: {$this->translateHasil($request->input('hasil'))}.";
            }
            if ($request->filled('status')) {
                $parts[] = "Status ujian: {$this->translateSeminarStatus($request->input('status'))}.";
            }
            return implode(' ', $parts);
        }

        if ($key === 'DELETE api/admin/ujian/{id}') {
            $ujianInfo = $this->getUjianInfo($this->extractId($routePath, 'ujian'));
            return "{$actor} menghapus jadwal ujian sidang {$ujianInfo}.";
        }

        // ──────────────────────────────────────────
        //  SK YUDISIUM
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/sk-yudisium') {
            $skripsiInfo = $this->getSkripsiInfo($request->input('skripsi_id'));
            $nomorSk = $request->input('nomor_sk', '-');
            $predikat = $request->filled('predikat') ? $this->translatePredikat($request->input('predikat')) : '-';
            return "{$actor} menerbitkan SK Yudisium (No: {$nomorSk}) untuk {$skripsiInfo}. Predikat: {$predikat}, IPK: {$request->input('ipk', '-')}.";
        }

        // ──────────────────────────────────────────
        //  SK YUDISIUM BATCH
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/sk-yudisium-batch') {
            $nomorBatch = $request->input('nomor_sk_batch', '-');
            return "{$actor} membuat batch SK Yudisium baru dengan nomor SK batch: \"{$nomorBatch}\".";
        }

        if ($key === 'POST api/admin/sk-yudisium-batch/assign') {
            $nomorBatch = $request->input('nomor_sk_batch', '-');
            $skripsiIds = $request->input('skripsi_ids', []);
            $count = count($skripsiIds);
            $mhsList = $this->getMahasiswaListFromSkripsiIds($skripsiIds);
            return "{$actor} meng-assign {$count} mahasiswa ke batch SK Yudisium \"{$nomorBatch}\". Mahasiswa yang di-assign: {$mhsList}.";
        }

        // Match: DELETE api/admin/sk-yudisium-batch/{id}/remove
        if (preg_match('#DELETE api/admin/sk-yudisium-batch/\{id\}/remove#', $key)) {
            $batchIdOrNomor = $this->extractSegment($routePath, 'sk-yudisium-batch');
            return "{$actor} menghapus mahasiswa dari batch SK Yudisium dengan ID/nomor batch: \"{$batchIdOrNomor}\".";
        }

        // Match: PUT api/admin/sk-yudisium-batch/{id}/update
        if (preg_match('#PUT api/admin/sk-yudisium-batch/\{id\}/update#', $key)) {
            $batchIdOrNomor = $this->extractSegment($routePath, 'sk-yudisium-batch');
            return "{$actor} mengubah data batch SK Yudisium dengan ID/nomor batch: \"{$batchIdOrNomor}\".";
        }

        // Match: DELETE api/admin/sk-yudisium-batch/{id}/destroy
        if (preg_match('#DELETE api/admin/sk-yudisium-batch/\{id\}/destroy#', $key)) {
            $batchIdOrNomor = $this->extractSegment($routePath, 'sk-yudisium-batch');
            return "{$actor} menghapus seluruh batch SK Yudisium dengan ID/nomor batch: \"{$batchIdOrNomor}\".";
        }

        // ──────────────────────────────────────────
        //  MAHASISWA (Master Data)
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/mahasiswa') {
            $nama = $request->input('nama', '-');
            $nim = $request->input('nim', '-');
            return "{$actor} menambahkan data mahasiswa baru: {$nama} (NIM: {$nim}).";
        }

        if ($key === 'PUT api/admin/mahasiswa/{id}') {
            $mhsInfo = $this->getMahasiswaInfoById($this->extractId($routePath, 'mahasiswa'));
            $parts = ["{$actor} mengubah data mahasiswa {$mhsInfo}."];
            if ($request->filled('nama')) $parts[] = "Nama: {$request->input('nama')}.";
            if ($request->filled('nim')) $parts[] = "NIM: {$request->input('nim')}.";
            if ($request->filled('status')) $parts[] = "Status: {$request->input('status')}.";
            return implode(' ', $parts);
        }

        if ($key === 'DELETE api/admin/mahasiswa/{id}') {
            $mhsInfo = $this->getMahasiswaInfoById($this->extractId($routePath, 'mahasiswa'));
            return "{$actor} menghapus data mahasiswa {$mhsInfo}.";
        }

        if (str_contains($normalizedPath, 'mahasiswa-import')) {
            return "{$actor} melakukan import data mahasiswa dari file.";
        }

        // ──────────────────────────────────────────
        //  DOSEN (Master Data)
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/dosen') {
            $nama = $request->input('nama', '-');
            $nip = $request->input('nip', '-');
            return "{$actor} menambahkan data dosen baru: {$nama} (NIP: {$nip}).";
        }

        if ($key === 'PUT api/admin/dosen/{id}') {
            $dosenInfo = $this->getDosenInfo($this->extractId($routePath, 'dosen'));
            $parts = ["{$actor} mengubah data dosen {$dosenInfo}."];
            if ($request->filled('nama')) $parts[] = "Nama diubah menjadi: {$request->input('nama')}.";
            if ($request->filled('nip')) $parts[] = "NIP: {$request->input('nip')}.";
            if ($request->filled('kuota_bimbingan')) $parts[] = "Kuota bimbingan: {$request->input('kuota_bimbingan')}.";
            return implode(' ', $parts);
        }

        if ($key === 'DELETE api/admin/dosen/{id}') {
            $dosenInfo = $this->getDosenInfo($this->extractId($routePath, 'dosen'));
            return "{$actor} menghapus data dosen {$dosenInfo}.";
        }

        if (str_contains($normalizedPath, 'dosen-import')) {
            return "{$actor} melakukan import data dosen dari file.";
        }

        // ──────────────────────────────────────────
        //  FAKULTAS, PRODI, TAHUN
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/fakultas') {
            return "{$actor} menambahkan fakultas baru: \"{$request->input('nama_fakultas', '-')}\".";
        }
        if ($key === 'PUT api/admin/fakultas/{id}') {
            return "{$actor} mengubah data fakultas (ID: {$this->extractId($routePath, 'fakultas')}). Nama: \"{$request->input('nama_fakultas', '-')}\".";
        }
        if ($key === 'DELETE api/admin/fakultas/{id}') {
            return "{$actor} menghapus fakultas (ID: {$this->extractId($routePath, 'fakultas')}).";
        }

        if ($key === 'POST api/admin/prodi') {
            return "{$actor} menambahkan program studi baru: \"{$request->input('nama', '-')}\".";
        }
        if ($key === 'PUT api/admin/prodi/{id}') {
            return "{$actor} mengubah data program studi (ID: {$this->extractId($routePath, 'prodi')}). Nama: \"{$request->input('nama', '-')}\".";
        }
        if ($key === 'DELETE api/admin/prodi/{id}') {
            return "{$actor} menghapus program studi (ID: {$this->extractId($routePath, 'prodi')}).";
        }

        if ($key === 'POST api/admin/tahun') {
            return "{$actor} menambahkan tahun akademik baru: \"{$request->input('name', '-')}\" ({$request->input('semester', '-')}).";
        }
        if ($key === 'PUT api/admin/tahun/{id}') {
            return "{$actor} mengubah tahun akademik (ID: {$this->extractId($routePath, 'tahun')}).";
        }
        if ($key === 'DELETE api/admin/tahun/{id}') {
            return "{$actor} menghapus tahun akademik (ID: {$this->extractId($routePath, 'tahun')}).";
        }

        // ──────────────────────────────────────────
        //  DOKUMEN
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/dokumen') {
            $skripsiInfo = $this->getSkripsiInfo($request->input('skripsi_id'));
            $jenis = ucfirst(str_replace('_', ' ', $request->input('jenis', '-')));
            return "{$actor} mengunggah dokumen baru (jenis: {$jenis}) untuk skripsi {$skripsiInfo}.";
        }
        if ($key === 'PUT api/admin/dokumen/{id}') {
            return "{$actor} mengubah data dokumen (ID: {$this->extractId($routePath, 'dokumen')}).";
        }
        if ($key === 'DELETE api/admin/dokumen/{id}') {
            return "{$actor} menghapus dokumen (ID: {$this->extractId($routePath, 'dokumen')}).";
        }

        // ──────────────────────────────────────────
        //  PDF
        // ──────────────────────────────────────────
        if (preg_match('#POST api/admin/pdf/sk-tugas/\{id\}#', $key)) {
            $skripsiInfo = $this->getSkripsiInfo($this->extractId($routePath, 'sk-tugas'));
            return "{$actor} membuat/memperbarui SK Tugas untuk skripsi {$skripsiInfo}.";
        }
        if (preg_match('#POST api/admin/pdf/sk-penguji/\{id\}#', $key)) {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'sk-penguji'));
            return "{$actor} membuat/memperbarui SK Penguji untuk seminar {$seminarInfo}.";
        }
        if (str_contains($routePath, 'pdf/rekap-yudisium')) {
            return "{$actor} mencetak rekap SK Yudisium dalam format PDF.";
        }
        if (preg_match('#POST api/admin/pdf/sk-yudisium/\{id\}#', $key)) {
            $skripsiInfo = $this->getSkripsiInfo($this->extractId($routePath, 'sk-yudisium'));
            return "{$actor} mencetak SK Yudisium individual untuk skripsi {$skripsiInfo}.";
        }
        if (preg_match('#api/admin/pdf/nota-bimbingan/\{id\}#', $key)) {
            $skripsiInfo = $this->getSkripsiInfo($this->extractId($routePath, 'nota-bimbingan'));
            return "{$actor} mencetak nota bimbingan untuk skripsi {$skripsiInfo}.";
        }
        if (preg_match('#api/admin/pdf/berita-acara/\{id\}#', $key)) {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'berita-acara'));
            return "{$actor} mencetak berita acara untuk seminar {$seminarInfo}.";
        }

        // ──────────────────────────────────────────
        //  BERITA ACARA (generate)
        // ──────────────────────────────────────────
        if (preg_match('#POST api/admin/berita-acara/\{id\}/generate#', $key)) {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'berita-acara'));
            return "{$actor} membuat berita acara untuk seminar/ujian {$seminarInfo}.";
        }

        // ──────────────────────────────────────────
        //  SK TUGAS (update)
        // ──────────────────────────────────────────
        if ($key === 'PUT api/admin/sk-tugas/{id}') {
            return "{$actor} mengubah data SK Tugas (ID: {$this->extractId($routePath, 'sk-tugas')}).";
        }

        // ──────────────────────────────────────────
        //  SKRIPSI VERIFICATION
        // ──────────────────────────────────────────
        if (str_contains($routePath, 'skripsi-verification') && str_contains($routePath, 'approve')) {
            $id = $this->extractId($routePath, 'skripsi-verification');
            return "{$actor} menyetujui perubahan data skripsi (verifikasi ID: {$id}).";
        }
        if (str_contains($routePath, 'skripsi-verification') && str_contains($routePath, 'reject')) {
            $id = $this->extractId($routePath, 'skripsi-verification');
            $alasan = $request->input('alasan', '-');
            return "{$actor} menolak perubahan data skripsi (verifikasi ID: {$id}). Alasan: \"{$alasan}\".";
        }
        if (str_contains($routePath, 'skripsi-verification/bulk-approve')) {
            return "{$actor} menyetujui perubahan data skripsi secara massal.";
        }
        if (str_contains($routePath, 'skripsi-verification/bulk-reject')) {
            return "{$actor} menolak perubahan data skripsi secara massal.";
        }

        // ──────────────────────────────────────────
        //  USER MANAGEMENT
        // ──────────────────────────────────────────
        if ($key === 'POST api/admin/users') {
            $nama = $request->input('name', '-');
            $email = $request->input('email', '-');
            $role = $this->translateRole($request->input('role', '-'));
            return "{$actor} menambahkan user baru: {$nama} ({$email}), role: {$role}.";
        }
        if ($key === 'PUT api/admin/users/{id}') {
            $targetUser = $this->getUserInfo($this->extractId($routePath, 'users'));
            $parts = ["{$actor} mengubah data user {$targetUser}."];
            if ($request->filled('name')) $parts[] = "Nama diubah menjadi: {$request->input('name')}.";
            if ($request->filled('role')) $parts[] = "Role diubah menjadi: {$this->translateRole($request->input('role'))}.";
            return implode(' ', $parts);
        }
        if (str_contains($routePath, 'users') && str_contains($routePath, 'toggle-status')) {
            $targetUser = $this->getUserInfo($this->extractId($routePath, 'users'));
            return "{$actor} mengubah status aktif/nonaktif user {$targetUser}.";
        }
        if (str_contains($routePath, 'users') && str_contains($routePath, 'reset-password')) {
            $targetUser = $this->getUserInfo($this->extractId($routePath, 'users'));
            return "{$actor} mereset password user {$targetUser} ke password default.";
        }

        // ──────────────────────────────────────────
        //  CONFIGURATION
        // ──────────────────────────────────────────
        if (str_contains($routePath, 'configuration/sk-tugas-signer')) {
            return "{$actor} mengubah konfigurasi pejabat penandatangan SK Tugas.";
        }
        if (str_contains($routePath, 'configuration/syarat-bimbingan')) {
            $minimal = $request->input('minimal_bimbingan', '-');
            return "{$actor} mengubah syarat minimal bimbingan menjadi {$minimal} kali.";
        }
        if (str_contains($routePath, 'configuration/kuota-bimbingan')) {
            $kuota = $request->input('kuota', '-');
            return "{$actor} mengubah kuota bimbingan dosen default menjadi {$kuota}.";
        }
        if (str_contains($routePath, 'configuration/jenis-ttd')) {
            $jenis = match ($request->input('jenis_ttd')) {
                'qrcode' => 'QR Code',
                'manual' => 'Tanda Tangan Manual/Cap',
                default => $request->input('jenis_ttd', '-'),
            };
            return "{$actor} mengubah jenis tanda tangan dokumen menjadi: {$jenis}.";
        }
        if (str_contains($routePath, 'configuration/panduan') && $method === 'POST') {
            return "{$actor} mengunggah file panduan baru.";
        }
        if (str_contains($routePath, 'configuration/panduan') && $method === 'DELETE') {
            return "{$actor} menghapus file panduan.";
        }

        // ──────────────────────────────────────────
        //  AUTH
        // ──────────────────────────────────────────
        if (str_contains($routePath, 'auth/password')) {
            return "{$actor} mengubah password akun miliknya sendiri.";
        }
        if (str_contains($routePath, 'auth/profile') || str_contains($routePath, 'admin/profile')) {
            return "{$actor} mengubah profil akun miliknya sendiri.";
        }

        // ──────────────────────────────────────────
        //  SUPER ADMIN
        // ──────────────────────────────────────────
        if (str_contains($routePath, 'super-admin/impersonate') && !str_contains($routePath, 'stop')) {
            $targetUser = $this->getUserInfo($this->extractId($routePath, 'impersonate'));
            return "{$actor} melakukan impersonasi (login sebagai) user {$targetUser}.";
        }
        if (str_contains($routePath, 'super-admin/stop-impersonate')) {
            return "{$actor} menghentikan sesi impersonasi dan kembali ke akun asli.";
        }
        if (str_contains($routePath, 'super-admin/force-logout-all')) {
            return "{$actor} melakukan force logout terhadap semua user di sistem (kecuali dirinya sendiri).";
        }
        if (str_contains($routePath, 'super-admin/toggle-system-lock')) {
            return "{$actor} mengubah status kunci sistem (lock/unlock).";
        }
        if (str_contains($routePath, 'super-admin/toggle-semhas')) {
            return "{$actor} mengubah status modul seminar hasil (aktif/nonaktif).";
        }

        // ──────────────────────────────────────────
        //  JABATAN / PERIODE JABATAN / TANDA TANGAN
        // ──────────────────────────────────────────
        if (str_contains($routePath, 'jabatan-pejabat')) {
            $action = match ($method) {
                'POST' => 'menambahkan jabatan pejabat baru',
                'PUT' => 'mengubah data jabatan pejabat (ID: ' . ($this->extractId($routePath, 'jabatan-pejabat') ?? '-') . ')',
                'DELETE' => 'menghapus jabatan pejabat (ID: ' . ($this->extractId($routePath, 'jabatan-pejabat') ?? '-') . ')',
                default => 'melakukan aksi pada jabatan pejabat',
            };
            return "{$actor} {$action}.";
        }
        if (str_contains($routePath, 'periode-jabatan')) {
            $action = match ($method) {
                'POST' => 'menambahkan periode jabatan baru',
                'PUT' => 'mengubah periode jabatan (ID: ' . ($this->extractId($routePath, 'periode-jabatan') ?? '-') . ')',
                'DELETE' => 'menghapus periode jabatan (ID: ' . ($this->extractId($routePath, 'periode-jabatan') ?? '-') . ')',
                default => 'melakukan aksi pada periode jabatan',
            };
            return "{$actor} {$action}.";
        }
        if (str_contains($routePath, 'tanda-tangan')) {
            $action = match ($method) {
                'POST' => 'menambahkan tanda tangan baru',
                'PUT' => 'mengubah data tanda tangan (ID: ' . ($this->extractId($routePath, 'tanda-tangan') ?? '-') . ')',
                'DELETE' => 'menghapus tanda tangan (ID: ' . ($this->extractId($routePath, 'tanda-tangan') ?? '-') . ')',
                default => 'melakukan aksi pada tanda tangan',
            };
            return "{$actor} {$action}.";
        }

        // ──────────────────────────────────────────
        //  DOSEN ACTIONS
        // ──────────────────────────────────────────
        if (str_contains($routePath, 'dosen/bimbingan/log') && str_contains($routePath, 'status')) {
            $statusLabel = match ($request->input('status')) {
                'approved' => 'Disetujui',
                'revision' => 'Perlu Revisi',
                'rejected' => 'Ditolak',
                default => ucfirst($request->input('status', '-')),
            };
            $catatan = $request->input('catatan_dosen', '');
            $detail = "{$actor} mengubah status log bimbingan menjadi: {$statusLabel}.";
            if ($catatan) $detail .= " Catatan: \"{$catatan}\".";
            return $detail;
        }
        if (str_contains($routePath, 'dosen/seminar') && str_contains($routePath, 'nilai')) {
            $seminarInfo = $this->getSeminarInfo($this->extractId($routePath, 'seminar'));
            return "{$actor} memberikan nilai pada seminar/ujian {$seminarInfo}.";
        }
        if (str_contains($routePath, 'dosen/ujian-requests') && str_contains($routePath, 'respond')) {
            $approved = $request->input('approved') ? 'MENYETUJUI' : 'MENOLAK';
            $catatan = $request->input('catatan', '');
            $detail = "{$actor} {$approved} pengajuan ujian dari mahasiswa.";
            if ($catatan) $detail .= " Catatan: \"{$catatan}\".";
            return $detail;
        }

        // ──────────────────────────────────────────
        //  MAHASISWA ACTIONS
        // ──────────────────────────────────────────
        if ($key === 'POST api/mahasiswa/skripsi') {
            $judul = $request->input('judul', '-');
            return "{$actor} mengajukan judul skripsi baru: \"{$judul}\".";
        }
        if ($key === 'PUT api/mahasiswa/skripsi') {
            $parts = ["{$actor} mengubah data skripsi miliknya."];
            if ($request->filled('judul')) $parts[] = "Judul: \"{$request->input('judul')}\".";
            return implode(' ', $parts);
        }
        if (str_contains($routePath, 'mahasiswa/skripsi/bimbingan')) {
            $topik = $request->input('topik', '-');
            return "{$actor} menambahkan log bimbingan baru dengan topik: \"{$topik}\".";
        }
        if (str_contains($routePath, 'mahasiswa/skripsi/request-ujian')) {
            return "{$actor} mengajukan permohonan ujian sidang skripsi.";
        }
        if (str_contains($routePath, 'mahasiswa/skripsi/dokumen') && $method === 'POST') {
            $jenis = ucfirst(str_replace('_', ' ', $request->input('jenis', '-')));
            return "{$actor} mengunggah dokumen skripsi (jenis: {$jenis}).";
        }
        if (str_contains($routePath, 'mahasiswa/skripsi/dokumen') && $method === 'DELETE') {
            return "{$actor} menghapus dokumen skripsi (ID: {$this->extractId($routePath, 'dokumen')}).";
        }

        // No match
        return null;
    }

    // ─────────────────────────────────────────────────
    //  DB lookup helpers → return human-readable strings
    // ─────────────────────────────────────────────────

    /**
     * Get "Mahasiswa Nama (NIM: xxx)" from mahasiswa_id
     */
    private function getMahasiswaInfo(?int $id): string
    {
        if (!$id) return '(mahasiswa tidak diketahui)';
        $mhs = Mahasiswa::find($id);
        return $mhs ? "{$mhs->nama} (NIM: {$mhs->nim})" : "(mahasiswa ID: {$id})";
    }

    /**
     * Get mahasiswa info by mahasiswa ID
     */
    private function getMahasiswaInfoById(?int $id): string
    {
        return $this->getMahasiswaInfo($id);
    }

    /**
     * Get "milik Mahasiswa X (NIM: Y), judul: Z" from skripsi_id
     */
    private function getSkripsiInfo(?int $id): string
    {
        if (!$id) return '(skripsi tidak diketahui)';
        $skripsi = Skripsi::with('mahasiswa')->find($id);
        if (!$skripsi) return "(skripsi ID: {$id})";
        $mhsLabel = $skripsi->mahasiswa
            ? "{$skripsi->mahasiswa->nama} (NIM: {$skripsi->mahasiswa->nim})"
            : '(mahasiswa tidak diketahui)';
        $judul = mb_substr($skripsi->judul, 0, 80);
        if (mb_strlen($skripsi->judul) > 80) $judul .= '...';
        return "milik {$mhsLabel}, judul: \"{$judul}\"";
    }

    /**
     * Get seminar info from seminar ID
     */
    private function getSeminarInfo(?int $id): string
    {
        if (!$id) return '(seminar tidak diketahui)';
        $seminar = Seminar::with('skripsi.mahasiswa')->find($id);
        if (!$seminar) return "(seminar ID: {$id})";
        $jenisLabel = match ($seminar->jenis) {
            'sempro' => 'Seminar Proposal',
            'semhas' => 'Seminar Hasil',
            'sidang' => 'Sidang Skripsi',
            default => ucfirst($seminar->jenis ?? '-'),
        };
        $mhsLabel = '';
        if ($seminar->skripsi && $seminar->skripsi->mahasiswa) {
            $mhs = $seminar->skripsi->mahasiswa;
            $mhsLabel = " — mahasiswa: {$mhs->nama} (NIM: {$mhs->nim})";
        }
        return "({$jenisLabel}{$mhsLabel})";
    }

    /**
     * Get ujian info from ujian (seminar with jenis=sidang) ID
     */
    private function getUjianInfo(?int $id): string
    {
        if (!$id) return '(ujian tidak diketahui)';
        $ujian = Seminar::with('skripsi.mahasiswa')->find($id);
        if (!$ujian) return "(ujian ID: {$id})";
        $mhsLabel = '';
        if ($ujian->skripsi && $ujian->skripsi->mahasiswa) {
            $mhs = $ujian->skripsi->mahasiswa;
            $mhsLabel = " — mahasiswa: {$mhs->nama} (NIM: {$mhs->nim})";
        }
        return "(Sidang Skripsi{$mhsLabel})";
    }

    /**
     * Get dosen nama from ID
     */
    private function getDosenNama(?int $id): string
    {
        if (!$id) return '(dosen tidak diketahui)';
        $dosen = Dosen::find($id);
        return $dosen ? "{$dosen->nama} (NIP: {$dosen->nip})" : "(dosen ID: {$id})";
    }

    /**
     * Get dosen info string
     */
    private function getDosenInfo(?int $id): string
    {
        return $this->getDosenNama($id);
    }

    /**
     * Get user info from user ID
     */
    private function getUserInfo(?int $id): string
    {
        if (!$id) return '(user tidak diketahui)';
        $user = \App\Models\User::find($id);
        if (!$user) return "(user ID: {$id})";
        return "{$user->name} ({$user->email}, role: {$this->translateRole($user->role)})";
    }

    /**
     * Get list of mahasiswa names from skripsi IDs
     */
    private function getMahasiswaListFromSkripsiIds(array $skripsiIds): string
    {
        if (empty($skripsiIds)) return '-';
        $skripsiList = Skripsi::with('mahasiswa')->whereIn('id', array_slice($skripsiIds, 0, 5))->get();
        $names = $skripsiList->map(
            fn($s) => $s->mahasiswa
                ? "{$s->mahasiswa->nama} ({$s->mahasiswa->nim})"
                : "skripsi ID:{$s->id}"
        )->implode(', ');
        $count = count($skripsiIds);
        if ($count > 5) $names .= ", dan " . ($count - 5) . " mahasiswa lainnya";
        return $names;
    }

    // ─────────────────────────────────────────────────
    //  Route extraction helpers
    // ─────────────────────────────────────────────────

    /**
     * Extract numeric ID after a given segment.
     * e.g. "api/admin/skripsi/42" + "skripsi" → 42
     */
    private function extractId(string $routePath, string $segment): ?int
    {
        if (preg_match('#' . preg_quote($segment, '#') . '/(\d+)#', $routePath, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Extract the segment value (could be non-numeric like a UUID) after a given key.
     * e.g. "api/admin/sk-yudisium-batch/abc123/destroy" + "sk-yudisium-batch" → "abc123"
     */
    private function extractSegment(string $routePath, string $segment): string
    {
        if (preg_match('#' . preg_quote($segment, '#') . '/([^/]+)#', $routePath, $matches)) {
            return $matches[1];
        }
        return '-';
    }

    // ─────────────────────────────────────────────────
    //  Translation helpers
    // ─────────────────────────────────────────────────

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'pengajuan' => 'Pengajuan Judul',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'proposal' => 'Proposal',
            'sempro' => 'Seminar Proposal',
            'penentuan_dospem' => 'Penentuan Dosen Pembimbing',
            'dospem' => 'Dosen Pembimbing Ditetapkan',
            'bimbingan' => 'Bimbingan',
            'pengajuan_sidang' => 'Pengajuan Sidang',
            'pengajuan_sidang_acc' => 'Pengajuan Sidang Disetujui',
            'pengajuan_sidang_tolak' => 'Pengajuan Sidang Ditolak',
            'semhas' => 'Seminar Hasil',
            'sidang' => 'Sidang Skripsi',
            'revisi' => 'Revisi Pasca-Sidang',
            'lulus' => 'Lulus',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function translateHasil(string $hasil): string
    {
        return match ($hasil) {
            'lulus' => 'Lulus',
            'lulus_bersyarat' => 'Lulus Bersyarat (ada revisi)',
            'lulus_revisi' => 'Lulus dengan Revisi',
            'tidak_lulus' => 'Tidak Lulus',
            'mengulang' => 'Harus Mengulang',
            default => ucfirst(str_replace('_', ' ', $hasil)),
        };
    }

    private function translateSeminarStatus(string $status): string
    {
        return match ($status) {
            'terjadwal' => 'Terjadwal',
            'berlangsung' => 'Sedang Berlangsung',
            'selesai' => 'Selesai',
            'batal' => 'Dibatalkan',
            'pending' => 'Menunggu',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function translatePredikat(string $predikat): string
    {
        return match ($predikat) {
            'memuaskan' => 'Memuaskan',
            'sangat_memuaskan' => 'Sangat Memuaskan',
            'cum_laude' => 'Cum Laude (Dengan Pujian)',
            default => ucfirst(str_replace('_', ' ', $predikat)),
        };
    }

    private function translateRole(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'staff' => 'Staff',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
            default => ucfirst($role),
        };
    }
}
