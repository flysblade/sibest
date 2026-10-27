<?php

namespace App\Http\Controllers\Penyeleksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ============================================================
        // AMBIL OFFERING YANG DI-ASSIGN KE PENYELEKSI
        // ============================================================
        $offeringIds = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->pluck('id_offering')
            ->toArray();

        // ============================================================
        // KPI 1: JUMLAH OFFERING YANG DITUGASKAN
        // ============================================================
        $totalOffering = count($offeringIds);

        // ============================================================
        // KPI 2: PENGAJUAN PERLU DIREVIEW
        // ============================================================
        $perluDireview = DB::table('pengajuan')
            ->whereIn('id_offering', $offeringIds)
            ->whereIn('status', ['diajukan', 'diproses'])
            ->count();

        // ============================================================
        // KPI 3: SUDAH DIREVIEW
        // ============================================================
        $sudahDireview = DB::table('pengajuan')
            ->whereIn('id_offering', $offeringIds)
            ->whereIn('status', ['selesai', 'ditolak'])
            ->count();

        // ============================================================
        // SECTION: OFFERING YANG DITUGASKAN
        // ============================================================
        $offeringStats = DB::table('pengajuan')
            ->whereIn('id_offering', $offeringIds)
            ->select(
                'id_offering',
                DB::raw('COUNT(*) as total_pengajuan'),
                DB::raw("SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as menunggu_review"),
                DB::raw("SUM(CASE WHEN status IN ('selesai', 'ditolak') THEN 1 ELSE 0 END) as sudah_review")
            )
            ->groupBy('id_offering')
            ->get()
            ->keyBy('id_offering');

        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->whereIn('o.id', $offeringIds)
            ->select([
                'o.id',
                'o.status as status_offering',
                'o.tanggal_tutup',
                'p.nama as nama_program',
                'pr.nama_periode',
            ])
            ->get()
            ->map(function ($item) use ($offeringStats) {
                $stats = $offeringStats->get($item->id);
                $item->total_pengajuan = (int) ($stats->total_pengajuan ?? 0);
                $item->menunggu_review = (int) ($stats->menunggu_review ?? 0);
                $item->sudah_review = (int) ($stats->sudah_review ?? 0);
                return $item;
            });

        // ============================================================
        // TABEL: PENGAJUAN PERLU DIREVIEW (max 10)
        // ============================================================
        $pengajuanPerlu = DB::table('pengajuan as pg')
            ->join('users as u', 'u.id', '=', 'pg.id_mahasiswa')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->whereIn('pg.status', ['diajukan', 'diproses'])
            ->where('pg.status', 'diproses')
            ->whereIn('pg.id_offering', $offeringIds)
            ->select([
                'pg.id',
                'pg.status',
                'pg.submitted_at',
                'pg.created_at',
                'u.nama as nama_mahasiswa',
                'u.username as nim_mahasiswa',
                'p.nama as nama_program',
            ])
            ->orderByDesc('pg.submitted_at')
            ->limit(10)
            ->get();

        $tahapAktifByPengajuan = $pengajuanPerlu->isEmpty()
            ? collect()
            : DB::table('pengajuan_tahapan as pt')
                ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
                ->whereIn('pt.id_pengajuan', $pengajuanPerlu->pluck('id'))
                ->where('pt.status', 'menunggu')
                ->orderBy('t.urutan')
                ->select('pt.id_pengajuan', 't.nama_tahapan', 't.urutan')
                ->get()
                ->groupBy('id_pengajuan');

        $pengajuanPerlu->each(function ($item) use ($tahapAktifByPengajuan) {
                $tahapAktif = ($tahapAktifByPengajuan->get($item->id) ?? collect())->first();
                $item->tahap_aktif = $tahapAktif ? $tahapAktif->nama_tahapan : '-';
                return $item;
            });

        return view('penyeleksi.dashboard', compact(
            'user',
            'totalOffering',
            'perluDireview',
            'sudahDireview',
            'offerings',
            'pengajuanPerlu'
        ));
    }
}