<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$logs = App\Models\User::where('email', 'admin@finaflow.test')->first()->activityLogs()->get();
foreach($logs as $l) {
    echo "ID: " . $l->id . " | Created At: " . ($l->created_at ? $l->created_at->toDateTimeString() : 'NULL') . "\n";
}
