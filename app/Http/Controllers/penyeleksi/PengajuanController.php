<?php

namespace App\Http\Controllers\Penyeleksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ============================================================
        // AMBIL OFFERING YANG DI-ASSIGN KE PENYELEKSI INI
        // ============================================================
        $offeringIds = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->pluck('id_offering')
            ->toArray();

        // Kalau penyeleksi gak handle offering apapun
        if (empty($offeringIds)) {
            return response()->json([
                'message' => 'Anda belum di-assign ke offering manapun.',
                'meta' => [
                    'total' => 0,
                    'per_page' => 10,
                    'current_page' => 1,
                    'last_page' => 1,
                ],
                'offerings' => [],
                'data' => [],
            ]);
        }

        // ============================================================
        // QUERY DASAR
        // ============================================================
        $query = DB::table('pengajuan as pg')
            ->join('users as u', 'u.id', '=', 'pg.id_mahasiswa')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            // Penyeleksi cuma liat offering yang dia handle
            ->whereIn('pg.id_offering', $offeringIds)
            // Penyeleksi cuma nilai yang statusnya 'diproses'
            ->where('pg.status', 'diproses');

        // ============================================================
        // FILTER: OFFERING
        // ============================================================
        if ($request->filled('offering')) {
            // Pastiin offering yang difilter emang yang dia handle
            if (in_array($request->offering, $offeringIds)) {
                $query->where('pg.id_offering', $request->offering);
            }
        }

        // ============================================================
        // SEARCH: nama / NIM
        // ============================================================
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
                'u.nama as nama_mahasiswa',
                'u.username as nim_mahasiswa',
                'o.id as id_offering',
                'p.nama as nama_program',
                'p.jalur',
                'k.nama_kategori',
                'pr.nama_periode',
            ])
            ->orderByDesc('pg.submitted_at')
            ->paginate(10)
            ->withQueryString();

        // ============================================================
        // HITUNG PROGRESS TAHAPAN PER PENGAJUAN
        // ============================================================
        $pengajuans->getCollection()->transform(function ($item) {

            $totalTahapan = DB::table('pengajuan_tahapan')
                ->where('id_pengajuan', $item->id)
                ->count();

            $tahapSelesai = DB::table('pengajuan_tahapan')
                ->where('id_pengajuan', $item->id)
                ->whereIn('status', ['lolos', 'gagal'])
                ->count();

            $tahapLolos = DB::table('pengajuan_tahapan')
                ->where('id_pengajuan', $item->id)
                ->where('status', 'lolos')
                ->count();

            $tahapGagal = DB::table('pengajuan_tahapan')
                ->where('id_pengajuan', $item->id)
                ->where('status', 'gagal')
                ->count();

            $item->progress = [
                'total'         => $totalTahapan,
                'selesai'       => $tahapSelesai,
                'lolos'         => $tahapLolos,
                'gagal'         => $tahapGagal,
                'belum_dinilai' => $totalTahapan - $tahapSelesai,
            ];

            return $item;
        });

        // ============================================================
        // DAFTAR OFFERING YANG DIA HANDLE (buat dropdown)
        // ============================================================
        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->whereIn('o.id', $offeringIds)
            ->select(
                'o.id',
                'p.nama as nama_program',
                'pr.nama_periode'
            )
            ->orderBy('p.nama')
            ->get();

        // ============================================================
        // RETURN JSON (debug mode)
        // ============================================================
        return response()->json([
            'filter' => [
                'offering' => $request->input('offering'),
                'search'   => $request->input('search'),
            ],
            'meta' => [
                'total'         => $pengajuans->total(),
                'per_page'      => $pengajuans->perPage(),
                'current_page'  => $pengajuans->currentPage(),
                'last_page'     => $pengajuans->lastPage(),
                'from'          => $pengajuans->firstItem(),
                'to'            => $pengajuans->lastItem(),
            ],
            'offerings' => $offerings,
            'data' => $pengajuans->items(),
        ]);
    }

    public function show(int $id)
    {
        $user = Auth::user();

        // ============================================================
        // CEK AKSES: penyeleksi handle offering apa aja
        // ============================================================
        $offeringIds = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->pluck('id_offering')
            ->toArray();

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
                'pg.hasil_akhir',
                'pg.admin_note',
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
        // KALAU GAK KETEMU → 404
        // ============================================================
        if (!$pengajuan) {
            return response()->json([
                'error' => 'Pengajuan tidak ditemukan',
                'id'    => $id,
            ], 404);
        }

        // ============================================================
        // CEK AKSES: offering pengajuan ini di-handle penyeleksi?
        // ============================================================
        if (!in_array($pengajuan->id_offering, $offeringIds)) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses ke pengajuan ini.',
                'id_offering' => $pengajuan->id_offering,
            ], 403);
        }

        // ============================================================
        // PROFIL MAHASISWA
        // ============================================================
        $profile = DB::table('mahasiswa_profile')
            ->where('user_id', $pengajuan->id_mahasiswa)
            ->first();

        // ============================================================
        // DOKUMEN (wajib + opsional) + status verifikasi admin
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
                $dok->file_url = $dok->file_path
                    ? asset('storage/' . $dok->file_path)
                    : null;
                return $dok;
            });

        // ============================================================
        // RINGKASAN DOKUMEN
        // ============================================================
        $totalWajib       = $dokumens->where('required', 1)->count();
        $totalWajibUpload = $dokumens->where('required', 1)->whereNotNull('original_filename')->count();
        $totalWajibVerified = $dokumens->where('required', 1)->where('verified_status', 'verified')->count();

        // ============================================================
        // TAHAPAN SELEKSI (lengkap dengan pemutus + catatan)
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
        // CEK APAKAH PENYELEKSI BISA NILAI
        // ============================================================
        $canDecide = ($pengajuan->status === 'diproses');

        // ============================================================
        // RETURN JSON (debug)
        // ============================================================
        return response()->json([
            'can_decide' => $canDecide,
            'can_decide_reason' => $canDecide
                ? 'Pengajuan siap dinilai.'
                : "Pengajuan berstatus '{$pengajuan->status}', hanya bisa dinilai kalau statusnya 'diproses'.",
            'pengajuan' => $pengajuan,
            'profil_mahasiswa' => $profile ?? [
                'user_id' => $pengajuan->id_mahasiswa,
                'prodi' => null,
                'angkatan' => null,
                'no_hp' => null,
            ],
            'ringkasan_dokumen' => [
                'total_wajib'           => $totalWajib,
                'total_wajib_upload'    => $totalWajibUpload,
                'total_wajib_verified'  => $totalWajibVerified,
                'semua_wajib_verified'  => ($totalWajib === $totalWajibVerified),
            ],
            'dokumen' => $dokumens,
            'tahapan' => $tahapans,
        ]);
    }

    public function decideTahapan(Request $request, int $id)
    {
        $user = Auth::user();

        // ============================================================
        // VALIDASI INPUT
        // ============================================================
        $request->validate([
            'status' => ['required', 'in:lolos,gagal'],
            'note'   => ['nullable', 'string', 'max:1000'],
        ]);

        // ============================================================
        // CARI TAHAPAN + PENGAJUAN
        // ============================================================
        $tahapan = DB::table('pengajuan_tahapan as pt')
            ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
            ->join('pengajuan as pg', 'pg.id', '=', 'pt.id_pengajuan')
            ->where('pt.id', $id)
            ->select([
                'pt.id',
                'pt.id_pengajuan',
                'pt.id_tahapan',
                'pt.status as status_tahapan',
                't.nama_tahapan',
                't.urutan',
                'pg.id as id_pengajuan',
                'pg.status as status_pengajuan',
                'pg.id_offering',
                'pg.id_mahasiswa',
            ])
            ->first();

        if (!$tahapan) {
            return response()->json([
                'error' => 'Tahapan tidak ditemukan',
                'id'    => $id,
            ], 404);
        }

        // ============================================================
        // CEK AKSES: penyeleksi handle offering pengajuan ini?
        // ============================================================
        $punyaAkses = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->where('id_offering', $tahapan->id_offering)
            ->exists();

        if (!$punyaAkses) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses ke tahapan ini.',
                'id_offering' => $tahapan->id_offering,
            ], 403);
        }

        // ============================================================
        // CEK STATUS PENGAJUAN: harus 'diproses'
        // ============================================================
        if ($tahapan->status_pengajuan !== 'diproses') {
            return response()->json([
                'error' => "Pengajuan berstatus '{$tahapan->status_pengajuan}'. Penilaian cuma bisa dilakukan saat status 'diproses'.",
            ], 422);
        }

        // ============================================================
        // CEK URUTAN: tahapan sebelumnya harus udah dinilai
        // ============================================================
        $tahapanSebelumnyaBelumDinilai = DB::table('pengajuan_tahapan as pt')
            ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
            ->where('pt.id_pengajuan', $tahapan->id_pengajuan)
            ->where('t.urutan', '<', $tahapan->urutan)
            ->where('pt.status', 'menunggu')
            ->exists();

        if ($tahapanSebelumnyaBelumDinilai) {
            return response()->json([
                'error' => 'Tahapan sebelumnya belum dinilai. Nilai secara berurutan.',
            ], 422);
        }

        // ============================================================
        // UPDATE TAHAPAN
        // ============================================================
        DB::table('pengajuan_tahapan')
            ->where('id', $id)
            ->update([
                'status'     => $request->status,
                'catatan'    => $request->note,
                'decided_by' => $user->id,
                'decided_at' => now(),
                'updated_at' => now(),
            ]);

        // ============================================================
        // CEK: APAKAH INI TAHAPAN TERAKHIR?
        // ============================================================
        $totalTahapan = DB::table('pengajuan_tahapan')
            ->where('id_pengajuan', $tahapan->id_pengajuan)
            ->count();

        $urutanTerakhir = DB::table('pengajuan_tahapan as pt')
            ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
            ->where('pt.id_pengajuan', $tahapan->id_pengajuan)
            ->max('t.urutan');

        $updatePengajuan = null;

        // Kalau ini tahapan terakhir → cek semua tahapan, tentuin hasil akhir
        if ($tahapan->urutan == $urutanTerakhir) {

            $adaGagal = DB::table('pengajuan_tahapan as pt')
                ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
                ->where('pt.id_pengajuan', $tahapan->id_pengajuan)
                ->where('pt.status', 'gagal')
                ->exists();

            $hasilAkhir = $adaGagal ? 'tidak_lulus' : 'lulus';

            DB::table('pengajuan')
                ->where('id', $tahapan->id_pengajuan)
                ->update([
                    'status'      => 'selesai',
                    'hasil_akhir' => $hasilAkhir,
                    'finalized_at'=> now(),
                    'updated_at'  => now(),
                ]);

            $updatePengajuan = [
                'status'      => 'selesai',
                'hasil_akhir' => $hasilAkhir,
            ];
        }

        // ============================================================
        // AMBIL DATA BARU
        // ============================================================
        $tahapanBaru = DB::table('pengajuan_tahapan as pt')
            ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
            ->leftJoin('users as u', 'u.id', '=', 'pt.decided_by')
            ->where('pt.id', $id)
            ->select([
                'pt.id',
                'pt.status',
                'pt.catatan',
                'pt.decided_at',
                't.nama_tahapan',
                't.urutan',
                'u.nama as nama_pemutus',
            ])
            ->first();

        // ============================================================
        // RETURN JSON
        // ============================================================
        return response()->json([
            'message' => 'Keputusan tahapan berhasil disimpan.',
            'tahapan' => $tahapanBaru,
            'pengajuan_updated' => $updatePengajuan,
            'total_tahapan' => $totalTahapan,
            'urutan_terakhir' => $urutanTerakhir,
        ]);
    }
}