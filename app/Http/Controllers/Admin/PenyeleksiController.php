<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyeleksiController extends Controller
{
    /**
     * List semua penyeleksi + offering yang di-handle.
     */
    public function index()
    {
        $penyeleksis = DB::table('users')
            ->where('role', 'penyeleksi')
            ->select('id', 'nama', 'username', 'email', 'created_at')
            ->orderBy('nama')
            ->get()
            ->map(function ($user) {
                // Cari offering yang di-handle
                $assignment = DB::table('offering_penyeleksi as op')
                    ->join('beasiswa_offering as o', 'o.id', '=', 'op.id_offering')
                    ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
                    ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
                    ->where('op.id_user', $user->id)
                    ->select(
                        'op.id_offering',
                        'p.nama as nama_program',
                        'pr.nama_periode',
                        'o.status as status_offering'
                    )
                    ->first();

                $user->offering = $assignment;

                return $user;
            });

        // Daftar offering untuk dropdown assign
        $offerings = DB::table('beasiswa_offering as o')
            ->join('beasiswa_program as p', 'p.id', '=', 'o.id_program')
            ->join('periode as pr', 'pr.id', '=', 'o.id_periode')
            ->where('o.status', 'published')
            ->select(
                'o.id',
                'p.nama as nama_program',
                'pr.nama_periode'
            )
            ->orderBy('p.nama')
            ->get();

        return view('admin.penyeleksi.index', compact('penyeleksis', 'offerings'));
    }

    /**
     * Assign penyeleksi ke offering.
     * Aturan: 1 penyeleksi = 1 offering. Kalau udah ada, otomatis cabut yang lama.
     */
    public function assign(Request $request, int $userId)
    {
        $request->validate([
            'id_offering' => ['required', 'integer', 'exists:beasiswa_offering,id'],
        ]);

        // Cek user valid dan role penyeleksi
        $user = DB::table('users')->where('id', $userId)->where('role', 'penyeleksi')->first();
        abort_if(!$user, 404);

        // Cek apakah offering ini udah di-handle penyeleksi LAIN
        // (Boleh 1 offering banyak penyeleksi, tapi 1 penyeleksi cuma 1 offering)
        // Jadi, kita gak perlu cek di sini — bebas.

        // Hapus assignment lama user ini (kalau ada)
        DB::table('offering_penyeleksi')->where('id_user', $userId)->delete();

        // Insert assignment baru
        DB::table('offering_penyeleksi')->insert([
            'id_user'     => $userId,
            'id_offering' => $request->id_offering,
            'created_at'  => now(),
        ]);

        return redirect()
            ->route('admin.penyeleksi.index')
            ->with('success', "Penyeleksi {$user->nama} berhasil di-assign ke offering.");
    }

    /**
     * Cabut assignment penyeleksi.
     */
    public function unassign(int $userId)
    {
        $user = DB::table('users')->where('id', $userId)->where('role', 'penyeleksi')->first();
        abort_if(!$user, 404);

        DB::table('offering_penyeleksi')->where('id_user', $userId)->delete();

        return redirect()
            ->route('admin.penyeleksi.index')
            ->with('success', "Assignment penyeleksi {$user->nama} berhasil dicabut.");
    }
}