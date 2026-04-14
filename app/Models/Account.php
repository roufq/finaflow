<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use \App\Models\Scopes\UserScope;
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'opening_date' => 'date',
        'is_active' => 'boolean',
        'setting_id' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_account_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_account_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getAvailableBalanceAttribute()
    {
        if ($this->type === 'credit_card') {
            return $this->credit_limit - abs($this->balance);
        }

        return $this->balance;
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 2);
    }

    public function getFormattedCreditLimitAttribute()
    {
        return $this->credit_limit ? number_format($this->credit_limit, 2) : null;
    }

    // Methods
    public function updateBalance($amount, $operation = 'add')
    {
        if ($operation === 'add') {
            $this->balance += $amount;
        } elseif ($operation === 'subtract') {
            $this->balance -= $amount;
        }

        $this->save();
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'bank' => 'Bank Account',
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'e_wallet' => 'E-Wallet',
            'investment' => 'Investment',
            'loan' => 'Loan',
            'savings' => 'Savings Account',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }
}
