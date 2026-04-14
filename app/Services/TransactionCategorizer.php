<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;

class TransactionCategorizer
{
    /**
     * Guess the most relevant category id for a transaction.
     */
    public function guessCategoryId(string $description, float $amount, array $transactionData = [], ?int $userId = null): ?int
    {
        $userId = $userId ?? auth()->id();
        $description = strtolower($description);

        // First, try exact keyword matching with scoring
        $categoryScores = $this->calculateCategoryScores($description, $amount, $transactionData);

        if (! empty($categoryScores)) {
            arsort($categoryScores);
            $topCategory = key($categoryScores);

            if ($categoryScores[$topCategory] > 0) {
                return $this->findCategoryIdBySlug($topCategory, $userId);
            }
        }

        // Fallback to amount-based heuristics
        return $this->guessByAmount($amount, $userId);
    }

    /**
     * Calculate scores for different categories based on description and amount
     */
    private function calculateCategoryScores(string $description, float $amount, array $transactionData = []): array
    {
        $scores = [];

        $categoryRules = [
            'grocery' => [
                'keywords' => ['indomaret', 'alfamart', 'carrefour', 'superindo', 'hypermart', 'lotte', 'grocery', 'supermarket', 'minimarket', 'mart'],
                'amount_range' => [10000, 1000000],
                'score' => 10,
            ],
            'transport' => [
                'keywords' => ['grab', 'gojek', 'uber', 'bluebird', 'taxi', 'angkot', 'bus', 'train', 'mrt', 'lrt', 'transjakarta', 'pertamina', 'shell', 'fuel', 'bensin', 'gas'],
                'amount_range' => [1000, 500000],
                'score' => 10,
            ],
            'shopping' => [
                'keywords' => ['tokopedia', 'shopee', 'lazada', 'bukalapak', 'blibli', 'jd.id', 'zalora', 'berrybenka', 'matahari', 'mall', 'department', 'store', 'retail'],
                'amount_range' => [5000, 5000000],
                'score' => 8,
            ],
            'entertainment' => [
                'keywords' => ['netflix', 'spotify', 'youtube', 'disney', 'viu', 'iflix', 'hbo', 'cinema', 'movie', 'bioskop', 'cgv', 'xxi', 'game', 'steam', 'playstation', 'nintendo'],
                'amount_range' => [5000, 200000],
                'score' => 9,
            ],
            'utilities' => [
                'keywords' => ['pln', 'electricity', 'listrik', 'pdam', 'water', 'air', 'telkom', 'internet', 'wifi', 'xl', 'telkomsel', 'indosat', 'axis', 'bpjs', 'health', 'insurance'],
                'amount_range' => [20000, 2000000],
                'score' => 10,
            ],
            'food_dining' => [
                'keywords' => ['restaurant', 'cafe', 'warung', 'food', 'makan', 'kfc', 'mcdonald', 'starbuck', 'domino', 'pizza', 'bakery', 'roti', 'kopi'],
                'amount_range' => [5000, 200000],
                'score' => 8,
            ],
            'healthcare' => [
                'keywords' => ['hospital', 'clinic', 'doctor', 'rs', 'puskesmas', 'pharmacy', 'apotik', 'kimia', 'k24', 'medicine', 'obat'],
                'amount_range' => [10000, 1000000],
                'score' => 9,
            ],
            'education' => [
                'keywords' => ['school', 'university', 'kuliah', 'course', 'kursus', 'book', 'buku', 'stationery', 'atk'],
                'amount_range' => [5000, 500000],
                'score' => 7,
            ],
            'salary' => [
                'keywords' => ['salary', 'gaji', 'payroll', 'bonus', 'thr', 'tunjangan', 'honor', 'fee', 'income', 'pendapatan'],
                'amount_range' => [500000, 50000000],
                'score' => 15,
            ],
            'investment' => [
                'keywords' => ['saham', 'stock', 'reksadana', 'mutual fund', 'bond', 'obligasi', 'crypto', 'bitcoin', 'ethereum', 'trading'],
                'amount_range' => [10000, 10000000],
                'score' => 12,
            ],
            'transfer' => [
                'keywords' => ['transfer', 'tf', 'kirim', 'send', 'bca', 'mandiri', 'bni', 'bri', 'cimb', 'danamon'],
                'amount_range' => [1000, 10000000],
                'score' => 5,
            ],
        ];

        foreach ($categoryRules as $categorySlug => $rules) {
            $score = 0;

            // Keyword matching
            foreach ($rules['keywords'] as $keyword) {
                if (str_contains($description, $keyword)) {
                    $score += $rules['score'];
                }
            }

            // Amount range checking
            if (isset($rules['amount_range'])) {
                [$min, $max] = $rules['amount_range'];
                if ($amount >= $min && $amount <= $max) {
                    $score += 2; // Bonus for amount in expected range
                }
            }

            // Merchant name patterns
            if ($this->isMerchantName($description)) {
                $score += 1;
            }

            if ($score > 0) {
                $scores[$categorySlug] = $score;
            }
        }

        return $scores;
    }

    /**
     * Check if description looks like a merchant name
     */
    private function isMerchantName(string $description): bool
    {
        // Merchant names are typically proper nouns, not generic terms
        $genericTerms = ['payment', 'transfer', 'purchase', 'buy', 'pay', 'fee', 'charge'];

        foreach ($genericTerms as $term) {
            if (str_contains($description, $term)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Guess category based on amount patterns
     */
    private function guessByAmount(float $amount, ?int $userId = null): ?int
    {
        $userId = $userId ?? auth()->id();
        // Large amounts are likely salary or transfers
        if ($amount >= 5000000) {
            return $this->findCategoryIdBySlug('salary', $userId) ?? $this->findCategoryIdBySlug('transfer', $userId);
        }

        // Medium amounts might be utilities or shopping
        if ($amount >= 500000) {
            return $this->findCategoryIdBySlug('utilities', $userId) ?? $this->findCategoryIdBySlug('shopping', $userId);
        }

        // Small amounts could be food or transport
        if ($amount <= 50000) {
            return $this->findCategoryIdBySlug('food_dining', $userId) ?? $this->findCategoryIdBySlug('transport', $userId);
        }

        return null;
    }

    /**
     * Learn from user's categorization patterns
     */
    public function learnFromTransaction(Transaction $transaction): void
    {
        // This could be extended to implement machine learning
        // For now, we'll just log patterns for future analysis
        $description = strtolower($transaction->description);
        $categoryId = $transaction->category_id;

        // Store learning data in cache or database for future use
        $learningKey = 'categorization_learning_'.auth()->id();
        $learningData = \Cache::get($learningKey, []);

        $key = md5($description);
        $learningData[$key] = [
            'description' => $description,
            'category_id' => $categoryId,
            'amount' => $transaction->amount,
            'count' => ($learningData[$key]['count'] ?? 0) + 1,
            'last_updated' => now(),
        ];

        \Cache::put($learningKey, $learningData, now()->addDays(30));
    }

    private function findCategoryIdBySlug(string $slug, ?int $userId = null): ?int
    {
        $userId = $userId ?? auth()->id();

        return Category::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->where(function ($query) use ($slug) {
                $query->where('name', 'like', '%'.str_replace('_', ' ', $slug).'%');
            })
            ->value('id');
    }
}
