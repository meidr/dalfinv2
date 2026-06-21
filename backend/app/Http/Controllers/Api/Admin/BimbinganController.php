<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembimbing;
use App\Models\Bimbingan;
use App\Models\Skripsi;
use App\Models\Configuration;
use App\Models\Notification;
use Carbon\Carbon;

class BimbinganController extends Controller
{
    public function index(Request $request)
    {
        $query = Skripsi::with([
            'mahasiswa.prodi',
            'pembimbing.dosen',
            'bimbingan'
        ])->has('pembimbing'); // Only show skripsi that have pembimbing assigned

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $perPage = max(5, min((int) $request->get('per_page', 10), 100));
        $skripsi = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        // Get bimbingan requirements
        $config = Configuration::where('key', 'syarat_bimbingan_ujian')->first();
        $requirements = $config ? $config->value : ['pembimbing_1' => 8, 'pembimbing_2' => 4];

        // Transform data to include total bimbingan count and eligibility
        $skripsi->getCollection()->transform(function ($item) use ($requirements) {
            $item->total_bimbingan = $item->bimbingan->count();

            // Calculate per-pembimbing counts
            $p1 = $item->pembimbing->where('jenis', 'pembimbing_1')->first();
            $p2 = $item->pembimbing->where('jenis', 'pembimbing_2')->first();

            $countP1 = $p1 ? $item->bimbingan->where('dosen_id', $p1->dosen_id)->where('status', 'approved')->count() : 0;
            $countP2 = $p2 ? $item->bimbingan->where('dosen_id', $p2->dosen_id)->where('status', 'approved')->count() : 0;

            $p1Met = $countP1 >= $requirements['pembimbing_1'];
            $p2Met = $p2 ? ($countP2 >= $requirements['pembimbing_2']) : true;

            $item->eligibility = [
                'requirements' => $requirements,
                'pembimbing_1' => ['count' => $countP1, 'required' => (int) $requirements['pembimbing_1'], 'met' => $p1Met],
                'pembimbing_2' => ['count' => $countP2, 'required' => (int) $requirements['pembimbing_2'], 'met' => $p2Met, 'exists' => (bool) $p2],
                'all_met' => $p1Met && $p2Met,
            ];

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $skripsi
        ]);
    }

    public function show($skripsiId)
    {
        $bimbingan = Bimbingan::with(['dosen'])
            ->where('skripsi_id', $skripsiId)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bimbingan
        ]);
    }

    /**
     * Store a new bimbingan record (admin/staff/superadmin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'dosen_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date',
            'topik' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'catatan_dosen' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,revision,rejected',
        ]);

        $bimbingan = Bimbingan::create([
            'skripsi_id' => $request->skripsi_id,
            'dosen_id' => $request->dosen_id,
            'tanggal' => $request->tanggal,
            'topik' => $request->topik,
            'deskripsi' => $request->deskripsi,
            'catatan_dosen' => $request->catatan_dosen,
            'status' => $request->status ?? 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bimbingan berhasil ditambahkan',
            'data' => $bimbingan->load('dosen')
        ], 201);
    }

    /**
     * Update an existing bimbingan record
     */
    public function update(Request $request, Bimbingan $bimbingan)
    {
        $request->validate([
            'dosen_id' => 'sometimes|exists:dosen,id',
            'tanggal' => 'sometimes|date',
            'topik' => 'sometimes|string|max:255',
            'deskripsi' => 'nullable|string',
            'catatan_dosen' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,revision,rejected',
        ]);

        $bimbingan->update($request->only([
            'dosen_id', 'tanggal', 'topik', 'deskripsi', 'catatan_dosen', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Bimbingan berhasil diperbarui',
            'data' => $bimbingan->fresh()->load('dosen')
        ]);
    }

    /**
     * Delete a bimbingan record
     */
    public function destroy(Bimbingan $bimbingan)
    {
        $bimbingan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bimbingan berhasil dihapus'
        ]);
    }

    /**
     * Generate bulk bimbingan records automatically
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'jumlah' => 'required|integer|min:1|max:50',
            'dosen_id' => 'required|exists:dosen,id',
            'status' => 'nullable|in:pending,approved,revision,rejected',
            'tanggal_mulai' => 'nullable|date',
            'interval_hari' => 'nullable|integer|min:1|max:30',
        ]);

        $skripsi = Skripsi::with('mahasiswa')->findOrFail($request->skripsi_id);
        $jumlah = $request->jumlah;
        $dosenId = $request->dosen_id;
        $status = $request->status ?? 'approved';
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::now()->subDays($jumlah * 7);
        $intervalHari = $request->interval_hari ?? 7;

        $topikList = [
            'Konsultasi BAB I - Pendahuluan',
            'Revisi Latar Belakang Masalah',
            'Pembahasan Rumusan Masalah',
            'Konsultasi BAB II - Tinjauan Pustaka',
            'Review Landasan Teori',
            'Diskusi Penelitian Terdahulu',
            'Konsultasi BAB III - Metodologi',
            'Pembahasan Desain Penelitian',
            'Review Instrumen Penelitian',
            'Konsultasi Teknik Analisis Data',
            'Konsultasi BAB IV - Hasil & Pembahasan',
            'Review Hasil Analisis Data',
            'Diskusi Temuan Penelitian',
            'Pembahasan Implikasi Hasil',
            'Konsultasi BAB V - Kesimpulan',
            'Review Draft Kesimpulan & Saran',
            'Konsultasi Progress Keseluruhan',
            'Review Format & Tata Tulis',
            'Finalisasi Draf Skripsi',
            'Persiapan Seminar/Sidang',
        ];

        $deskripsiList = [
            'Membahas poin-poin perbaikan dari pertemuan sebelumnya',
            'Menyerahkan revisi draft dan mendiskusikan umpan balik',
            'Diskusi mengenai arah penelitian dan langkah selanjutnya',
            'Konsultasi teknis terkait implementasi penelitian',
            'Review keseluruhan progress dan timeline',
        ];

        $created = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $tanggal = $tanggalMulai->copy()->addDays($i * $intervalHari);
            $topik = $topikList[$i % count($topikList)];
            $deskripsi = $deskripsiList[$i % count($deskripsiList)];

            $bimbingan = Bimbingan::create([
                'skripsi_id' => $skripsi->id,
                'dosen_id' => $dosenId,
                'tanggal' => $tanggal->format('Y-m-d'),
                'topik' => $topik,
                'deskripsi' => $deskripsi,
                'status' => $status,
            ]);

            $created[] = $bimbingan;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil generate {$jumlah} data bimbingan",
            'data' => collect($created)->map(fn($b) => $b->load('dosen'))
        ], 201);
    }

    /**
     * Submit pengajuan ujian on behalf of mahasiswa (admin/superadmin)
     */
    public function submitPengajuanUjian(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
        ]);

        $skripsi = Skripsi::with(['pembimbing.dosen', 'mahasiswa', 'dokumen'])->findOrFail($request->skripsi_id);

        if (!in_array($skripsi->status, ['bimbingan', 'dospem', 'pengajuan_sidang_tolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ujian hanya bisa dilakukan pada status bimbingan atau setelah penolakan. Status saat ini: ' . $skripsi->status
            ], 422);
        }

        // Update status
        $skripsi->status = 'pengajuan_sidang';
        $skripsi->alasan_tolak_sidang = null;
        $skripsi->save();

        // Notify dosen pembimbing utama
        $pembimbing1 = $skripsi->pembimbing->where('jenis', 'pembimbing_1')->first();
        if ($pembimbing1?->dosen) {
            Notification::create([
                'type' => 'pengajuan_ujian',
                'title' => 'Pengajuan Ujian Skripsi (oleh Admin)',
                'message' => ($skripsi->mahasiswa->nama ?? 'Mahasiswa') . ' diajukan ujian skripsi oleh admin. Menunggu persetujuan.',
                'data' => ['skripsi_id' => $skripsi->id],
                'user_id' => $pembimbing1->dosen->user_id ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan ujian berhasil disubmit',
        ]);
    }

    /**
     * Approve or reject pengajuan ujian (admin/superadmin)
     */
    public function reviewPengajuanUjian(Request $request)
    {
        $request->validate([
            'skripsi_id' => 'required|exists:skripsi,id',
            'action' => 'required|in:approve,reject',
            'alasan' => 'required_if:action,reject|nullable|string|max:500',
        ]);

        $skripsi = Skripsi::with('mahasiswa')->findOrFail($request->skripsi_id);

        if ($skripsi->status !== 'pengajuan_sidang') {
            return response()->json([
                'success' => false,
                'message' => 'Skripsi tidak dalam status pengajuan sidang'
            ], 422);
        }

        if ($request->action === 'approve') {
            $skripsi->status = 'sidang';
            $skripsi->progress_percentage = min(($skripsi->progress_percentage ?? 0) + 10, 100);
            $skripsi->save();

            // Notify mahasiswa
            if ($skripsi->mahasiswa?->user_id) {
                Notification::create([
                    'type' => 'pengajuan_ujian_approved',
                    'title' => 'Pengajuan Ujian Disetujui',
                    'message' => 'Pengajuan ujian skripsi Anda telah disetujui oleh admin.',
                    'data' => ['skripsi_id' => $skripsi->id],
                    'user_id' => $skripsi->mahasiswa->user_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan ujian disetujui',
            ]);
        } else {
            $skripsi->status = 'pengajuan_sidang_tolak';
            $skripsi->alasan_tolak_sidang = $request->alasan;
            $skripsi->save();

            // Notify mahasiswa
            if ($skripsi->mahasiswa?->user_id) {
                Notification::create([
                    'type' => 'pengajuan_ujian_rejected',
                    'title' => 'Pengajuan Ujian Ditolak',
                    'message' => 'Pengajuan ujian skripsi Anda ditolak. Alasan: ' . $request->alasan,
                    'data' => ['skripsi_id' => $skripsi->id],
                    'user_id' => $skripsi->mahasiswa->user_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan ujian ditolak',
            ]);
        }
    }
}
