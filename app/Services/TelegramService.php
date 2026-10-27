<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramService
{
    private string $botToken;

    private string $botUsername;

    public function __construct()
    {
        $this->botToken = (string) config('services.telegram.bot_token');
        $this->botUsername = trim((string) config('services.telegram.bot_username'));

        if ($this->botToken === '') {
            throw new RuntimeException('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }
    }

    public function sendMessage(string $chatId, string $text): array
    {
        $response = Http::timeout(10)
            ->post($this->apiUrl('sendMessage'), [
                'chat_id' => $chatId,
                'text' => $text,
            ]);

        return $this->ensureSuccessfulResponse($response);
    }

    public function buildStartLink(string $token): string
    {
        if ($this->botUsername === '') {
            throw new RuntimeException('TELEGRAM_BOT_USERNAME belum dikonfigurasi.');
        }

        if ($token === '') {
            throw new RuntimeException('Token koneksi Telegram tidak boleh kosong.');
        }

        return 'https://t.me/'.$this->botUsername.'?start='.rawurlencode($token);
    }

    private function apiUrl(string $method): string
    {
        return 'https://api.telegram.org/bot'.$this->botToken.'/'.$method;
    }

    private function ensureSuccessfulResponse(Response $response): array
    {
        if ($response->failed()) {
            throw new RuntimeException(
                'Permintaan ke Telegram gagal dengan HTTP '.$response->status().'.'
            );
        }

        $payload = $response->json();

        if (!is_array($payload) || ($payload['ok'] ?? false) !== true) {
            $description = is_array($payload) ? ($payload['description'] ?? 'Respons tidak valid.') : 'Respons tidak valid.';

            throw new RuntimeException('Telegram API gagal: '.$description);
        }

        return $payload;
    }
}
