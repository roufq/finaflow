<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

echo "Before update:\n";
var_dump($user->only(['telegram_bot_token', 'telegram_bot_username', 'telegram_proxy_url']));

$success = $user->update([
    'telegram_bot_token' => '123456789:TESTTOKEN',
    'telegram_bot_username' => 'test_bot',
    'telegram_proxy_url' => 'https://example.com'
]);

echo "Update result: " . ($success ? "true" : "false") . "\n";

$user->refresh();
echo "After update (from DB):\n";
var_dump($user->only(['telegram_bot_token', 'telegram_bot_username', 'telegram_proxy_url']));

try {
    $user->logActivity('Test Action', 'Test description');
    echo "Activity logged successfully.\n";
} catch (\Exception $e) {
    echo "Failed to log activity: " . $e->getMessage() . "\n";
}

$activities = $user->activityLogs()->orderByDesc('id')->limit(2)->get();
echo "Recent activities:\n";
foreach ($activities as $act) {
    echo "- " . $act->action . " at " . ($act->created_at ? $act->created_at->toDateTimeString() : 'null') . "\n";
}
