<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RouteNameUniquenessTest extends TestCase
{
    public function test_routes_can_be_cached(): void
    {
        try {
            $exitCode = Artisan::call('route:cache');

            $this->assertSame(0, $exitCode, Artisan::output());
        } finally {
            Artisan::call('route:clear');
        }
    }

    public function test_web_and_api_transaction_routes_have_distinct_names(): void
    {
        $this->assertSame('/transactions', route('transactions.index', absolute: false));
        $this->assertSame('/api/v1/transactions', route('api.v1.transactions.index', absolute: false));
    }
}
