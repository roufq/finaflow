<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'category_allocations' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_budget - $this->spent_amount);
    }

    public function getSpentPercentageAttribute(): float
    {
        if ($this->total_budget <= 0) {
            return 0;
        }

        return min(100, ($this->spent_amount / $this->total_budget) * 100);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->spent_amount > $this->total_budget;
    }

    public function getDaysRemainingAttribute(): int
    {
        $endDate = $this->end_date ?? now()->addDays(30); // Default to 30 days if no end date

        return max(0, now()->diffInDays($endDate, false));
    }

    public function getDailyBudgetAttribute(): float
    {
        $totalDays = $this->getPeriodDays();

        return $totalDays > 0 ? $this->total_budget / $totalDays : 0;
    }

    public function getAverageDailySpendAttribute(): float
    {
        $daysElapsed = max(1, now()->diffInDays($this->start_date));

        return $this->spent_amount / $daysElapsed;
    }

    private function getPeriodDays(): int
    {
        if ($this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }

        // Default periods
        return match ($this->period) {
            'weekly' => 7,
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
            default => 30
        };
    }

    // Accessors for labels
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'zero_based' => 'Zero Based',
            'envelope' => 'Envelope',
            'percentage_based' => 'Persentase',
            'fixed_amount' => 'Jumlah Tetap',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    public function getPeriodLabelAttribute()
    {
        return match ($this->period) {
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'quarterly' => 'Triwulanan',
            'yearly' => 'Tahunan',
            default => ucfirst($this->period)
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'paused' => 'Dijeda',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'completed' => 'primary',
            'paused' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
}
