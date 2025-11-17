<?php

namespace App\Services;

use App\Models\Category;

class TransactionCategorizer
{
    /**
     * Guess the most relevant category id for a transaction.
     */
    public function guessCategoryId(string $description, float $amount, array $transactionData = []): ?int
    {
        $description = strtolower($description);

        $keywordMatrix = [
            'grocery' => ['grocery', 'supermarket', 'indomaret', 'alfamart', 'carrefour'],
            'transport' => ['grab', 'gojek', 'uber', 'train', 'bus', 'fuel', 'pertamina'],
            'shopping' => ['tokopedia', 'shopee', 'lazada', 'mall', 'store', 'retail'],
            'entertainment' => ['movie', 'cinema', 'game', 'spotify', 'netflix'],
            'utilities' => ['pln', 'pdam', 'internet', 'telkom', 'wifi', 'bpjs'],
            'salary' => ['salary', 'payroll', 'gaji', 'bonus'],
        ];

        foreach ($keywordMatrix as $categorySlug => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($description, $keyword)) {
                    return $this->findCategoryIdBySlug($categorySlug);
                }
            }
        }

        // Amount-based heuristics
        if ($amount >= 5000000) {
            return $this->findCategoryIdBySlug('salary');
        }

        if ($amount >= 2000000) {
            return $this->findCategoryIdBySlug('utilities');
        }

        return null;
    }

    private function findCategoryIdBySlug(string $slug): ?int
    {
        $query = Category::where('user_id', auth()->id());

        return $query->where('slug', $slug)
            ->orWhere('name', 'like', '%' . str_replace('_', ' ', $slug) . '%')
            ->value('id');
    }
}
