<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $currentMonth = $validated['month'] ?? now()->month;
        $currentYear = $validated['year'] ?? now()->year;
        $periodDate = Carbon::createFromDate($currentYear, $currentMonth, 1);

        // Calculate total income and expense for current month
        $totalIncome = Transaction::where('type', 'income')
            ->whereYear('transaction_date', $currentYear)
            ->whereMonth('transaction_date', $currentMonth)
            ->sum('amount');

        $totalExpense = Transaction::where('type', 'expense')
            ->whereYear('transaction_date', $currentYear)
            ->whereMonth('transaction_date', $currentMonth)
            ->sum('amount');

        $netBalance = $totalIncome - $totalExpense;

        // Income distribution by category
        $incomeCategories = DB::select("
            SELECT c.name, SUM(t.amount) as total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.type = 'income'
            AND t.user_id = ?
            AND c.user_id = ?
            AND YEAR(t.transaction_date) = ?
            AND MONTH(t.transaction_date) = ?
            GROUP BY c.name
        ", [auth()->id(), auth()->id(), $currentYear, $currentMonth]);

        // Expense distribution by category
        $expenseCategories = DB::select("
            SELECT c.name, SUM(t.amount) as total
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.type = 'expense'
            AND t.user_id = ?
            AND c.user_id = ?
            AND YEAR(t.transaction_date) = ?
            AND MONTH(t.transaction_date) = ?
            GROUP BY c.name
        ", [auth()->id(), auth()->id(), $currentYear, $currentMonth]);

        // Cash flow for the last 12 months
        $cashFlow = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $income = Transaction::where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $expense = Transaction::where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $cashFlow[] = [
                'month' => $date->format('M Y'),
                'net' => $income - $expense,
            ];
        }

        // Cash Flow Projections
        $cashFlowProjections = $this->calculateCashFlowProjections();

        // Burn Rate Calculations
        $burnRate = $this->calculateBurnRate();

        // Cash Runway Predictions
        $cashRunway = $this->calculateCashRunway();

        // Emergency Fund Tracking
        $emergencyFund = $this->calculateEmergencyFund();

        $debtHealth = $this->calculateDebtHealth($totalIncome, $totalExpense);

        // Account Balances Summary
        $accountBalances = Account::active()->get();

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'netBalance',
            'incomeCategories',
            'expenseCategories',
            'cashFlow',
            'cashFlowProjections',
            'burnRate',
            'cashRunway',
            'emergencyFund',
            'debtHealth',
            'accountBalances',
            'periodDate'
        ));
    }

    private function calculateCashFlowProjections()
    {
        $projections = [];

        // Daily projections for next 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            // Get average daily income/expense based on historical data
            $avgDailyIncome = $this->getAverageDailyAmount('income', $dayOfWeek);
            $avgDailyExpense = $this->getAverageDailyAmount('expense', $dayOfWeek);

            $projections[] = [
                'date' => $date->format('M d'),
                'projected_income' => $avgDailyIncome,
                'projected_expense' => $avgDailyExpense,
                'projected_net' => $avgDailyIncome - $avgDailyExpense,
            ];
        }

        // Weekly projections for next 12 weeks
        $weeklyProjections = [];
        for ($i = 0; $i < 12; $i++) {
            $weekStart = now()->addWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $avgWeeklyIncome = $this->getAverageWeeklyAmount('income');
            $avgWeeklyExpense = $this->getAverageWeeklyAmount('expense');

            $weeklyProjections[] = [
                'week' => 'Week '.($i + 1),
                'period' => $weekStart->format('M d').' - '.$weekEnd->format('M d'),
                'projected_income' => $avgWeeklyIncome,
                'projected_expense' => $avgWeeklyExpense,
                'projected_net' => $avgWeeklyIncome - $avgWeeklyExpense,
            ];
        }

        // Monthly projections for next 6 months
        $monthlyProjections = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->addMonths($i);

            $avgMonthlyIncome = $this->getAverageMonthlyAmount('income');
            $avgMonthlyExpense = $this->getAverageMonthlyAmount('expense');

            $monthlyProjections[] = [
                'month' => $month->format('M Y'),
                'projected_income' => $avgMonthlyIncome,
                'projected_expense' => $avgMonthlyExpense,
                'projected_net' => $avgMonthlyIncome - $avgMonthlyExpense,
            ];
        }

        return [
            'daily' => $projections,
            'weekly' => $weeklyProjections,
            'monthly' => $monthlyProjections,
        ];
    }

    private function calculateDebtHealth(float $monthlyIncome, float $monthlyExpense): array
    {
        $totalDebtBalance = Debt::sum('current_balance');
        $totalMinPayment = Debt::sum('minimum_payment');

        $expenseToIncome = $monthlyIncome > 0 ? ($monthlyExpense / $monthlyIncome) * 100 : 0;
        $debtToIncome = $monthlyIncome > 0 ? ($totalMinPayment / $monthlyIncome) * 100 : 0;
        $savingsRate = $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpense) / $monthlyIncome) * 100 : 0;

        $score = 100;
        if ($savingsRate < 20) {
            $score -= 10;
        }
        if ($expenseToIncome > 80) {
            $score -= 15;
        } elseif ($expenseToIncome > 70) {
            $score -= 8;
        }
        if ($debtToIncome > 40) {
            $score -= 20;
        } elseif ($debtToIncome > 30) {
            $score -= 12;
        }

        $score = max(0, min(100, round($score)));

        return [
            'total_debt_balance' => $totalDebtBalance,
            'total_min_payment' => $totalMinPayment,
            'expense_to_income' => round($expenseToIncome, 1),
            'debt_to_income' => round($debtToIncome, 1),
            'savings_rate' => round($savingsRate, 1),
            'score' => $score,
        ];
    }

    private function calculateBurnRate()
    {
        // Calculate monthly burn rate (negative cash flow)
        $last6Months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $income = Transaction::where('type', 'income')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $expense = Transaction::where('type', 'expense')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $net = $income - $expense;
            $last6Months[] = [
                'month' => $date->format('M Y'),
                'net_cash_flow' => $net,
                'burn_rate' => $net < 0 ? abs($net) : 0,
            ];
        }

        // Average monthly burn rate
        $totalBurn = array_sum(array_column($last6Months, 'burn_rate'));
        $avgMonthlyBurnRate = $totalBurn / 6;

        // Current burn rate (last month)
        $currentBurnRate = end($last6Months)['burn_rate'];

        return [
            'monthly_history' => $last6Months,
            'average_monthly' => $avgMonthlyBurnRate,
            'current' => $currentBurnRate,
            'trend' => $this->calculateTrend($last6Months, 'burn_rate'),
        ];
    }

    private function calculateCashRunway()
    {
        // Get total cash available (sum of all account balances)
        $totalCash = Account::active()->sum('balance');

        // Get average monthly burn rate
        $burnRate = $this->calculateBurnRate()['average_monthly'];

        if ($burnRate > 0) {
            $runwayMonths = $totalCash / $burnRate;
            $runwayDays = $runwayMonths * 30; // Approximate
        } else {
            $runwayMonths = null; // Infinite runway if positive cash flow
            $runwayDays = null;
        }

        // Calculate runway based on different scenarios
        $scenarios = [
            'conservative' => $burnRate * 1.2, // 20% higher burn rate
            'optimistic' => $burnRate * 0.8,   // 20% lower burn rate
            'current' => $burnRate,
        ];

        $runwayScenarios = [];
        foreach ($scenarios as $scenario => $rate) {
            if ($rate > 0) {
                $months = $totalCash / $rate;
                $runwayScenarios[$scenario] = [
                    'months' => round($months, 1),
                    'days' => round($months * 30),
                    'date' => $rate > 0 ? now()->addMonths($months)->format('M Y') : 'Never',
                ];
            } else {
                $runwayScenarios[$scenario] = [
                    'months' => 'Infinite',
                    'days' => 'Infinite',
                    'date' => 'Never',
                ];
            }
        }

        return [
            'total_cash' => $totalCash,
            'average_burn_rate' => $burnRate,
            'runway_months' => $runwayMonths ? round($runwayMonths, 1) : null,
            'runway_days' => $runwayDays ? round($runwayDays) : null,
            'estimated_date' => $runwayMonths ? now()->addMonths($runwayMonths)->format('M Y') : 'Never',
            'scenarios' => $runwayScenarios,
        ];
    }

    private function calculateEmergencyFund()
    {
        // Get total emergency fund (accounts marked as savings or emergency)
        $emergencyAccounts = Account::active()
            ->whereIn('type', ['savings', 'bank'])
            ->where('name', 'like', '%emergency%')
            ->orWhere('name', 'like', '%darurat%')
            ->get();

        $totalEmergencyFund = $emergencyAccounts->sum('balance');

        // Calculate recommended emergency fund (3-6 months of expenses)
        $avgMonthlyExpense = $this->getAverageMonthlyAmount('expense');
        $recommendedMin = $avgMonthlyExpense * 3; // 3 months minimum
        $recommendedMax = $avgMonthlyExpense * 6; // 6 months maximum

        // Calculate coverage percentage
        $coveragePercentage = $avgMonthlyExpense > 0 ? ($totalEmergencyFund / $avgMonthlyExpense) * 100 : 0;

        // Determine status
        if ($totalEmergencyFund >= $recommendedMax) {
            $status = 'excellent';
            $statusText = 'Dana darurat sangat memadai';
        } elseif ($totalEmergencyFund >= $recommendedMin) {
            $status = 'good';
            $statusText = 'Dana darurat memadai';
        } elseif ($totalEmergencyFund > 0) {
            $status = 'warning';
            $statusText = 'Dana darurat kurang';
        } else {
            $status = 'danger';
            $statusText = 'Tidak ada dana darurat';
        }

        return [
            'total_amount' => $totalEmergencyFund,
            'recommended_min' => $recommendedMin,
            'recommended_max' => $recommendedMax,
            'coverage_months' => $avgMonthlyExpense > 0 ? round($totalEmergencyFund / $avgMonthlyExpense, 1) : 0,
            'coverage_percentage' => round($coveragePercentage, 1),
            'status' => $status,
            'status_text' => $statusText,
            'accounts' => $emergencyAccounts,
        ];
    }

    private function getAverageDailyAmount($type, $dayOfWeek)
    {
        // Get transactions for the same day of week over last 3 months
        $amounts = [];
        for ($i = 0; $i < 12; $i++) { // Last 12 weeks
            $date = now()->subWeeks($i);
            if ($date->dayOfWeek === $dayOfWeek) {
                $amount = Transaction::where('type', $type)
                    ->whereDate('transaction_date', $date->toDateString())
                    ->sum('amount');
                $amounts[] = $amount;
            }
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0;
    }

    private function getAverageWeeklyAmount($type)
    {
        $amounts = [];
        for ($i = 0; $i < 12; $i++) { // Last 12 weeks
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $amount = Transaction::where('type', $type)
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount');
            $amounts[] = $amount;
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0;
    }

    private function getAverageMonthlyAmount($type)
    {
        $amounts = [];
        for ($i = 0; $i < 6; $i++) { // Last 6 months
            $date = now()->subMonths($i);
            $amount = Transaction::where('type', $type)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
            $amounts[] = $amount;
        }

        return count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0;
    }

    private function calculateTrend($data, $field)
    {
        if (count($data) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($data, 0, count($data) / 2);
        $secondHalf = array_slice($data, count($data) / 2);

        $firstAvg = array_sum(array_column($firstHalf, $field)) / count($firstHalf);
        $secondAvg = array_sum(array_column($secondHalf, $field)) / count($secondHalf);

        $change = (($secondAvg - $firstAvg) / ($firstAvg ?: 1)) * 100;

        if ($change > 10) {
            return 'increasing';
        }
        if ($change < -10) {
            return 'decreasing';
        }

        return 'stable';
    }
}
