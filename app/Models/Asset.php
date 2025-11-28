<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use \App\Models\Scopes\UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'purchase_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'purchase_date' => 'date',
        'insurance_expiry' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculate depreciated value based on time passed
    public function getDepreciatedValueAttribute(): float
    {
        if (! $this->depreciation_rate || ! $this->purchase_date) {
            return $this->current_value;
        }

        $yearsPassed = Carbon::now()->diffInYears($this->purchase_date);
        $depreciationFactor = pow(1 - ($this->depreciation_rate / 100), $yearsPassed);

        return $this->purchase_value * $depreciationFactor;
    }

    // Calculate annual depreciation amount
    public function getAnnualDepreciationAttribute(): float
    {
        if (! $this->depreciation_rate) {
            return 0;
        }

        return $this->purchase_value * ($this->depreciation_rate / 100);
    }

    // Calculate total depreciation to date
    public function getTotalDepreciationAttribute(): float
    {
        return $this->purchase_value - $this->depreciated_value;
    }

    // Calculate annual income
    public function getAnnualIncomeAttribute(): float
    {
        return $this->monthly_income * 12;
    }

    // Check if insurance is expired
    public function getIsInsuranceExpiredAttribute(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->isPast();
    }

    // Check if insurance expires soon (within 30 days)
    public function getInsuranceExpiresSoonAttribute(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->diffInDays(Carbon::now()) <= 30;
    }

    // Get asset age in years
    public function getAgeInYearsAttribute(): float
    {
        return $this->purchase_date ? $this->purchase_date->diffInDays(Carbon::now()) / 365.25 : 0;
    }
}
