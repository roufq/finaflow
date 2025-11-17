<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyGoal extends Model
{
    protected $fillable = [
        'user_id',
        'goal_name',
        'description',
        'target_amount',
        'current_amount',
        'target_date',
        'goal_type',
        'contributors',
        'is_achieved',
        'achieved_date',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'contributors' => 'array',
        'is_achieved' => 'boolean',
        'achieved_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount == 0) return 0;
        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->target_date) return null;
        return now()->diffInDays($this->target_date, false);
    }

    public function scopeAchieved($query)
    {
        return $query->where('is_achieved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_achieved', false)->where('target_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('goal_type', $type);
    }
}
