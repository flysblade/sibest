<?php

namespace App\Http\Controllers\Penyeleksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BeasiswaSayaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ============================================================
        // AMBIL OFFERING YANG DI-ASSIGN
        // ============================================================
        $offeringIds = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->pluck('id_offering')
            ->toArray();

        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->whereIn('o.id', $offeringIds)
            ->select([
                'o.id',
                'o.status as status_offering',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'o.link_portal',
                'o.kontak_pic',
                'p.nama as nama_program',
                'p.jalur',
                'p.deskripsi',
                'pr.nama_periode',
            ])
            ->get()
            ->map(function ($item) {
                // Statistik pengajuan per offering
                $item->total_pengajuan = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->count();

                $item->menunggu_review = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->where('status', 'diproses')
                    ->count();

                $item->selesai_review = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->whereIn('status', ['selesai', 'ditolak'])
                    ->count();

                // Tahapan per offering
                $item->tahapan = DB::table('tahapan')
                    ->where('id_offering', $item->id)
                    ->orderBy('urutan')
                    ->select('id', 'nama_tahapan', 'urutan', 'mulai_at', 'selesai_at', 'lokasi')
                    ->get()
                    ->map(function ($tahap) use ($item) {
                        // Hitung berapa pengajuan yang udah dinilai di tahap ini
                        $tahap->sudah_dinilai = DB::table('pengajuan_tahapan as pt')
                            ->join('pengajuan as pg', 'pg.id', '=', 'pt.id_pengajuan')
                            ->where('pg.id_offering', $item->id)
                            ->where('pt.id_tahapan', $tahap->id)
                            ->whereIn('pt.status', ['lolos', 'gagal'])
                            ->count();

                        return $tahap;
                    });

                return $item;
            });

        // ============================================================
        // RETURN JSON (debug mode)
        // ============================================================
        return response()->json([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'role' => $user->role,
            ],
            'total_offering' => count($offeringIds),
            'offerings' => $offerings,
        ]);
    }
}