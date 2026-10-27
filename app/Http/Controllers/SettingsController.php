<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil preferensi (kalau belum ada, bikin default)
        $preferences = DB::table('user_preferences')->where('user_id', $user->id)->first();

        if (!$preferences) {
            DB::table('user_preferences')->insert([
                'user_id' => $user->id,
                'notif_pengajuan' => 1,
                'notif_status' => 1,
                'notif_pengumuman' => 1,
            ]);
            $preferences = DB::table('user_preferences')->where('user_id', $user->id)->first();
        }

        $telegramSubscriber = DB::table('telegram_subscriber')
            ->where('id_mahasiswa', $user->id)
            ->first();

        if ($telegramSubscriber) {
            $preferences->telegram_chat_id = $telegramSubscriber->chat_id;
        }

        return view('settings.index', compact('user', 'preferences'));
    }

    public function connectTelegram(TelegramService $telegram)
    {
        $user = Auth::user();
        $token = Str::random(32);

        Cache::put(
            'telegram:connect:'.$token,
            ['user_id' => $user->id],
            now()->addMinutes(10)
        );

        return redirect()->away($telegram->buildStartLink($token));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_lama' => ['required'],
            'password_baru' => ['required', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->password_lama, $user->password_hash)) {
            return back()->with('error_password', 'Password lama tidak sesuai.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'password_hash' => Hash::make($request->password_baru),
            'updated_at'    => now(),
        ]);

        return back()->with('success_password', 'Password berhasil diperbarui.');
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        DB::table('user_preferences')->where('user_id', $user->id)->update([
            'notif_pengajuan'  => $request->has('notif_pengajuan') ? 1 : 0,
            'notif_status'     => $request->has('notif_status') ? 1 : 0,
            'notif_pengumuman' => $request->has('notif_pengumuman') ? 1 : 0,
            'updated_at'       => now(),
        ]);

        return back()->with('success_notif', 'Preferensi notifikasi berhasil disimpan.');
    }
}