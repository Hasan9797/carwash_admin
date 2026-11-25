<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotHandler;
use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll {--once : Run only once and exit} {--timeout=20 : Long polling timeout in seconds}';
    protected $description = 'Poll for incoming Telegram bot updates';

    public function handle(TelegramService $telegram, TelegramBotHandler $handler): int
    {
        if (! $telegram->isConfigured()) {
            $this->error('Telegram bot token (TELEGRAM_BOT_TOKEN) is not configured.');

            return self::FAILURE;
        }

        $me = $telegram->getMe();
        if (empty($me['ok'])) {
            $this->error('Failed to connect to Telegram API. Check bot token: ' . ($me['description'] ?? 'unknown error'));

            return self::FAILURE;
        }

        $botUsername = $me['result']['username'] ?? 'Bot';
        $this->info("Telegram bot @{$botUsername} started polling...");

        $offset = 0;
        $timeout = (int) $this->option('timeout');
        $runOnce = (bool) $this->option('once');

        do {
            try {
                $updates = $telegram->getUpdates($offset, 100, $timeout);

                foreach ($updates as $update) {
                    $offset = (int) $update['update_id'] + 1;
                    $handler->handleUpdate($update);
                }
            } catch (\Throwable $e) {
                $this->warn('Error fetching updates: ' . $e->getMessage());
                sleep(2);
            }

            if ($runOnce) {
                break;
            }
        } while (true);

        return self::SUCCESS;
    }
}
