<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'points_balance' => 'decimal:2',
        'benefits' => 'array',
        'expiry_date' => 'date',
        'redemption_history' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add points to balance
     */
    public function addPoints(float $points): void
    {
        $this->increment('points_balance', $points);
        $this->checkTierUpgrade();
        $this->save();
    }

    /**
     * Redeem points
     */
    public function redeemPoints(float $points, ?string $rewardDescription = null): bool
    {
        if ($this->points_balance >= $points) {
            $this->decrement('points_balance', $points);
            $this->addRedemptionHistory($points, $rewardDescription);
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Add redemption to history
     */
    private function addRedemptionHistory(float $points, ?string $description = null): void
    {
        $history = $this->redemption_history ?? [];
        $history[] = [
            'points_redeemed' => $points,
            'description' => $description,
            'date' => Carbon::now()->toDateString(),
        ];
        $this->redemption_history = $history;
    }

    /**
     * Check if tier should be upgraded
     */
    public function checkTierUpgrade(): void
    {
        $tierThresholds = [
            'bronze' => 0,
            'silver' => 10000,
            'gold' => 25000,
            'platinum' => 50000,
            'diamond' => 100000,
        ];

        $currentTierValue = $tierThresholds[$this->tier_level] ?? 0;

        foreach ($tierThresholds as $tier => $threshold) {
            if ($this->points_balance >= $threshold && $threshold > $currentTierValue) {
                $this->tier_level = $tier;
                $this->updateBenefitsForTier();
                break;
            }
        }
    }

    /**
     * Update benefits based on tier
     */
    private function updateBenefitsForTier(): void
    {
        $tierBenefits = [
            'bronze' => ['Basic member benefits'],
            'silver' => ['Priority boarding', 'Extra baggage allowance'],
            'gold' => ['Lounge access', 'Bonus points on flights', 'Priority customer service'],
            'platinum' => ['Premium lounge access', 'Complimentary upgrades', 'Dedicated concierge'],
            'diamond' => ['VIP treatment', 'Exclusive events', 'Personal travel consultant'],
        ];

        $this->benefits = $tierBenefits[$this->tier_level] ?? [];
    }

    /**
     * Get points needed for next tier
     */
    public function getPointsToNextTier(): ?int
    {
        $tierThresholds = [
            'bronze' => 0,
            'silver' => 10000,
            'gold' => 25000,
            'platinum' => 50000,
            'diamond' => 100000,
        ];

        $tiers = array_keys($tierThresholds);
        $currentIndex = array_search($this->tier_level, $tiers);

        if ($currentIndex !== false && isset($tiers[$currentIndex + 1])) {
            $nextTier = $tiers[$currentIndex + 1];

            return $tierThresholds[$nextTier] - $this->points_balance;
        }

        return null; // Already at highest tier
    }

    /**
     * Check if membership is expired
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
     * Get earning rate for different program types
     */
    public static function getEarningRate(string $programType): float
    {
        return match ($programType) {
            'airline' => 1.5, // 1.5 points per dollar
            'hotel' => 1.0, // 1 point per dollar
            'credit_card' => 2.0, // 2 points per dollar
            'retail' => 1.0, // 1 point per dollar
            default => 1.0,
        };
    }

    /**
     * Calculate potential points for a transaction
     */
    public function calculatePotentialPoints(float $transactionAmount): float
    {
        $earningRate = self::getEarningRate($this->program_type);

        return $transactionAmount * $earningRate;
    }

    /**
     * Scope for active programs
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>', Carbon::now());
        });
    }
}
