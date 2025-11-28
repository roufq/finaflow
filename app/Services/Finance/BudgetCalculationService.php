<?php

namespace App\Services\Finance;

use App\Models\Transaction;
use Illuminate\Support\Collection;

class BudgetCalculationService
{
    public function summarizeMonthly(int $userId, int $months = 6): Collection
    {
        return Transaction::selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income, SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
            ->where('user_id', $userId)
            ->where('transaction_date', '>=', now()->startOfMonth()->subMonths($months - 1))
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->orderByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->get()
            ->map(function ($row) {
                $net = (float) $row->income - (float) $row->expense;

                return [
                    'period' => sprintf('%d-%02d', $row->year, $row->month),
                    'income' => (float) $row->income,
                    'expense' => (float) $row->expense,
                    'net' => $net,
                ];
            });
    }
}
