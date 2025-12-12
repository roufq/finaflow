<?php

namespace App\Services\Reports;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategoryReportService
{
    private CacheRepository $cache;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cache = $this->resolveCacheStore($cacheManager);
    }

    /**
     * @param  array{period:string,start:CarbonImmutable,end:CarbonImmutable,category_id:int|null,account_id:int|null,view:string}  $filters
     */
    public function getSummary(int $userId, array $filters): Collection
    {
        $cacheKey = $this->cacheKey('summary', $userId, $filters);

        return $this->cache->remember($cacheKey, $this->ttl(), function () use ($filters, $userId) {
            return Transaction::query()
                ->selectRaw('category_id, SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income_total, SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense_total, COUNT(*) as transactions_count')
                ->where('user_id', $userId)
                ->where('type', 'expense')
                ->when($filters['category_id'], fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId))
                ->when($filters['account_id'], fn (Builder $query, int $accountId) => $query->where('account_id', $accountId))
                ->whereBetween('transaction_date', [$filters['start'], $filters['end']])
                ->groupBy('category_id')
                ->with('category')
                ->orderByDesc(DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END)'))
                ->get()
                ->map(function (Transaction $transaction) {
                    return [
                        'category_id' => $transaction->category_id,
                        'category_name' => $transaction->category?->name ?? 'Uncategorized',
                        'income_total' => (float) $transaction->income_total,
                        'expense_total' => (float) $transaction->expense_total,
                        'transactions_count' => (int) $transaction->transactions_count,
                        'total' => (float) $transaction->expense_total,
                    ];
                });
        });
    }

    /**
     * @param  array{period:string,start:CarbonImmutable,end:CarbonImmutable,category_id:int|null,account_id:int|null,view:string}  $filters
     */
    public function getMonthlyStacks(int $userId, array $filters): array
    {
        $cacheKey = $this->cacheKey('monthly', $userId, $filters);

        return $this->cache->remember($cacheKey, $this->ttl(), function () use ($filters, $userId) {
            $start = CarbonImmutable::now()->subMonths(11)->startOfMonth();
            $end = CarbonImmutable::now()->endOfMonth();
            $monthExpression = $this->monthSelectExpression();

            $rows = Transaction::query()
                ->selectRaw("{$monthExpression} as month_key, category_id, SUM(amount) as total")
                ->where('user_id', $userId)
                ->where('type', 'expense')
                ->when($filters['category_id'], fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId))
                ->when($filters['account_id'], fn (Builder $query, int $accountId) => $query->where('account_id', $accountId))
                ->whereBetween('transaction_date', [$start, $end])
                ->groupBy('month_key', 'category_id')
                ->orderBy('month_key')
                ->get();

            if ($rows->isEmpty()) {
                return ['labels' => [], 'datasets' => []];
            }

            $categoryTotals = [];
            foreach ($rows as $row) {
                $categoryTotals[$row->category_id] = ($categoryTotals[$row->category_id] ?? 0) + (float) $row->total;
            }

            $topCategoryIds = $filters['category_id']
                ? [$filters['category_id']]
                : array_slice(array_keys(collect($categoryTotals)->sortDesc()->toArray()), 0, 6);

            $categoryNames = Category::query()
                ->where('user_id', $userId)
                ->whereIn('id', $topCategoryIds)
                ->pluck('name', 'id')
                ->toArray();

            $monthKeys = [];
            $monthLabels = [];
            for ($i = 0; $i < 12; $i++) {
                $month = $start->addMonths($i);
                $monthKey = $month->format('Y-m');
                $monthKeys[] = $monthKey;
                $monthLabels[] = $month->translatedFormat('M Y');
            }

            $dataByCategory = [];
            foreach ($topCategoryIds as $categoryId) {
                $dataByCategory[$categoryId] = array_fill_keys($monthKeys, 0.0);
            }

            foreach ($rows as $row) {
                if (! in_array($row->category_id, $topCategoryIds, true)) {
                    continue;
                }

                $dataByCategory[$row->category_id][$row->month_key] = (float) $row->total;
            }

            $datasets = [];
            $palette = [
                '#2563eb', '#ea580c', '#16a34a', '#9333ea', '#f59e0b', '#0ea5e9',
                '#dc2626', '#059669', '#1d4ed8',
            ];

            foreach ($topCategoryIds as $index => $categoryId) {
                $datasets[] = [
                    'label' => $categoryNames[$categoryId] ?? 'Uncategorized',
                    'data' => array_values($dataByCategory[$categoryId]),
                    'backgroundColor' => $palette[$index % count($palette)],
                    'stack' => 'monthly',
                ];
            }

            return [
                'labels' => $monthLabels,
                'datasets' => $datasets,
            ];
        });
    }

    /**
     * @param  array{period:string,start:CarbonImmutable,end:CarbonImmutable,category_id:int|null,account_id:int|null,view:string}  $filters
     */
    public function getCategoryTransactions(int $userId, int $categoryId, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return Transaction::query()
            ->with(['category', 'account'])
            ->where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('type', 'expense')
            ->when($filters['account_id'], fn (Builder $query, int $accountId) => $query->where('account_id', $accountId))
            ->whereBetween('transaction_date', [$filters['start'], $filters['end']])
            ->orderByDesc('transaction_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array{period:string,start:CarbonImmutable,end:CarbonImmutable,category_id:int|null,account_id:int|null,view:string}  $filters
     * @return array{income_total: float, expense_total: float, transactions_count:int}
     */
    public function summarizeCategory(int $userId, int $categoryId, array $filters): array
    {
        $totals = Transaction::query()
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income_total, SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense_total, COUNT(*) as transactions_count')
            ->where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('type', 'expense')
            ->when($filters['account_id'], fn (Builder $query, int $accountId) => $query->where('account_id', $accountId))
            ->whereBetween('transaction_date', [$filters['start'], $filters['end']])
            ->first();

        return [
            'income_total' => (float) ($totals->income_total ?? 0),
            'expense_total' => (float) ($totals->expense_total ?? 0),
            'transactions_count' => (int) ($totals->transactions_count ?? 0),
        ];
    }

    public function bumpCacheVersion(int $userId): void
    {
        $key = $this->cacheVersionKey($userId);
        $this->cache->forever($key, $this->cacheVersion($userId) + 1);
    }

    /**
     * @param  array{period:string,start:CarbonImmutable,end:CarbonImmutable,category_id:int|null,account_id:int|null,view:string}  $filters
     */
    private function cacheKey(string $prefix, int $userId, array $filters): string
    {
        $hash = md5(json_encode([
            $filters['period'],
            $filters['start']->format('Y-m-d'),
            $filters['end']->format('Y-m-d'),
            $filters['category_id'],
            $filters['account_id'],
        ], JSON_THROW_ON_ERROR));

        return sprintf(
            'category-report:%d:v%d:%s:%s',
            $userId,
            $this->cacheVersion($userId),
            $prefix,
            $hash
        );
    }

    private function cacheVersion(int $userId): int
    {
        return (int) $this->cache->get($this->cacheVersionKey($userId), 1);
    }

    private function cacheVersionKey(int $userId): string
    {
        return sprintf('category-report:%d:version', $userId);
    }

    private function ttl(): \DateTimeInterface
    {
        return CarbonImmutable::now()->addMinutes(15);
    }

    private function monthSelectExpression(): string
    {
        $driver = Transaction::query()->getConnection()->getDriverName();

        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', transaction_date)",
            'pgsql' => "to_char(transaction_date, 'YYYY-MM')",
            default => "DATE_FORMAT(transaction_date, '%Y-%m')",
        };
    }

    private function resolveCacheStore(CacheManager $cacheManager): CacheRepository
    {
        if (class_exists(\Redis::class)) {
            return $cacheManager->store('redis');
        }

        return $cacheManager->store(config('cache.default'));
    }
}
