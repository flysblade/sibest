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
            ->get();

        $offeringStats = DB::table('pengajuan')
            ->whereIn('id_offering', $offeringIds)
            ->select(
                'id_offering',
                DB::raw('COUNT(*) as total_pengajuan'),
                DB::raw("SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as menunggu_review"),
                DB::raw("SUM(CASE WHEN status IN ('selesai', 'ditolak') THEN 1 ELSE 0 END) as selesai_review")
            )
            ->groupBy('id_offering')
            ->get()
            ->keyBy('id_offering');

        $tahapan = DB::table('tahapan')
            ->whereIn('id_offering', $offeringIds)
            ->orderBy('urutan')
            ->select('id', 'id_offering', 'nama_tahapan', 'urutan', 'mulai_at', 'selesai_at', 'lokasi')
            ->get();

        $tahapanDinilai = DB::table('pengajuan_tahapan as pt')
            ->join('pengajuan as pg', 'pg.id', '=', 'pt.id_pengajuan')
            ->whereIn('pg.id_offering', $offeringIds)
            ->whereIn('pt.status', ['lolos', 'gagal'])
            ->select('pg.id_offering', 'pt.id_tahapan', DB::raw('COUNT(*) as sudah_dinilai'))
            ->groupBy('pg.id_offering', 'pt.id_tahapan')
            ->get()
            ->keyBy(function ($item) {
                return $item->id_offering . ':' . $item->id_tahapan;
            });

        $offerings->map(function ($item) use ($offeringStats, $tahapan, $tahapanDinilai) {
                $stats = $offeringStats->get($item->id);
                $item->total_pengajuan = (int) ($stats->total_pengajuan ?? 0);
                $item->menunggu_review = (int) ($stats->menunggu_review ?? 0);
                $item->selesai_review = (int) ($stats->selesai_review ?? 0);
                $item->tahapan = $tahapan
                    ->where('id_offering', $item->id)
                    ->map(function ($tahap) use ($item, $tahapanDinilai) {
                        $key = $item->id . ':' . $tahap->id;
                        $stats = $tahapanDinilai->get($key);
                        $tahap->sudah_dinilai = (int) ($stats->sudah_dinilai ?? 0);
                        return $tahap;
                    })
                    ->values();
                return $item;
            });

        // ============================================================
        return view('penyeleksi.beasiswa-saya.index', compact(
            'user',
            'offerings',
            'offeringIds'
        ));
    }
}