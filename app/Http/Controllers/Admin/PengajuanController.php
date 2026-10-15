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
        $pengajuans->getCollection()->transform(function ($item) {

            // Total dokumen wajib untuk offering ini
            $totalWajib = DB::table('dokumen_template')
                ->where('id_offering', $item->id_offering)
                ->where('required', 1)
                ->count();

            // Total wajib yang sudah diverifikasi (verified) untuk pengajuan ini
            $totalVerified = DB::table('dokumen_pengajuan as dp')
                ->join('dokumen_template as dt', 'dt.id', '=', 'dp.id_template')
                ->where('dp.id_pengajuan', $item->id)
                ->where('dt.required', 1)
                ->where('dp.verified_status', 'verified')
                ->count();

            // Total wajib yang sudah diupload (belum tentu verified)
            $totalUploaded = DB::table('dokumen_pengajuan as dp')
                ->join('dokumen_template as dt', 'dt.id', '=', 'dp.id_template')
                ->where('dp.id_pengajuan', $item->id)
                ->where('dt.required', 1)
                ->count();

            $item->dokumen_wajib      = $totalWajib;
            $item->dokumen_verified   = $totalVerified;
            $item->dokumen_uploaded   = $totalUploaded;

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
        // KALAU GAK KETEMU → 404 JSON
        // ============================================================
        if (!$pengajuan) {
            return response()->json([
                'error' => 'Pengajuan tidak ditemukan',
                'id'    => $id,
            ], 404);
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
            return response()->json([
                'error' => 'Catatan wajib diisi kalau dokumen ditolak.',
            ], 422);
        }

        // ============================================================
        // CARI DOKUMEN
        // ============================================================
        $dokumen = DB::table('dokumen_pengajuan')
            ->where('id', $id)
            ->first();

        if (!$dokumen) {
            return response()->json([
                'error' => 'Dokumen tidak ditemukan',
                'id'    => $id,
            ], 404);
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

        // ============================================================
        // AMBIL DATA BARU SETELAH UPDATE
        // ============================================================
        $dokumenBaru = DB::table('dokumen_pengajuan as dp')
            ->leftJoin('users as u', 'u.id', '=', 'dp.verified_by')
            ->where('dp.id', $id)
            ->select([
                'dp.id',
                'dp.id_pengajuan',
                'dp.id_template',
                'dp.original_filename',
                'dp.verified_status',
                'dp.verified_at',
                'dp.verified_note',
                'u.nama as nama_verifier',
            ])
            ->first();

        // ============================================================
        // RETURN JSON (debug)
        // ============================================================
        return response()->json([
            'message' => 'Status dokumen berhasil diubah.',
            'dokumen' => $dokumenBaru,
        ]);
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
            return response()->json([
                'error' => 'Catatan wajib diisi kalau pengajuan ditolak.',
            ], 422);
        }

        // ============================================================
        // CARI PENGAJUAN
        // ============================================================
        $pengajuan = DB::table('pengajuan')
            ->where('id', $id)
            ->first();

        if (!$pengajuan) {
            return response()->json([
                'error' => 'Pengajuan tidak ditemukan',
                'id'    => $id,
            ], 404);
        }

        // ============================================================
        // VALIDASI TRANSISI STATUS
        // ============================================================
        $statusSekarang = $pengajuan->status;

        // Status yang boleh diubah admin
        $statusBolehDiubahAdmin = ['diajukan', 'diproses'];

        if (!in_array($statusSekarang, $statusBolehDiubahAdmin)) {
            return response()->json([
                'error' => "Status pengajuan saat ini '{$statusSekarang}' tidak bisa diubah oleh admin.",
                'status_saat_ini' => $statusSekarang,
            ], 422);
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
                return response()->json([
                    'error' => 'Masih ada dokumen wajib yang belum diupload.',
                    'dokumen_belum_upload' => $belumUpload->values(),
                ], 422);
            }

            // Cek yang belum diverifikasi
            $belumVerified = $dokumenWajib->filter(function ($d) {
                return $d->verified_status !== 'verified';
            });

            if ($belumVerified->isNotEmpty()) {
                return response()->json([
                    'error' => 'Masih ada dokumen wajib yang belum diverifikasi.',
                    'dokumen_belum_verified' => $belumVerified->values(),
                ], 422);
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

        // ============================================================
        // AMBIL DATA BARU
        // ============================================================
        $pengajuanBaru = DB::table('pengajuan')
            ->where('id', $id)
            ->select('id', 'status', 'hasil_akhir', 'updated_at')
            ->first();

        // ============================================================
        // RETURN JSON
        // ============================================================
        return response()->json([
            'message' => 'Status pengajuan berhasil diubah.',
            'note' => $request->note,
            'pengajuan' => $pengajuanBaru,
        ]);
    }
}