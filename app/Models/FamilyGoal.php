<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyGoal extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'target_date' => 'date',
            'contributors' => 'array',
            'is_achieved' => 'boolean',
            'achieved_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }

        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (! $this->target_date) {
            return null;
        }

        return now()->diffInDays($this->target_date, false);
    }

    public function getContributorsAttribute($value): array
    {
        $contributors = $value;

        // If it's already an array (due to casting), it might still be double encoded if the DB has a JSON string inside a JSON string
        // But usually, if it's a string, we need to decode it.
        while (is_string($contributors)) {
            $decoded = json_decode($contributors, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }
            $contributors = $decoded;
            if (! is_array($contributors) && ! is_string($contributors)) {
                break;
            }
            if (is_array($contributors)) {
                break;
            }
        }

        return is_array($contributors) ? $contributors : [];
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
