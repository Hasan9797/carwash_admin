<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramBotHandler $handler,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $secret = config('telegram.webhook_secret');
        if (! empty($secret)) {
            $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($headerSecret !== $secret) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $payload = $request->all();
        if (! empty($payload)) {
            $this->handler->handleUpdate($payload);
        }

        return response()->json(['ok' => true]);
    }
}
