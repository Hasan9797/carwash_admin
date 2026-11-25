<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $token;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = config('telegram.bot_token');
        $this->apiUrl = rtrim((string) config('telegram.api_url', 'https://api.telegram.org/bot'), '/');
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    public function getBaseUrl(): string
    {
        return "{$this->apiUrl}{$this->token}";
    }

    public function sendMessage(
        string|int $chatId,
        string $text,
        array $replyMarkup = [],
        string $parseMode = 'HTML'
    ): array|bool {
        if (! $this->isConfigured()) {
            Log::info('Telegram bot token is not configured. Skipping sendMessage.', [
                'chat_id' => $chatId,
                'text' => $text,
            ]);

            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if (! empty($replyMarkup)) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            $response = Http::timeout(10)->post("{$this->getBaseUrl()}/sendMessage", $payload);

            if (! $response->successful()) {
                Log::warning('Telegram sendMessage failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('Telegram sendMessage exception: ' . $e->getMessage());

            return false;
        }
    }

    public function editMessageText(
        string|int $chatId,
        int $messageId,
        string $text,
        array $replyMarkup = [],
        string $parseMode = 'HTML'
    ): array|bool {
        if (! $this->isConfigured()) {
            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if (! empty($replyMarkup)) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        try {
            $response = Http::timeout(10)->post("{$this->getBaseUrl()}/editMessageText", $payload);

            return $response->successful() ? $response->json() : false;
        } catch (\Throwable $e) {
            Log::warning('Telegram editMessageText exception: ' . $e->getMessage());

            return false;
        }
    }

    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false
    ): bool {
        if (! $this->isConfigured()) {
            return false;
        }

        $payload = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => $showAlert,
        ];

        if ($text !== null) {
            $payload['text'] = $text;
        }

        try {
            $response = Http::timeout(10)->post("{$this->getBaseUrl()}/answerCallbackQuery", $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Telegram answerCallbackQuery exception: ' . $e->getMessage());

            return false;
        }
    }

    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout($timeout + 5)->get("{$this->getBaseUrl()}/getUpdates", [
                'offset' => $offset,
                'limit' => $limit,
                'timeout' => $timeout,
            ]);

            if ($response->successful()) {
                return (array) ($response->json('result') ?? []);
            }
        } catch (\Throwable $e) {
            Log::warning('Telegram getUpdates exception: ' . $e->getMessage());
        }

        return [];
    }

    public function setWebhook(string $url, ?string $secretToken = null): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token is not configured'];
        }

        $payload = ['url' => $url];
        if (! empty($secretToken)) {
            $payload['secret_token'] = $secretToken;
        }

        try {
            $response = Http::timeout(15)->post("{$this->getBaseUrl()}/setWebhook", $payload);

            return (array) $response->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }

    public function deleteWebhook(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token is not configured'];
        }

        try {
            $response = Http::timeout(15)->post("{$this->getBaseUrl()}/deleteWebhook");

            return (array) $response->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }

    public function getWebhookInfo(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token is not configured'];
        }

        try {
            $response = Http::timeout(10)->get("{$this->getBaseUrl()}/getWebhookInfo");

            return (array) $response->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }

    public function getMe(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'description' => 'Bot token is not configured'];
        }

        try {
            $response = Http::timeout(10)->get("{$this->getBaseUrl()}/getMe");

            return (array) $response->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'description' => $e->getMessage()];
        }
    }

    public function sendOrderAssignedNotification(Order $order, User $washer): bool
    {
        if (empty($washer->chat_id) || ! $this->isConfigured()) {
            return false;
        }

        $order->loadMissing(['category', 'region']);

        $categoryName = $order->category?->name ?? '—';
        $regionName = $order->region?->name ?? '—';
        $price = number_format((float) $order->price, 0, '.', ' ');
        $time = $order->created_at?->format('H:i') ?? now()->format('H:i');

        $text = "🔔 <b>Sizga yangi zakaz biriktirildi!</b>\n\n"
            . "🚗 Avtomobil: <code>{$order->plate_number}</code>\n"
            . "🧼 Xizmat: <b>{$categoryName}</b>\n"
            . "💰 Narxi: <b>{$price} so'm</b>\n"
            . "📍 Filial: {$regionName}\n"
            . "⏰ Vaqti: {$time}\n\n"
            . "Quyidagi tugma orqali zakasni yakunlashingiz mumkin:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => "✅ Zakasni yakunlash (#{$order->id})",
                        'callback_data' => "finish_order_{$order->id}",
                    ],
                ],
            ],
        ];

        $res = $this->sendMessage($washer->chat_id, $text, $keyboard);

        return $res !== false;
    }
}
