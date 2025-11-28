<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftEvent extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'event_date' => 'date',
        'budget_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'recipients' => 'array',
        'gifts' => 'array',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBudgetRemainingAttribute(): float
    {
        return $this->budget_amount - $this->spent_amount;
    }

    public function getBudgetUsedPercentageAttribute(): float
    {
        if ($this->budget_amount == 0) {
            return 0;
        }

        return min(100, ($this->spent_amount / $this->budget_amount) * 100);
    }

    public function getRecipientCountAttribute(): int
    {
        return count($this->recipients ?? []);
    }

    public function getGiftCountAttribute(): int
    {
        return count($this->gifts ?? []);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }
}
