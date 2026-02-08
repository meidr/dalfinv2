<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembimbing;
use App\Models\Bimbingan;
use App\Models\Skripsi;

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

        $skripsi = $query->orderBy('updated_at', 'desc')->paginate(10);

        // Transform data to include total bimbingan count
        $skripsi->getCollection()->transform(function ($item) {
            $item->total_bimbingan = $item->bimbingan->count();
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
}
