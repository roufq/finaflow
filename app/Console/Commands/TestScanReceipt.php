<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestScanReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scan-receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if scan receipt route is properly registered';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing scan receipt functionality...');

        // Check if route exists
        if (Route::has('transactions.scanReceipt')) {
            $this->info('✅ Route transactions.scanReceipt exists');
        } else {
            $this->error('❌ Route transactions.scanReceipt does not exist');
        }

        // Check route list
        $this->info('Available transaction routes:');
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return str_contains($route->uri(), 'transactions');
        });

        foreach ($routes as $route) {
            $this->line('  '.$route->methods()[0].' '.$route->uri().' -> '.$route->getActionName());
        }

        $this->info('Test completed.');
    }
}
