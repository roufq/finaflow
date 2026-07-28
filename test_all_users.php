<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
foreach($users as $u) {
    echo "User: " . $u->email . " - Token: " . $u->telegram_bot_token . "\n";
    echo "Activities: " . $u->activityLogs()->count() . "\n";
}
