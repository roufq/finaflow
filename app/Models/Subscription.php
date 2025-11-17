<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'provider',
        'amount',
        'frequency',
        'next_billing_date',
        'category',
        'auto_renewal',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_billing_date' => 'date',
        'auto_renewal' => 'boolean',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is due for renewal
     */
    public function isDueForRenewal(): bool
    {
        return $this->next_billing_date && $this->next_billing_date->isPast();
    }

    /**
     * Get days until next billing
     */
    public function getDaysUntilBilling(): int
    {
        return $this->next_billing_date ? Carbon::now()->diffInDays($this->next_billing_date, false) : 0;
    }

    /**
     * Calculate monthly cost
     */
    public function getMonthlyCost(): float
    {
        return match ($this->frequency) {
            'weekly' => $this->amount * 4.33, // Average weeks per month
            'monthly' => $this->amount,
            'quarterly' => $this->amount / 3,
            'yearly' => $this->amount / 12,
            default => $this->amount,
        };
    }

    /**
     * Update next billing date based on frequency
     */
    public function updateNextBillingDate(): void
    {
        if (!$this->next_billing_date) {
            return;
        }

        $this->next_billing_date = match ($this->frequency) {
            'weekly' => $this->next_billing_date->addWeek(),
            'monthly' => $this->next_billing_date->addMonth(),
            'quarterly' => $this->next_billing_date->addMonths(3),
            'yearly' => $this->next_billing_date->addYear(),
            default => $this->next_billing_date->addMonth(),
        };

        $this->save();
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->auto_renewal = false;
        $this->save();
    }

    /**
     * Pause subscription
     */
    public function pause(): void
    {
        $this->status = 'paused';
        $this->save();
    }

    /**
     * Resume subscription
     */
    public function resume(): void
    {
        $this->status = 'active';
        $this->save();
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for subscriptions due soon
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('next_billing_date', '<=', Carbon::now()->addDays($days))
                    ->where('next_billing_date', '>', Carbon::now());
    }
}
