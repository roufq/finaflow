<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Anomaly;
use App\Models\Prediction;
use App\Models\Recommendation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AIInsightsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $recommendations = Recommendation::where('user_id', $userId)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $anomalies = Anomaly::where('user_id', $userId)
            ->unresolved()
            ->orderBy('severity', 'desc')
            ->take(5)
            ->get();

        $predictions = Prediction::where('user_id', $userId)
            ->where('prediction_date', '>=', now())
            ->orderBy('prediction_date', 'asc')
            ->take(5)
            ->get();

        return view('insights.index', compact('recommendations', 'anomalies', 'predictions'));
    }

    public function recommendations()
    {
        $recommendations = Recommendation::where('user_id', Auth::id())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('insights.recommendations', compact('recommendations'));
    }

    public function markRecommendationRead(Recommendation $recommendation)
    {
        if ($recommendation->user_id !== Auth::id()) {
            abort(403);
        }

        $recommendation->markAsRead();

        return redirect()->back()->with('success', __('insights.messages.recommendation_marked_read'));
    }

    public function anomalies()
    {
        $anomalies = Anomaly::where('user_id', Auth::id())
            ->orderBy('severity', 'desc')
            ->orderBy('detected_at', 'desc')
            ->paginate(10);

        return view('insights.anomalies', compact('anomalies'));
    }

    public function resolveAnomaly(Anomaly $anomaly)
    {
        if ($anomaly->user_id !== Auth::id()) {
            abort(403);
        }

        $anomaly->markAsResolved();

        return redirect()->back()->with('success', __('insights.messages.anomaly_resolved'));
    }

    public function predictions()
    {
        $predictions = Prediction::where('user_id', Auth::id())
            ->orderBy('prediction_date', 'asc')
            ->paginate(10);

        return view('insights.predictions', compact('predictions'));
    }

    public function generateInsights(Request $request)
    {
        $userId = Auth::id();

        try {
            // Generate recommendations based on user data
            $this->generateRecommendations($userId);

            // Detect anomalies in transactions
            $this->detectAnomalies($userId);

            // Generate spending predictions
            $this->generatePredictions($userId);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('insights.messages.insights_generated'),
                ]);
            }

            return back()->with('success', __('insights.messages.insights_generated'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate insights: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->route('insights.index')->with('error', 'Failed to generate insights: '.$e->getMessage());
        }
    }

    private function generateRecommendations($userId)
    {
        // Clear old recommendations
        Recommendation::where('user_id', $userId)->delete();

        $recommendations = [];

        // Check savings rate
        $savingsRate = $this->calculateSavingsRate($userId);
        if ($savingsRate < 0.1) { // Less than 10%
            $recommendations[] = [
                'type' => 'saving',
                'content' => __('insights.recommendations.low_savings_rate'),
                'priority' => 3,
                'metadata' => ['savings_rate' => $savingsRate],
            ];
        }

        // Check emergency fund
        $emergencyFund = $this->calculateEmergencyFund($userId);
        if ($emergencyFund < 3) { // Less than 3 months
            $recommendations[] = [
                'type' => 'saving',
                'content' => __('insights.recommendations.build_emergency_fund'),
                'priority' => 3,
                'metadata' => ['months_covered' => $emergencyFund],
            ];
        }

        // Check debt-to-income ratio
        $dtiRatio = $this->calculateDebtToIncomeRatio($userId);
        if ($dtiRatio > 0.4) { // More than 40%
            $recommendations[] = [
                'type' => 'debt',
                'content' => __('insights.recommendations.high_debt_ratio'),
                'priority' => 2,
                'metadata' => ['dti_ratio' => $dtiRatio],
            ];
        }

        // Check budget adherence
        $budgetAdherence = $this->calculateBudgetAdherence($userId);
        if ($budgetAdherence > 1.1) { // Over budget by 10%
            $recommendations[] = [
                'type' => 'budgeting',
                'content' => __('insights.recommendations.overspending'),
                'priority' => 2,
                'metadata' => ['budget_adherence' => $budgetAdherence],
            ];
        }

        // Create recommendations
        foreach ($recommendations as $rec) {
            Recommendation::create(array_merge($rec, ['user_id' => $userId]));
        }
    }

    private function detectAnomalies($userId)
    {
        // Clear old anomalies
        Anomaly::where('user_id', $userId)->delete();

        $anomalies = [];

        // Get recent transactions
        $transactions = Transaction::where('user_id', $userId)
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Detect unusual amounts
        $avgAmount = $transactions->avg('amount');
        $amounts = $transactions->pluck('amount');
        $stdDev = sqrt($amounts->map(function ($amount) use ($avgAmount) {
            return pow($amount - $avgAmount, 2);
        })->avg());

        foreach ($transactions as $transaction) {
            if (abs($transaction->amount - $avgAmount) > 2 * $stdDev && $transaction->amount > $avgAmount) {
                $anomalies[] = [
                    'transaction_id' => $transaction->id,
                    'anomaly_type' => 'unusual_amount',
                    'description' => __('insights.anomalies.unusual_amount', [
                        'amount' => number_format($transaction->amount, 0),
                        'category' => $transaction->category->name ?? 'Unknown',
                    ]),
                    'severity' => $transaction->amount > $avgAmount * 3 ? 3 : 2,
                    'metadata' => [
                        'transaction_amount' => $transaction->amount,
                        'average_amount' => $avgAmount,
                    ],
                    'detected_at' => now(),
                ];
            }
        }

        // Detect duplicate transactions
        $duplicates = $transactions->groupBy(function ($transaction) {
            return $transaction->amount.'-'.$transaction->description.'-'.$transaction->transaction_date->format('Y-m-d');
        })->filter(function ($group) {
            return $group->count() > 1;
        });

        foreach ($duplicates as $group) {
            $first = $group->first();
            $anomalies[] = [
                'transaction_id' => $first->id,
                'anomaly_type' => 'duplicate',
                'description' => __('insights.anomalies.duplicate_transaction', [
                    'description' => $first->description,
                    'count' => $group->count(),
                ]),
                'severity' => 1,
                'metadata' => ['duplicate_count' => $group->count()],
                'detected_at' => now(),
            ];
        }

        // Create anomalies
        foreach ($anomalies as $anomaly) {
            Anomaly::create(array_merge($anomaly, ['user_id' => $userId]));
        }
    }

    private function generatePredictions($userId)
    {
        // Clear old predictions
        Prediction::where('user_id', $userId)->delete();

        $predictions = [];

        // Get last 6 months of expenses by category
        $categoryExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        foreach ($categoryExpenses as $expense) {
            if ($expense->count >= 3) { // Need at least 3 transactions for prediction
                $avgMonthly = $expense->total / 6;
                $confidence = min(0.9, $expense->count / 10); // Higher confidence with more data

                $predictions[] = [
                    'category' => $expense->category->name ?? 'Unknown',
                    'predicted_amount' => $avgMonthly,
                    'confidence' => $confidence,
                    'period' => 'monthly',
                    'prediction_date' => now()->addMonth(),
                    'factors' => [
                        'historical_average' => $avgMonthly,
                        'transaction_count' => $expense->count,
                        'period_months' => 6,
                    ],
                ];
            }
        }

        // Create predictions
        foreach ($predictions as $prediction) {
            Prediction::create(array_merge($prediction, ['user_id' => $userId]));
        }
    }

    private function calculateSavingsRate($userId)
    {
        $income = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->sum('amount');

        $expenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->sum('amount');

        return $income > 0 ? ($income - $expenses) / $income : 0;
    }

    private function calculateEmergencyFund($userId)
    {
        $emergencySavings = Account::where('user_id', $userId)
            ->where('type', 'savings')
            ->sum('balance');

        $monthlyExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->sum('amount') / 3;

        return $monthlyExpenses > 0 ? $emergencySavings / $monthlyExpenses : 0;
    }

    private function calculateDebtToIncomeRatio($userId)
    {
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->sum('amount') / 3;

        $debtPayments = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('category_id', function ($query) {
                $query->select('id')
                    ->from('categories')
                    ->where('name', 'Debt Payment');
            })
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->sum('amount') / 3;

        return $monthlyIncome > 0 ? $debtPayments / $monthlyIncome : 0;
    }

    private function calculateBudgetAdherence($userId)
    {
        // This is a simplified calculation - in reality you'd compare against actual budgets
        $currentMonthExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $avgMonthlyExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->sum('amount') / 6;

        return $avgMonthlyExpenses > 0 ? $currentMonthExpenses / $avgMonthlyExpenses : 1;
    }
}
