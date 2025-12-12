<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageAccessTest extends TestCase
{
    public function test_public_storage_file_is_served_without_symlink(): void
    {
        $disk = Storage::disk('public');
        $disk->put('testing/hello.txt', 'hello world');

        $response = $this->get('/storage/testing/hello.txt');

        $response->assertOk();
        $this->assertSame('hello world', $response->streamedContent());

        $disk->delete('testing/hello.txt');
    }

    public function test_missing_public_storage_file_returns_not_found(): void
    {
        Storage::disk('public')->delete('testing/missing.txt');

        $response = $this->get('/storage/testing/missing.txt');

        $response->assertNotFound();
    }
}
