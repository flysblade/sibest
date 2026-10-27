<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        // ============================================================
        // QUERY DASAR
        // ============================================================
        $query = DB::table('pengajuan as pg')
            ->join('users as u', 'u.id', '=', 'pg.id_mahasiswa')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode');

        // ============================================================
        // FILTER: STATUS
        // ============================================================
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('pg.status', $request->status);
        }

        // FILTER: OFFERING
        if ($request->filled('offering')) {
            $query->where('pg.id_offering', $request->offering);
        }
        // SEARCH: nama mahasiswa / NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('u.nama', 'like', "%{$search}%")
                ->orWhere('u.username', 'like', "%{$search}%");
            });
        }

        // ============================================================
        // SELECT + PAGINATE
        // ============================================================
        $pengajuans = $query
            ->select([
                'pg.id',
                'pg.status',
                'pg.hasil_akhir',
                'pg.submitted_at',
                'pg.created_at',
                'pg.finalized_at',
                'u.nama as nama_mahasiswa',
                'u.username as nim_mahasiswa',
                'u.email as email_mahasiswa',
                'o.id as id_offering',  
                'p.nama as nama_program',
                'p.jalur',
                'k.nama_kategori',
                'pr.nama_periode',
            ])
            ->orderByDesc('pg.created_at')
            ->paginate(10)
            ->withQueryString(); // biar filter tetep ada pas pindah page

        // ============================================================
        // DAFTAR OFFERING (buat dropdown filter)
        // ============================================================
        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->select(
                'o.id',
                'p.nama as nama_program',
                'pr.nama_periode',
                'o.status'
            )
            ->orderBy('p.nama')
            ->get();

        // DOKUMEN PROGRESS PER PENGAJUAN
        $pageItems = $pengajuans->getCollection();
        $totalWajibByOffering = DB::table('dokumen_template')
            ->whereIn('id_offering', $pageItems->pluck('id_offering'))
            ->where('required', 1)
            ->select('id_offering', DB::raw('COUNT(*) as total_wajib'))
            ->groupBy('id_offering')
            ->pluck('total_wajib', 'id_offering');

        $documentStats = DB::table('dokumen_pengajuan as dp')
            ->join('dokumen_template as dt', 'dt.id', '=', 'dp.id_template')
            ->whereIn('dp.id_pengajuan', $pageItems->pluck('id'))
            ->where('dt.required', 1)
            ->select(
                'dp.id_pengajuan',
                DB::raw('COUNT(*) as total_uploaded'),
                DB::raw("SUM(CASE WHEN dp.verified_status = 'verified' THEN 1 ELSE 0 END) as total_verified")
            )
            ->groupBy('dp.id_pengajuan')
            ->get()
            ->keyBy('id_pengajuan');

        $pageItems->transform(function ($item) use ($totalWajibByOffering, $documentStats) {
            $stats = $documentStats->get($item->id);

            $item->dokumen_wajib = (int) ($totalWajibByOffering[$item->id_offering] ?? 0);
            $item->dokumen_verified = (int) ($stats->total_verified ?? 0);
            $item->dokumen_uploaded = (int) ($stats->total_uploaded ?? 0);

            return $item;
        });


        // RETURN VIEW
        // ============================================================
        return view('admin.pengajuan.index', compact(
            'pengajuans',
            'offerings'
        ));
    }

    public function show(int $id)
    {
        // ============================================================
        // AMBIL PENGAJUAN
        // ============================================================
        $pengajuan = DB::table('pengajuan as pg')
            ->join('users as u', 'u.id', '=', 'pg.id_mahasiswa')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            ->where('pg.id', $id)
            ->select([
                'pg.id',
                'pg.status',
                'pg.admin_note',
                'pg.hasil_akhir',
                'pg.submitted_at',
                'pg.finalized_at',
                'pg.created_at',
                'pg.updated_at',
                'u.id as id_mahasiswa',
                'u.nama as nama_mahasiswa',
                'u.username as nim_mahasiswa',
                'u.email as email_mahasiswa',
                'o.id as id_offering',
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

        // ============================================================
        // KALAU GAK KETEMU → KEMBALI KE DAFTAR
        // ============================================================
        if (!$pengajuan) {
            return redirect()
                ->route('admin.pengajuan.index')
                ->with('error', 'Pengajuan tidak ditemukan.');
        }

        // ============================================================
        // PROFIL MAHASISWA (dari mahasiswa_profile)
        // ============================================================
        $profile = DB::table('mahasiswa_profile')
            ->where('user_id', $pengajuan->id_mahasiswa)
            ->first();

        // ============================================================
        // DOKUMEN — gabung template + file yang diupload
        // ============================================================
        $dokumens = DB::table('dokumen_template as dt')
            ->leftJoin('dokumen_pengajuan as dp', function ($join) use ($id) {
                $join->on('dp.id_template', '=', 'dt.id')
                    ->where('dp.id_pengajuan', '=', $id);
            })
            ->leftJoin('users as verifier', 'verifier.id', '=', 'dp.verified_by')
            ->where('dt.id_offering', $pengajuan->id_offering)
            ->orderByDesc('dt.required')
            ->orderBy('dt.id')
            ->select([
                'dt.id as id_template',
                'dt.nama_dokumen',
                'dt.required',
                'dt.keterangan',
                'dp.id as id_dokumen_pengajuan',
                'dp.original_filename',
                'dp.file_path',
                'dp.mime_type',
                'dp.file_size',
                'dp.uploaded_at',
                'dp.verified_status',
                'dp.verified_at',
                'dp.verified_note',
                'verifier.nama as nama_verifier',
            ])
            ->get()
            ->map(function ($dok) {
                // Tambahin URL full ke file (kalau ada)
                $dok->file_url = $dok->file_path
                    ? asset('storage/' . $dok->file_path)
                    : null;

                return $dok;
            });

        // ============================================================
        // TAHAPAN SELEKSI
        // ============================================================
        $tahapans = DB::table('pengajuan_tahapan as pt')
            ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
            ->leftJoin('users as u', 'u.id', '=', 'pt.decided_by')
            ->where('pt.id_pengajuan', $id)
            ->select([
                'pt.id',
                'pt.status',
                'pt.catatan',
                'pt.decided_at',
                'pt.decided_by',
                't.id as id_tahapan',
                't.nama_tahapan',
                't.urutan',
                't.mulai_at',
                't.selesai_at',
                't.lokasi',
                't.keterangan',
                'u.nama as nama_pemutus',
            ])
            ->orderBy('t.urutan')
            ->get();

        // ============================================================
        // RINGKASAN KELENGKAPAN DOKUMEN
        // ============================================================
        $totalWajib        = $dokumens->where('required', 1)->count();
        $totalWajibUpload  = $dokumens->where('required', 1)->whereNotNull('original_filename')->count();
        $totalWajibVerified = $dokumens->where('required', 1)->where('verified_status', 'verified')->count();
        $totalSemuaUpload  = $dokumens->whereNotNull('original_filename')->count();
        $totalSemua        = $dokumens->count();

        $semuaWajibLengkap = ($totalWajib === $totalWajibUpload);

        return view('admin.pengajuan.show', compact(
            'pengajuan',
            'profile',
            'dokumens',
            'tahapans',
            'totalWajib',
            'totalWajibUpload',
            'totalWajibVerified',
            'totalSemua',
            'totalSemuaUpload',
            'semuaWajibLengkap'
        ));
    }

    public function verifyDokumen(Request $request, int $id)
    {
        // ============================================================
        // VALIDASI INPUT
        // ============================================================
        $request->validate([
            'status' => ['required', 'in:verified,rejected,pending'],
            'note'   => ['nullable', 'string', 'max:1000'],
        ]);

        // Kalau status = rejected, note wajib
        if ($request->status === 'rejected' && !$request->filled('note')) {
            return back()->with('error', 'Catatan wajib diisi kalau dokumen ditolak.');
        }

        // ============================================================
        // CARI DOKUMEN
        // ============================================================
        $dokumen = DB::table('dokumen_pengajuan')
            ->where('id', $id)
            ->first();

        if (!$dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // ============================================================
        // UPDATE STATUS
        // ============================================================
        $updateData = [
            'verified_status' => $request->status,
            'verified_by'     => Auth::id(),
            'verified_at'     => now(),
            'verified_note'   => $request->note,
            'updated_at'      => now(),
        ];

        // Kalau status balik ke pending, reset verifier info
        if ($request->status === 'pending') {
            $updateData['verified_by']   = null;
            $updateData['verified_at']   = null;
            $updateData['verified_note'] = null;
        }

        DB::table('dokumen_pengajuan')
            ->where('id', $id)
            ->update($updateData);

        return redirect()
            ->route('admin.pengajuan.show', $dokumen->id_pengajuan)
            ->with('success', 'Status dokumen berhasil diubah.');
    }

    public function updateStatus(Request $request, int $id)
    {
        // ============================================================
        // VALIDASI INPUT
        // ============================================================
        $request->validate([
            'status' => ['required', 'in:diproses,ditolak'],
            'note'   => ['nullable', 'string', 'max:1000'],
        ]);

        // Kalau status = ditolak, note wajib
        if ($request->status === 'ditolak' && !$request->filled('note')) {
            return back()->with('error', 'Catatan wajib diisi kalau pengajuan ditolak.');
        }

        // ============================================================
        // CARI PENGAJUAN
        // ============================================================
        $pengajuan = DB::table('pengajuan')
            ->where('id', $id)
            ->first();

        if (!$pengajuan) {
            return redirect()
                ->route('admin.pengajuan.index')
                ->with('error', 'Pengajuan tidak ditemukan.');
        }

        // ============================================================
        // VALIDASI TRANSISI STATUS
        // ============================================================
        $statusSekarang = $pengajuan->status;

        // Status yang boleh diubah admin
        $statusBolehDiubahAdmin = ['diajukan', 'diproses'];

        if (!in_array($statusSekarang, $statusBolehDiubahAdmin)) {
            return back()->with(
                'error',
                "Status pengajuan saat ini '{$statusSekarang}' tidak bisa diubah oleh admin."
            );
        }

        // ============================================================
        // KALAU MAU DIPROSES → CEK DOKUMEN WAJIB HARUS VERIFIED SEMUA
        // ============================================================
        if ($request->status === 'diproses') {

            $dokumenWajib = DB::table('dokumen_template as dt')
                ->leftJoin('dokumen_pengajuan as dp', function ($join) use ($id) {
                    $join->on('dp.id_template', '=', 'dt.id')
                        ->where('dp.id_pengajuan', '=', $id);
                })
                ->where('dt.id_offering', $pengajuan->id_offering)
                ->where('dt.required', 1)
                ->select(
                    'dt.id as id_template',
                    'dt.nama_dokumen',
                    'dp.id as id_dokumen_pengajuan',
                    'dp.verified_status',
                    'dp.original_filename'
                )
                ->get();

            // Cek yang belum diupload
            $belumUpload = $dokumenWajib->filter(function ($d) {
                return is_null($d->original_filename);
            });

            if ($belumUpload->isNotEmpty()) {
                return back()->with('error', 'Masih ada dokumen wajib yang belum diupload.');
            }

            // Cek yang belum diverifikasi
            $belumVerified = $dokumenWajib->filter(function ($d) {
                return $d->verified_status !== 'verified';
            });

            if ($belumVerified->isNotEmpty()) {
                return back()->with('error', 'Masih ada dokumen wajib yang belum diverifikasi.');
            }
        }

        // ============================================================
        // UPDATE STATUS
        // ============================================================
        $updateData = [
            'status'     => $request->status,
            'updated_at' => now(),
        ];

        // ============================================================
        // UPDATE STATUS + CATATAN
        // ============================================================
        $updateData = [
            'status'     => $request->status,
            'updated_at' => now(),
        ];

// Simpan catatan admin kalau ada
if ($request->filled('note')) {
    $updateData['admin_note'] = $request->note;
}

// Kalau status bukan ditolak, reset admin_note (biar gak nyangkut catatan lama)
if ($request->status !== 'ditolak') {
    $updateData['admin_note'] = null;
}

DB::table('pengajuan')
    ->where('id', $id)
    ->update($updateData);

        DB::table('pengajuan')
            ->where('id', $id)
            ->update($updateData);

        return redirect()
            ->route('admin.pengajuan.show', $pengajuan->id)
            ->with('success', 'Status pengajuan berhasil diubah.');
    }
}