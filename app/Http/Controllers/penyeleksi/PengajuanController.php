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
            ->whereIn('pg.status', ['diajukan', 'diproses']);

        // ============================================================
        // FILTER: OFFERING
        // ============================================================
        if ($request->filled('offering')) {
            // Pastiin offering yang difilter emang yang dia handle
            $offeringId = (int) $request->input('offering');

            if (in_array($offeringId, $offeringIds, true)) {
                $query->where('pg.id_offering', $offeringId);
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

        // Filter: Tahapan (by nama_tahapan, bukan id)
        if ($request->filled('tahapan')) {
            $namaTahapan = $request->tahapan;
            $query->whereExists(function ($q) use ($namaTahapan) {
                $q->select(DB::raw(1))
                ->from('pengajuan_tahapan as pt')
                ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
                ->whereColumn('pt.id_pengajuan', 'pg.id')
                ->where('t.nama_tahapan', $namaTahapan)
                ->where('pt.status', 'menunggu');
            });
        }

        // ============================================================
        // FILTER: STATUS (per pengajuan)
        // ============================================================
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('pg.status', $request->status);
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
                'pg.id_offering',
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
        $pageItems = $pengajuans->getCollection();
        $stageStats = DB::table('pengajuan_tahapan')
            ->whereIn('id_pengajuan', $pageItems->pluck('id'))
            ->select(
                'id_pengajuan',
                DB::raw('COUNT(*) as total_tahapan'),
                DB::raw("SUM(CASE WHEN status IN ('lolos', 'gagal') THEN 1 ELSE 0 END) as tahap_selesai"),
                DB::raw("SUM(CASE WHEN status = 'lolos' THEN 1 ELSE 0 END) as tahap_lolos"),
                DB::raw("SUM(CASE WHEN status = 'gagal' THEN 1 ELSE 0 END) as tahap_gagal")
            )
            ->groupBy('id_pengajuan')
            ->get()
            ->keyBy('id_pengajuan');

        $activeStages = $pageItems->isEmpty()
            ? collect()
            : DB::table('pengajuan_tahapan as pt')
                ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
                ->whereIn('pt.id_pengajuan', $pageItems->pluck('id'))
                ->where('pt.status', 'menunggu')
                ->orderBy('t.urutan')
                ->select('pt.id_pengajuan', 't.id', 't.nama_tahapan', 't.urutan')
                ->get()
                ->groupBy('id_pengajuan');

        $totalWajibByOffering = DB::table('dokumen_template')
            ->whereIn('id_offering', $pageItems->pluck('id_offering'))
            ->where('required', 1)
            ->select('id_offering', DB::raw('COUNT(*) as total_wajib'))
            ->groupBy('id_offering')
            ->pluck('total_wajib', 'id_offering');

        $uploadedDocuments = DB::table('dokumen_pengajuan as dp')
            ->join('dokumen_template as dt', 'dt.id', '=', 'dp.id_template')
            ->whereIn('dp.id_pengajuan', $pageItems->pluck('id'))
            ->where('dt.required', 1)
            ->select('dp.id_pengajuan', DB::raw('COUNT(*) as total_uploaded'))
            ->groupBy('dp.id_pengajuan')
            ->pluck('total_uploaded', 'dp.id_pengajuan');

        $pageItems->transform(function ($item) use ($stageStats, $activeStages, $totalWajibByOffering, $uploadedDocuments) {
            $stats = $stageStats->get($item->id);
            $totalTahapan = (int) ($stats->total_tahapan ?? 0);
            $tahapSelesai = (int) ($stats->tahap_selesai ?? 0);
            $tahapLolos = (int) ($stats->tahap_lolos ?? 0);
            $tahapGagal = (int) ($stats->tahap_gagal ?? 0);

            $item->progress = [
                'total'         => $totalTahapan,
                'selesai'       => $tahapSelesai,
                'lolos'         => $tahapLolos,
                'gagal'         => $tahapGagal,
                'belum_dinilai' => $totalTahapan - $tahapSelesai,
            ];
        $item->total_tahapan = $totalTahapan;
        $item->tahapan_selesai = $tahapSelesai;

        $tahapAktif = ($activeStages->get($item->id) ?? collect())->first();
        $item->tahap_aktif_nama = $tahapAktif ? $tahapAktif->nama_tahapan : '-';
        $item->tahap_aktif_id = $tahapAktif ? $tahapAktif->id : null;
        $item->dokumen_wajib = (int) ($totalWajibByOffering[$item->id_offering] ?? 0);
        $item->dokumen_uploaded = (int) ($uploadedDocuments[$item->id] ?? 0);
        $item->sudah_dinilai_semua = ($item->total_tahapan > 0 && $item->tahapan_selesai >= $item->total_tahapan);
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



        // DAFTAR TAHAPAN (buat dropdown filter)
        $tahapanList = DB::table('tahapan')
            ->whereIn('id_offering', $offeringIds)
            ->select('nama_tahapan')
            ->distinct()
            ->orderBy('nama_tahapan')
            ->get();

        // RETURN VIEW
        return view('penyeleksi.pengajuan.index', compact(
            'user',
            'pengajuans',
            'offerings',
            'tahapanList'
        ));
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
            return redirect()
                ->route('penyeleksi.pengajuan.index')
                ->with('error', 'Pengajuan tidak ditemukan.');
        }

        // ============================================================
        // CEK AKSES: offering pengajuan ini di-handle penyeleksi?
        // ============================================================
        if (!in_array($pengajuan->id_offering, $offeringIds)) {
            return redirect()
                ->route('penyeleksi.pengajuan.index')
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini.');
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

        // Ringkasan dokumen wajib untuk dikirim ke view.
        $dokumenWajib = $dokumens->filter(function ($dokumen) {
            return (int) $dokumen->required === 1;
        });

        $totalWajib = $dokumenWajib->count();
        $totalWajibUpload = $dokumenWajib->filter(function ($dokumen) {
            return !empty($dokumen->id_dokumen_pengajuan);
        })->count();
        $totalWajibVerified = $dokumenWajib->filter(function ($dokumen) {
            return $dokumen->verified_status === 'verified';
        })->count();
        $semuaWajibLengkap = $totalWajib === $totalWajibVerified;


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

        return view('penyeleksi.pengajuan.show', compact(
            'canDecide',
            'pengajuan',
            'profile',
            'dokumens',
            'tahapans',
            'totalWajib',
            'totalWajibUpload',
            'totalWajibVerified',
            'semuaWajibLengkap'
        ));
    }

    public function decideTahapan(Request $request, int $id)
    {
        $user = Auth::user();

        // ============================================================
        // VALIDASI INPUT
        // ============================================================
        $request->validate([
            'status'       => ['required', 'in:lolos,gagal'],
            'note'         => ['nullable', 'string', 'max:1000'],
            'pewawancara'  => ['nullable', 'string', 'max:255'],
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
            return back()->with('error', 'Tahapan tidak ditemukan.');
        }

        // ============================================================
        // CEK AKSES: penyeleksi handle offering pengajuan ini?
        // ============================================================
        $punyaAkses = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->where('id_offering', $tahapan->id_offering)
            ->exists();

        if (!$punyaAkses) {
            return back()->with('error', 'Anda tidak memiliki akses ke tahapan ini.');
        }

        // ============================================================
        // CEK STATUS PENGAJUAN: harus 'diproses'
        // ============================================================
        if ($tahapan->status_pengajuan !== 'diproses') {
            return back()->with(
                'error',
                "Pengajuan berstatus '{$tahapan->status_pengajuan}'. Penilaian cuma bisa dilakukan saat status 'diproses'."
            );
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
            return back()->with('error', 'Tahapan sebelumnya belum dinilai. Nilai secara berurutan.');
        }

        // ============================================================
        // UPDATE TAHAPAN
        // ============================================================
        $catatanFinal = $request->note;
        if ($request->filled('pewawancara')) {
            $catatanFinal = "Pewawancara: {$request->pewawancara}. " . ($request->note ?? '');
        }

        DB::table('pengajuan_tahapan')
            ->where('id', $id)
            ->update([
                'status'     => $request->status,
                'catatan'    => trim($catatanFinal ?? ''),
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

        return redirect()
            ->route('penyeleksi.pengajuan.show', $tahapan->id_pengajuan)
            ->with('success', 'Keputusan tahapan berhasil disimpan.');
    }

    // VERIFIKASI DOKUMEN (copy logic dari admin)
    public function verifyDokumen(Request $request, int $id)
    {
        $user = Auth::user();

        $request->validate([
            'status' => ['required', 'in:verified,rejected,pending'],
            'note'   => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->status === 'rejected' && !$request->filled('note')) {
            return back()->with('error', 'Catatan wajib diisi kalau dokumen ditolak.');
        }

        // Cari dokumen
        $dokumen = DB::table('dokumen_pengajuan')->where('id', $id)->first();

        if (!$dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Cek akses: penyeleksi harus handle offering pengajuan ini
        $pengajuan = DB::table('pengajuan')->where('id', $dokumen->id_pengajuan)->first();
        if (!$pengajuan) {
            return back()->with('error', 'Pengajuan tidak ditemukan.');
        }

        $punyaAkses = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->where('id_offering', $pengajuan->id_offering)
            ->exists();

        if (!$punyaAkses) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Update
        $updateData = [
            'verified_status' => $request->status,
            'verified_by'     => $user->id,
            'verified_at'     => now(),
            'verified_note'   => $request->note,
            'updated_at'      => now(),
        ];

        if ($request->status === 'pending') {
            $updateData['verified_by']   = null;
            $updateData['verified_at']   = null;
            $updateData['verified_note'] = null;
        }

        DB::table('dokumen_pengajuan')->where('id', $id)->update($updateData);

        return redirect()
            ->route('penyeleksi.pengajuan.show', $dokumen->id_pengajuan)
            ->with('success', 'Status dokumen berhasil diubah.');
    }

    // UPDATE STATUS PENGAJUAN (copy logic dari admin)
    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();

        $request->validate([
            'status' => ['required', 'in:diproses,ditolak'],
            'note'   => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->status === 'ditolak' && !$request->filled('note')) {
            return back()->with('error', 'Catatan wajib diisi kalau pengajuan ditolak.');
        }

        $pengajuan = DB::table('pengajuan')->where('id', $id)->first();

        if (!$pengajuan) {
            return back()->with('error', 'Pengajuan tidak ditemukan.');
        }

        // Cek akses
        $punyaAkses = DB::table('offering_penyeleksi')
            ->where('id_user', $user->id)
            ->where('id_offering', $pengajuan->id_offering)
            ->exists();

        if (!$punyaAkses) {
            abort(403);
        }

        $statusSekarang = $pengajuan->status;
        $statusBoleh = ['diajukan', 'diproses'];

        if (!in_array($statusSekarang, $statusBoleh)) {
            return back()->with('error', "Status '{$statusSekarang}' tidak bisa diubah.");
        }

        // Kalau mau diproses, cek dokumen wajib verified semua
        if ($request->status === 'diproses') {
            $dokumenWajib = DB::table('dokumen_template as dt')
                ->leftJoin('dokumen_pengajuan as dp', function ($join) use ($id) {
                    $join->on('dp.id_template', '=', 'dt.id')
                        ->where('dp.id_pengajuan', '=', $id);
                })
                ->where('dt.id_offering', $pengajuan->id_offering)
                ->where('dt.required', 1)
                ->select('dp.verified_status', 'dp.original_filename')
                ->get();

            $belumVerified = $dokumenWajib->filter(function ($d) {
                return is_null($d->original_filename) || $d->verified_status !== 'verified';
            });

            if ($belumVerified->isNotEmpty()) {
                return back()->with('error', 'Masih ada dokumen wajib yang belum diverifikasi.');
            }
        }

        $updateData = [
            'status'     => $request->status,
            'updated_at' => now(),
        ];

        if ($request->filled('note')) {
            $updateData['admin_note'] = $request->note;
        }
        if ($request->status !== 'ditolak') {
            $updateData['admin_note'] = null;
        }

        DB::table('pengajuan')->where('id', $id)->update($updateData);

        return back()->with('success', 'Status pengajuan berhasil diubah.');
    }
}