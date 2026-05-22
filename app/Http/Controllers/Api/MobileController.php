<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\Dashboard\DashboardChartService;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function __construct(private DashboardChartService $chartService) {}

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $periodDate = now()->startOfMonth();

        // Gunakan service yang sama dengan web dashboard untuk konsistensi data
        $dashboardData = $this->chartService->buildDashboardData($user->id, $periodDate);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar_path ? asset('storage/'.$user->avatar_path) : null,
                ],
                'security' => [
                    'two_factor_enabled' => $user->hasValidTwoFactorSecret(),
                    'security_recommendation' => ! $user->hasValidTwoFactorSecret() ? 'Protect your financial data with an extra layer of security. We highly recommend activating Two-Factor Authentication.' : null,
                ],
                'summary' => [
                    'total_income' => (float) $dashboardData['totalIncome'],
                    'total_expense' => (float) $dashboardData['totalExpense'],
                    'net_balance' => (float) $dashboardData['netBalance'],
                    'total_cash' => (float) $dashboardData['totalCash'],
                    'net_worth_trend' => (float) $dashboardData['netWorthTrend'],
                ],
                'analysis' => [
                    'health_score' => $dashboardData['debtHealth']['score'] ?? 84, // Menggunakan score dinamis jika ada, fallback ke default dashboard
                    'burn_rate' => $dashboardData['burnRate'],
                    'cash_runway' => $dashboardData['cashRunway'],
                    'emergency_fund' => $dashboardData['emergencyFund'],
                    'debt_health' => $dashboardData['debtHealth'],
                ],
                'distribution' => [
                    'income_categories' => $dashboardData['incomeCategories'],
                    'expense_categories' => $dashboardData['expenseCategories'],
                ],
                'history' => [
                    'cash_flow' => $dashboardData['cashFlow'],
                    'cash_flow_projections' => $dashboardData['cashFlowProjections'],
                ],
                'accounts' => $dashboardData['accountBalances'],
                'recent_transactions' => TransactionResource::collection($dashboardData['recentTransactions']),
                'currency_symbol' => $dashboardData['currencySymbol'],
                'period' => [
                    'month' => $periodDate->month,
                    'year' => $periodDate->year,
                    'label' => $periodDate->format('F Y'),
                ],
            ],
        ]);
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil dicatat via Mobile',
            'data' => new TransactionResource($transaction),
        ], 201);
    }

    public function transactions(Request $request)
    {
        $transactions = Transaction::with(['category', 'account'])
            ->where('user_id', $request->user()->id)
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->account_id, function ($query, $accountId) {
                return $query->where('account_id', $accountId);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->latest('transaction_date')
            ->latest('id')
            ->paginate($request->per_page ?? 15);

        return TransactionResource::collection($transactions);
    }

    public function categories()
    {
        $categories = Category::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function accounts()
    {
        $accounts = Account::active()->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $accounts,
        ]);
    }
}
