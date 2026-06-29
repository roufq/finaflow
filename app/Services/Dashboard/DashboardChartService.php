<?php

namespace App\Services\Dashboard;

use App\Models\Setting;
use Carbon\Carbon;

class DashboardChartService
{
    public function __construct(
        private DashboardDataService $dataService,
        private DashboardCalculationService $calculationService
    ) {}

    public function buildDashboardData(int $userId, Carbon $periodDate): array
    {
        $monthlyTotals = $this->dataService->getMonthlyTotals($userId, $periodDate);

        $incomeCategories = $this->dataService->getCategoryDistribution($userId, $periodDate, 'income');
        $expenseCategories = $this->dataService->getCategoryDistribution($userId, $periodDate, 'expense');

        $cashFlow = $this->dataService->getCashFlowHistory($userId);

        $cashFlowProjections = $this->calculationService->calculateCashFlowProjections($userId);
        $burnRate = $this->calculationService->calculateBurnRate($userId);

        $totalCash = $this->dataService->getTotalCash($userId);
        $cashRunway = $this->calculationService->calculateCashRunway($userId, $totalCash, $burnRate['average_monthly']);

        $avgMonthlyExpense = $this->dataService->getAverageMonthlyAmount('expense', $userId);
        $emergencyFund = $this->calculationService->calculateEmergencyFund($userId, $avgMonthlyExpense);

        $debtHealth = $this->calculationService->calculateDebtHealth(
            (float) $monthlyTotals['income'],
            (float) $monthlyTotals['expense'],
            $userId
        );

        $accountBalances = $this->dataService->getAccountBalances($userId);
        $recentTransactions = $this->dataService->getRecentTransactions($userId);

        // Multi-currency: Get the primary symbol
        $primarySetting = Setting::where('user_id', $userId)->where('is_default', true)->first()
            ?? Setting::where('user_id', $userId)->first();
        $currencySymbol = $primarySetting->currency_symbol ?? 'Rp';

        // Previous month's health metrics for trend comparison
        $lastMonth = $periodDate->copy()->subMonth();
        $lastMonthTotals = $this->dataService->getMonthlyTotals($userId, $lastMonth);
        $netWorthTrend = $lastMonthTotals['income'] > 0 ? (($monthlyTotals['income'] - $lastMonthTotals['income']) / $lastMonthTotals['income']) * 100 : 0;

        return [
            'totalIncome' => $monthlyTotals['income'],
            'totalExpense' => $monthlyTotals['expense'],
            'netBalance' => $monthlyTotals['net'],
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'cashFlow' => $cashFlow,
            'cashFlowProjections' => $cashFlowProjections,
            'burnRate' => $burnRate,
            'cashRunway' => $cashRunway,
            'emergencyFund' => $emergencyFund,
            'debtHealth' => $debtHealth,
            'accountBalances' => $accountBalances,
            'recentTransactions' => $recentTransactions,
            'netWorthTrend' => $netWorthTrend,
            'totalCash' => $totalCash,
            'periodDate' => $periodDate,
            'currencySymbol' => $currencySymbol,
        ];
    }
}
