<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = DB::table('pengumuman as p')
            ->join('beasiswa_offering as o', 'o.id', '=', 'p.id_offering')
            ->join('beasiswa_program as bp', 'bp.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->select(
                'p.id',
                'p.judul',
                'p.isi',
                'p.published_at',
                'p.created_at',
                'bp.nama as nama_program',
                'pr.nama_periode'
            )
            ->orderByDesc('p.created_at')
            ->paginate(15);

        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create()
    {
        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->where('o.status', 'published')
            ->select('o.id', 'p.nama as nama_program', 'pr.nama_periode')
            ->get();

        return view('admin.pengumuman.create', compact('offerings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_offering'  => ['required', 'integer', 'exists:beasiswa_offering,id'],
            'judul'        => ['required', 'string', 'max:255'],
            'isi'          => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        DB::table('pengumuman')->insert([
            'id_offering'  => $request->id_offering,
            'judul'        => $request->judul,
            'isi'          => $request->isi,
            'published_at' => $request->published_at ?: now(),
            'created_by'   => Auth::id(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $pengumuman = DB::table('pengumuman')->where('id', $id)->first();
        abort_if(!$pengumuman, 404);

        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->select('o.id', 'p.nama as nama_program', 'pr.nama_periode')
            ->get();

        return view('admin.pengumuman.edit', compact('pengumuman', 'offerings'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'id_offering'  => ['required', 'integer', 'exists:beasiswa_offering,id'],
            'judul'        => ['required', 'string', 'max:255'],
            'isi'          => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        DB::table('pengumuman')->where('id', $id)->update([
            'id_offering'  => $request->id_offering,
            'judul'        => $request->judul,
            'isi'          => $request->isi,
            'published_at' => $request->published_at,
            'updated_at'   => now(),
        ]);

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('pengumuman')->where('id', $id)->delete();

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}