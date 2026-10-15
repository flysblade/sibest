<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Query dasar: semua pengumuman yang sudah dipublikasikan
        $query = DB::table('pengumuman as pm')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pm.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->whereNotNull('pm.published_at')
            ->where('pm.published_at', '<=', now());

        // Filter by program (dari tab)
        if ($request->filled('program')) {
            $query->where('p.id', $request->program);
        }

        // Search by judul / isi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pm.judul', 'like', "%{$search}%")
                  ->orWhere('pm.isi', 'like', "%{$search}%");
            });
        }

        $pengumumans = $query
            ->select([
                'pm.id',
                'pm.judul',
                'pm.isi',
                'pm.published_at',
                'p.id as id_program',
                'p.nama as nama_program',
                'pr.nama_periode',
            ])
            ->orderByDesc('pm.published_at')
            ->get();

        // Ambil daftar program untuk tab filter
        $programs = DB::table('beasiswa_program')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        // Hitung yang "belum dibaca" — sederhananya: dalam 7 hari terakhir
        $pengumumans->transform(function ($item) {
            $item->is_baru = \Carbon\Carbon::parse($item->published_at)
                ->greaterThanOrEqualTo(now()->subDays(7));
            return $item;
        });

        return view('mahasiswa.pengumuman.index', compact(
            'user',
            'pengumumans',
            'programs'
        ));
    }
}