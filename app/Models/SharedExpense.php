<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedExpense extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'expense_date' => 'date',
            'participants' => 'array',
            'is_settled' => 'boolean',
            'settlement_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getParticipantsAttribute($value): array
    {
        $participants = $value;

        // Handle potential double-encoding or string storage
        while (is_string($participants)) {
            $decoded = json_decode($participants, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }
            $participants = $decoded;
            // Avoid infinite loop if it decodes to the same string
            if (! is_array($participants) && ! is_string($participants)) {
                break;
            }
            if (is_array($participants)) {
                break;
            }
        }

        return is_array($participants) ? $participants : [];
    }

    public function getParticipantCountAttribute(): int
    {
        return count($this->participants);
    }

    public function getAverageShareAttribute(): float
    {
        $count = $this->participant_count;

        if ($count === 0) {
            return 0;
        }

        return (float) $this->total_amount / $count;
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
