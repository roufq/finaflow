<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageAccessController extends Controller
{
    public function __invoke(Request $request, string $path): StreamedResponse
    {
        $cleanPath = ltrim($path, '/');

        abort_unless(Storage::disk('public')->exists($cleanPath), 404);

        return Storage::disk('public')->response($cleanPath, null, [
            'Cache-Control' => 'max-age=86400, public',
        ]);
    }
}
