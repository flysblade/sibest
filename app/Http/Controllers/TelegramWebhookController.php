<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramService $telegram): JsonResponse
    {
        $message = $request->input('message');
        $chatId = data_get($message, 'chat.id');
        $text = trim((string) data_get($message, 'text', ''));

        if ($chatId === null || !preg_match('/^\/start(?:@\w+)?(?:\s+(\S+))?$/', $text, $matches)) {
            return response()->json(['ok' => true]);
        }

        $token = $matches[1] ?? '';
        $connection = Cache::pull('telegram:connect:'.$token);

        if (!is_array($connection) || !isset($connection['user_id'])) {
            $telegram->sendMessage((string) $chatId, 'Link koneksi Telegram tidak valid atau sudah kedaluwarsa.');

            return response()->json(['ok' => true]);
        }

        $userId = (int) $connection['user_id'];
        $telegramUsername = data_get($message, 'chat.username');
        $existingChat = DB::table('telegram_subscriber')
            ->where('chat_id', (string) $chatId)
            ->first();
        $existingUser = DB::table('telegram_subscriber')
            ->where('id_mahasiswa', $userId)
            ->first();

        if ($existingChat && (int) $existingChat->id_mahasiswa !== $userId) {
            $telegram->sendMessage((string) $chatId, 'Akun Telegram ini sudah terhubung ke mahasiswa lain.');

            return response()->json(['ok' => true]);
        }

        DB::transaction(function () use ($chatId, $userId, $existingChat, $existingUser): void {
            if ($existingUser && (!$existingChat || (int) $existingUser->id !== (int) $existingChat->id)) {
                DB::table('telegram_subscriber')
                    ->where('id', $existingUser->id)
                    ->update([
                        'chat_id' => (string) $chatId,
                        'tanggal_subscribe' => now(),
                    ]);

                return;
            }

            if ($existingChat) {
                DB::table('telegram_subscriber')
                    ->where('id', $existingChat->id)
                    ->update([
                        'id_mahasiswa' => $userId,
                        'tanggal_subscribe' => now(),
                    ]);

                return;
            }

            DB::table('telegram_subscriber')->insert([
                'chat_id' => (string) $chatId,
                'id_mahasiswa' => $userId,
                'tanggal_subscribe' => now(),
            ]);
        });

        DB::table('user_preferences')
            ->where('user_id', $userId)
            ->update([
                'telegram_chat_id' => (string) $chatId,
                'telegram_username' => $telegramUsername,
                'updated_at' => now(),
            ]);

        $telegram->sendMessage(
            (string) $chatId,
            'Telegram berhasil terhubung ke akun SIBEST kamu.'
        );

        return response()->json(['ok' => true]);
    }
}
