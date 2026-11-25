<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;

class TelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook {--set : Register the webhook URL with Telegram} {--delete : Delete the current webhook} {--info : Get current webhook status}';
    protected $description = 'Manage Telegram bot webhook';

    public function handle(TelegramService $telegram): int
    {
        if (! $telegram->isConfigured()) {
            $this->error('Telegram bot token is not configured in .env (TELEGRAM_BOT_TOKEN).');

            return self::FAILURE;
        }

        if ($this->option('delete')) {
            $result = $telegram->deleteWebhook();
            $this->info('Webhook delete result: ' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        if ($this->option('set')) {
            $url = config('telegram.webhook_url') ?: url('/api/telegram/webhook');
            $secret = config('telegram.webhook_secret');

            $this->info("Setting webhook URL to: {$url}");
            $result = $telegram->setWebhook($url, $secret);
            $this->info('Webhook set result: ' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        // Default to --info
        $result = $telegram->getWebhookInfo();
        $this->info('Webhook info: ' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
