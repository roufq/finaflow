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

        $income = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'income')
            ->where('t.user_id', $userId)
            ->whereBetween('t.transaction_date', [$periodStart, $periodEnd])
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0;

        $expense = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'expense')
            ->where('t.user_id', $userId)
            ->whereBetween('t.transaction_date', [$periodStart, $periodEnd])
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0;

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'net' => (float) ($income - $expense),
        ];
    }

    public function getCategoryDistribution(int $userId, Carbon $period, string $type): BaseCollection
    {
        $periodStart = $period->copy()->startOfMonth();
        $periodEnd = $period->copy()->endOfMonth();

        return DB::table('transactions as t')
            ->join('categories as c', 't.category_id', '=', 'c.id')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->selectRaw('c.name, SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
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

            $income = DB::table('transactions as t')
                ->join('accounts as a', 't.account_id', '=', 'a.id')
                ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
                ->where('t.user_id', $userId)
                ->where('t.type', 'income')
                ->whereYear('t.transaction_date', $date->year)
                ->whereMonth('t.transaction_date', $date->month)
                ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
                ->value('total') ?? 0;

            $expense = DB::table('transactions as t')
                ->join('accounts as a', 't.account_id', '=', 'a.id')
                ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
                ->where('t.user_id', $userId)
                ->where('t.type', 'expense')
                ->whereYear('t.transaction_date', $date->year)
                ->whereMonth('t.transaction_date', $date->month)
                ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
                ->value('total') ?? 0;

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

        $income = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'income')
            ->where('t.user_id', $userId)
            ->whereBetween('t.transaction_date', [$periodStart, $periodEnd])
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0;

        $expense = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'expense')
            ->where('t.user_id', $userId)
            ->whereBetween('t.transaction_date', [$periodStart, $periodEnd])
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0;

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
        return DB::table('accounts as a')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('a.is_active', true)
            ->where('a.user_id', $userId)
            ->selectRaw('SUM(a.balance * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;
    }

    public function getAverageDailyAmount(string $type, int $userId, int $dayOfWeek): float
    {
        $amounts = [];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subWeeks($i);
            if ($date->dayOfWeek === $dayOfWeek) {
                $amounts[] = DB::table('transactions as t')
                    ->join('accounts as a', 't.account_id', '=', 'a.id')
                    ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
                    ->where('t.type', $type)
                    ->where('t.user_id', $userId)
                    ->whereDate('t.transaction_date', $date->toDateString())
                    ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
                    ->value('total') ?? 0;
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

            $amounts[] = DB::table('transactions as t')
                ->join('accounts as a', 't.account_id', '=', 'a.id')
                ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
                ->where('t.type', $type)
                ->where('t.user_id', $userId)
                ->whereBetween('t.transaction_date', [$weekStart, $weekEnd])
                ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
                ->value('total') ?? 0;
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0.0;
    }

    public function getAverageMonthlyAmount(string $type, int $userId, int $months = 6): float
    {
        $amounts = [];

        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($i);

            $amounts[] = DB::table('transactions as t')
                ->join('accounts as a', 't.account_id', '=', 'a.id')
                ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
                ->where('t.type', $type)
                ->where('t.user_id', $userId)
                ->whereYear('t.transaction_date', $date->year)
                ->whereMonth('t.transaction_date', $date->month)
                ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
                ->value('total') ?? 0;
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0.0;
    }

    public function getRecentTransactions(int $userId, int $limit = 10): Collection
    {
        return Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
