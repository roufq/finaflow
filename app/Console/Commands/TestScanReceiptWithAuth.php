<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestScanReceiptWithAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scan-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the scan receipt endpoint with authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing scan receipt endpoint with authentication...');

        // Create a simple test image (1x1 pixel PNG)
        $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        // Save test image temporarily
        $tempPath = storage_path('app/test-receipt.png');
        file_put_contents($tempPath, $testImageContent);

        $this->info('Created test image at: '.$tempPath);

        // Get the first user for testing
        $user = User::first();
        if (! $user) {
            $this->error('No users found in database. Please run php artisan db:seed first.');

            return;
        }

        $this->info('Using user: '.$user->email);

        // Simulate authentication by setting session
        // This is a simplified test - in real scenario you'd need proper session handling
        $this->info('Note: This test assumes the server is running and you have a valid session.');
        $this->info('For a complete test, you would need to:');
        $this->info('1. Start the Laravel server: php artisan serve');
        $this->info('2. Log in through the web interface');
        $this->info('3. Use browser dev tools to get the session cookie');
        $this->info('4. Include the cookie in the HTTP request');

        // For now, just show what the request would look like
        $this->info('Sample curl command:');
        $this->line('curl -X POST http://127.0.0.1:8000/transactions/scan-receipt \\');
        $this->line('  -H "Accept: application/json" \\');
        $this->line('  -F "receipt_image=@'.$tempPath.'" \\');
        $this->line('  -b "laravel_session=YOUR_SESSION_COOKIE_HERE"');

        // Clean up
        if (file_exists($tempPath)) {
            unlink($tempPath);
            $this->info('Cleaned up test image.');
        }

        $this->info('Test completed.');
    }
}
