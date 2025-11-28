<?php

namespace App\Services\Dashboard;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;

class DashboardDataService
{
    public function getMonthlyTotals(int $userId, Carbon $period): array
    {
        $periodStart = $period->copy()->startOfMonth();
        $periodEnd = $period->copy()->endOfMonth();

        $income = Transaction::where('type', 'income')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$periodStart, $periodEnd])
            ->sum('amount');

        $expense = Transaction::where('type', 'expense')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$periodStart, $periodEnd])
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
        ];
    }

    public function getCategoryDistribution(int $userId, Carbon $period, string $type): BaseCollection
    {
        $periodStart = $period->copy()->startOfMonth();
        $periodEnd = $period->copy()->endOfMonth();

        return DB::table('transactions as t')
            ->selectRaw('c.name, SUM(t.amount) as total')
            ->join('categories as c', 't.category_id', '=', 'c.id')
            ->where('t.type', $type)
            ->where('t.user_id', $userId)
            ->where('c.user_id', $userId)
            ->whereBetween('t.transaction_date', [$periodStart, $periodEnd])
            ->groupBy('c.name')
            ->get();
    }

    public function getCashFlowHistory(int $userId, int $months = 12): array
    {
        $cashFlow = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $income = Transaction::where('type', 'income')
                ->where('user_id', $userId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $expense = Transaction::where('type', 'expense')
                ->where('user_id', $userId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $cashFlow[] = [
                'month' => $date->format('M Y'),
                'net' => $income - $expense,
            ];
        }

        return $cashFlow;
    }

    public function getMonthlyCashFlow(int $userId, Carbon $period): float
    {
        $periodStart = $period->copy()->startOfMonth();
        $periodEnd = $period->copy()->endOfMonth();

        $income = Transaction::where('type', 'income')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$periodStart, $periodEnd])
            ->sum('amount');

        $expense = Transaction::where('type', 'expense')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$periodStart, $periodEnd])
            ->sum('amount');

        return $income - $expense;
    }

    public function getAccountBalances(int $userId): Collection
    {
        return Account::active()->where('user_id', $userId)->get();
    }

    public function getEmergencyAccounts(int $userId): Collection
    {
        return Account::active()
            ->where('user_id', $userId)
            ->whereIn('type', ['savings', 'bank'])
            ->where(function ($query) {
                $query->where('name', 'like', '%emergency%')
                    ->orWhere('name', 'like', '%darurat%');
            })
            ->get();
    }

    public function getTotalCash(int $userId): float
    {
        return Account::active()->where('user_id', $userId)->sum('balance');
    }

    public function getAverageDailyAmount(string $type, int $userId, int $dayOfWeek): float
    {
        $amounts = [];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subWeeks($i);
            if ($date->dayOfWeek === $dayOfWeek) {
                $amounts[] = Transaction::where('type', $type)
                    ->where('user_id', $userId)
                    ->whereDate('transaction_date', $date->toDateString())
                    ->sum('amount');
            }
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0.0;
    }

    public function getAverageWeeklyAmount(string $type, int $userId): float
    {
        $amounts = [];

        for ($i = 0; $i < 12; $i++) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $amounts[] = Transaction::where('type', $type)
                ->where('user_id', $userId)
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount');
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0.0;
    }

    public function getAverageMonthlyAmount(string $type, int $userId, int $months = 6): float
    {
        $amounts = [];

        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($i);

            $amounts[] = Transaction::where('type', $type)
                ->where('user_id', $userId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0.0;
    }
}
