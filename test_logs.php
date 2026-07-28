<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'admin@finaflow.test')->first();
$logs = clone $user->activityLogs()->get();
echo "Total logs: " . count($logs) . "\n";
foreach($logs as $log) {
    echo "- " . $log->action . "\n";
}
