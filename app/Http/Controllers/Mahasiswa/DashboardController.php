<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Mahasiswa yang sedang login.
        $user = Auth::user();

        // Hitung beasiswa yang sedang dibuka.
        $beasiswaDibuka = DB::table('beasiswa_offering')
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('tanggal_buka')
                    ->orWhereDate('tanggal_buka', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('tanggal_tutup')
                    ->orWhereDate('tanggal_tutup', '>=', now()->toDateString());
            })
            ->count();

        // Pilih kolom nama periode yang benar-benar tersedia.
        // Jika tidak ada kolom nama yang cocok, tampilkan ID periode sebagai fallback.
        $kolomNamaPeriode = null;
        if (Schema::hasTable('periode')) {
            foreach (['nama_periode', 'nama', 'periode', 'tahun_ajaran', 'tahun'] as $kolom) {
                if (Schema::hasColumn('periode', $kolom)) {
                    $kolomNamaPeriode = $kolom;
                    break;
                }
            }
        }

        $queryPengajuan = DB::table('pengajuan as pg')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->where('pg.id_mahasiswa', $user->id)
            ->where('pg.active_flag', 1)
            ->whereIn('pg.status', ['draft', 'diajukan', 'diproses']);

        if ($kolomNamaPeriode !== null) {
            $queryPengajuan->leftJoin('periode as pr', 'pr.id', '=', 'pg.id_periode');
            $selectNamaPeriode = 'pr.' . $kolomNamaPeriode . ' as nama_periode';
        } else {
            $selectNamaPeriode = 'pg.id_periode as nama_periode';
        }

        // Semua pengajuan aktif mahasiswa, bukan hanya satu pengajuan.
        // Di sini kita pakai ->get() biar bisa nampilin banyak pengajuan.
        $pengajuanAktif = $queryPengajuan
            ->select(
                'pg.id as id_pengajuan',
                'pg.id_periode',
                'pg.status as status_pengajuan',
                'pg.hasil_akhir',
                'pg.submitted_at',
                'pg.created_at',
                'p.nama as nama_program',
                'p.jalur',
                DB::raw($selectNamaPeriode)
            )
            ->orderByDesc('pg.created_at')
            ->get();

        // Hitung tahap untuk setiap pengajuan aktif.
        // Karena pakai ->get(), kita looping datanya satu per satu.
        $tahapSelesai = 0;
        $totalTahapan = 0;

        $tahapanStats = DB::table('pengajuan_tahapan')
            ->whereIn('id_pengajuan', $pengajuanAktif->pluck('id_pengajuan'))
            ->select(
                'id_pengajuan',
                DB::raw('COUNT(*) as total_tahapan'),
                DB::raw("SUM(CASE WHEN status = 'lolos' THEN 1 ELSE 0 END) as tahap_selesai")
            )
            ->groupBy('id_pengajuan')
            ->get()
            ->keyBy('id_pengajuan');

        foreach ($pengajuanAktif as $pengajuan) {
            $stats = $tahapanStats->get($pengajuan->id_pengajuan);
            $pengajuan->total_tahapan = (int) ($stats->total_tahapan ?? 0);
            $pengajuan->tahap_selesai = (int) ($stats->tahap_selesai ?? 0);

            $totalTahapan += $pengajuan->total_tahapan;
            $tahapSelesai += $pengajuan->tahap_selesai;
        }

        // Hitung pengumuman yang sudah dipublikasikan.
        $jumlahPengumuman = DB::table('pengumuman')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->count();

        // Ambil maksimal 3 pengumuman terbaru yang sudah dipublikasikan
        $pengumumans = DB::table('pengumuman as pm')
            ->leftJoin('beasiswa_offering as o', 'o.id', '=', 'pm.id_offering')
            ->leftJoin('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->whereNotNull('pm.published_at')
            ->where('pm.published_at', '<=', now())
            ->select(
                'pm.*',
                'o.tanggal_tutup',
                'p.nama as nama_program'
            )
            ->orderByDesc('pm.published_at')
            ->limit(3)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'user',
            'beasiswaDibuka',
            'pengajuanAktif',
            'tahapSelesai',
            'totalTahapan',
            'jumlahPengumuman',
            'pengumumans'
        ));
    }
}