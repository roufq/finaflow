<?php

namespace App\Console\Commands;

use App\Models\BankIntegration;
use Illuminate\Console\Command;

class SyncBankIntegrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:sync-integrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all active bank integrations and refresh balances';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $integrations = BankIntegration::where('is_active', true)->get();

        foreach ($integrations as $integration) {
            $result = $integration->syncTransactions();
            $status = $result['success'] ? 'info' : 'error';
            $this->{$status}($integration->bank_name.': '.$result['message']);
        }

        return self::SUCCESS;
    }
}
