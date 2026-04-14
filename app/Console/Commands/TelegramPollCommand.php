<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';

    protected $description = 'Poll Telegram for updates (useful for localhost)';

    protected $controller;

    public function __construct(TelegramController $controller)
    {
        parent::__construct();
        $this->controller = $controller;
    }

    public function handle()
    {
        $systemToken = config('services.telegram.bot_token');
        $this->info('FinaFlow Telegram Polling started...');

        $offsets = [];
        if ($systemToken) {
            $offsets['system'] = 0;
        }

        while (true) {
            // 1. System Bot Polling
            if ($systemToken) {
                $this->pollBot($systemToken, $offsets, 'system');
            }

            // 2. Personal Bots Polling
            $userBots = \App\Models\User::whereNotNull('telegram_bot_token')->get();
            foreach ($userBots as $user) {
                $this->pollBot($user->telegram_bot_token, $offsets, "user_{$user->id}", $user);
            }

            sleep(1);
        }
    }

    protected function pollBot($token, &$offsets, $key, $botOwner = null)
    {
        try {
            $offset = $offsets[$key] ?? 0;
            $baseUrl = config('services.telegram.base_url', 'https://api.telegram.org');
            $response = Http::withoutVerifying()->get("{$baseUrl}/bot{$token}/getUpdates", [
                'offset' => $offset,
                'timeout' => 2,
            ]);

            if ($response->successful()) {
                $updates = $response->json('result');
                foreach ($updates as $update) {
                    $offsets[$key] = $update['update_id'] + 1;
                    $this->info("Processing [{$key}] Update ID: {$update['update_id']}");
                    $this->controller->processUpdate($update, $botOwner, $token);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error polling [{$key}]: ".$e->getMessage());
        }
    }
}
