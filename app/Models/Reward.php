<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_type',
        'reward_type',
        'points_earned',
        'points_redeemed',
        'cashback_amount',
        'expiry_date',
        'status',
        'transaction_history',
    ];

    protected $casts = [
        'points_earned' => 'decimal:2',
        'points_redeemed' => 'decimal:2',
        'cashback_amount' => 'decimal:2',
        'expiry_date' => 'date',
        'transaction_history' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get available points balance
     */
    public function getAvailablePoints(): float
    {
        return $this->points_earned - $this->points_redeemed;
    }

    /**
     * Add earned points
     */
    public function addPoints(float $points): void
    {
        $this->increment('points_earned', $points);
        $this->addTransactionHistory('earned', $points);
        $this->save();
    }

    /**
     * Redeem points
     */
    public function redeemPoints(float $points): bool
    {
        if ($this->getAvailablePoints() >= $points) {
            $this->increment('points_redeemed', $points);
            $this->addTransactionHistory('redeemed', $points);
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Add cashback amount
     */
    public function addCashback(float $amount): void
    {
        $this->increment('cashback_amount', $amount);
        $this->addTransactionHistory('cashback', $amount);
        $this->save();
    }

    /**
     * Add transaction to history
     */
    private function addTransactionHistory(string $type, float $amount): void
    {
        $history = $this->transaction_history ?? [];
        $history[] = [
            'type' => $type,
            'amount' => $amount,
            'date' => Carbon::now()->toDateString(),
        ];
        $this->transaction_history = $history;
    }

    /**
     * Check if reward is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry(): ?int
    {
        return $this->expiry_date ? Carbon::now()->diffInDays($this->expiry_date, false) : null;
    }

    /**
     * Get cashback rate for different card types
     */
    public static function getCashbackRate(string $cardType): float
    {
        return match ($cardType) {
            'visa' => 0.01, // 1%
            'mastercard' => 0.015, // 1.5%
            'amex' => 0.02, // 2%
            default => 0.01,
        };
    }

    /**
     * Calculate potential rewards for a transaction
     */
    public function calculatePotentialReward(float $transactionAmount, string $category = null): array
    {
        $rewards = [];

        // Points calculation
        if ($this->reward_type === 'points') {
            $pointsRate = 1; // 1 point per dollar spent
            $points = $transactionAmount * $pointsRate;
            $rewards['points'] = $points;
        }

        // Cashback calculation
        if ($this->reward_type === 'cashback') {
            $cashbackRate = self::getCashbackRate($this->card_type);
            $cashback = $transactionAmount * $cashbackRate;
            $rewards['cashback'] = $cashback;
        }

        return $rewards;
    }

    /**
     * Scope for active rewards
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', Carbon::now());
                    });
    }
}
