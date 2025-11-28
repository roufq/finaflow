<?php

namespace App\Services\Dashboard;

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
            'periodDate' => $periodDate,
        ];
    }
}
