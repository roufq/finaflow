<?php

namespace App\Services\Dashboard;

use App\Models\Debt;

class DashboardCalculationService
{
    public function __construct(private DashboardDataService $dataService) {}

    public function calculateCashFlowProjections(int $userId): array
    {
        $dailyProjections = [];
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            $avgDailyIncome = $this->dataService->getAverageDailyAmount('income', $userId, $dayOfWeek);
            $avgDailyExpense = $this->dataService->getAverageDailyAmount('expense', $userId, $dayOfWeek);

            $dailyProjections[] = [
                'date' => $date->format('M d'),
                'projected_income' => $avgDailyIncome,
                'projected_expense' => $avgDailyExpense,
                'projected_net' => $avgDailyIncome - $avgDailyExpense,
            ];
        }

        $weeklyProjections = [];
        for ($i = 0; $i < 12; $i++) {
            $weekStart = now()->addWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $avgWeeklyIncome = $this->dataService->getAverageWeeklyAmount('income', $userId);
            $avgWeeklyExpense = $this->dataService->getAverageWeeklyAmount('expense', $userId);

            $weeklyProjections[] = [
                'week' => 'Week '.($i + 1),
                'period' => $weekStart->format('M d').' - '.$weekEnd->format('M d'),
                'projected_income' => $avgWeeklyIncome,
                'projected_expense' => $avgWeeklyExpense,
                'projected_net' => $avgWeeklyIncome - $avgWeeklyExpense,
            ];
        }

        $monthlyProjections = [];
        for ($i = 0; $i < 6; $i++) {
            $month = now()->addMonths($i);

            $avgMonthlyIncome = $this->dataService->getAverageMonthlyAmount('income', $userId);
            $avgMonthlyExpense = $this->dataService->getAverageMonthlyAmount('expense', $userId);

            $monthlyProjections[] = [
                'month' => $month->format('M Y'),
                'projected_income' => $avgMonthlyIncome,
                'projected_expense' => $avgMonthlyExpense,
                'projected_net' => $avgMonthlyIncome - $avgMonthlyExpense,
            ];
        }

        return [
            'daily' => $dailyProjections,
            'weekly' => $weeklyProjections,
            'monthly' => $monthlyProjections,
        ];
    }

    public function calculateBurnRate(int $userId): array
    {
        $last6Months = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $net = $this->dataService->getMonthlyCashFlow($userId, $date);

            $last6Months[] = [
                'month' => $date->format('M Y'),
                'net_cash_flow' => $net,
                'burn_rate' => $net < 0 ? abs($net) : 0.0,
            ];
        }

        $totalBurn = array_sum(array_column($last6Months, 'burn_rate'));
        $avgMonthlyBurnRate = $totalBurn / max(count($last6Months), 1);
        $currentBurnRate = end($last6Months)['burn_rate'] ?? 0.0;

        return [
            'monthly_history' => $last6Months,
            'average_monthly' => $avgMonthlyBurnRate,
            'current' => $currentBurnRate,
            'trend' => $this->calculateTrend($last6Months, 'burn_rate'),
        ];
    }

    public function calculateCashRunway(int $userId, float $totalCash, float $averageMonthlyBurnRate): array
    {
        $runwayMonths = $averageMonthlyBurnRate > 0 ? $totalCash / $averageMonthlyBurnRate : null;
        $runwayDays = $runwayMonths ? $runwayMonths * 30 : null;

        $scenarios = [
            'conservative' => $averageMonthlyBurnRate * 1.2,
            'optimistic' => $averageMonthlyBurnRate * 0.8,
            'current' => $averageMonthlyBurnRate,
        ];

        $runwayScenarios = [];
        foreach ($scenarios as $scenario => $rate) {
            if ($rate > 0) {
                $months = $totalCash / $rate;
                $runwayScenarios[$scenario] = [
                    'months' => round($months, 1),
                    'days' => round($months * 30),
                    'date' => now()->addDays($months * 30)->format('M Y'),
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
            'average_burn_rate' => $averageMonthlyBurnRate,
            'runway_months' => $runwayMonths ? round($runwayMonths, 1) : null,
            'runway_days' => $runwayDays ? round($runwayDays) : null,
            'estimated_date' => $runwayMonths ? now()->addDays($runwayMonths * 30)->format('M Y') : 'Never',
            'scenarios' => $runwayScenarios,
        ];
    }

    public function calculateEmergencyFund(int $userId, float $avgMonthlyExpense): array
    {
        $emergencyAccounts = $this->dataService->getEmergencyAccounts($userId);
        $totalEmergencyFund = $emergencyAccounts->sum('balance');

        $recommendedMin = $avgMonthlyExpense * 3;
        $recommendedMax = $avgMonthlyExpense * 6;
        $coveragePercentage = $avgMonthlyExpense > 0 ? ($totalEmergencyFund / $avgMonthlyExpense) * 100 : 0.0;

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

    public function calculateDebtHealth(float $monthlyIncome, float $monthlyExpense, int $userId): array
    {
        $totalDebtBalance = Debt::where('user_id', $userId)->sum('current_balance');
        $totalMinPayment = Debt::where('user_id', $userId)->sum('minimum_payment');

        $safeIncome = $monthlyIncome > 0 ? $monthlyIncome : 1;
        $expenseToIncome = ($monthlyExpense / $safeIncome) * 100;
        $debtToIncome = ($totalMinPayment / $safeIncome) * 100;
        $savingsRate = (($monthlyIncome - $monthlyExpense) / $safeIncome) * 100;

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

    private function calculateTrend(array $data, string $field): string
    {
        if (count($data) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($data, 0, count($data) / 2);
        $secondHalf = array_slice($data, count($data) / 2);

        $firstAvg = array_sum(array_column($firstHalf, $field)) / max(count($firstHalf), 1);
        $secondAvg = array_sum(array_column($secondHalf, $field)) / max(count($secondHalf), 1);

        $safeFirst = $firstAvg === 0.0 ? 1.0 : $firstAvg;
        $change = (($secondAvg - $firstAvg) / $safeFirst) * 100;

        if ($change > 10) {
            return 'increasing';
        }

        if ($change < -10) {
            return 'decreasing';
        }

        return 'stable';
    }
}
