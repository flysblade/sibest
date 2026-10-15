<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ============================================================
        // STATISTIK PENGAJUAN
        // ============================================================
        $totalPengajuan = DB::table('pengajuan')->count();

        $pengajuanByStatus = DB::table('pengajuan')
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        $statusList = ['draft', 'diajukan', 'diproses', 'ditolak', 'selesai'];
        $statusCount = [];
        foreach ($statusList as $s) {
            $statusCount[$s] = $pengajuanByStatus[$s] ?? 0;
        }

        // ============================================================
        // STATISTIK BEASISWA
        // ============================================================
        $offeringPublished = DB::table('beasiswa_offering')
            ->where('status', 'published')
            ->count();

        $offeringTotal = DB::table('beasiswa_offering')->count();

        // ============================================================
        // STATISTIK USER
        // ============================================================
        $totalMahasiswa = DB::table('users')
            ->where('role', 'mahasiswa')
            ->count();

        $totalPenyeleksi = DB::table('users')
            ->where('role', 'penyeleksi')
            ->count();

        // ============================================================
        // PENGAJUAN TERBARU (5 TERAKHIR)
        // ============================================================
        $pengajuanTerbaru = DB::table('pengajuan as pg')
            ->join('users as u', 'u.id', '=', 'pg.id_mahasiswa')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            ->select([
                'pg.id',
                'pg.status',
                'pg.hasil_akhir',
                'pg.submitted_at',
                'pg.created_at',
                'u.nama as nama_mahasiswa',
                'u.username as nim_mahasiswa',
                'p.nama as nama_program',
                'pr.nama_periode',
            ])
            ->orderByDesc('pg.created_at')
            ->limit(5)
            ->get();

        // ============================================================
        // YANG PERLU VERIFIKASI (status = diajukan)
        // ============================================================
        $perluVerifikasi = DB::table('pengajuan')
            ->where('status', 'diajukan')
            ->count();

        // ============================================================
        // OFFERING AKTIF (buat section "Offering Aktif" di dashboard)
        // ============================================================
        $offeringAktif = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->where('o.status', 'published')
            ->select([
                'o.id',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'p.nama as nama_program',
                'pr.nama_periode',
            ])
            ->orderByDesc('o.created_at')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                // Hitung jumlah pengajuan per offering
                $item->jumlah_pengajuan = DB::table('pengajuan')
                    ->where('id_offering', $item->id)
                    ->count();

                return $item;
            });

                return view('admin.dashboard', compact(
                    'user',
                    'totalPengajuan',
                    'statusCount',
                    'offeringPublished',
                    'offeringTotal',
                    'totalMahasiswa',
                    'totalPenyeleksi',
                    'perluVerifikasi',
                    'pengajuanTerbaru',
                    'offeringAktif'
                ));            
            }
        }