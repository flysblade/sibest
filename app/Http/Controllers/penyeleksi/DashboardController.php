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
            ->map(function ($item) {
                // Total pengajuan per offering
                $item->total_pengajuan = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->count();

                // Menunggu review (status diproses)
                $item->menunggu_review = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->where('status', 'diproses')
                    ->count();

                // Sudah direview (selesai/ditolak)
                $item->sudah_review = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->whereIn('status', ['selesai', 'ditolak'])
                    ->count();

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
            ->get()
            ->map(function ($item) {
                // Cari tahapan yang sedang aktif (menunggu)
                $tahapAktif = DB::table('pengajuan_tahapan as pt')
                    ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
                    ->where('pt.id_pengajuan', $item->id)
                    ->where('pt.status', 'menunggu')
                    ->orderBy('t.urutan')
                    ->select('t.nama_tahapan', 't.urutan')
                    ->first();

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