<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpendingTrigger extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'amount_threshold' => 'decimal:2',
        'frequency' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment the trigger frequency
     */
    public function incrementFrequency(): void
    {
        $this->increment('frequency');
    }

    /**
     * Check if this trigger matches a transaction
     */
    public function matchesTransaction(Transaction $transaction): bool
    {
        // Implement logic to check if transaction matches this trigger
        // Based on amount, category, time, location, etc.
        if ($this->amount_threshold && $transaction->amount >= $this->amount_threshold) {
            return true;
        }

        return false;
    }
}
