<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\Debt;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;

class NetWorthController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Calculate Assets
        $totalCash = Account::where('user_id', $userId)->active()->sum('balance');
        $totalInvestments = Investment::withoutGlobalScopes()->where('user_id', $userId)->sum(DB::raw('quantity * current_price'));
        $totalAssets = Asset::withoutGlobalScopes()->where('user_id', $userId)->sum('current_value');

        // Calculate Liabilities
        $totalDebts = Debt::where('user_id', $userId)->where('status', 'active')->sum('current_balance');

        // Net Worth
        $netWorth = $totalCash + $totalInvestments + $totalAssets - $totalDebts;

        // Asset Breakdown
        $assetBreakdown = [
            'cash' => $totalCash,
            'investments' => $totalInvestments,
            'physical_assets' => $totalAssets,
        ];

        // Liability Breakdown
        $liabilityBreakdown = [
            'debts' => $totalDebts,
        ];

        // Net Worth History (last 12 months)
        $netWorthHistory = $this->calculateNetWorthHistory();

        // Financial Health Score
        $healthScore = $this->calculateFinancialHealthScore($netWorth, $totalDebts, $totalAssets);

        // Asset Allocation
        $assetAllocation = $this->calculateAssetAllocation($assetBreakdown, $netWorth);

        // Debt-to-Asset Ratio
        $debtToAssetRatio = $totalAssets > 0 ? ($totalDebts / $totalAssets) * 100 : 0;

        // Savings Rate
        $savingsRate = $this->calculateSavingsRate();

        // Emergency Fund Ratio
        $emergencyFundRatio = $this->calculateEmergencyFundRatio();

        return view('net-worth.index', compact(
            'netWorth',
            'assetBreakdown',
            'liabilityBreakdown',
            'netWorthHistory',
            'healthScore',
            'assetAllocation',
            'debtToAssetRatio',
            'savingsRate',
            'emergencyFundRatio'
        ));
    }

    private function calculateNetWorthHistory()
    {
        $history = [];
        $userId = auth()->id();

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            // Get data as of that month (simplified - in real app would need historical snapshots)
            $cash = Account::where('user_id', $userId)->active()->sum('balance');
            $investments = Investment::withoutGlobalScopes()->where('user_id', $userId)->sum(DB::raw('quantity * current_price'));
            $assets = Asset::withoutGlobalScopes()->where('user_id', $userId)->sum('current_value');
            $debts = Debt::where('user_id', $userId)->where('status', 'active')->sum('current_balance');

            $netWorth = $cash + $investments + $assets - $debts;

            $history[] = [
                'month' => $date->format('M Y'),
                'net_worth' => $netWorth,
                'assets' => $cash + $investments + $assets,
                'liabilities' => $debts,
            ];
        }

        return $history;
    }

    private function calculateFinancialHealthScore($netWorth, $totalDebts, $totalAssets)
    {
        $score = 0;
        $maxScore = 100;

        // Net Worth Score (30 points)
        if ($netWorth > 100000000) { // > 100M IDR
            $score += 30;
        } elseif ($netWorth > 50000000) { // > 50M IDR
            $score += 25;
        } elseif ($netWorth > 10000000) { // > 10M IDR
            $score += 20;
        } elseif ($netWorth > 0) {
            $score += 15;
        }

        // Debt-to-Asset Ratio Score (25 points)
        $debtRatio = $totalAssets > 0 ? ($totalDebts / $totalAssets) * 100 : 0;
        if ($debtRatio < 20) {
            $score += 25;
        } elseif ($debtRatio < 40) {
            $score += 20;
        } elseif ($debtRatio < 60) {
            $score += 15;
        } elseif ($debtRatio < 80) {
            $score += 10;
        }

        // Asset Diversity Score (20 points)
        $assetTypes = 0;
        if (Account::where('user_id', auth()->id())->active()->count() > 0) {
            $assetTypes++;
        }
        if (Investment::withoutGlobalScopes()->where('user_id', auth()->id())->count() > 0) {
            $assetTypes++;
        }
        if (Asset::withoutGlobalScopes()->where('user_id', auth()->id())->count() > 0) {
            $assetTypes++;
        }

        $score += min($assetTypes * 7, 20); // Max 20 points for 3+ asset types

        // Emergency Fund Score (15 points)
        $emergencyRatio = $this->calculateEmergencyFundRatio();
        if ($emergencyRatio >= 6) {
            $score += 15;
        } elseif ($emergencyRatio >= 3) {
            $score += 10;
        } elseif ($emergencyRatio >= 1) {
            $score += 5;
        }

        // Savings Rate Score (10 points)
        $savingsRate = $this->calculateSavingsRate();
        if ($savingsRate >= 20) {
            $score += 10;
        } elseif ($savingsRate >= 10) {
            $score += 7;
        } elseif ($savingsRate >= 5) {
            $score += 5;
        }

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100, 1),
            'grade' => $this->getHealthGrade($score),
        ];
    }

    private function calculateAssetAllocation($assetBreakdown, $netWorth)
    {
        $allocation = [];
        $totalAssets = array_sum($assetBreakdown);

        foreach ($assetBreakdown as $type => $value) {
            $allocation[$type] = [
                'amount' => $value,
                'percentage' => $totalAssets > 0 ? round(($value / $totalAssets) * 100, 1) : 0,
                'label' => ucfirst(str_replace('_', ' ', $type)),
            ];
        }

        return $allocation;
    }

    private function calculateSavingsRate()
    {
        // Calculate average monthly savings over last 6 months
        $monthlyData = [];
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $income = DB::table('transactions')
                ->where('user_id', auth()->id())
                ->where('type', 'income')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $expense = DB::table('transactions')
                ->where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $savings = $income - $expense;
            $monthlyData[] = $income > 0 ? ($savings / $income) * 100 : 0;
        }

        return count($monthlyData) > 0 ? round(array_sum($monthlyData) / count($monthlyData), 1) : 0;
    }

    private function calculateEmergencyFundRatio()
    {
        // Get emergency fund accounts
        $emergencyAccounts = Account::where('user_id', auth()->id())
            ->active()
            ->where(function ($query) {
                $query->whereIn('type', ['savings', 'bank'])
                    ->where('name', 'like', '%emergency%')
                    ->orWhere('name', 'like', '%darurat%');
            })
            ->sum('balance');

        // Get average monthly expenses
        $avgMonthlyExpense = 0;
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $expense = DB::table('transactions')
                ->where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
            $avgMonthlyExpense += $expense;
        }
        $avgMonthlyExpense = $avgMonthlyExpense / 6;

        return $avgMonthlyExpense > 0 ? round($emergencyAccounts / $avgMonthlyExpense, 1) : 0;
    }

    private function getHealthGrade($score)
    {
        if ($score >= 90) {
            return 'Excellent';
        }
        if ($score >= 80) {
            return 'Very Good';
        }
        if ($score >= 70) {
            return 'Good';
        }
        if ($score >= 60) {
            return 'Fair';
        }
        if ($score >= 50) {
            return 'Poor';
        }

        return 'Critical';
    }
}
