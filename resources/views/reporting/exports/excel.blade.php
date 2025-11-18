<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr style="background:#f1f5fb">
            <th colspan="4" style="font-size:16px;text-align:left;">{{ $report->name }}</th>
        </tr>
        <tr>
            <td colspan="4">{{ __('reporting.exports.summary') }}</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>{{ __('reporting.metrics.income') }}</th>
            <td>Rp {{ number_format($summary['income'] ?? 0, 0, ',', '.') }}</td>
            <th>{{ __('reporting.metrics.expenses') }}</th>
            <td>Rp {{ number_format($summary['expenses'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>{{ __('reporting.metrics.net_flow') }}</th>
            <td>Rp {{ number_format($summary['net_flow'] ?? 0, 0, ',', '.') }}</td>
            <th>{{ __('reporting.metrics.goals') }}</th>
            <td>{{ $summary['active_goals'] ?? 0 }}</td>
        </tr>
    </tbody>
</table>

<table border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top:16px;">
    <thead style="background:#f1f5fb">
        <tr>
            <th colspan="4">{{ __('reporting.exports.transactions') }}</th>
        </tr>
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

<table border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top:16px;">
    <thead style="background:#f1f5fb">
        <tr>
            <th colspan="3">{{ __('reporting.exports.widgets') }}</th>
        </tr>
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
