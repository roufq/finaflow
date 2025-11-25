<?php

namespace App\Console\Commands;

use App\Jobs\SyncBankIntegration;
use App\Models\BankIntegration;
use Illuminate\Console\Command;

class ScheduleBankSyncs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bank-integrations:schedule-sync
                            {--balance-only : Sync only account balances}
                            {--user-id= : Sync only integrations for specific user}';

    /**
     * The console command description.
     */
    protected $description = 'Schedule bank integration syncs for all active integrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $balanceOnly = $this->option('balance-only');
        $userId = $this->option('user-id');

        $query = BankIntegration::where('is_active', true);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $integrations = $query->get();

        if ($integrations->isEmpty()) {
            $this->info('No active bank integrations found.');
            return;
        }

        $this->info("Scheduling sync for {$integrations->count()} bank integrations...");

        $scheduled = 0;

        foreach ($integrations as $integration) {
            try {
                SyncBankIntegration::dispatch($integration, $balanceOnly);
                $scheduled++;

                $this->info("✓ Scheduled sync for {$integration->bank_name}");

            } catch (\Exception $e) {
                $this->error("✗ Failed to schedule {$integration->bank_name}: {$e->getMessage()}");
            }
        }

        $syncType = $balanceOnly ? 'balance-only' : 'full';
        $this->info("Successfully scheduled {$scheduled} {$syncType} syncs.");
    }
}
