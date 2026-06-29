<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    use UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'quantity' => 'decimal:8',
        'purchase_price' => 'decimal:4',
        'current_price' => 'decimal:4',
        'dividends_received' => 'decimal:2',
        'fees' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculate total investment value at purchase
    public function getTotalPurchaseValueAttribute(): float
    {
        return $this->quantity * $this->purchase_price;
    }

    // Calculate current total value
    public function getCurrentValueAttribute(): float
    {
        return $this->current_price ? $this->quantity * $this->current_price : 0;
    }

    // Calculate unrealized gain/loss
    public function getUnrealizedGainLossAttribute(): float
    {
        return $this->current_value - $this->total_purchase_value;
    }

    // Calculate ROI percentage
    public function getRoiPercentageAttribute(): float
    {
        if ($this->total_purchase_value == 0) {
            return 0;
        }

        return (($this->current_value - $this->total_purchase_value) / $this->total_purchase_value) * 100;
    }

    // Calculate total return including dividends
    public function getTotalReturnAttribute(): float
    {
        return $this->unrealized_gain_loss + $this->dividends_received - $this->fees;
    }

    // Calculate total return percentage
    public function getTotalReturnPercentageAttribute(): float
    {
        if ($this->total_purchase_value == 0) {
            return 0;
        }

        return ($this->total_return / $this->total_purchase_value) * 100;
    }
}
