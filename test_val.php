<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = [
    "telegram_bot_token" => "8769834244:AAGoynPCK32y9oDAj-w1GwachPYTIJvA7bE",
    "telegram_bot_username" => " mawanto_bot",
    "telegram_proxy_url" => "https://my-telegram.roufmawanto963.workers.dev/bot-webhook-jembatan"
];

$rules = [
    'telegram_bot_token' => 'required|string|max:100',
    'telegram_bot_username' => 'required|string|max:100',
    'telegram_proxy_url' => 'nullable|url|max:200',
];

$validator = Illuminate\Support\Facades\Validator::make($data, $rules);

if ($validator->fails()) {
    echo "Validation failed:\n";
    print_r($validator->errors()->all());
} else {
    echo "Validation passed!\n";
}
