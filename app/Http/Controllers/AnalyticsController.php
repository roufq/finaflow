<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Debt;
use App\Models\Recommendation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private array $monthlyTotalsCache = [];

    public function index()
    {
        $currentMonthExpense = $this->getCurrentMonthAmount('expense');
        $currentMonthIncome = $this->getCurrentMonthAmount('income');

        $primarySetting = Setting::where('user_id', Auth::id())->where('is_default', true)->first()
            ?? Setting::where('user_id', Auth::id())->first();
        $currencySymbol = $primarySetting->currency_symbol ?? 'Rp';

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

        $discretionaryRatio = $this->getDiscretionaryRatio();
        $actionableTips = $this->getActionableTips($healthMetrics, $discretionaryRatio);

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
            'taxInsights',
            'currencySymbol',
            'discretionaryRatio',
            'actionableTips'
        ));
    }

    private function getCurrentMonthAmount(string $type): float
    {
        $date = now();

        return (float) DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', $type)
            ->where('t.user_id', Auth::id())
            ->whereYear('t.transaction_date', $date->year)
            ->whereMonth('t.transaction_date', $date->month)
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;
    }

    private function getDailySpending(): array
    {
        $startDate = now()->subDays(29)->startOfDay();

        $rows = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->selectRaw('DATE(t.transaction_date) as date, SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->where('t.type', 'expense')
            ->where('t.user_id', Auth::id())
            ->where('t.transaction_date', '>=', $startDate)
            ->groupBy(DB::raw('DATE(t.transaction_date)'))
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
        $rows = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->selectRaw('YEAR(t.transaction_date) as year, WEEK(t.transaction_date, 3) as week, MIN(t.transaction_date) as week_start, SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->where('t.type', 'expense')
            ->where('t.user_id', Auth::id())
            ->where('t.transaction_date', '>=', $startOfWeek)
            ->groupByRaw('YEAR(t.transaction_date), WEEK(t.transaction_date, 3)')
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

        $setting = Setting::where('user_id', Auth::id())->where('is_default', true)->first()
            ?? Setting::where('user_id', Auth::id())->first();

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

        $totalMinimumPayments = Debt::where('user_id', Auth::id())->where('status', 'active')->sum('minimum_payment');

        if ($avgMonthlyIncome <= 0) {
            return 0.0;
        }

        return round(($totalMinimumPayments / $avgMonthlyIncome) * 100, 1);
    }

    private function calculateEmergencyFundRatio(): float
    {
        $emergencyAccounts = DB::table('accounts as a')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('a.is_active', true)
            ->where('a.user_id', Auth::id())
            ->whereIn('a.type', ['savings', 'bank'])
            ->where(function ($query) {
                $query->where('a.name', 'like', '%emergency%')
                    ->orWhere('a.name', 'like', '%darurat%');
            })
            ->selectRaw('SUM(a.balance * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;

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

        $totalIncome = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'income')
            ->where('t.user_id', Auth::id())
            ->whereYear('t.transaction_date', $year)
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;

        $totalExpense = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'expense')
            ->where('t.user_id', Auth::id())
            ->whereYear('t.transaction_date', $year)
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;

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

        $deductibleExpenses = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'expense')
            ->where('t.user_id', Auth::id())
            ->whereYear('t.transaction_date', $year)
            ->whereIn('t.category_id', $deductibleCategories)
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;

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

    private function getDiscretionaryRatio(): float
    {
        $totalExpense = $this->getCurrentMonthAmount('expense');
        if ($totalExpense <= 0) {
            return 0.0;
        }

        $essentialKeywords = ['rent', 'mortgage', 'utility', 'listrik', 'air', 'internet', 'grocery', 'sembako', 'transport', 'insurance', 'asuransi', 'medical', 'obat', 'hospital'];
        $essentialCategoryIds = Category::where('type', 'expense')
            ->where(function ($query) use ($essentialKeywords) {
                foreach ($essentialKeywords as $keyword) {
                    $query->orWhere('name', 'like', "%{$keyword}%");
                }
            })->pluck('id');

        $essentialExpense = (float) DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->where('t.type', 'expense')
            ->where('t.user_id', Auth::id())
            ->whereYear('t.transaction_date', now()->year)
            ->whereMonth('t.transaction_date', now()->month)
            ->whereIn('t.category_id', $essentialCategoryIds)
            ->selectRaw('SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->value('total') ?? 0.0;

        $discretionaryExpense = max(0, $totalExpense - $essentialExpense);

        return round(($discretionaryExpense / $totalExpense) * 100, 1);
    }

    private function getActionableTips(array $metrics, float $discretionaryRatio): array
    {
        // Try to fetch real AI recommendations first
        $realRecs = Recommendation::where('user_id', Auth::id())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        if ($realRecs->count() > 0) {
            return $realRecs->map(function ($rec) {
                return [
                    'title' => ucfirst($rec->type).' Optimization',
                    'tip' => $rec->content,
                ];
            })->toArray();
        }

        // Fallback to basic logic if no AI recommendations exist
        $tips = [];

        if ($metrics['savings_rate'] < 10) {
            $tips[] = ['title' => 'Savings Booster', 'tip' => 'Automate a 5% transfer to your savings on payday to force a higher savings rate.'];
        }

        if ($discretionaryRatio > 40) {
            $tips[] = ['title' => 'Lifestyle Audit', 'tip' => 'Your discretionary spending is high. Review non-essential subscriptions and dining out habits.'];
        }

        if ($metrics['emergency_fund_ratio'] < 2) {
            $tips[] = ['title' => 'Safety Net', 'tip' => 'Prioritize building your emergency fund. Aim for at least 3 months of expenses in a liquid account.'];
        }

        if ($metrics['debt_to_income'] > 30) {
            $tips[] = ['title' => 'Debt Avalanche', 'tip' => 'Focus on paying off your highest interest rate debt first to reduce your debt-to-income ratio.'];
        }

        if (empty($tips)) {
            $tips[] = ['title' => 'Asset Optimization', 'tip' => 'You are in a strong position. Consider diversified investment vehicles to hedge against inflation.'];
        }

        return array_slice($tips, 0, 3);
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

        $rows = DB::table('transactions as t')
            ->join('accounts as a', 't.account_id', '=', 'a.id')
            ->leftJoin('settings as s', 'a.setting_id', '=', 's.id')
            ->selectRaw('YEAR(t.transaction_date) as year, MONTH(t.transaction_date) as month, SUM(t.amount * COALESCE(s.exchange_rate, 1.0)) as total')
            ->where('t.type', $type)
            ->where('t.user_id', Auth::id())
            ->where('t.transaction_date', '>=', $start)
            ->groupByRaw('YEAR(t.transaction_date), MONTH(t.transaction_date)')
            ->orderByRaw('YEAR(t.transaction_date), MONTH(t.transaction_date)')
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
