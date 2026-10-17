<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('users');

        if ($request->filled('role') && $request->role !== 'semua') {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query
            ->select('id', 'nama', 'username', 'email', 'role', 'created_at')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.user.index', compact('users'));
    }

    public function edit(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_if(!$user, 404);

        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_if(!$user, 404);

        $rules = [
            'nama'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $id],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'role'     => ['required', 'in:mahasiswa,admin,penyeleksi'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        $updateData = [
            'nama'       => $request->nama,
            'username'   => $request->username,
            'email'      => $request->email,
            'role'       => $request->role,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password_hash'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($updateData);

        return redirect()
            ->route('admin.user.index')
            ->with('success', "User {$request->nama} berhasil diupdate.");
    }
}