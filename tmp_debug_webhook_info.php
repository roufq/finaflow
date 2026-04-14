<?php

use App\Models\User;

$user = User::find(3);
$token = $user->telegram_bot_token;
$url = "https://api.telegram.org/bot{$token}/getWebhookInfo";
$response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($url);
echo 'Webhook Info: '.$response->body()."\n";
