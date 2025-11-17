<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class TestScanReceiptWithSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scan-session';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the scan receipt endpoint with proper session authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing scan receipt endpoint with session authentication...');

        // Create a test image with text using GD
        $image = imagecreatetruecolor(400, 200);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        // Add some text to simulate receipt content using imagestring (built-in font)
        $lines = [
            "RECEIPT",
            "Total: $25.99",
            "Date: 2024-11-15",
            "Merchant: Test Store"
        ];

        $y = 20;
        foreach ($lines as $line) {
            imagestring($image, 5, 10, $y, $line, $black);
            $y += 20;
        }

        // Save test image temporarily
        $tempPath = storage_path('app/test-receipt.png');
        imagepng($image, $tempPath);
        imagedestroy($image);

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

        // Test the endpoint with session cookie
        try {
            $response = Http::timeout(30)
                ->withCookies([
                    'laravel_session' => $sessionId
                ], '127.0.0.1')
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
