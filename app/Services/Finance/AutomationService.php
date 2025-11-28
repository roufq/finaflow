<?php

namespace App\Services\Finance;

use Illuminate\Support\Facades\Log;

class AutomationService
{
    public function syncRecurringTransactions(int $userId): void
    {
        // Placeholder for future automation logic; logs invocation for observability.
        Log::info('Recurring transaction sync triggered', ['user_id' => $userId]);
    }
}
