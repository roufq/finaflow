<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kategori</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h2 { margin-bottom: 6px; }
        .meta { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Laporan Kategori</h2>
    <div class="meta">
        <div>Periode: {{ $periodLabel }}</div>
        <div>Rentang: {{ $start->toDateString() }} - {{ $end->toDateString() }}</div>
        <div>Dibuat: {{ $generatedAt->format('d M Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-right">Transaksi</th>
                <th class="text-right">Total Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary as $row)
                <tr>
                    <td>{{ $row['category_name'] }}</td>
                    <td class="text-right">{{ number_format($row['transactions_count'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['expense_total'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-right">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
