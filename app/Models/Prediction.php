<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'predicted_amount' => 'decimal:2',
        'confidence' => 'decimal:2',
        'prediction_date' => 'date',
        'factors' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeHighConfidence($query, $threshold = 0.7)
    {
        return $query->where('confidence', '>=', $threshold);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('prediction_date', $date);
    }

    public function getConfidencePercentageAttribute()
    {
        return round($this->confidence * 100, 1);
    }

    public function getPeriodLabelAttribute()
    {
        return match ($this->period) {
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            default => ucfirst($this->period)
        };
    }
}
