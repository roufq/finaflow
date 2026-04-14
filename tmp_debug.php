<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Account::all() as $a) {
    echo 'ID: '.$a->id.' Name: '.$a->name.' Balance: '.$a->balance.' Rate: '.($a->setting->exchange_rate ?? '1.0')."\n";
}
