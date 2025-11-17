<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class TestScanReceiptWithBrowser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scan-browser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the scan receipt endpoint using browser simulation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing scan receipt endpoint with browser simulation...');

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

        // First, login to get a proper session
        $this->info('Logging in user...');
        $loginResponse = Http::timeout(30)->asForm()->post('http://127.0.0.1:8000/login', [
            'email' => $user->email,
            'password' => 'password', // Default password from seeder
        ]);

        if (!$loginResponse->successful()) {
            $this->error('Login failed: ' . $loginResponse->status());
            return;
        }

        $this->info('Login successful');

        // Get the session cookie from the response
        $cookies = $loginResponse->cookies();
        $sessionCookie = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'laravel_session') {
                $sessionCookie = $cookie->getValue();
                break;
            }
        }

        if (!$sessionCookie) {
            $this->error('No session cookie found after login');
            return;
        }

        $this->info('Session cookie obtained: ' . $sessionCookie);

        // Now test the scan receipt endpoint with the session cookie
        $this->info('Testing scan receipt endpoint...');
        $response = Http::timeout(30)
            ->withCookies([
                'laravel_session' => $sessionCookie
            ], '127.0.0.1:8000')
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

        // Clean up
        if (file_exists($tempPath)) {
            unlink($tempPath);
            $this->info('Cleaned up test image.');
        }

        $this->info('Test completed.');
    }
}
