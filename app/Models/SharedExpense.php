<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedExpense extends Model
{
    protected $fillable = [
        'user_id',
        'expense_name',
        'description',
        'total_amount',
        'category',
        'expense_date',
        'participants',
        'split_method',
        'is_settled',
        'settlement_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expense_date' => 'date',
        'participants' => 'array',
        'is_settled' => 'boolean',
        'settlement_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getParticipantCountAttribute(): int
    {
        return count($this->participants ?? []);
    }

    public function getAverageShareAttribute(): float
    {
        if ($this->participant_count === 0) return 0;
        return $this->total_amount / $this->participant_count;
    }

    public function scopeSettled($query)
    {
        return $query->where('is_settled', true);
    }

    public function scopeUnsettled($query)
    {
        return $query->where('is_settled', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
