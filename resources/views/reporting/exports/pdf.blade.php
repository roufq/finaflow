<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #2f2f2f; }
        h1, h2, h3 { color: #1b4b72; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table th, table td { border: 1px solid #e1e7ed; padding: 8px; text-align: left; }
        table th { background-color: #f4f6f9; font-weight: bold; }
        .meta { font-size: 11px; color: #6c757d; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>{{ $report->name }}</h1>
    <p class="meta">{{ __('reporting.exports.title') }} — {{ now()->format('d M Y H:i') }}</p>

    <h2>{{ __('reporting.exports.summary') }}</h2>
    <table>
        <tbody>
            <tr>
                <th>{{ __('reporting.metrics.income') }}</th>
                <td>Rp {{ number_format($summary['income'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>{{ __('reporting.metrics.expenses') }}</th>
                <td>Rp {{ number_format($summary['expenses'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>{{ __('reporting.metrics.net_flow') }}</th>
                <td>Rp {{ number_format($summary['net_flow'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>{{ __('reporting.metrics.goals') }}</th>
                <td>{{ $summary['active_goals'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <h2>{{ __('reporting.exports.transactions') }}</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date?->format('d M Y') }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ ucfirst($transaction->type) }}</td>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">{{ __('transactions.no_transactions') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>{{ __('reporting.exports.widgets') }}</h2>
    <table>
        <thead>
            <tr>
                <th>Widget</th>
                <th>{{ __('reporting.forms.widget_size') }}</th>
                <th>{{ __('reporting.forms.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($widgets as $widget)
            <tr>
                <td>{{ $widget->config['title'] ?? ucfirst($widget->type) }}</td>
                <td>{{ ucfirst($widget->size) }}</td>
                <td>{{ $widget->config['notes'] ?? '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">{{ __('reporting.builder.empty') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
