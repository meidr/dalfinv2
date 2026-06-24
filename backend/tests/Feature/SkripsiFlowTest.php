<?php

namespace Tests\Feature;

use App\Models\Bimbingan;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Pembimbing;
use App\Models\Prodi;
use App\Models\Seminar;
use App\Models\Skripsi;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SkripsiFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Mahasiswa $mahasiswa;

    protected Dosen $dosen1;

    protected Dosen $dosen2;

    protected Dosen $dosen3;

    protected Dosen $dosen4;

    protected Dosen $dosen5;

    protected Tahun $tahun;

    protected Prodi $prodi;

    protected Fakultas $fakultas;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create admin user
        $this->admin = User::create([
            'username' => 'admin_test',
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create fakultas
        $this->fakultas = Fakultas::create([
            'kode' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'is_active' => true,
        ]);

        // Create prodi
        $this->prodi = Prodi::create([
            'kode' => 'TI',
            'nama' => 'Teknik Informatika',
            'fakultas_id' => $this->fakultas->id,
            'jenjang' => 'S1',
            'is_active' => true,
        ]);

        // Create tahun akademik
        $this->tahun = Tahun::create([
            'name' => '2025/2026',
            'kode' => '20252',
            'semester' => 'Genap',
            'is_active' => true,
        ]);

        // Create mahasiswa user + profile
        $mhsUser = User::create([
            'username' => '12345678',
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@test.com',
            'password' => 'password',
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'user_id' => $mhsUser->id,
            'nim' => '12345678',
            'nama' => 'Mahasiswa Test',
            'prodi_id' => $this->prodi->id,
            'tahun_id' => $this->tahun->id,
            'semester' => 8,
            'is_active' => true,
            'status' => 'aktif',
        ]);

        // Create 5 dosen (2 pembimbing + 3 penguji)
        $dosenData = [
            ['nip' => '111', 'nama' => 'Dosen Pembimbing 1', 'email' => 'dosen1@test.com'],
            ['nip' => '222', 'nama' => 'Dosen Pembimbing 2', 'email' => 'dosen2@test.com'],
            ['nip' => '333', 'nama' => 'Dosen Penguji 1',    'email' => 'dosen3@test.com'],
            ['nip' => '444', 'nama' => 'Dosen Penguji 2',    'email' => 'dosen4@test.com'],
            ['nip' => '555', 'nama' => 'Dosen Penguji 3',    'email' => 'dosen5@test.com'],
        ];

        $dosenModels = [];
        foreach ($dosenData as $d) {
            $user = User::create([
                'username' => $d['nip'],
                'name' => $d['nama'],
                'email' => $d['email'],
                'password' => 'password',
                'role' => 'dosen',
                'is_active' => true,
            ]);

            $dosenModels[] = Dosen::create([
                'user_id' => $user->id,
                'nip' => $d['nip'],
                'nama' => $d['nama'],
                'prodi_id' => $this->prodi->id,
                'is_active' => true,
                'kuota_bimbingan' => 10,
            ]);
        }

        [$this->dosen1, $this->dosen2, $this->dosen3, $this->dosen4, $this->dosen5] = $dosenModels;
    }

    // ========================
    //  HELPER METHODS
    // ========================

    /**
     * Create a new skripsi via API (status = pengajuan).
     * Pre-sets file_skripsi so later status transitions won't fail the file requirement.
     */
    private function createSkripsi(string $judul = 'Judul Skripsi Test'): array
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/skripsi', [
                'mahasiswa_id' => $this->mahasiswa->id,
                'judul' => $judul,
                'th_akademik_id' => $this->tahun->id,
                'status' => 'pengajuan',
            ]);

        $response->assertStatus(201)->assertJsonPath('success', true);

        $data = $response->json('data');

        // Pre-set file_skripsi so transitions to proposal/sempro/semhas/revisi don't fail
        Skripsi::where('id', $data['id'])->update(['file_skripsi' => 'skripsi_files/dummy_test.pdf']);

        return $data;
    }

    /**
     * Update skripsi status via API
     */
    private function updateSkripsiStatus(int $skripsiId, string $status, array $extra = []): array
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/admin/skripsi/{$skripsiId}", array_merge([
                'status' => $status,
            ], $extra));

        $response->assertStatus(200)->assertJsonPath('success', true);

        return $response->json('data');
    }

    /**
     * Schedule a sempro, complete it with given hasil.
     *
     * Pre-condition: skripsi must be in a status accepted by SeminarController.store
     *                i.e. ['pengajuan', 'draft', 'ditolak', 'proposal'].
     *                The store method auto-sets status to 'sempro'.
     *
     * Post-condition if hasil=lulus:  skripsi.status = penentuan_dospem
     * Post-condition if hasil=lulus_bersyarat: skripsi.status remains sempro
     * Post-condition if hasil=tidak_lulus:     skripsi.status = ditolak
     */
    private function scheduleSempro(int $skripsiId, string $hasil = 'lulus'): array
    {
        // Schedule sempro (auto-sets skripsi status to 'sempro')
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/seminar', [
                'skripsi_id' => $skripsiId,
                'jenis' => 'sempro',
                'tanggal' => now()->addDays(7)->toDateString(),
                'waktu' => '09:00',
                'ruangan' => 'Ruang A1',
                'penguji' => [
                    ['dosen_id' => $this->dosen3->id, 'peran' => 'ketua'],
                    ['dosen_id' => $this->dosen4->id, 'peran' => 'penguji_1'],
                ],
            ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $seminar = $response->json('data');

        // Verify status was auto-set to 'sempro'
        $this->assertDatabaseHas('skripsi', [
            'id' => $skripsiId,
            'status' => 'sempro',
        ]);

        // Complete the sempro
        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/admin/seminar/{$seminar['id']}", [
                'status' => 'selesai',
                'hasil' => $hasil,
                'nilai' => 80,
            ]);

        $updateResponse->assertStatus(200)->assertJsonPath('success', true);

        return $updateResponse->json('data');
    }

    /**
     * Assign pembimbing (dospem) via API.
     *
     * Pre-condition:  skripsi status in ['pengajuan', 'penentuan_dospem']
     * Post-condition: skripsi.status = 'dospem'
     */
    private function assignPembimbing(int $skripsiId): array
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/pembimbing', [
                'skripsi_id' => $skripsiId,
                'pembimbing_1_id' => $this->dosen1->id,
                'pembimbing_2_id' => $this->dosen2->id,
            ]);

        $response->assertStatus(200)->assertJsonPath('success', true);

        return $response->json('data');
    }

    /**
     * Schedule a seminar hasil, then mark it selesai.
     *
     * Pre-condition: skripsi should be in semhas-eligible status.
     */
    private function scheduleSemhas(int $skripsiId): array
    {
        // Schedule semhas
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/seminar-hasil', [
                'skripsi_id' => $skripsiId,
                'tanggal' => now()->addDays(14)->toDateString(),
                'waktu' => '10:00',
                'ruangan' => 'Ruang B2',
                'penguji' => [
                    ['dosen_id' => $this->dosen3->id, 'peran' => 'ketua'],
                    ['dosen_id' => $this->dosen4->id, 'peran' => 'penguji_1'],
                ],
            ]);

        $response->assertStatus(201)->assertJsonPath('success', true);
        $seminar = $response->json('data');

        // Complete semhas
        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/admin/seminar-hasil/{$seminar['id']}", [
                'status' => 'selesai',
                'nilai' => 82,
            ]);

        $updateResponse->assertStatus(200);

        return $updateResponse->json('data');
    }

    /**
     * Schedule sidang via API.
     *
     * Pre-condition:  skripsi.status = 'pengajuan_sidang_acc'
     * Post-condition: skripsi.status = 'sidang'
     */
    private function scheduleSidang(int $skripsiId): array
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/ujian', [
                'skripsi_id' => $skripsiId,
                'tanggal' => now()->addDays(21)->toDateString(),
                'waktu' => '13:00',
                'ruangan' => 'Ruang C3',
                'penguji' => [
                    ['dosen_id' => $this->dosen3->id, 'peran' => 'ketua'],
                    ['dosen_id' => $this->dosen4->id, 'peran' => 'penguji_1'],
                    ['dosen_id' => $this->dosen5->id, 'peran' => 'penguji_2'],
                ],
            ]);

        $response->assertStatus(201)->assertJsonPath('success', true);

        return $response->json('data');
    }

    public function test_student_sidang_request_requires_sk6_file(): void
    {
        $skripsi = $this->createSkripsi('Sidang tanpa SK 6');

        $response = $this->actingAs($this->mahasiswa->user, 'sanctum')
            ->postJson('/api/mahasiswa/skripsi/request-ujian');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file_sk6')
            ->assertJsonPath('errors.file_sk6.0', 'File SK 6 wajib dilampirkan saat pengajuan sidang.');

        $this->assertDatabaseMissing('dokumen', [
            'skripsi_id' => $skripsi['id'],
            'jenis' => 'sk6',
        ]);
    }

    public function test_student_sidang_request_stores_sk6_in_student_documents_and_admin_table(): void
    {
        $skripsi = $this->createSkripsi('Sidang dengan SK 6');
        Skripsi::whereKey($skripsi['id'])->update(['status' => 'bimbingan']);
        Pembimbing::create([
            'skripsi_id' => $skripsi['id'],
            'dosen_id' => $this->dosen1->id,
            'jenis' => 'pembimbing_1',
        ]);
        \App\Models\Configuration::updateOrCreate(
            ['key' => 'syarat_bimbingan_ujian'],
            ['value' => ['pembimbing_1' => 0, 'pembimbing_2' => 0]]
        );
        \App\Models\Dokumen::create([
            'skripsi_id' => $skripsi['id'],
            'jenis' => 'final',
            'nama_file' => 'naskah_final.pdf',
            'path' => 'dokumen/final.pdf',
            'status' => 'approved',
            'uploaded_by' => $this->mahasiswa->user_id,
        ]);

        $response = $this->actingAs($this->mahasiswa->user, 'sanctum')
            ->post('/api/mahasiswa/skripsi/request-ujian', [
                'file_sk6' => UploadedFile::fake()->create('SK_6.pdf', 100, 'application/pdf'),
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.sk6_document.jenis', 'sk6')
            ->assertJsonPath('data.sk6_document.status', 'approved');

        $sk6 = \App\Models\Dokumen::where('skripsi_id', $skripsi['id'])
            ->where('jenis', 'sk6')
            ->firstOrFail();

        $this->assertSame('approved', $sk6->status);
        Storage::disk('public')->assertExists($sk6->path);
        $this->assertDatabaseHas('skripsi', [
            'id' => $skripsi['id'],
            'status' => 'pengajuan_sidang',
            'progress_percentage' => 60,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/ujian/eligible?search=Sidang+dengan+SK+6')
            ->assertOk()
            ->assertJsonPath('data.data.0.status', 'pengajuan_sidang')
            ->assertJsonPath('data.data.0.dokumen.0.jenis', 'sk6');

        $this->actingAs($this->mahasiswa->user, 'sanctum')
            ->getJson('/api/mahasiswa/skripsi/detail')
            ->assertOk()
            ->assertJsonFragment([
                'jenis' => 'sk6',
                'nama_file' => 'SK_6.pdf',
            ]);
    }

    public function test_admin_sidang_request_requires_and_stores_sk6_file(): void
    {
        $skripsi = $this->createSkripsi('Pengajuan admin dengan SK 6');
        Skripsi::whereKey($skripsi['id'])->update(['status' => 'bimbingan']);
        Pembimbing::create([
            'skripsi_id' => $skripsi['id'],
            'dosen_id' => $this->dosen1->id,
            'jenis' => 'pembimbing_1',
        ]);
        \App\Models\Configuration::updateOrCreate(
            ['key' => 'syarat_bimbingan_ujian'],
            ['value' => ['pembimbing_1' => 0, 'pembimbing_2' => 0]]
        );

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/bimbingan/pengajuan-ujian', ['skripsi_id' => $skripsi['id']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file_sk6');

        $this->actingAs($this->admin, 'sanctum')
            ->post('/api/admin/bimbingan/pengajuan-ujian', [
                'skripsi_id' => $skripsi['id'],
                'file_sk6' => UploadedFile::fake()->create('SK_6_Admin.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ])
            ->assertOk()
            ->assertJsonPath('data.sk6_document.jenis', 'sk6');

        $this->assertDatabaseHas('skripsi', [
            'id' => $skripsi['id'],
            'status' => 'pengajuan_sidang',
            'progress_percentage' => 60,
        ]);

        $this->assertDatabaseHas('dokumen', [
            'skripsi_id' => $skripsi['id'],
            'jenis' => 'sk6',
            'nama_file' => 'SK_6_Admin.docx',
        ]);
    }

    public function test_data_ujian_lists_bimbingan_student_after_guidance_requirement_is_met(): void
    {
        $skripsi = $this->createSkripsi('Kandidat Pengajuan dari Data Ujian');
        Skripsi::whereKey($skripsi['id'])->update(['status' => 'bimbingan']);
        Pembimbing::create([
            'skripsi_id' => $skripsi['id'],
            'dosen_id' => $this->dosen1->id,
            'jenis' => 'pembimbing_1',
        ]);
        \App\Models\Configuration::updateOrCreate(
            ['key' => 'syarat_bimbingan_ujian'],
            ['value' => ['pembimbing_1' => 1, 'pembimbing_2' => 1]]
        );

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/ujian/eligible?search=Kandidat+Pengajuan')
            ->assertOk()
            ->assertJsonPath('data.total', 0);

        Bimbingan::create([
            'skripsi_id' => $skripsi['id'],
            'dosen_id' => $this->dosen1->id,
            'tanggal' => now()->toDateString(),
            'topik' => 'Persiapan sidang',
            'status' => 'approved',
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/ujian/eligible?search=Kandidat+Pengajuan')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.status', 'bimbingan')
            ->assertJsonPath('data.data.0.eligibility.pembimbing_1.count', 1)
            ->assertJsonPath('data.data.0.eligibility.all_met', true);
    }

    public function test_admin_can_cancel_unscheduled_sidang_request_but_not_a_scheduled_exam(): void
    {
        $skripsi = $this->createSkripsi('Pengajuan yang dibatalkan');
        Skripsi::whereKey($skripsi['id'])->update([
            'status' => 'pengajuan_sidang_acc',
            'progress_percentage' => 65,
        ]);
        \App\Models\Dokumen::create([
            'skripsi_id' => $skripsi['id'],
            'jenis' => 'sk6',
            'nama_file' => 'SK_6.pdf',
            'path' => 'dokumen/sk6.pdf',
            'status' => 'approved',
            'uploaded_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/admin/ujian/{$skripsi['id']}/cancel-request")
            ->assertOk()
            ->assertJsonPath('data.status', 'bimbingan')
            ->assertJsonPath('data.progress_percentage', 50);

        $this->assertDatabaseHas('dokumen', [
            'skripsi_id' => $skripsi['id'],
            'jenis' => 'sk6',
        ]);

        Skripsi::whereKey($skripsi['id'])->update(['status' => 'pengajuan_sidang_acc']);
        $this->scheduleSidang($skripsi['id']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/admin/ujian/{$skripsi['id']}/cancel-request")
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'Pengajuan tidak dapat dibatalkan karena jadwal sidang sudah dibuat.'
            );
    }

    /**
     * Complete sidang with a given hasil.
     *
     * Post-condition if hasil=lulus:       skripsi.status = lulus, progress = 100
     * Post-condition if hasil=lulus_revisi: skripsi.status = revisi, progress = 90
     */
    private function completeSidang(int $ujianId, string $hasil = 'lulus'): array
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/admin/ujian/{$ujianId}", [
                'status' => 'selesai',
                'hasil' => $hasil,
            ]);

        $response->assertStatus(200)->assertJsonPath('success', true);

        return $response->json('data');
    }

    /**
     * Create SK Yudisium via API
     */
    private function createSKYudisium(int $skripsiId): array
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/sk-yudisium', [
                'skripsi_id' => $skripsiId,
                'nomor_sk' => 'SK-'.uniqid(),
                'tanggal' => now()->toDateString(),
                'ipk' => 3.75,
                'predikat' => 'cum_laude',
            ]);

        $response->assertStatus(201)->assertJsonPath('success', true);

        return $response->json('data');
    }

    /**
     * Create batch and assign skripsi to it.
     */
    private function assignToBatch(int $skripsiId): string
    {
        $nomorBatch = 'BATCH-'.uniqid();

        // Create batch
        $batchResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/sk-yudisium-batch', [
                'nomor_sk_batch' => $nomorBatch,
                'th_akademik_id' => $this->tahun->id,
                'prodi_id' => $this->prodi->id,
                'tanggal_terbit' => now()->toDateString(),
                'tanggal_yudisium' => now()->toDateString(),
            ]);

        $batchResponse->assertStatus(201)->assertJsonPath('success', true);

        // Assign skripsi to batch
        $assignResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/sk-yudisium-batch/assign', [
                'nomor_sk_batch' => $nomorBatch,
                'th_akademik_id' => $this->tahun->id,
                'prodi_id' => $this->prodi->id,
                'tanggal_terbit' => now()->toDateString(),
                'tanggal_yudisium' => now()->toDateString(),
                'skripsi_ids' => [$skripsiId],
            ]);

        $assignResponse->assertStatus(200)->assertJsonPath('success', true);

        return $nomorBatch;
    }

    /**
     * Verify final state: skripsi lulus, SK Yudisium exists, mahasiswa lulus, batch assigned.
     */
    private function assertFinalState(int $skripsiId, string $nomorBatch): void
    {
        $this->assertDatabaseHas('skripsi', [
            'id' => $skripsiId,
            'status' => 'lulus',
            'progress_percentage' => 100,
        ]);

        $this->assertDatabaseHas('sk_yudisium', [
            'skripsi_id' => $skripsiId,
            'nomor_sk_batch' => $nomorBatch,
        ]);

        $this->assertDatabaseHas('mahasiswa', [
            'id' => $this->mahasiswa->id,
            'status' => 'lulus',
        ]);
    }

    // ========================
    //  SCENARIO 1: HAPPY PATH
    // ========================

    /**
     * Skenario 1: Semua lancar dari pengajuan sampai SK Yudisium Batch.
     *
     * pengajuan → disetujui → proposal → [sempro: lulus] → penentuan_dospem
     * → dospem → bimbingan → pengajuan_sidang → pengajuan_sidang_acc
     * → [semhas] → [sidang: lulus] → lulus → SK Yudisium → Batch
     */
    public function test_scenario_1_happy_path_all_approved(): void
    {
        // Step 1: Create skripsi (status = pengajuan)
        $skripsi = $this->createSkripsi('Implementasi Machine Learning untuk Deteksi Anomali');
        $skripsiId = $skripsi['id'];

        // Step 2: Approve → disetujui
        $this->updateSkripsiStatus($skripsiId, 'disetujui');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'disetujui', 'progress_percentage' => 10]);

        // Step 3: Move to proposal (mahasiswa uploads proposal)
        $this->updateSkripsiStatus($skripsiId, 'proposal');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'proposal']);

        // Step 4: Schedule and complete sempro (lulus)
        // SeminarController.store auto-sets status to 'sempro' from 'proposal'
        $this->scheduleSempro($skripsiId, 'lulus');
        // After lulus, auto-transition to penentuan_dospem
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'penentuan_dospem', 'progress_percentage' => 30]);

        // Step 5: Assign pembimbing → dospem
        $this->assignPembimbing($skripsiId);
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'dospem']);

        // Step 6: Bimbingan
        $this->updateSkripsiStatus($skripsiId, 'bimbingan');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'bimbingan', 'progress_percentage' => 50]);

        // Step 7: Pengajuan sidang → approved
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'pengajuan_sidang_acc']);

        // Step 8: Seminar hasil
        $this->scheduleSemhas($skripsiId);

        // Step 9: Schedule and complete sidang (lulus)
        // Need to ensure status is pengajuan_sidang_acc for UjianController
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $sidang = $this->scheduleSidang($skripsiId);
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'sidang', 'progress_percentage' => 85]);

        $this->completeSidang($sidang['id'], 'lulus');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'lulus', 'progress_percentage' => 100]);

        // Step 10: SK Yudisium
        $this->createSKYudisium($skripsiId);

        // Step 11: Assign to batch
        $nomorBatch = $this->assignToBatch($skripsiId);

        // Final assertions
        $this->assertFinalState($skripsiId, $nomorBatch);
    }

    // ========================
    //  SCENARIO 2: SEMPRO LULUS BERSYARAT
    // ========================

    /**
     * Skenario 2: Sempro lulus bersyarat (revisi proposal), lalu lancar.
     *
     * pengajuan → disetujui → proposal → [sempro: lulus_bersyarat]
     * → status tetap sempro → admin approve revisi → penentuan_dospem
     * → dospem → bimbingan → ... → SK Yudisium Batch
     */
    public function test_scenario_2_sempro_lulus_bersyarat_then_smooth(): void
    {
        $skripsi = $this->createSkripsi('Analisis Sentimen Menggunakan Deep Learning');
        $skripsiId = $skripsi['id'];

        // Approve → proposal
        $this->updateSkripsiStatus($skripsiId, 'disetujui');
        $this->updateSkripsiStatus($skripsiId, 'proposal');

        // Sempro → lulus_bersyarat (status tetap 'sempro')
        $this->scheduleSempro($skripsiId, 'lulus_bersyarat');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'sempro']);

        // Admin approves revision → manually advance to penentuan_dospem
        $this->updateSkripsiStatus($skripsiId, 'penentuan_dospem');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'penentuan_dospem']);

        // Assign dospem → bimbingan → pengajuan_sidang → semhas → sidang(lulus)
        $this->assignPembimbing($skripsiId);
        $this->updateSkripsiStatus($skripsiId, 'bimbingan');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');

        $this->scheduleSemhas($skripsiId);

        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $sidang = $this->scheduleSidang($skripsiId);
        $this->completeSidang($sidang['id'], 'lulus');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'lulus']);

        // SK Yudisium + Batch
        $this->createSKYudisium($skripsiId);
        $nomorBatch = $this->assignToBatch($skripsiId);

        $this->assertFinalState($skripsiId, $nomorBatch);
    }

    // ========================
    //  SCENARIO 3: SIDANG LULUS REVISI
    // ========================

    /**
     * Skenario 3: Normal sampai sidang, sidang lulus dengan revisi, lalu lancar.
     *
     * pengajuan → ... → sidang(lulus_revisi) → revisi → lulus → SK Yudisium Batch
     */
    public function test_scenario_3_sidang_lulus_revisi_then_smooth(): void
    {
        $skripsi = $this->createSkripsi('Pengembangan Sistem IoT untuk Smart Home');
        $skripsiId = $skripsi['id'];

        // Normal flow until sidang
        $this->updateSkripsiStatus($skripsiId, 'disetujui');
        $this->updateSkripsiStatus($skripsiId, 'proposal');
        $this->scheduleSempro($skripsiId, 'lulus');
        $this->assignPembimbing($skripsiId);
        $this->updateSkripsiStatus($skripsiId, 'bimbingan');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $this->scheduleSemhas($skripsiId);

        // Schedule sidang
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $sidang = $this->scheduleSidang($skripsiId);

        // Sidang: lulus_revisi → status = revisi
        $this->completeSidang($sidang['id'], 'lulus_revisi');
        $this->assertDatabaseHas('skripsi', [
            'id' => $skripsiId,
            'status' => 'revisi',
            'progress_percentage' => 90,
        ]);

        // Admin approves revision → update to lulus
        $this->updateSkripsiStatus($skripsiId, 'lulus');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'lulus']);

        // SK Yudisium + Batch
        $this->createSKYudisium($skripsiId);
        $nomorBatch = $this->assignToBatch($skripsiId);

        $this->assertFinalState($skripsiId, $nomorBatch);
    }

    // ========================
    //  SCENARIO 4: JUDUL DITOLAK → RESUBMIT
    // ========================

    /**
     * Skenario 4: Pengajuan judul ditolak, ajukan ulang, lalu lancar.
     *
     * pengajuan → ditolak → pengajuan baru → disetujui → ... → SK Yudisium Batch
     */
    public function test_scenario_4_judul_ditolak_then_resubmit(): void
    {
        // First attempt: ditolak
        $skripsi1 = $this->createSkripsi('Judul Pertama Yang Ditolak');
        $skripsi1Id = $skripsi1['id'];

        $this->updateSkripsiStatus($skripsi1Id, 'ditolak', [
            'catatan_admin' => 'Judul terlalu umum, silakan perbaiki',
        ]);
        $this->assertDatabaseHas('skripsi', ['id' => $skripsi1Id, 'status' => 'ditolak', 'progress_percentage' => 0]);

        // Second attempt: approved
        $skripsi2 = $this->createSkripsi('Implementasi Blockchain untuk Keamanan Data Medis');
        $skripsi2Id = $skripsi2['id'];

        $this->updateSkripsiStatus($skripsi2Id, 'disetujui');
        $this->updateSkripsiStatus($skripsi2Id, 'proposal');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsi2Id, 'status' => 'proposal']);

        // Full flow on second skripsi
        $this->scheduleSempro($skripsi2Id, 'lulus');
        $this->assignPembimbing($skripsi2Id);
        $this->updateSkripsiStatus($skripsi2Id, 'bimbingan');
        $this->updateSkripsiStatus($skripsi2Id, 'pengajuan_sidang');
        $this->updateSkripsiStatus($skripsi2Id, 'pengajuan_sidang_acc');
        $this->scheduleSemhas($skripsi2Id);

        $this->updateSkripsiStatus($skripsi2Id, 'pengajuan_sidang_acc');
        $sidang = $this->scheduleSidang($skripsi2Id);
        $this->completeSidang($sidang['id'], 'lulus');

        $this->createSKYudisium($skripsi2Id);
        $nomorBatch = $this->assignToBatch($skripsi2Id);

        // Verify new skripsi is lulus with batch
        $this->assertFinalState($skripsi2Id, $nomorBatch);

        // Old skripsi still ditolak
        $this->assertDatabaseHas('skripsi', ['id' => $skripsi1Id, 'status' => 'ditolak']);
    }

    // ========================
    //  SCENARIO 5: SIDANG TIDAK LULUS → RETRY
    // ========================

    /**
     * Skenario 5: Sidang tidak lulus, mengulang sidang, lalu lancar.
     *
     * pengajuan → ... → sidang(tidak_lulus) → reset ke bimbingan
     * → pengajuan_sidang → sidang(lulus) → SK Yudisium Batch
     */
    public function test_scenario_5_sidang_tidak_lulus_then_retry(): void
    {
        $skripsi = $this->createSkripsi('Optimasi Algoritma K-Means untuk Big Data');
        $skripsiId = $skripsi['id'];

        // Normal flow until first sidang
        $this->updateSkripsiStatus($skripsiId, 'disetujui');
        $this->updateSkripsiStatus($skripsiId, 'proposal');
        $this->scheduleSempro($skripsiId, 'lulus');
        $this->assignPembimbing($skripsiId);
        $this->updateSkripsiStatus($skripsiId, 'bimbingan');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $this->scheduleSemhas($skripsiId);

        // First sidang: TIDAK LULUS
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');
        $sidang1 = $this->scheduleSidang($skripsiId);
        $this->completeSidang($sidang1['id'], 'tidak_lulus');

        // Admin resets skripsi to bimbingan for re-preparation
        $this->updateSkripsiStatus($skripsiId, 'bimbingan');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'bimbingan']);

        // Delete the old sidang so we can create a new one
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/admin/ujian/{$sidang1['id']}");

        // Re-submit pengajuan sidang
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang');
        $this->updateSkripsiStatus($skripsiId, 'pengajuan_sidang_acc');

        // Second sidang: LULUS
        $sidang2 = $this->scheduleSidang($skripsiId);
        $this->completeSidang($sidang2['id'], 'lulus');
        $this->assertDatabaseHas('skripsi', ['id' => $skripsiId, 'status' => 'lulus', 'progress_percentage' => 100]);

        // SK Yudisium + Batch
        $this->createSKYudisium($skripsiId);
        $nomorBatch = $this->assignToBatch($skripsiId);

        $this->assertFinalState($skripsiId, $nomorBatch);
    }
}
