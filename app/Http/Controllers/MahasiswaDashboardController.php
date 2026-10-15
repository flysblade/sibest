<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MahasiswaDashboardController extends Controller
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

        foreach ($pengajuanAktif as $pengajuan) {
            $tahapanQuery = DB::table('pengajuan_tahapan')
                ->where('id_pengajuan', $pengajuan->id_pengajuan); // <-- Panggil id_pengajuan, bukan id

            $pengajuan->total_tahapan = (clone $tahapanQuery)->count();
            $pengajuan->tahap_selesai = (clone $tahapanQuery)
                ->where('status', 'lolos')
                ->count();

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