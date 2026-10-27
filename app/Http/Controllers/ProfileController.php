<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profile = null;
        if ($user->role === 'mahasiswa') {
            $profile = DB::table('mahasiswa_profile')
                ->where('user_id', $user->id)
                ->first();
        }

        return view('profile.index', compact('user', 'profile'));
    }

    public function updateData(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'nama'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        if ($user->role === 'mahasiswa') {
            $rules = array_merge($rules, [
                'nik'                 => ['nullable', 'string', 'max:20'],
                'tempat_lahir'        => ['nullable', 'string', 'max:100'],
                'tanggal_lahir'       => ['nullable', 'date'],
                'jenis_kelamin'       => ['nullable', 'in:L,P'],
                'agama'               => ['nullable', 'string', 'max:50'],
                'alamat'              => ['nullable', 'string', 'max:500'],
                'no_hp'               => ['nullable', 'string', 'max:30'],
                'prodi'               => ['nullable', 'string', 'max:150'],
                'angkatan'            => ['nullable', 'integer', 'min:2000', 'max:2100'],
                'nama_ayah'           => ['nullable', 'string', 'max:255'],
                'pekerjaan_ayah'      => ['nullable', 'string', 'max:100'],
                'nama_ibu'            => ['nullable', 'string', 'max:255'],
                'pekerjaan_ibu'       => ['nullable', 'string', 'max:100'],
                'penghasilan_orang_tua' => ['nullable', 'string', 'max:50'],
            ]);
        }

        $request->validate($rules);

        DB::table('users')->where('id', $user->id)->update([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'updated_at' => now(),
        ]);

        if ($user->role === 'mahasiswa') {
            $data = [
                'nik'                 => $request->nik,
                'tempat_lahir'        => $request->tempat_lahir,
                'tanggal_lahir'       => $request->tanggal_lahir,
                'jenis_kelamin'       => $request->jenis_kelamin,
                'agama'               => $request->agama,
                'alamat'              => $request->alamat,
                'no_hp'               => $request->no_hp,
                'prodi'               => $request->prodi,
                'angkatan'            => $request->angkatan,
                'nama_ayah'           => $request->nama_ayah,
                'pekerjaan_ayah'      => $request->pekerjaan_ayah,
                'nama_ibu'            => $request->nama_ibu,
                'pekerjaan_ibu'       => $request->pekerjaan_ibu,
                'penghasilan_orang_tua' => $request->penghasilan_orang_tua,
                'updated_at'          => now(),
            ];

            $exists = DB::table('mahasiswa_profile')->where('user_id', $user->id)->exists();

            if ($exists) {
                DB::table('mahasiswa_profile')->where('user_id', $user->id)->update($data);
            } else {
                $data['user_id'] = $user->id;
                $data['created_at'] = now();
                DB::table('mahasiswa_profile')->insert($data);
            }
        }

        return back()->with('success', 'Data diri berhasil diperbarui.');
    }
}