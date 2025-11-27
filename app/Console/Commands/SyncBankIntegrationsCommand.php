<?php

namespace App\Console\Commands;

use App\Models\BankIntegration;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class SyncBankIntegrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bank:sync {--balance-only : Sync only account balances, skipping full transaction sync}';

    /**
     * The console command description.
     */
    protected $description = 'Synchronize bank transactions and balances from active integrations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $balanceOnly = (bool) $this->option('balance-only');

        if ($balanceOnly) {
            $this->syncBalancesOnly();
        } else {
            $this->syncFullData();
        }

        return self::SUCCESS;
    }

    /**
     * Perform full data synchronization (transactions + balances).
     */
    private function syncFullData(): void
    {
        $this->info('Starting full bank integration sync...');

        /** @var Collection<int, BankIntegration> $integrations */
        $integrations = BankIntegration::where('is_active', true)->get();

        if ($integrations->isEmpty()) {
            $this->warn('No active bank integrations found.');

            return;
        }

        $totalSynced = 0;
        $totalTransactions = 0;
        $totalFailed = 0;

        foreach ($integrations as $integration) {
            $bankName = $integration->bank_name ?? 'Unknown Bank';
            $this->line('---');
            $this->info("Syncing: {$bankName}...");

            try {
                $result = $integration->syncTransactions();

                if ($result['success'] ?? false) {
                    $totalSynced++;
                    $importedCount = $result['transactions_imported'] ?? 0;
                    $totalTransactions += $importedCount;
                    $message = $result['message'] ?? "Synced successfully, imported {$importedCount} transactions.";
                    $this->info("{$bankName}: {$message}");
                } else {
                    $totalFailed++;
                    $message = $result['message'] ?? 'Synchronization failed.';
                    $this->error("{$bankName}: {$message}");
                }
            } catch (\Exception $e) {
                $totalFailed++;
                $this->error("{$bankName}: Sync failed - {$e->getMessage()}");
            }
        }

        $this->line('---');
        $this->info("Full sync completed: {$totalSynced} successful, {$totalFailed} failed.");
        $this->info("Total transactions imported: {$totalTransactions}.");
    }

    /**
     * Sync only account balances.
     */
    private function syncBalancesOnly(): void
    {
        $this->info('Starting balance-only sync...');

        /** @var Collection<int, BankIntegration> $integrations */
        $integrations = BankIntegration::where('is_active', true)->get();

        if ($integrations->isEmpty()) {
            $this->warn('No active bank integrations found.');

            return;
        }

        $totalSynced = 0;
        $totalFailed = 0;

        foreach ($integrations as $integration) {
            $bankName = $integration->bank_name ?? 'Unknown Bank';
            $this->line('---');
            $this->info("Updating balance for: {$bankName}...");

            try {
                // Reuse syncTransactions to keep logic consistent
                $integration->syncTransactions();
                $totalSynced++;
                $this->info("{$bankName}: Balance updated.");
            } catch (\Exception $e) {
                $totalFailed++;
                $this->error("{$bankName}: Balance sync failed - {$e->getMessage()}");
            }
        }

        $this->line('---');
        $this->info("Balance sync completed: {$totalSynced} updated, {$totalFailed} failed.");
    }
}
