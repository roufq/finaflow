<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankIntegration extends Model
{
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'account_type',
        'integration_type',
        'credentials',
        'settings',
        'last_sync_at',
        'last_balance_sync_at',
        'is_active',
        'current_balance',
        'available_balance',
        'notes',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'last_sync_at' => 'datetime',
        'last_balance_sync_at' => 'datetime',
        'current_balance' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sync transactions from bank
     */
    public function syncTransactions(): array
    {
        try {
            $transactions = [];

            switch ($this->integration_type) {
                case 'api':
                    $transactions = $this->syncViaApi();
                    break;
                case 'csv':
                    $transactions = $this->syncViaCsv();
                    break;
                case 'manual':
                    // Manual sync - no automatic import
                    break;
            }

            if (! empty($transactions)) {
                $importResult = $this->importTransactions($transactions, true);
                $transactionsImported = $importResult['imported'];
            } else {
                $transactionsImported = 0;
            }

            $this->update([
                'last_sync_at' => now(),
                'last_balance_sync_at' => now(),
                'current_balance' => $this->getBalanceFromProvider(),
                'available_balance' => $this->getBalanceFromProvider(true),
            ]);

            return [
                'success' => true,
                'transactions_imported' => $transactionsImported,
                'message' => "Successfully synced {$this->bank_name}",
            ];

        } catch (\Exception $e) {
            \Log::error("Bank sync failed for {$this->bank_name}: ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Sync failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Sync via API (placeholder for actual bank API integration)
     */
    private function syncViaApi(): array
    {
        \Log::info("API sync attempted for {$this->bank_name}");

        return $this->generateSimulatedTransactions();
    }

    /**
     * Sync via CSV upload
     */
    private function syncViaCsv(): array
    {
        \Log::info("CSV sync attempted for {$this->bank_name}");

        return $this->generateSimulatedTransactions(rand(3, 8));
    }

    /**
     * Check for duplicate transactions
     */
    public function findDuplicateTransactions(array $transactions): array
    {
        $duplicates = [];

        foreach ($transactions as $transaction) {
            $existing = Transaction::where('user_id', $this->user_id)
                ->where('transaction_date', $transaction['date'])
                ->where('amount', $transaction['amount'])
                ->where('description', $transaction['description'])
                ->first();

            if ($existing) {
                $duplicates[] = [
                    'new' => $transaction,
                    'existing' => $existing,
                ];
            }
        }

        return $duplicates;
    }

    /**
     * Import transactions with duplicate checking
     */
    public function importTransactions(array $transactions, bool $skipDuplicates = true): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $accountId = $this->findTargetAccountId();
        $account = $accountId ? Account::find($accountId) : null;

        foreach ($transactions as $transactionData) {
            try {
                // Check for duplicates if enabled
                if ($skipDuplicates) {
                    $duplicate = Transaction::where('user_id', $this->user_id)
                        ->where('transaction_date', $transactionData['date'])
                        ->where('amount', $transactionData['amount'])
                        ->where('description', $transactionData['description'])
                        ->exists();

                    if ($duplicate) {
                        $skipped++;

                        continue;
                    }
                }

                $type = $transactionData['type'] ?? 'expense';
                $amount = $transactionData['amount'];
                $description = $transactionData['description'];

                // Create transaction
                $transaction = Transaction::create([
                    'user_id' => $this->user_id,
                    'category_id' => $this->guessCategory($transactionData),
                    'account_id' => $accountId,
                    'transaction_date' => $transactionData['date'],
                    'type' => $type,
                    'amount' => $amount,
                    'description' => $description,
                    'location_metadata' => [
                        'bank_integration_id' => $this->id,
                        'imported_at' => now(),
                        'raw_data' => $transactionData,
                    ],
                ]);

                if ($account) {
                    $account->updateBalance($amount, $type === 'income' ? 'add' : 'subtract');
                }

                $imported++;

            } catch (\Exception $e) {
                $errors[] = [
                    'transaction' => $transactionData,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    private function findTargetAccountId(): ?int
    {
        $query = Account::active();

        if ($this->account_number) {
            $matched = $query->where('account_number', $this->account_number)->value('id');
            if ($matched) {
                return $matched;
            }
        }

        if ($this->bank_name) {
            $matched = $query->where('bank_name', $this->bank_name)->value('id');
            if ($matched) {
                return $matched;
            }
        }

        return $query->value('id');
    }

    /**
     * Guess transaction category based on description
     */
    private function guessCategory(array $transactionData): ?int
    {
        $categorizer = app(\App\Services\TransactionCategorizer::class);

        return $categorizer->guessCategoryId(
            $transactionData['description'],
            $transactionData['amount'],
            $transactionData
        );
    }

    /**
     * Get integration status
     */
    public function getStatus(): array
    {
        return [
            'is_active' => $this->is_active,
            'last_sync' => $this->last_sync_at?->diffForHumans(),
            'last_balance_sync' => $this->last_balance_sync_at?->diffForHumans(),
            'integration_type' => $this->integration_type,
            'bank_name' => $this->bank_name,
            'current_balance' => $this->current_balance,
            'available_balance' => $this->available_balance,
        ];
    }

    /**
     * Generate sample transactions for demo integrations
     */
    private function generateSimulatedTransactions(int $count = 5): array
    {
        $merchants = [
            'Indomaret', 'Alfamart', 'Grab', 'Gojek', 'Tokopedia',
            'Shopee', 'Starbucks', 'KFC', 'Pertamina', 'BPJS',
        ];

        $transactions = [];

        for ($i = 0; $i < $count; $i++) {
            $amount = rand(10, 200) * 1000;
            $merchant = $merchants[array_rand($merchants)];

            $transactions[] = [
                'date' => now()->subDays(rand(0, 10))->format('Y-m-d'),
                'description' => "{$merchant} purchase",
                'amount' => $amount,
                'type' => 'expense',
            ];
        }

        // Occasionally add income transaction
        if (rand(0, 1)) {
            $transactions[] = [
                'date' => now()->subDays(rand(0, 10))->format('Y-m-d'),
                'description' => 'Salary Payment',
                'amount' => rand(5, 15) * 1000000,
                'type' => 'income',
            ];
        }

        return $transactions;
    }

    /**
     * Get balance from provider (simulated)
     */
    private function getBalanceFromProvider(bool $available = false): float
    {
        if ($available) {
            return $this->current_balance
                ? max(0, $this->current_balance - rand(100000, 500000))
                : rand(5, 25) * 1000000;
        }

        return rand(10, 30) * 1000000;
    }
}
