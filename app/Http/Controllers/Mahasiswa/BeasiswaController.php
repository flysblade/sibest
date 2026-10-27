<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\BeasiswaOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BeasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = BeasiswaOffering::with(['program', 'periode'])
            ->where('status', 'published');

        if ($request->filled('jalur')) {
            $query->whereHas('program', function ($q) use ($request) {
                $q->where('jalur', $request->jalur);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('program', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $beasiswas = $query
            ->orderBy('tanggal_tutup')
            ->get();

        return view('mahasiswa.beasiswa.index', compact('beasiswas', 'user'));
    }

    public function show(int $id): View
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Data utama beasiswa
        |--------------------------------------------------------------------------
        */
        $beasiswa = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->where('o.id', $id)
            ->where('o.status', 'published')
            ->select([
                'o.id',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'o.link_portal',
                'o.kontak_pic',
                'p.nama as nama_program',
                'p.deskripsi',
                'p.syarat',
                'p.jalur',
                'k.nama_kategori',
                'pr.nama_periode',
            ])
            ->first();

        abort_if(!$beasiswa, 404);

        /*
        |--------------------------------------------------------------------------
        | Tahapan seleksi
        |--------------------------------------------------------------------------
        */
        $tahapan = DB::table('tahapan')
            ->where('id_offering', $beasiswa->id)
            ->orderBy('urutan')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Dokumen yang dibutuhkan
        |--------------------------------------------------------------------------
        */
        $dokumen = DB::table('dokumen_template')
            ->where('id_offering', $beasiswa->id)
            ->orderByDesc('required')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pengumuman terkait
        |--------------------------------------------------------------------------
        */
        $pengumuman = DB::table('pengumuman')
            ->where('id_offering', $beasiswa->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->get();

        return view('mahasiswa.beasiswa.show', compact(
            'user',
            'beasiswa',
            'tahapan',
            'dokumen',
            'pengumuman'
        ));
    }
}