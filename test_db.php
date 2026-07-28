<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'admin@finaflow.test')->first();
var_dump($user->only(['telegram_bot_token', 'telegram_bot_username', 'telegram_proxy_url']));
