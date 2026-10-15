<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $pengajuans = DB::table('pengajuan as pg')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            ->where('pg.id_mahasiswa', $user->id)
            ->whereNotNull('pg.active_flag')
            ->select([
                'pg.id',
                'pg.status',
                'pg.hasil_akhir',
                'pg.submitted_at',
                'pg.created_at',
                'o.id as id_offering',
                'o.tanggal_tutup',
                'p.nama as nama_program',
                'p.jalur',
                'k.nama_kategori',
                'pr.nama_periode',
            ])
            ->orderByDesc('pg.created_at')
            ->get();

        return view('mahasiswa.pengajuan.index', compact(
            'user',
            'pengajuans'
        ));
    }

    

public function tracking(): View
{
    $user = Auth::user();

    // Ambil semua pengajuan milik mahasiswa.
    $pengajuans = DB::table('pengajuan as pg')
        ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
        ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
        ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
        ->where('pg.id_mahasiswa', $user->id)
        ->select(
            'pg.id',
            'pg.status',
            'pg.hasil_akhir',
            'pg.submitted_at',
            'pg.created_at',
            'pg.finalized_at',
            'o.id as id_offering',
            'p.nama as nama_program',
            'p.jalur',
            'pr.nama_periode'
        )
        ->orderByRaw('COALESCE(pg.submitted_at, pg.created_at) DESC')
        ->get();

    // Ambil tahapan seleksi setiap pengajuan.
    foreach ($pengajuans as $pengajuan) {
        $pengajuan->tahapan = DB::table('pengajuan_tahapan as pt')
            ->join('tahapan as t', 't.id', '=', 'pt.id_tahapan')
            ->leftJoin('users as u', 'u.id', '=', 'pt.decided_by')
            ->where('pt.id_pengajuan', $pengajuan->id)
            ->select(
                'pt.id',
                'pt.status',
                'pt.catatan',
                'pt.decided_at',
                'pt.decided_by',
                't.nama_tahapan',
                't.urutan',
                't.lokasi',
                't.mulai_at',
                't.selesai_at',
                'u.nama as nama_pemutus'
            )
            ->orderBy('t.urutan')
            ->get();
    }

    // Pengajuan aktif: belum memiliki hasil akhir.
    $pengajuanAktif = $pengajuans->first(
        fn ($item) => $item->hasil_akhir === null
            && $item->status !== 'selesai'
    );

    return view('mahasiswa.tracking.index', compact(
        'user',
        'pengajuans',
        'pengajuanAktif'
    ));
}

    
public function riwayat(): View
    {
        $user = Auth::user();

        $riwayats = DB::table('pengajuan as pg')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            ->where('pg.id_mahasiswa', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('pg.hasil_akhir')
                ->orWhere('pg.status', 'selesai');
            })
            ->select([
                'pg.id',
                'pg.status',
                'pg.hasil_akhir',
                'pg.submitted_at',
                'pg.finalized_at',
                'o.id as id_offering',
                'p.nama as nama_program',
                'p.jalur',
                'k.nama_kategori',
                'pr.nama_periode',
            ])
            ->orderByDesc('pg.submitted_at')
            ->get();

        return view('mahasiswa.riwayat.index', compact('user', 'riwayats'));
    }

   public function review(int $id): View
    {
        $user = Auth::user();

        // Ambil pengajuan milik mahasiswa yang sedang login.
        $pengajuan = DB::table('pengajuan as pg')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            ->where('pg.id', $id)
            ->where('pg.id_mahasiswa', $user->id)
            ->whereNotNull('pg.active_flag')
            ->select([
                'pg.id',
                'pg.status',
                'pg.submitted_at',
                'pg.id_offering',
                'pg.id_periode',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'p.nama as nama_program',
                'p.deskripsi',
                'p.jalur',
                'pr.nama_periode',
            ])
            ->first();

        abort_if(!$pengajuan, 404);

        // Review hanya boleh dilakukan untuk pengajuan draft.
        abort_if(
            $pengajuan->status !== 'draft',
            403,
            'Pengajuan ini sudah tidak berada dalam tahap draft.'
        );

        // Ambil profil mahasiswa.
        $profile = DB::table('mahasiswa_profile')
            ->where('user_id', $user->id)
            ->first();

        // Ambil semua template dokumen + file yang sudah diupload.
        $dokumen = DB::table('dokumen_template as dt')
            ->leftJoin('dokumen_pengajuan as dp', function ($join) use ($id) {
                $join->on('dp.id_template', '=', 'dt.id')
                    ->where('dp.id_pengajuan', $id);
            })
            ->where('dt.id_offering', $pengajuan->id_offering)
            ->orderByDesc('dt.required')
            ->orderBy('dt.id')
            ->select([
                'dt.id',
                'dt.nama_dokumen',
                'dt.required',
                'dt.keterangan',
                'dp.id as id_dokumen_pengajuan',
                'dp.original_filename',
                'dp.file_path',
                'dp.verified_status',
            ])
            ->get();

        // Hitung kelengkapan dokumen wajib.
        $totalWajib = $dokumen
            ->where('required', 1)
            ->count();

        $totalWajibUpload = $dokumen
            ->where('required', 1)
            ->whereNotNull('original_filename')
            ->count();

        $semuaDokumenWajibLengkap =
            $totalWajib === $totalWajibUpload;

        return view('mahasiswa.pengajuan.review', compact(
            'user',
            'profile',
            'pengajuan',
            'dokumen',
            'semuaDokumenWajibLengkap'
        ));
    }
    


    public function upload(int $id): View
    {
        $user = Auth::user();

        $pengajuan = DB::table('pengajuan as pg')
            ->join('beasiswa_offering as o', 'o.id', '=', 'pg.id_offering')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'pg.id_periode')
            ->where('pg.id', $id)
            ->where('pg.id_mahasiswa', $user->id)
            ->whereNotNull('pg.active_flag')
            ->select([
                'pg.id',
                'pg.status',
                'o.id as id_offering',
                'p.nama as nama_program',
                'pr.nama_periode',
            ])
            ->first();

        abort_if(!$pengajuan, 404);

        $dokumen = DB::table('dokumen_template as dt')
            ->leftJoin('dokumen_pengajuan as dp', function ($join) use ($id) {
                $join->on('dp.id_template', '=', 'dt.id')
                    ->where('dp.id_pengajuan', $id);
            })
            ->where('dt.id_offering', $pengajuan->id_offering)
            ->orderByDesc('dt.required')
            ->orderBy('dt.id')
            ->select([
                'dt.id',
                'dt.nama_dokumen',
                'dt.required',
                'dt.keterangan',
                'dp.id as id_dokumen_pengajuan',
                'dp.original_filename',
                'dp.file_path',
                'dp.verified_status',
            ])
            ->get();

        $totalWajib = $dokumen->where('required', 1)->count();

        $totalWajibUpload = $dokumen
            ->where('required', 1)
            ->whereNotNull('original_filename')
            ->count();

        $semuaDokumenWajibLengkap =
            $totalWajib === $totalWajibUpload;

        return view('mahasiswa.pengajuan.upload', compact(
            'user',
            'pengajuan',
            'dokumen',
            'semuaDokumenWajibLengkap'
        ));
    }

    public function uploadStore(Request $request, int $id)
    {
        $user = Auth::user();

        // Pastikan pengajuan memang milik mahasiswa yang sedang login.
        $pengajuan = DB::table('pengajuan')
            ->where('id', $id)
            ->where('id_mahasiswa', $user->id)
            ->whereNotNull('active_flag')
            ->first();

        abort_if(!$pengajuan, 404);

        // Pastikan template dokumen memang milik offering pengajuan ini.
        $template = DB::table('dokumen_template')
            ->where('id', $request->id_template)
            ->where('id_offering', $pengajuan->id_offering)
            ->first();

        abort_if(!$template, 404);

        // Cek apakah dokumen ini merupakan foto.
        // Contoh: Foto 3x4.
        $isFoto = str_contains(
            strtolower($template->nama_dokumen),
            'foto'
        );

        // Validasi dasar.
        $rules = [
            'id_template' => ['required', 'integer'],
            'file' => [
                'required',
                'file',
                'max:2048',
            ],
        ];

        // Foto hanya boleh JPG/JPEG/PNG.
        // Dokumen lainnya hanya boleh PDF.
        $rules['file'][] = $isFoto
            ? 'mimes:jpg,jpeg,png'
            : 'mimes:pdf';

        $request->validate($rules);

        $file = $request->file('file');

        // Cari apakah sebelumnya sudah ada file untuk dokumen ini.
        $dokumenLama = DB::table('dokumen_pengajuan')
            ->where('id_pengajuan', $pengajuan->id)
            ->where('id_template', $template->id)
            ->first();

        // Kalau ada file lama, hapus dari storage.
        if ($dokumenLama && $dokumenLama->file_path) {
            Storage::disk('public')->delete($dokumenLama->file_path);
        }

        // Simpan file baru.
        $path = $file->store('dokumen-pengajuan', 'public');

        // Kalau record dokumen sudah ada, update.
        if ($dokumenLama) {

            DB::table('dokumen_pengajuan')
                ->where('id', $dokumenLama->id)
                ->update([
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'verified_status' => 'pending',
                    'updated_at' => now(),
                ]);

        } else {

            // Kalau belum ada, buat record baru.
            DB::table('dokumen_pengajuan')
                ->insert([
                    'id_pengajuan' => $pengajuan->id,
                    'id_template' => $template->id,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'verified_status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return redirect()
            ->route('mahasiswa.pengajuan.upload', $pengajuan->id)
            ->with('success', 'Dokumen berhasil diupload.');
    }

    

    public function finalize(Request $request, int $id)
    {
        $user = Auth::user();

        $pengajuan = DB::table('pengajuan')
            ->where('id', $id)
            ->where('id_mahasiswa', $user->id)
            ->whereNotNull('active_flag')
            ->first();

        abort_if(!$pengajuan, 404);

        if ($pengajuan->status !== 'draft') {
            return redirect()
                ->route('mahasiswa.pengajuan.index')
                ->with('error', 'Pengajuan ini sudah pernah diajukan.');
        }

        $request->validate([
            'pernyataan' => ['required', 'accepted'],
        ], [
            'pernyataan.required' => 'Kamu harus menyetujui pernyataan terlebih dahulu.',
            'pernyataan.accepted' => 'Kamu harus menyetujui pernyataan terlebih dahulu.',
        ]);

        $dokumen = DB::table('dokumen_template as dt')
            ->leftJoin('dokumen_pengajuan as dp', function ($join) use ($id) {
                $join->on('dp.id_template', '=', 'dt.id')
                    ->where('dp.id_pengajuan', $id);
            })
            ->where('dt.id_offering', $pengajuan->id_offering)
            ->select([
                'dt.required',
                'dp.original_filename',
            ])
            ->get();

        $totalWajib = $dokumen->where('required', 1)->count();

        $totalWajibUpload = $dokumen
            ->where('required', 1)
            ->whereNotNull('original_filename')
            ->count();

        if ($totalWajib !== $totalWajibUpload) {
            return redirect()
                ->route('mahasiswa.pengajuan.review', $id)
                ->with('error', 'Masih ada dokumen wajib yang belum diupload.');
        }

        DB::transaction(function () use ($id) {
            DB::table('pengajuan')
                ->where('id', $id)
                ->where('status', 'draft')
                ->update([
                    'status' => 'diajukan',
                    'submitted_at' => now(),
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('mahasiswa.pengajuan.index')
            ->with('success', 'Pengajuan berhasil difinalisasi dan diajukan.');
    }

    public function create(int $id): View
    {
        $user = Auth::user();

        // Form ini dipakai untuk beasiswa internal
        // yang membutuhkan pengajuan + upload dokumen melalui SIBEST.
        $beasiswa = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('kategori_beasiswa as k', 'k.id', '=', 'p.id_kategori')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->where('o.id', $id)
            ->where('o.status', 'published')
            ->where('p.jalur', 'internal')
            ->select([
                'o.id',
                'o.id_periode',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'o.link_portal',
                'p.nama as nama_program',
                'p.deskripsi',
                'p.syarat',
                'p.jalur',
                'k.nama_kategori',
                'pr.nama_periode',
            ])
            ->first();

        abort_if(!$beasiswa, 404);

        // Cek apakah pendaftaran sedang dibuka.
        $today = now()->startOfDay();

        if (
            ($beasiswa->tanggal_buka && $today->lt($beasiswa->tanggal_buka)) ||
            ($beasiswa->tanggal_tutup && $today->gt($beasiswa->tanggal_tutup))
        ) {
            abort(403, 'Pendaftaran beasiswa tidak sedang dibuka.');
        }

        // Cari pengajuan aktif mahasiswa pada periode ini.
        $pengajuanAktif = DB::table('pengajuan')
            ->where('id_mahasiswa', $user->id)
            ->where('id_periode', $beasiswa->id_periode)
            ->whereNotNull('active_flag')
            ->first();

        // Ambil profil mahasiswa.
        $profile = DB::table('mahasiswa_profile')
            ->where('user_id', $user->id)
            ->first();

        // Ambil dokumen yang memang harus diunggah di SIBEST.
        $dokumen = DB::table('dokumen_template')
            ->where('id_offering', $beasiswa->id)
            ->orderByDesc('required')
            ->orderBy('id')
            ->get();

        return view('mahasiswa.pengajuan.create', compact(
            'user',
            'profile',
            'beasiswa',
            'dokumen',
            'pengajuanAktif'
        ));
    }

    public function store(int $id)
    {
        $user = Auth::user();

        // Method ini untuk beasiswa INTERNAL.
        // KIP-K punya method khusus storeKipK() di bawah.
        $beasiswa = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->where('o.id', $id)
            ->where('o.status', 'published')
            ->where('p.jalur', 'internal')
            ->select([
                'o.id',
                'o.id_periode',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'p.jalur',
            ])
            ->first();

        abort_if(!$beasiswa, 404);

        // Cek apakah pendaftaran sedang dibuka.
        $today = now()->startOfDay();

        if (
            ($beasiswa->tanggal_buka && $today->lt($beasiswa->tanggal_buka)) ||
            ($beasiswa->tanggal_tutup && $today->gt($beasiswa->tanggal_tutup))
        ) {
            abort(403, 'Pendaftaran beasiswa tidak sedang dibuka.');
        }

        // Cari pengajuan aktif mahasiswa pada periode ini.
        $pengajuanAktif = DB::table('pengajuan')
            ->where('id_mahasiswa', $user->id)
            ->where('id_periode', $beasiswa->id_periode)
            ->whereNotNull('active_flag')
            ->first();

        if ($pengajuanAktif) {

            // Kalau sebelumnya tersimpan sebagai draft,
            // lanjutkan ke upload dokumen.
            if ($pengajuanAktif->status === 'draft') {
                return redirect()
                    ->route('mahasiswa.pengajuan.upload', $pengajuanAktif->id);
            }

            return redirect()
                ->route('mahasiswa.pengajuan.create', $id)
                ->with('error', 'Kamu sudah memiliki pengajuan aktif pada periode ini.');
        }

        $pengajuanId = DB::transaction(function () use ($user, $beasiswa) {

            $pengajuanId = DB::table('pengajuan')->insertGetId([
                'id_offering' => $beasiswa->id,
                'id_mahasiswa' => $user->id,
                'id_periode' => $beasiswa->id_periode,
                'status' => 'draft',
            ]);

            $tahapan = DB::table('tahapan')
                ->where('id_offering', $beasiswa->id)
                ->orderBy('urutan')
                ->get(['id']);

            $dataTahapan = $tahapan->map(function ($tahap) use ($pengajuanId) {
                return [
                    'id_pengajuan' => $pengajuanId,
                    'id_tahapan' => $tahap->id,
                    'status' => 'menunggu',
                ];
            })->all();

            if (!empty($dataTahapan)) {
                DB::table('pengajuan_tahapan')->insert($dataTahapan);
            }

            return $pengajuanId;
        });

        return redirect()
            ->route('mahasiswa.pengajuan.upload', $pengajuanId)
            ->with('success', 'Data pengajuan berhasil disimpan. Silakan upload dokumen.');
    }

    public function storeKipK(int $id)
    {
        $user = Auth::user();

        // KIP-K menggunakan jalur hybrid:
        // daftar di portal KIP-K, lalu pengajuan ke UNUJA lewat SIBEST.
        $beasiswa = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->where('o.id', $id)
            ->where('o.status', 'published')
            ->where('p.jalur', 'hybrid')
            ->select([
                'o.id',
                'o.id_periode',
                'o.tanggal_buka',
                'o.tanggal_tutup',
                'p.nama as nama_program',
            ])
            ->first();

        // Pastikan offering yang diakses benar-benar KIP-K.
        abort_if(
            !$beasiswa || strtolower($beasiswa->nama_program) !== 'kip-k',
            404
        );

        // Cek apakah pendaftaran sedang dibuka.
        $today = now()->startOfDay();

        if (
            ($beasiswa->tanggal_buka && $today->lt($beasiswa->tanggal_buka)) ||
            ($beasiswa->tanggal_tutup && $today->gt($beasiswa->tanggal_tutup))
        ) {
            abort(403, 'Pendaftaran beasiswa tidak sedang dibuka.');
        }

        // Cari pengajuan aktif mahasiswa pada periode ini.
        $pengajuanAktif = DB::table('pengajuan')
            ->where('id_mahasiswa', $user->id)
            ->where('id_periode', $beasiswa->id_periode)
            ->whereNotNull('active_flag')
            ->first();

        // Kalau dari flow lama masih ada draft KIP-K,
        // jangan membuat pengajuan kedua.
        if ($pengajuanAktif) {

            if ($pengajuanAktif->status === 'draft') {

                DB::table('pengajuan')
                    ->where('id', $pengajuanAktif->id)
                    ->update([
                        'status' => 'diajukan',
                        'submitted_at' => now(),
                    ]);

                return redirect()
                    ->route('mahasiswa.tracking.index')
                    ->with('success', 'Pengajuan KIP-K berhasil diajukan ke UNUJA.');
            }

            return redirect()
                ->route('mahasiswa.tracking.index')
                ->with('error', 'Kamu sudah memiliki pengajuan KIP-K aktif pada periode ini.');
        }

        // Buat pengajuan KIP-K langsung sebagai "diajukan".
        DB::transaction(function () use ($user, $beasiswa) {

            $pengajuanId = DB::table('pengajuan')->insertGetId([
                'id_offering' => $beasiswa->id,
                'id_mahasiswa' => $user->id,
                'id_periode' => $beasiswa->id_periode,
                'status' => 'diajukan',
                'submitted_at' => now(),
            ]);

            // Tetap membuat record tahapan.
            $tahapan = DB::table('tahapan')
                ->where('id_offering', $beasiswa->id)
                ->orderBy('urutan')
                ->get(['id']);

            $dataTahapan = $tahapan->map(function ($tahap) use ($pengajuanId) {
                return [
                    'id_pengajuan' => $pengajuanId,
                    'id_tahapan' => $tahap->id,
                    'status' => 'menunggu',
                ];
            })->all();

            if (!empty($dataTahapan)) {
                DB::table('pengajuan_tahapan')->insert($dataTahapan);
            }
        });

        return redirect()
            ->route('mahasiswa.pengajuan.index')
            ->with('success', 'Pengajuan KIP-K berhasil diajukan ke UNUJA.');
    }
}