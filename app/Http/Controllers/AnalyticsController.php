<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private array $monthlyTotalsCache = [];

    public function index()
    {
        $currentMonthExpense = $this->getCurrentMonthAmount('expense');
        $currentMonthIncome = $this->getCurrentMonthAmount('income');

        $dailySpending = $this->getDailySpending();
        $weeklySpending = $this->getWeeklySpending();
        $monthlySpending = $this->getMonthlySpending();

        $seasonalInsights = $this->getSeasonalInsights($monthlySpending);
        $peerComparison = $this->getPeerComparison();
        $spendingPredictions = $this->getSpendingPredictions($monthlySpending);

        $healthMetrics = $this->getHealthMetrics();
        $advice = $this->getPersonalizedAdvice($healthMetrics);
        $riskProfile = $healthMetrics['risk_profile'] ?? 'balanced';

        $taxInsights = $this->getTaxInsights();

        return view('analytics.index', compact(
            'currentMonthExpense',
            'currentMonthIncome',
            'dailySpending',
            'weeklySpending',
            'monthlySpending',
            'seasonalInsights',
            'peerComparison',
            'spendingPredictions',
            'healthMetrics',
            'advice',
            'riskProfile',
            'taxInsights'
        ));
    }

    private function getCurrentMonthAmount(string $type): float
    {
        $date = now();

        return (float) Transaction::where('type', $type)
            ->whereYear('transaction_date', $date->year)
            ->whereMonth('transaction_date', $date->month)
            ->sum('amount');
    }

    private function getDailySpending(): array
    {
        $startDate = now()->subDays(29)->startOfDay();

        $rows = Transaction::select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $startDate)
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get();

        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $data[$date] = 0;
        }

        foreach ($rows as $row) {
            $data[$row->date] = (float) $row->total;
        }

        $labels = [];
        $values = [];
        foreach ($data as $date => $total) {
            $labels[] = date('d M', strtotime($date));
            $values[] = $total;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getWeeklySpending(): array
    {
        $startOfWeek = now()->startOfWeek()->subWeeks(11);
        $rows = Transaction::selectRaw('YEAR(transaction_date) as year, WEEK(transaction_date, 3) as week, MIN(transaction_date) as week_start, SUM(amount) as total')
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $startOfWeek)
            ->groupByRaw('YEAR(transaction_date), WEEK(transaction_date, 3)')
            ->orderBy('week_start')
            ->get();

        $mapped = [];
        foreach ($rows as $row) {
            $mapped[sprintf('%d-%02d', $row->year, $row->week)] = (float) $row->total;
        }

        $labels = [];
        $values = [];

        for ($i = 0; $i < 12; $i++) {
            $weekStart = $startOfWeek->copy()->addWeeks($i);
            $key = sprintf('%s-%02d', $weekStart->format('o'), $weekStart->isoWeek());
            $weekEnd = $weekStart->copy()->endOfWeek();

            $labels[] = $weekStart->format('d M').' - '.$weekEnd->format('d M');
            $values[] = $mapped[$key] ?? 0.0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getMonthlySpending(): array
    {
        return $this->getMonthlyTotals('expense', 12);
    }

    private function getSeasonalInsights(array $monthlySpending): array
    {
        if (count($monthlySpending) === 0) {
            return [
                'average' => 0,
                'highest' => null,
                'lowest' => null,
                'trend' => 'stable',
            ];
        }

        $totals = array_column($monthlySpending, 'total');
        $average = count($totals) > 0 ? array_sum($totals) / count($totals) : 0;

        $highest = null;
        $lowest = null;
        foreach ($monthlySpending as $row) {
            if ($highest === null || $row['total'] > $highest['total']) {
                $highest = $row;
            }
            if ($lowest === null || $row['total'] < $lowest['total']) {
                $lowest = $row;
            }
        }

        $trend = $this->calculateTrend(
            array_map(function ($row) {
                return ['value' => $row['total']];
            }, $monthlySpending),
            'value'
        );

        return [
            'average' => $average,
            'highest' => $highest,
            'lowest' => $lowest,
            'trend' => $trend,
        ];
    }

    private function getPeerComparison(): array
    {
        $userAvg = $this->getAverageMonthlyAmount('expense');

        $globalTotals = $this->getMonthlyTotals('expense', 6, false);
        $globalValues = array_column($globalTotals, 'total');

        $globalAvg = count($globalValues) > 0 ? array_sum($globalValues) / count($globalValues) : 0;

        $difference = $globalAvg > 0 ? (($userAvg - $globalAvg) / $globalAvg) * 100 : 0;
        $relative = $difference > 10 ? 'above' : ($difference < -10 ? 'below' : 'similar');

        return [
            'user_average' => $userAvg,
            'peer_average' => $globalAvg,
            'difference_percentage' => round($difference, 1),
            'relative' => $relative,
        ];
    }

    private function getSpendingPredictions(array $monthlySpending): array
    {
        $values = array_column($monthlySpending, 'total');
        $n = count($values);

        if ($n === 0) {
            return [];
        }

        if ($n < 3) {
            $average = array_sum($values) / $n;
            $predictions = [];
            for ($i = 1; $i <= 3; $i++) {
                $date = now()->addMonths($i);
                $predictions[] = [
                    'label' => $date->format('M Y'),
                    'total' => $average,
                ];
            }

            return $predictions;
        }

        $xSum = 0;
        $ySum = 0;
        $xySum = 0;
        $x2Sum = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;
            $y = $values[$i];
            $xSum += $x;
            $ySum += $y;
            $xySum += $x * $y;
            $x2Sum += $x * $x;
        }

        $denominator = ($n * $x2Sum) - ($xSum * $xSum);
        if ($denominator == 0) {
            $average = array_sum($values) / $n;
            $predictions = [];
            for ($i = 1; $i <= 3; $i++) {
                $date = now()->addMonths($i);
                $predictions[] = [
                    'label' => $date->format('M Y'),
                    'total' => $average,
                ];
            }

            return $predictions;
        }

        $slope = (($n * $xySum) - ($xSum * $ySum)) / $denominator;
        $intercept = ($ySum - $slope * $xSum) / $n;

        $predictions = [];
        for ($i = 1; $i <= 3; $i++) {
            $x = $n + $i;
            $y = $slope * $x + $intercept;
            $date = now()->addMonths($i);
            $predictions[] = [
                'label' => $date->format('M Y'),
                'total' => max(0, $y),
            ];
        }

        return $predictions;
    }

    private function getHealthMetrics(): array
    {
        $savingsRate = $this->calculateSavingsRate();
        $debtToIncome = $this->calculateDebtToIncomeRatio();
        $emergencyFundRatio = $this->calculateEmergencyFundRatio();

        $setting = Setting::query()->first();

        $creditScore = $setting && $setting->credit_score !== null
            ? (int) $setting->credit_score
            : null;
        $riskProfile = $setting && $setting->risk_profile
            ? $setting->risk_profile
            : $this->inferRiskProfile($creditScore, $savingsRate, $debtToIncome);

        return [
            'savings_rate' => $savingsRate,
            'debt_to_income' => $debtToIncome,
            'emergency_fund_ratio' => $emergencyFundRatio,
            'credit_score' => $creditScore,
            'risk_profile' => $riskProfile,
        ];
    }

    private function calculateSavingsRate(): float
    {
        $incomeTotals = $this->getMonthlyTotals('income', 6);
        $expenseTotals = $this->getMonthlyTotals('expense', 6);

        $monthlyRates = [];
        foreach (range(0, 5) as $i) {
            $income = $incomeTotals[$i]['total'] ?? 0.0;
            $expense = $expenseTotals[$i]['total'] ?? 0.0;
            $savings = $income - $expense;
            $monthlyRates[] = $income > 0 ? ($savings / $income) * 100 : 0;
        }

        return count($monthlyRates) > 0
            ? round(array_sum($monthlyRates) / count($monthlyRates), 1)
            : 0.0;
    }

    private function calculateDebtToIncomeRatio(): float
    {
        $avgMonthlyIncome = $this->getAverageMonthlyAmount('income');

        $totalMinimumPayments = Debt::where('status', 'active')->sum('minimum_payment');

        if ($avgMonthlyIncome <= 0) {
            return 0.0;
        }

        return round(($totalMinimumPayments / $avgMonthlyIncome) * 100, 1);
    }

    private function calculateEmergencyFundRatio(): float
    {
        $emergencyAccounts = Account::active()
            ->whereIn('type', ['savings', 'bank'])
            ->where(function ($query) {
                $query->where('name', 'like', '%emergency%')
                    ->orWhere('name', 'like', '%darurat%');
            })
            ->sum('balance');

        $expenseTotals = $this->getMonthlyTotals('expense', 6);
        $avgMonthlyExpense = count($expenseTotals) > 0 ? array_sum(array_column($expenseTotals, 'total')) / count($expenseTotals) : 0.0;

        if ($avgMonthlyExpense <= 0) {
            return 0.0;
        }

        return round($emergencyAccounts / $avgMonthlyExpense, 1);
    }

    private function inferRiskProfile(?int $creditScore, float $savingsRate, float $debtToIncome): string
    {
        if ($creditScore !== null && $creditScore >= 750 && $savingsRate >= 20 && $debtToIncome < 20) {
            return 'aggressive';
        }

        if ($creditScore !== null && $creditScore <= 600 || $debtToIncome > 40 || $savingsRate < 5) {
            return 'conservative';
        }

        return 'balanced';
    }

    private function getPersonalizedAdvice(array $metrics): array
    {
        $advice = [];

        if ($metrics['savings_rate'] < 10) {
            $advice[] = 'Tingkatkan savings rate Anda ke minimal 10% dari penghasilan bulanan.';
        } elseif ($metrics['savings_rate'] < 20) {
            $advice[] = 'Savings rate Anda cukup baik, pertimbangkan menaikkannya ke 20% untuk percepat pencapaian tujuan.';
        } else {
            $advice[] = 'Savings rate Anda sangat sehat. Pertahankan konsistensi ini.';
        }

        if ($metrics['debt_to_income'] > 50) {
            $advice[] = 'Debt-to-income ratio Anda sangat tinggi. Prioritaskan pelunasan hutang dan hindari hutang baru.';
        } elseif ($metrics['debt_to_income'] > 35) {
            $advice[] = 'Debt-to-income ratio Anda di atas batas ideal. Pertimbangkan mengurangi komitmen hutang bulanan.';
        } else {
            $advice[] = 'Debt-to-income ratio Anda dalam batas sehat.';
        }

        if ($metrics['emergency_fund_ratio'] < 3) {
            $advice[] = 'Dana darurat Anda kurang dari 3 bulan pengeluaran. Fokuskan sebagian tabungan untuk membangun dana darurat.';
        } elseif ($metrics['emergency_fund_ratio'] < 6) {
            $advice[] = 'Dana darurat Anda cukup, namun bisa ditingkatkan hingga 6 bulan pengeluaran untuk perlindungan ekstra.';
        } else {
            $advice[] = 'Dana darurat Anda sangat kuat. Anda dapat mulai fokus ke investasi jangka panjang.';
        }

        if ($metrics['risk_profile'] === 'aggressive') {
            $advice[] = 'Profil risiko Anda agresif. Pastikan portofolio investasi terdiversifikasi untuk mengelola volatilitas.';
        } elseif ($metrics['risk_profile'] === 'conservative') {
            $advice[] = 'Profil risiko Anda konservatif. Fokus pada instrumen rendah risiko dan jaga likuiditas.';
        } else {
            $advice[] = 'Profil risiko Anda seimbang. Kombinasikan instrumen pendapatan tetap dan pertumbuhan.';
        }

        return $advice;
    }

    private function getTaxInsights(): array
    {
        $year = now()->year;

        $totalIncome = Transaction::where('type', 'income')
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $totalExpense = Transaction::where('type', 'expense')
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $deductibleCategories = Category::where('type', 'expense')
            ->where(function ($query) {
                $keywords = [
                    'zakat',
                    'donasi',
                    'charity',
                    'sumbangan',
                    'pendidikan',
                    'education',
                    'kesehatan',
                    'health',
                    'asuransi',
                    'insurance',
                ];

                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%'.$keyword.'%');
                }
            })
            ->pluck('id');

        $deductibleExpenses = Transaction::where('type', 'expense')
            ->whereYear('transaction_date', $year)
            ->whereIn('category_id', $deductibleCategories)
            ->sum('amount');

        $taxableIncome = max(0, $totalIncome - $deductibleExpenses);

        $brackets = [
            ['limit' => 60000000, 'rate' => 5],
            ['limit' => 250000000, 'rate' => 15],
            ['limit' => 500000000, 'rate' => 25],
            ['limit' => null, 'rate' => 30],
        ];

        $currentBracket = null;
        $nextBracket = null;

        foreach ($brackets as $index => $bracket) {
            $limit = $bracket['limit'];
            if ($limit === null || $taxableIncome <= $limit) {
                $currentBracket = $bracket;
                $nextBracket = $brackets[$index + 1] ?? null;
                break;
            }
        }

        $estimatedTax = $currentBracket
            ? $taxableIncome * ($currentBracket['rate'] / 100)
            : 0;

        return [
            'year' => $year,
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'deductible_expenses' => (float) $deductibleExpenses,
            'taxable_income' => (float) $taxableIncome,
            'current_bracket' => $currentBracket,
            'next_bracket' => $nextBracket,
            'estimated_tax' => (float) $estimatedTax,
        ];
    }

    private function getAverageMonthlyAmount(string $type): float
    {
        $totals = $this->getMonthlyTotals($type, 6);
        $values = array_column($totals, 'total');

        return count($values) > 0
            ? array_sum($values) / count($values)
            : 0.0;
    }

    private function calculateTrend(array $data, string $field): string
    {
        if (count($data) < 2) {
            return 'stable';
        }

        $mid = (int) floor(count($data) / 2);
        $firstHalf = array_slice($data, 0, $mid);
        $secondHalf = array_slice($data, $mid);

        $firstValues = array_column($firstHalf, $field);
        $secondValues = array_column($secondHalf, $field);

        $firstAvg = count($firstValues) > 0 ? array_sum($firstValues) / count($firstValues) : 0;
        $secondAvg = count($secondValues) > 0 ? array_sum($secondValues) / count($secondValues) : 0;

        if ($firstAvg == 0) {
            return 'stable';
        }

        $change = (($secondAvg - $firstAvg) / $firstAvg) * 100;

        if ($change > 10) {
            return 'increasing';
        }

        if ($change < -10) {
            return 'decreasing';
        }

        return 'stable';
    }

    private function getMonthlyTotals(string $type, int $months, bool $useCache = true): array
    {
        $cacheKey = $type.':'.$months;
        if ($useCache && isset($this->monthlyTotalsCache[$cacheKey])) {
            return $this->monthlyTotalsCache[$cacheKey];
        }

        $start = now()->startOfMonth()->subMonths($months - 1);

        $rows = Transaction::selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as total')
            ->where('type', $type)
            ->where('transaction_date', '>=', $start)
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->orderByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->get();

        $mapped = [];
        foreach ($rows as $row) {
            $period = Carbon::create((int) $row->year, (int) $row->month, 1)->format('Y-m');
            $mapped[$period] = (float) $row->total;
        }

        $totals = [];
        for ($i = 0; $i < $months; $i++) {
            $periodDate = $start->copy()->addMonths($i);
            $key = $periodDate->format('Y-m');

            $totals[] = [
                'label' => $periodDate->format('M Y'),
                'total' => $mapped[$key] ?? 0.0,
            ];
        }

        if ($useCache) {
            $this->monthlyTotalsCache[$cacheKey] = $totals;
        }

        return $totals;
    }
}
