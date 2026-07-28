<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'admin@finaflow.test')->first();
$activities = $user->activityLogs()
            ->where('action', 'like', 'Telegram%')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
echo "Count with like Telegram%: " . count($activities) . "\n";
