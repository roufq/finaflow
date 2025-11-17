<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class TestScanReceiptWithCsrf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scan-csrf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the scan receipt endpoint with proper CSRF token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing scan receipt endpoint with CSRF token...');

        // Create a simple test image (1x1 pixel PNG)
        $testImageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        // Save test image temporarily
        $tempPath = storage_path('app/test-receipt.png');
        file_put_contents($tempPath, $testImageContent);

        $this->info('Created test image at: ' . $tempPath);

        // Get the first user for testing
        $user = User::first();
        if (!$user) {
            $this->error('No users found in database. Please run php artisan db:seed first.');
            return;
        }

        $this->info('Using user: ' . $user->email);

        // Authenticate the user
        Auth::login($user);

        // Get the session ID
        $sessionId = Session::getId();
        $this->info('Session ID: ' . $sessionId);

        // Generate CSRF token
        $csrfToken = csrf_token();
        $this->info('CSRF Token: ' . $csrfToken);

        // Test the endpoint with session cookie and CSRF token
        try {
            $response = Http::timeout(30)
                ->withCookies([
                    'laravel_session' => $sessionId,
                    'XSRF-TOKEN' => $csrfToken
                ], '127.0.0.1')
                ->withHeaders([
                    'X-CSRF-TOKEN' => $csrfToken,
                    'Referer' => 'http://127.0.0.1:8000/transactions'
                ])
                ->attach(
                    'receipt_image',
                    file_get_contents($tempPath),
                    'test-receipt.png'
                )
                ->post('http://127.0.0.1:8000/transactions/scan-receipt');

            $this->info('Response status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                $this->info('Response data:');
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
            } else {
                $this->error('Request failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
        }

        // Clean up
        if (file_exists($tempPath)) {
            unlink($tempPath);
            $this->info('Cleaned up test image.');
        }

        $this->info('Test completed.');
    }
}
