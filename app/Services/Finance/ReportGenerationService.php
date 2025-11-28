<?php

namespace App\Services\Finance;

use App\Models\Transaction;
use Illuminate\Support\Collection;

class ReportGenerationService
{
    public function generateSummary(int $userId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $totals = Transaction::selectRaw('type, SUM(amount) as total')
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('type')
            ->pluck('total', 'type');

        $income = (float) ($totals['income'] ?? 0);
        $expense = (float) ($totals['expense'] ?? 0);

        return [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
        ];
    }

    public function exportCsv(Collection $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Date', 'Type', 'Amount', 'Category', 'Description']);

        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['date'] ?? '',
                $row['type'] ?? '',
                $row['amount'] ?? '',
                $row['category'] ?? '',
                $row['description'] ?? '',
            ]);
        }

        rewind($handle);

        return stream_get_contents($handle);
    }
}
