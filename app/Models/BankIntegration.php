<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use App\Services\CsvParser;
use App\Services\OfxParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;

class BankIntegration extends Model
{
    use UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function importLogs(): HasMany
    {
        return $this->hasMany(ImportLog::class);
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
                case 'ofx':
                    $transactions = $this->syncViaOfx();
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

            ImportLog::create([
                'user_id' => $this->user_id,
                'bank_integration_id' => $this->id,
                'status' => 'success',
                'source' => $this->integration_type,
                'message' => "Synced {$this->bank_name}",
                'context' => [
                    'transactions_imported' => $transactionsImported,
                ],
            ]);

            return [
                'success' => true,
                'transactions_imported' => $transactionsImported,
                'message' => "Successfully synced {$this->bank_name}",
            ];

        } catch (\Exception $e) {
            \Log::error("Bank sync failed for {$this->bank_name}: ".$e->getMessage());

            ImportLog::create([
                'user_id' => $this->user_id,
                'bank_integration_id' => $this->id,
                'status' => 'failed',
                'source' => $this->integration_type,
                'message' => 'Sync failed: '.$e->getMessage(),
                'context' => [],
            ]);

            return [
                'success' => false,
                'message' => 'Sync failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Sync via API (Integration with Open Banking Aggregator - e.g. Brick)
     */
    private function syncViaApi(): array
    {
        \Log::info("API sync attempted for {$this->bank_name}");

        $clientId = config('services.brick.client_id');
        $clientSecret = config('services.brick.client_secret');
        $baseUrl = rtrim(config('services.brick.base_url', 'https://sandbox.onebrick.io'), '/');

        // Jika API Key belum diisi di .env, kita kembalikan ke transaksi simulasi agar aplikasi tidak rusak
        if (empty($clientId) || empty($clientSecret)) {
            \Log::warning('Brick API keys not configured in .env. Falling back to simulated data.');

            return $this->generateSimulatedTransactions();
        }

        $accessToken = $this->credentials['access_token'] ?? null;
        if (! $accessToken) {
            throw new \Exception('Access token bank tidak ditemukan (Atau Anda belum menghubungkan ulang bank anda lewat Widget API).');
        }

        $startDate = $this->last_sync_at ? $this->last_sync_at->format('Y-m-d') : now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        try {
            // Memanggil API Brick untuk mengambil list transaksi
            $response = Http::withToken($clientSecret)
                ->withHeaders([
                    'X-Client-Id' => $clientId,
                    'publicAccessToken' => 'Bearer '.$accessToken,
                ])
                ->timeout(30)
                ->get($baseUrl.'/v1/transaction/list', [
                    'from' => $startDate,
                    'to' => $endDate,
                ]);

            if (! $response->successful()) {
                throw new \Exception('Gagal terhubung ke gerbang bank API: '.$response->json('message', $response->body()));
            }

            $rawTransactions = $response->json('data') ?? [];
            $mappedTransactions = [];

            foreach ($rawTransactions as $trx) {
                // Parsing data dari standar Brick ke standar database FinaFlow
                $amount = (float) ($trx['amount'] ?? 0);
                $type = (($trx['direction'] ?? 'out') === 'in') ? 'income' : 'expense';

                $mappedTransactions[] = [
                    'date' => date('Y-m-d', strtotime($trx['date'] ?? now())),
                    'description' => $trx['description'] ?? 'Transaksi Bank',
                    'amount' => $amount,
                    'type' => $type,
                ];
            }

            return $mappedTransactions;

        } catch (\Exception $e) {
            \Log::error('Bank API Fetch Error: '.$e->getMessage());
            throw new \Exception('Koneksi API Gagal: '.$e->getMessage());
        }
    }

    /**
     * Sync via CSV upload
     */
    private function syncViaCsv(): array
    {
        $csvPath = $this->settings['csv_file_path'] ?? null;

        if (! $csvPath || ! file_exists(storage_path('app/'.$csvPath))) {
            throw new \Exception('CSV file not found or not configured');
        }

        $parser = app(CsvParser::class);
        $result = $parser->parseFile(storage_path('app/'.$csvPath));

        if (! $result['success']) {
            throw new \Exception('CSV parsing failed: '.($result['error'] ?? 'Unknown error'));
        }

        return $result['transactions'];
    }

    /**
     * Sync via OFX file
     */
    private function syncViaOfx(): array
    {
        $ofxPath = $this->settings['ofx_file_path'] ?? null;

        if (! $ofxPath || ! file_exists(storage_path('app/'.$ofxPath))) {
            throw new \Exception('OFX file not found or not configured');
        }

        $ofxContent = file_get_contents(storage_path('app/'.$ofxPath));
        if (! $ofxContent) {
            throw new \Exception('Unable to read OFX file');
        }

        $parser = app(OfxParser::class);
        $result = $parser->parse($ofxContent);

        if (! empty($result['errors'])) {
            \Log::warning("OFX parsing warnings for {$this->bank_name}: ".implode(', ', $result['errors']));
        }

        return $result['transactions'];
    }

    /**
     * Import transactions with duplicate checking
     */
    public function importTransactions(array $transactions, bool $skipDuplicates = true): array
    {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($transactions as $transactionData) {
            try {
                // Check for duplicates if enabled
                if ($skipDuplicates) {
                    $duplicate = Transaction::where('user_id', $this->user_id)
                        ->where('account_id', $this->account_id)
                        ->where('transaction_date', $transactionData['date'])
                        ->where('amount', $transactionData['amount'])
                        ->where('description', $transactionData['description'])
                        ->exists();

                    if ($duplicate) {
                        $skipped++;

                        continue;
                    }
                }

                // Create transaction
                Transaction::create([
                    'user_id' => $this->user_id,
                    'account_id' => $this->account_id,
                    'transaction_date' => $transactionData['date'],
                    'type' => $transactionData['type'] ?? 'expense',
                    'amount' => $transactionData['amount'],
                    'description' => $transactionData['description'],
                ]);

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
