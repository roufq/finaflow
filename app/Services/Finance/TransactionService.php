<?php

namespace App\Services\Finance;

use App\Models\Account;
use App\Models\Gamification;
use App\Models\SpendingTrigger;
use App\Models\Transaction;
use App\Services\Reports\CategoryReportService;
use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    private const POINTS_PER_UNIT = 1000;

    private CacheRepository $cache;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cache = $this->resolveCacheStore($cacheManager);
    }

    public function create(array $data): void
    {
        DB::transaction(function () use ($data) {
            $transaction = Transaction::create([
                'user_id' => $data['user_id'],
                'account_id' => $data['account_id'],
                'category_id' => $data['category_id'],
                'transaction_date' => $data['transaction_date'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
            ]);

            $account = Account::whereKey($data['account_id'])->lockForUpdate()->firstOrFail();
            $this->applyAccountBalanceChange($account, (float) $data['amount'], $data['type'], 'apply');

            if ($data['type'] === 'expense') {
                $this->checkSpendingTriggers($transaction);
                $this->awardGamificationPoints($transaction);
            }

            $this->invalidateDashboardCache($transaction->user_id, $transaction->transaction_date);
            $this->invalidateCategoryReportCache($transaction->user_id);
        });
    }

    public function update(Transaction $transaction, array $data): void
    {
        DB::transaction(function () use ($transaction, $data) {
            $originalAccount = $transaction->account_id
                ? Account::whereKey($transaction->account_id)->lockForUpdate()->first()
                : null;
            $originalAmount = (float) $transaction->amount;
            $originalType = $transaction->type;
            $originalDate = $transaction->transaction_date;

            $transaction->update([
                'category_id' => $data['category_id'],
                'account_id' => $data['account_id'],
                'transaction_date' => $data['transaction_date'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
            ]);

            if ($originalAccount) {
                $this->applyAccountBalanceChange($originalAccount, $originalAmount, $originalType, 'reverse');
            }

            $updatedAccount = Account::whereKey($data['account_id'])->lockForUpdate()->firstOrFail();
            $this->applyAccountBalanceChange($updatedAccount, (float) $data['amount'], $data['type'], 'apply');

            $this->invalidateDashboardCache($transaction->user_id, $transaction->transaction_date);
            $this->invalidateDashboardCache($transaction->user_id, $originalDate);
            $this->invalidateCategoryReportCache($transaction->user_id);
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $account = $transaction->account_id
                ? Account::whereKey($transaction->account_id)->lockForUpdate()->first()
                : null;

            if ($account) {
                $this->applyAccountBalanceChange($account, (float) $transaction->amount, $transaction->type, 'reverse');
            }

            $date = $transaction->transaction_date;
            $userId = $transaction->user_id;

            $transaction->delete();

            $this->invalidateDashboardCache($userId, $date);
            $this->invalidateCategoryReportCache($userId);
        });
    }

    private function applyAccountBalanceChange(Account $account, float $amount, string $type, string $operation = 'apply'): void
    {
        $delta = $type === 'income' ? $amount : -$amount;

        if ($operation === 'reverse') {
            $delta = -$delta;
        }

        $account->update([
            'balance' => $account->balance + $delta,
        ]);
    }

    private function checkSpendingTriggers(Transaction $transaction): void
    {
        $triggers = SpendingTrigger::where('user_id', $transaction->user_id)->get();

        foreach ($triggers as $trigger) {
            if ($trigger->matchesTransaction($transaction)) {
                $trigger->incrementFrequency();

                \Log::channel('daily')->info('Spending trigger activated', [
                    'trigger_id' => $trigger->id,
                    'trigger_description' => $trigger->description,
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                ]);
            }
        }
    }

    private function awardGamificationPoints(Transaction $transaction): void
    {
        $gamification = Gamification::firstOrCreate(
            ['user_id' => $transaction->user_id],
            ['points' => 0, 'level' => 1, 'streak_days' => 0]
        );

        $pointsEarned = (int) floor($transaction->amount / self::POINTS_PER_UNIT);
        if ($pointsEarned > 0) {
            $gamification->addPoints($pointsEarned);

            $todayTransactions = Transaction::where('user_id', $transaction->user_id)
                ->whereDate('transaction_date', today())
                ->count();

            if ($todayTransactions === 1) {
                $gamification->awardAchievement('First Transaction of the Day');
            }

            \Log::channel('daily')->info('Gamification points awarded', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'points' => $pointsEarned,
            ]);
        }
    }

    private function invalidateDashboardCache(int $userId, $transactionDate): void
    {
        $date = Carbon::parse($transactionDate);
        $cacheKey = sprintf('dashboard:%d:%d-%02d', $userId, $date->year, $date->month);
        $this->cache->forget($cacheKey);

        \Log::channel('daily')->info('Dashboard cache invalidated', [
            'user_id' => $userId,
            'period' => $date->format('Y-m'),
        ]);
    }

    private function invalidateCategoryReportCache(int $userId): void
    {
        app(CategoryReportService::class)->bumpCacheVersion($userId);
    }

    private function resolveCacheStore(CacheManager $cacheManager): CacheRepository
    {
        if (class_exists(\Redis::class)) {
            return $cacheManager->store('redis');
        }

        return $cacheManager->store(config('cache.default'));
    }
}
