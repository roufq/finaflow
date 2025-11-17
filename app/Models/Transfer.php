<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use \App\Models\Scopes\UserScope;

    protected $fillable = [
        'user_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'fee',
        'transfer_date',
        'description',
        'reference_number',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'transfer_date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transfer_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->fee;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedFeeAttribute()
    {
        return number_format($this->fee, 2);
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'primary'
        };
    }

    // Methods
    public function processTransfer()
    {
        // Update account balances
        $this->fromAccount->updateBalance($this->total_amount, 'subtract');
        $this->toAccount->updateBalance($this->amount, 'add');

        $this->status = 'completed';
        $this->save();
    }

    public function reverseTransfer()
    {
        if ($this->status === 'completed') {
            // Reverse the balances
            $this->fromAccount->updateBalance($this->total_amount, 'add');
            $this->toAccount->updateBalance($this->amount, 'subtract');

            $this->status = 'cancelled';
            $this->save();
        }
    }
}
