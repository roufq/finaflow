<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    use \App\Models\Scopes\UserScope;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'lender',
        'original_amount',
        'current_balance',
        'interest_rate',
        'minimum_payment',
        'due_date',
        'status',
        'payoff_strategy',
        'payment_history'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'minimum_payment' => 'decimal:2',
        'due_date' => 'date',
        'payment_history' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->original_amount - $this->current_balance;
    }

    public function getPayoffProgressAttribute(): float
    {
        if ($this->original_amount <= 0) {
            return 100;
        }

        return (($this->original_amount - $this->current_balance) / $this->original_amount) * 100;
    }

    public function getMonthlyInterestAttribute(): float
    {
        return ($this->current_balance * $this->interest_rate / 100) / 12;
    }

    public function getEstimatedPayoffMonthsAttribute(): int
    {
        if ($this->minimum_payment <= $this->monthly_interest) {
            return 0; // Cannot pay off with minimum payment
        }

        $principal = $this->current_balance;
        $monthlyRate = $this->interest_rate / 100 / 12;
        $monthlyPayment = $this->minimum_payment;

        if ($monthlyRate == 0) {
            return ceil($principal / $monthlyPayment);
        }

        $months = -(log(1 - ($principal * $monthlyRate) / $monthlyPayment)) / log(1 + $monthlyRate);
        return (int) ceil($months);
    }

    public function getTotalInterestPaidAttribute(): float
    {
        return $this->total_paid - ($this->original_amount - $this->current_balance);
    }

    public function getEstimatedTotalPaymentAttribute(): float
    {
        return $this->current_balance + $this->estimated_total_interest;
    }

    public function getEstimatedTotalInterestAttribute(): float
    {
        $months = $this->estimated_payoff_months;
        $monthlyInterest = $this->monthly_interest;
        return $monthlyInterest * $months;
    }

    public function getEstimatedInterestPercentageAttribute(): float
    {
        if ($this->estimated_total_payment <= 0) {
            return 0;
        }
        return ($this->estimated_total_interest / $this->estimated_total_payment) * 100;
    }

    public function getIsMinimumPaymentMetAttribute(): bool
    {
        // This would need to be calculated based on recent payments
        // For now, return true as placeholder
        return true;
    }

    public function getNextPaymentDateAttribute(): \Carbon\Carbon
    {
        $today = now();
        $dueDate = $this->due_date;

        if ($dueDate->isFuture()) {
            return $dueDate;
        }

        // If due date has passed, calculate next due date
        while ($dueDate->isPast()) {
            $dueDate = $dueDate->addMonth();
        }

        return $dueDate;
    }

    // Accessors for labels
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'credit_card' => 'Kartu Kredit',
            'personal_loan' => 'Pinjaman Pribadi',
            'student_loan' => 'Pinjaman Pendidikan',
            'car_loan' => 'Pinjaman Mobil',
            'mortgage' => 'KPR',
            'business_loan' => 'Pinjaman Bisnis',
            'other' => 'Lainnya',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Aktif',
            'paid_off' => 'Lunas',
            'defaulted' => 'Macet',
            'settled' => 'Diselesaikan',
            'in_collections' => 'Dalam Penagihan',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'warning',
            'paid_off' => 'success',
            'defaulted' => 'danger',
            'settled' => 'info',
            'in_collections' => 'danger',
            default => 'secondary'
        };
    }

    public function getPayoffStrategyLabelAttribute()
    {
        return match($this->payoff_strategy) {
            'avalanche' => 'Avalanche (Bunga Tertinggi)',
            'snowball' => 'Snowball (Saldo Terkecil)',
            'custom' => 'Kustom',
            default => ucfirst($this->payoff_strategy ?? 'Tidak Ditentukan')
        };
    }
}
