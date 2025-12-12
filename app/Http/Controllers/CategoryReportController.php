<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryReportRequest;
use App\Models\Account;
use App\Models\Category;
use App\Services\Reports\CategoryReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryReportController extends Controller
{
    public function __construct(private CategoryReportService $categoryReportService) {}

    public function index(CategoryReportRequest $request): View
    {
        $filters = $request->filters();
        $userId = (int) $request->user()->id;

        $summary = $this->categoryReportService->getSummary($userId, $filters);
        $monthlyStacks = $this->categoryReportService->getMonthlyStacks($userId, $filters);

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $accounts = Account::active()->orderBy('name')->get(['id', 'name']);

        $expenseData = $summary->pluck('expense_total')->map(fn ($value) => round($value, 2));
        $incomeData = $summary->pluck('income_total')->map(fn ($value) => round($value, 2));

        $datasets = [
            [
                'label' => 'Pengeluaran',
                'backgroundColor' => '#2563eb',
                'data' => $expenseData,
                'stack' => 'totals',
            ],
        ];

        if ($incomeData->sum() > 0) {
            $datasets[] = [
                'label' => 'Pemasukan',
                'backgroundColor' => '#16a34a',
                'data' => $incomeData,
                'stack' => 'totals',
            ];
        }

        $chartSummary = [
            'labels' => $summary->pluck('category_name'),
            'datasets' => $datasets,
        ];

        return view('reports.categories', [
            'filters' => $filters,
            'periodLabel' => $request->periodLabel(),
            'summary' => $summary,
            'chartSummary' => $chartSummary,
            'monthlyStacks' => $monthlyStacks,
            'categories' => $categories,
            'accounts' => $accounts,
        ]);
    }

    public function detail(CategoryReportRequest $request, Category $category): JsonResponse
    {
        $filters = $request->filters();
        $userId = (int) $request->user()->id;

        $transactions = $this->categoryReportService->getCategoryTransactions($userId, $category->id, $filters, 10);
        $totals = $this->categoryReportService->summarizeCategory($userId, $category->id, $filters);

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'totals' => [
                'amount' => $totals['expense_total'],
                'transactions' => $totals['transactions_count'],
            ],
            'transactions' => $transactions->getCollection()->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => optional($transaction->transaction_date)->format('Y-m-d'),
                    'description' => $transaction->description,
                    'amount' => (float) $transaction->amount,
                    'account' => $transaction->account?->name,
                ];
            })->values(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function export(CategoryReportRequest $request): Response
    {
        $filters = $request->filters();
        $userId = (int) $request->user()->id;

        $summary = $this->categoryReportService->getSummary($userId, $filters);

        $pdf = Pdf::loadView('reports.exports.category', [
            'summary' => $summary,
            'periodLabel' => $request->periodLabel(),
            'start' => $filters['start'],
            'end' => $filters['end'],
            'generatedAt' => CarbonImmutable::now(),
        ]);

        return $pdf->download('category-report.pdf');
    }
}
