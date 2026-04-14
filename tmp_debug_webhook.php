<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

Auth::loginUsingId(3);
$user = User::find(3);
$appWebhookUrl = url("/telegram/webhook/{$user->telegram_webhook_token}");
$webhookUrl = $user->telegram_proxy_url
    ? "{$user->telegram_proxy_url}/{$user->telegram_webhook_token}"
    : $appWebhookUrl;

echo 'Webhook URL to be set: '.$webhookUrl."\n";
echo 'Bot Token: '.$user->telegram_bot_token."\n";

$baseUrl = config('services.telegram.base_url', 'https://api.telegram.org');
$url = "{$baseUrl}/bot{$user->telegram_bot_token}/setWebhook?url={$webhookUrl}";

echo 'Full API URL: '.$url."\n";

$response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url);
echo 'Response body: '.$response->body()."\n";
