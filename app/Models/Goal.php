<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use \App\Models\Scopes\UserScope;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'type',
        'target_amount',
        'current_amount',
        'target_date',
        'status',
        'milestones'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'milestones' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->target_date, false));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->target_date < now() && $this->status === 'active';
    }

    public function getMonthlyContributionNeededAttribute(): float
    {
        $daysRemaining = $this->days_remaining;
        if ($daysRemaining <= 0) {
            return 0;
        }

        $monthsRemaining = $daysRemaining / 30; // Approximate
        return $this->remaining_amount / max(1, $monthsRemaining);
    }

    public function getCategoryLabel(): string
    {
        $categories = [
            'savings' => 'Tabungan',
            'investment' => 'Investasi',
            'debt_payment' => 'Pelunasan Hutang',
            'purchase' => 'Pembelian',
            'travel' => 'Perjalanan',
            'education' => 'Pendidikan',
            'health' => 'Kesehatan',
            'emergency' => 'Dana Darurat',
            'other' => 'Lainnya'
        ];

        return $categories[$this->category] ?? 'Lainnya';
    }

    public function getTypeLabel(): string
    {
        $types = [
            'short_term' => 'Jangka Pendek',
            'medium_term' => 'Jangka Menengah',
            'long_term' => 'Jangka Panjang'
        ];

        return $types[$this->type] ?? 'Jangka Pendek';
    }

    public function getStatusLabel(): string
    {
        $statuses = [
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'paused' => 'Ditunda',
            'cancelled' => 'Dibatalkan'
        ];

        return $statuses[$this->status] ?? 'Aktif';
    }

    public function getStatusColor(): string
    {
        $colors = [
            'active' => 'primary',
            'completed' => 'success',
            'paused' => 'warning',
            'cancelled' => 'danger'
        ];

        return $colors[$this->status] ?? 'primary';
    }
}
