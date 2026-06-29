<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TestScanReceiptWithApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scan-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the scan receipt endpoint using API approach';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing scan receipt endpoint using API approach...');

        // Create a test image with actual text that OCR can read
        $image = imagecreatetruecolor(400, 200);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        // Add text to the image
        imagestring($image, 5, 20, 20, 'INDOMARET', $black);
        imagestring($image, 3, 20, 50, 'Total: Rp 25,000', $black);
        imagestring($image, 3, 20, 70, 'Date: 10/11/2025', $black);
        imagestring($image, 3, 20, 90, 'Thank you for shopping!', $black);

        // Save image to temporary file
        ob_start();
        imagepng($image);
        $testImageContent = ob_get_clean();
        imagedestroy($image);

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

        // Authenticate the user
        Auth::login($user);

        // Get the session ID
        $sessionId = Session::getId();
        $this->info('Session ID: '.$sessionId);

        // Generate CSRF token
        $csrfToken = csrf_token();
        $this->info('CSRF Token: '.$csrfToken);

        // Test the endpoint with session cookie only (CSRF disabled for this route)
        try {
            $response = Http::timeout(30)
                ->withCookies([
                    'laravel_session' => $sessionId,
                ], '127.0.0.1')
                ->withHeaders([
                    'Referer' => 'http://127.0.0.1:8000/transactions',
                    'Accept' => 'application/json',
                ])
                ->attach(
                    'receipt_image',
                    file_get_contents($tempPath),
                    'test-receipt.png'
                )
                ->post('http://127.0.0.1:8000/transactions/scan-receipt');

            $this->info('Response status: '.$response->status());

            if ($response->successful()) {
                $data = $response->json();
                $this->info('Response data:');
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
            } else {
                $this->error('Request failed: '.$response->body());
            }

        } catch (\Exception $e) {
            $this->error('Exception occurred: '.$e->getMessage());
        }

        // Clean up
        if (file_exists($tempPath)) {
            unlink($tempPath);
            $this->info('Cleaned up test image.');
        }

        $this->info('Test completed.');
    }
}
