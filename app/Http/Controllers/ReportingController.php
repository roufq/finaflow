<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Goal;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\Widget;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportingController extends Controller
{
    public function dashboard(): View
    {
        $userId = Auth::id();
        $reports = Report::forUser($userId)->withCount('widgets')->latest()->get();
        $widgets = Widget::where('user_id', $userId)->orderBy('position')->get();
        $metrics = array_merge(
            $this->buildSummaryMetrics($userId),
            [
                'reports' => $reports->count(),
                'widgets' => $widgets->count(),
            ]
        );

        return view('reporting.dashboard', compact('reports', 'widgets', 'metrics'));
    }

    public function builder(?Report $report = null): View
    {
        $userId = Auth::id();
        if ($report && $report->user_id !== $userId) {
            abort(403);
        }

        $report ??= Report::forUser($userId)->latest()->first();

        $widgets = Widget::query()
            ->where('user_id', $userId)
            ->when($report, fn ($query) => $query->where('report_id', $report->id))
            ->orderBy('position')
            ->get();

        return view('reporting.builder', [
            'report' => $report,
            'widgets' => $widgets,
            'availableWidgets' => $this->availableWidgets(),
        ]);
    }

    public function storeReport(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'schedule' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $report = Report::create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'schedule' => $data['schedule'] ?? null,
            'format' => $data['format'],
            'config' => [
                'filters' => $request->input('filters', []),
                'description' => $request->input('description'),
            ],
        ]);

        return redirect()
            ->route('reporting.builder', $report)
            ->with('success', __('reporting.messages.report_created'));
    }

    public function storeWidget(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:cash_flow,spending_category,goal_progress,budget_health,net_worth,custom',
            'title' => 'nullable|string|max:255',
            'report_id' => 'nullable|exists:reports,id',
            'size' => 'nullable|in:small,medium,large',
        ]);

        $reportId = $data['report_id'] ?? null;
        if ($reportId) {
            $report = Report::find($reportId);
            if (! $report || $report->user_id !== Auth::id()) {
                abort(403);
            }
        }

        $position = Widget::where('user_id', Auth::id())->max('position') ?? 0;

        Widget::create([
            'user_id' => Auth::id(),
            'report_id' => $reportId,
            'type' => $data['type'],
            'config' => [
                'title' => $data['title'] ?? ($this->availableWidgets()[$data['type']]['title'] ?? __('reporting.widgets.custom.title')),
                'filters' => $request->input('filters', []),
                'notes' => $request->input('notes'),
            ],
            'position' => $position + 1,
            'size' => $data['size'] ?? 'medium',
        ]);

        return back()->with('success', __('reporting.messages.widget_created'));
    }

    public function updateWidgetPositions(Request $request): JsonResponse
    {
        $request->validate([
            'widgets' => 'required|array',
            'widgets.*.id' => 'required|exists:widgets,id',
            'widgets.*.position' => 'required|integer',
            'widgets.*.size' => 'nullable|string|in:small,medium,large',
        ]);

        foreach ($request->input('widgets', []) as $widgetData) {
            $widget = Widget::where('user_id', Auth::id())->find($widgetData['id']);
            if (! $widget) {
                continue;
            }

            $widget->update([
                'position' => $widgetData['position'],
                'size' => $widgetData['size'] ?? $widget->size,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function exportReport(Request $request, Report $report)
    {
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $format = $request->input('format', $report->format);
        $payload = $this->generateReportData($report);

        return match ($format) {
            'excel' => $this->exportExcel($report, $payload),
            'csv' => $this->exportCsv($report, $payload['transactions']),
            default => $this->exportPdf($report, $payload),
        };
    }

    protected function exportPdf(Report $report, array $payload)
    {
        $pdf = Pdf::loadView('reporting.exports.pdf', [
            'report' => $report,
            'summary' => $payload['summary'],
            'transactions' => $payload['transactions'],
            'widgets' => $payload['widgets'],
        ]);

        return $pdf->download(sprintf('report-%s.pdf', $report->id));
    }

    protected function exportExcel(Report $report, array $payload): StreamedResponse
    {
        $filename = sprintf('report-%s.xls', $report->id);
        $content = view('reporting.exports.excel', [
            'report' => $report,
            'summary' => $payload['summary'],
            'transactions' => $payload['transactions'],
            'widgets' => $payload['widgets'],
        ])->render();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    protected function exportCsv(Report $report, Collection $transactions): StreamedResponse
    {
        $filename = sprintf('report-%s.csv', $report->id);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Description', 'Type', 'Amount']);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction->transaction_date?->format('Y-m-d'),
                    $transaction->description,
                    ucfirst($transaction->type),
                    $transaction->amount,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function generateReportData(Report $report): array
    {
        $transactions = Transaction::query()
            ->where('user_id', $report->user_id)
            ->latest('transaction_date')
            ->limit(25)
            ->get();

        $summary = $this->buildSummaryMetrics($report->user_id);

        $widgets = Widget::where('user_id', $report->user_id)
            ->when($report, fn ($query) => $query->where('report_id', $report->id))
            ->orderBy('position')
            ->get();

        return [
            'summary' => $summary,
            'transactions' => $transactions,
            'widgets' => $widgets,
        ];
    }

    protected function buildSummaryMetrics(int $userId): array
    {
        $income = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $expenses = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $activeGoals = Goal::where('user_id', $userId)->where('status', 'active')->count();
        $activeBudgets = Budget::where('user_id', $userId)->where('status', 'active')->count();

        return [
            'income' => $income,
            'expenses' => $expenses,
            'net_flow' => $income - $expenses,
            'active_goals' => $activeGoals,
            'active_budgets' => $activeBudgets,
        ];
    }

    protected function availableWidgets(): array
    {
        return [
            'cash_flow' => [
                'title' => __('reporting.widgets.cash_flow.title'),
                'description' => __('reporting.widgets.cash_flow.description'),
                'icon' => 'fas fa-random',
            ],
            'spending_category' => [
                'title' => __('reporting.widgets.spending_category.title'),
                'description' => __('reporting.widgets.spending_category.description'),
                'icon' => 'fas fa-chart-pie',
            ],
            'goal_progress' => [
                'title' => __('reporting.widgets.goal_progress.title'),
                'description' => __('reporting.widgets.goal_progress.description'),
                'icon' => 'fas fa-bullseye',
            ],
            'budget_health' => [
                'title' => __('reporting.widgets.budget_health.title'),
                'description' => __('reporting.widgets.budget_health.description'),
                'icon' => 'fas fa-wallet',
            ],
            'net_worth' => [
                'title' => __('reporting.widgets.net_worth.title'),
                'description' => __('reporting.widgets.net_worth.description'),
                'icon' => 'fas fa-chart-line',
            ],
            'custom' => [
                'title' => __('reporting.widgets.custom.title'),
                'description' => __('reporting.widgets.custom.description'),
                'icon' => 'fas fa-layer-group',
            ],
        ];
    }
}
