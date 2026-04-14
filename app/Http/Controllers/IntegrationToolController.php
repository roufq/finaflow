<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\EmailReceiptParser;
use App\Services\TransactionCategorizer;
use App\Services\VoiceEntryParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IntegrationToolController extends Controller
{
    public function telegramBot()
    {
        $user = Auth::user();

        if (empty($user->remember_token)) {
            $user->update(['remember_token' => \Illuminate\Support\Str::random(60)]);
        }

        // Ensure user has a webhook token for their custom bot
        if (! $user->telegram_webhook_token) {
            $user->update([
                'telegram_webhook_token' => \Illuminate\Support\Str::random(40),
            ]);
        }

        $isBotConfigured = $user->telegram_bot_token && $user->telegram_bot_username;
        $botName = $user->telegram_bot_username ?? config('services.telegram.bot_name');

        // Fetch Last 10 Telegram Bot Activities
        $activities = $user->activityLogs()
            ->where('action', 'like', 'Telegram%')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('integrations.bot', [
            'user' => $user,
            'telegramToken' => $user->remember_token,
            'telegramBotName' => $botName ?? 'FinaFlow_Bot',
            'isBotConfigured' => ! empty($isBotConfigured) || (! empty(config('services.telegram.bot_token'))),
            'usingCustomBot' => ! empty($isBotConfigured),
            'activities' => $activities,
        ]);
    }

    public function saveBotSettings(Request $request)
    {
        $request->validate([
            'telegram_bot_token' => 'required|string|max:100',
            'telegram_bot_username' => 'required|string|max:100',
            'telegram_proxy_url' => 'nullable|url|max:200',
        ]);

        $user = Auth::user();
        $user->update([
            'telegram_bot_token' => $request->telegram_bot_token,
            'telegram_bot_username' => ltrim(trim($request->telegram_bot_username), '@'),
            'telegram_proxy_url' => rtrim(trim($request->telegram_proxy_url), '/'),
        ]);

        $user->logActivity('Telegram Bot Settings Updated', 'User updated custom bot settings.');

        return back()->with('success', 'Telegram Bot settings saved successfully.');
    }

    public function setWebhook()
    {
        $user = Auth::user();
        if (! $user->telegram_bot_token || ! $user->telegram_webhook_token) {
            return back()->with('error', 'Bot Token not configured.');
        }

        $appWebhookUrl = url("/telegram/webhook/{$user->telegram_webhook_token}");

        // If user has a proxy URL, use it as the webhook URL for Telegram,
        // appending the user token as a path or query if needed.
        // We assume the user follows the format: https://worker.dev/bot-webhook-jembatan
        $webhookUrl = $user->telegram_proxy_url
            ? "{$user->telegram_proxy_url}/{$user->telegram_webhook_token}"
            : $appWebhookUrl;

        $baseUrl = config('services.telegram.base_url', 'https://api.telegram.org');
        $url = "{$baseUrl}/bot{$user->telegram_bot_token}/setWebhook?url={$webhookUrl}";

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url);
            if ($response->successful()) {
                $user->logActivity('Telegram Webhook Registered', 'Webhook registered to '.($user->telegram_proxy_url ? "Proxy: {$user->telegram_proxy_url}" : "App: {$appWebhookUrl}"));

                return back()->with('success', 'Webhook set successfully! Your bot is now live.');
            }

            $errorMessage = $response->json()['description'] ?? 'Unknown error';
            Log::error("Telegram Webhook Error: {$errorMessage}", ['url' => $url, 'response' => $response->body()]);

            return back()->with('error', 'Telegram API error: '.$errorMessage);
        } catch (\Exception $e) {
            Log::error('Telegram Webhook Exception: '.$e->getMessage());

            return back()->with('error', 'Connection error: '.$e->getMessage());
        }
    }

    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'telegram_id' => null,
            'telegram_chat_id' => null,
        ]);

        $user->logActivity('Telegram Bot Disconnected', 'User manually disconnected the bot.');

        return back()->with('success', 'Telegram Bot has been disconnected from your account.');
    }

    public function voiceEntry()
    {
        return view('integrations.voice-entry');
    }

    public function storeVoiceEntry(Request $request, VoiceEntryParser $parser, TransactionCategorizer $categorizer)
    {
        $request->validate([
            'voice_text' => 'required|string|min:5',
        ]);

        $parsed = $parser->parse($request->voice_text);

        $categoryId = $categorizer->guessCategoryId($parsed['description'], $parsed['amount'], $parsed)
            ?? $this->defaultExpenseCategoryId();

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $categoryId,
            'transaction_date' => $parsed['date'],
            'type' => $parsed['type'],
            'amount' => $parsed['amount'],
            'description' => $parsed['description'],
            'location_metadata' => ['source' => 'voice-entry'],
        ]);

        return back()->with([
            'success' => 'Voice entry converted to transaction.',
            'parsed' => $parsed,
            'transaction_id' => $transaction->id,
        ]);
    }

    public function emailParser()
    {
        return view('integrations.email-parser');
    }

    public function parseEmail(Request $request, EmailReceiptParser $parser)
    {
        $request->validate([
            'email_content' => 'required|string|min:20',
        ]);

        $parsed = $parser->parse($request->email_content);

        return back()->with([
            'success' => 'Email parsed successfully.',
            'parsed' => $parsed,
        ]);
    }

    public function reminders()
    {
        $reminders = Automation::where('user_id', Auth::id())
            ->where('type', 'reminder')
            ->get();

        return view('integrations.bill-reminders', compact('reminders'));
    }

    public function storeReminder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'required|date',
            'vendor' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'reminder_days' => 'required|integer|min:1|max:30',
        ]);

        Automation::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => "Bill reminder for {$request->vendor}",
            'type' => 'reminder',
            'conditions' => [
                [
                    'field' => 'due_date',
                    'operator' => 'equals',
                    'value' => $request->due_date,
                ],
            ],
            'actions' => [
                [
                    'type' => 'send_notification',
                    'params' => [
                        'message' => "Bill {$request->vendor} (Rp {$request->amount}) due on {$request->due_date}",
                        'reminder_days' => $request->reminder_days,
                    ],
                ],
            ],
            'is_active' => true,
        ]);

        return redirect()->route('integrations.reminders')->with('success', 'Reminder created successfully.');
    }

    private function defaultExpenseCategoryId(): int
    {
        $category = Category::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'name' => 'Uncategorized',
                'type' => 'expense',
            ],
            [
                'description' => 'Auto-generated fallback category for voice entries.',
            ]
        );

        return $category->id;
    }
}
