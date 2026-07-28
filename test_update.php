<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'admin@finaflow.test')->first();

$data = [
    'telegram_bot_token' => '8769834244:AAGoynPCK32y9oDAj-w1GwachPYTIJvA7bE',
    'telegram_bot_username' => 'mawanto_bot',
    'telegram_proxy_url' => 'https://my-telegram.roufmawanto963.workers.dev/bot-webhook-jembatan',
];

$user->update($data);

var_dump($user->getChanges());

$freshUser = $user->fresh();
var_dump($freshUser->only(['telegram_bot_token', 'telegram_bot_username', 'telegram_proxy_url']));
