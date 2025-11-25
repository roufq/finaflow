<?php

namespace App\Services;

use App\Models\BankIntegration;
use App\Models\Transaction;

class DataImportValidator
{
    /**
     * Validate imported data against bank integration
     */
    public function validateImportData(BankIntegration $integration, array $importData): array
    {
        $result = [
            'valid' => true,
            'severity' => 'low', // low, medium, high
            'errors' => [],
            'warnings' => [],
            'recommendations' => []
        ];

        // Validate transactions
        if (isset($importData['transactions'])) {
            $transactionValidation = $this->validateTransactions($integration, $importData['transactions']);
            $result = array_merge($result, $transactionValidation);

            if (!$transactionValidation['valid']) {
                $result['valid'] = false;
                $result['severity'] = max($result['severity'], $transactionValidation['severity']);
            }
        }

        // Validate balance
        if (isset($importData['balance'])) {
            $balanceValidation = $this->validateBalance($integration, $importData['balance'], $importData['transactions'] ?? []);
            if (!$balanceValidation['valid']) {
                $result['valid'] = false;
                $result['severity'] = max($result['severity'], $balanceValidation['severity']);
                $result['errors'] = array_merge($result['errors'], $balanceValidation['errors']);
            }
            $result['warnings'] = array_merge($result['warnings'], $balanceValidation['warnings']);
        }

        // Check for data completeness
        $completenessCheck = $this->checkDataCompleteness($importData);
        $result['warnings'] = array_merge($result['warnings'], $completenessCheck['warnings']);
        $result['recommendations'] = array_merge($result['recommendations'], $completenessCheck['recommendations']);

        return $result;
    }

    /**
     * Validate transactions data
     */
    private function validateTransactions(BankIntegration $integration, array $transactions): array
    {
        $result = [
            'valid' => true,
            'severity' => 'low',
            'errors' => [],
            'warnings' => []
        ];

        if (empty($transactions)) {
            $result['valid'] = false;
            $result['severity'] = 'high';
            $result['errors'][] = 'No transactions found in import data';
            return $result;
        }

        // Validate each transaction
        foreach ($transactions as $index => $transaction) {
            $transactionErrors = $this->validateTransaction($transaction, $index + 1);

            if (!empty($transactionErrors['errors'])) {
                $result['valid'] = false;
                $result['severity'] = max($result['severity'], 'medium');
                $result['errors'] = array_merge($result['errors'], $transactionErrors['errors']);
            }

            if (!empty($transactionErrors['warnings'])) {
                $result['warnings'] = array_merge($result['warnings'], $transactionErrors['warnings']);
            }
        }

        // Check for date range consistency
        $dates = array_column($transactions, 'date');
        if (!empty($dates)) {
            sort($dates);
            $oldestDate = reset($dates);
            $newestDate = end($dates);

            if ($oldestDate && $newestDate) {
                $daysDiff = \Carbon\Carbon::parse($oldestDate)->diffInDays(\Carbon\Carbon::parse($newestDate));

                if ($daysDiff > 365) {
                    $result['warnings'][] = 'Transaction date range spans more than a year. This may indicate incomplete data.';
                }

                if ($daysDiff < 7) {
                    $result['warnings'][] = 'Transaction date range is very short. Consider importing more historical data.';
                }
            }
        }

        // Check for amount distribution
        $amounts = array_column($transactions, 'amount');
        if (!empty($amounts)) {
            $avgAmount = array_sum($amounts) / count($amounts);
            $maxAmount = max($amounts);
            $minAmount = min($amounts);

            if ($maxAmount > $avgAmount * 20) {
                $result['warnings'][] = 'Some transactions have unusually high amounts. Please verify the data accuracy.';
            }

            if ($minAmount < 1000 && $avgAmount > 50000) {
                $result['warnings'][] = 'Mix of very small and large transactions detected. Please check for data consistency.';
            }
        }

        return $result;
    }

    /**
     * Validate single transaction
     */
    private function validateTransaction(array $transaction, int $rowNumber): array
    {
        $errors = [];
        $warnings = [];

        // Required fields
        if (empty($transaction['date'])) {
            $errors[] = "Row {$rowNumber}: Missing transaction date";
        } elseif (!strtotime($transaction['date'])) {
            $errors[] = "Row {$rowNumber}: Invalid date format '{$transaction['date']}'";
        }

        if (empty($transaction['description'])) {
            $errors[] = "Row {$rowNumber}: Missing transaction description";
        } elseif (strlen($transaction['description']) < 3) {
            $warnings[] = "Row {$rowNumber}: Transaction description is very short";
        }

        if (!isset($transaction['amount']) || $transaction['amount'] <= 0) {
            $errors[] = "Row {$rowNumber}: Invalid or missing transaction amount";
        } elseif ($transaction['amount'] > 100000000) { // 100 million
            $warnings[] = "Row {$rowNumber}: Transaction amount is unusually high";
        }

        if (empty($transaction['type']) || !in_array($transaction['type'], ['income', 'expense'])) {
            $errors[] = "Row {$rowNumber}: Invalid transaction type. Must be 'income' or 'expense'";
        }

        // Date validation
        if (!empty($transaction['date'])) {
            $transactionDate = \Carbon\Carbon::parse($transaction['date']);
            $now = \Carbon\Carbon::now();

            if ($transactionDate->isFuture()) {
                $warnings[] = "Row {$rowNumber}: Transaction date is in the future";
            }

            if ($transactionDate->diffInYears($now) > 5) {
                $warnings[] = "Row {$rowNumber}: Transaction date is more than 5 years old";
            }
        }

        return ['errors' => $errors, 'warnings' => $warnings];
    }

    /**
     * Validate balance against transactions
     */
    private function validateBalance(BankIntegration $integration, float $csvBalance, array $transactions): array
    {
        $result = [
            'valid' => true,
            'severity' => 'low',
            'errors' => [],
            'warnings' => []
        ];

        // Calculate expected balance from transactions
        $calculatedBalance = $this->calculateBalanceFromTransactions($transactions);

        // Get current account balance
        $accountBalance = $integration->account ? $integration->account->balance : 0;

        // Compare balances
        $balanceDiff = abs($csvBalance - $calculatedBalance);
        $tolerance = max(1000, abs($csvBalance) * 0.01); // 1% tolerance or minimum 1000

        if ($balanceDiff > $tolerance) {
            $result['valid'] = false;
            $result['severity'] = 'high';
            $result['errors'][] = sprintf(
                'Balance mismatch detected. CSV balance: %s, Calculated from transactions: %s, Difference: %s',
                number_format($csvBalance, 2),
                number_format($calculatedBalance, 2),
                number_format($balanceDiff, 2)
            );
        }

        // Compare with account balance
        $accountDiff = abs($csvBalance - $accountBalance);
        if ($accountDiff > $tolerance) {
            $result['warnings'][] = sprintf(
                'CSV balance does not match current account balance. CSV: %s, Account: %s, Difference: %s',
                number_format($csvBalance, 2),
                number_format($accountBalance, 2),
                number_format($accountDiff, 2)
            );
        }

        // Check if balance makes sense relative to transaction amounts
        $totalTransactionAmount = array_sum(array_column($transactions, 'amount'));
        if ($totalTransactionAmount > 0 && abs($csvBalance) > $totalTransactionAmount * 10) {
            $result['warnings'][] = 'Balance amount seems unusually high compared to transaction amounts';
        }

        return $result;
    }

    /**
     * Calculate balance from transactions
     */
    private function calculateBalanceFromTransactions(array $transactions): float
    {
        $balance = 0.0;

        foreach ($transactions as $transaction) {
            $amount = $transaction['amount'] ?? 0;
            $type = $transaction['type'] ?? 'expense';

            if ($type === 'income') {
                $balance += $amount;
            } else {
                $balance -= $amount;
            }
        }

        return $balance;
    }

    /**
     * Check data completeness
     */
    private function checkDataCompleteness(array $importData): array
    {
        $warnings = [];
        $recommendations = [];

        $transactions = $importData['transactions'] ?? [];

        if (empty($transactions)) {
            $warnings[] = 'No transaction data found';
            $recommendations[] = 'Ensure your CSV file contains transaction data with columns for date, description, and amount';
            return ['warnings' => $warnings, 'recommendations' => $recommendations];
        }

        // Check date coverage
        if (count($transactions) > 0) {
            $dates = array_column($transactions, 'date');
            sort($dates);

            $oldestDate = \Carbon\Carbon::parse($dates[0]);
            $newestDate = \Carbon\Carbon::parse(end($dates));
            $daysSpan = $oldestDate->diffInDays($newestDate);
            $expectedTransactions = max(1, $daysSpan / 7); // Expect at least weekly transactions

            if (count($transactions) < $expectedTransactions) {
                $warnings[] = 'Transaction count seems low for the date range. You may have incomplete data.';
                $recommendations[] = 'Consider importing a longer date range or checking if your CSV export was filtered';
            }
        }

        // Check for regular transaction patterns
        $descriptions = array_count_values(array_column($transactions, 'description'));
        arsort($descriptions);

        $recurringTransactions = array_filter($descriptions, function($count) {
            return $count >= 3; // Transactions that appear 3+ times
        });

        if (empty($recurringTransactions)) {
            $warnings[] = 'No recurring transactions detected. This may indicate incomplete data.';
            $recommendations[] = 'Verify that regular payments (utilities, subscriptions) are included in your data';
        }

        // Check amount distribution
        $amounts = array_column($transactions, 'amount');
        $totalIncome = array_sum(array_map(function($t) {
            return $t['type'] === 'income' ? $t['amount'] : 0;
        }, $transactions));

        $totalExpense = array_sum(array_map(function($t) {
            return $t['type'] === 'expense' ? $t['amount'] : 0;
        }, $transactions));

        if ($totalIncome === 0) {
            $warnings[] = 'No income transactions found';
            $recommendations[] = 'Ensure income transactions (salary, deposits) are included in your CSV data';
        }

        if ($totalExpense === 0) {
            $warnings[] = 'No expense transactions found';
            $recommendations[] = 'Ensure expense transactions (purchases, payments) are included in your CSV data';
        }

        // Check for balance information
        if (!isset($importData['balance'])) {
            $recommendations[] = 'Consider including account balance information in your CSV for validation';
        }

        return ['warnings' => $warnings, 'recommendations' => $recommendations];
    }

    /**
     * Generate error messages for validation results
     */
    public function generateErrorMessages(array $validation): array
    {
        $messages = [];

        foreach ($validation['errors'] as $error) {
            $messages[] = [
                'type' => 'error',
                'message' => $error,
                'severity' => $validation['severity']
            ];
        }

        foreach ($validation['warnings'] as $warning) {
            $messages[] = [
                'type' => 'warning',
                'message' => $warning,
                'severity' => 'medium'
            ];
        }

        foreach ($validation['recommendations'] as $recommendation) {
            $messages[] = [
                'type' => 'info',
                'message' => $recommendation,
                'severity' => 'low'
            ];
        }

        return $messages;
    }
}
