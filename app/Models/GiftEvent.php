<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftEvent extends Model
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
            'event_date' => 'date',
            'budget_amount' => 'decimal:2',
            'spent_amount' => 'decimal:2',
            'recipients' => 'array',
            'gifts' => 'array',
            'is_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBudgetRemainingAttribute(): float
    {
        return $this->budget_amount - $this->spent_amount;
    }

    public function getBudgetUsedPercentageAttribute(): float
    {
        if ($this->budget_amount == 0) {
            return 0;
        }

        return min(100, ($this->spent_amount / $this->budget_amount) * 100);
    }

    public function getRecipientsAttribute($value): array
    {
        $recipients = $value;
        while (is_string($recipients)) {
            $decoded = json_decode($recipients, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }
            $recipients = $decoded;
            if (! is_array($recipients) && ! is_string($recipients)) {
                break;
            }
            if (is_array($recipients)) {
                break;
            }
        }

        return is_array($recipients) ? $recipients : [];
    }

    public function getGiftsAttribute($value): array
    {
        $gifts = $value;
        while (is_string($gifts)) {
            $decoded = json_decode($gifts, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }
            $gifts = $decoded;
            if (! is_array($gifts) && ! is_string($gifts)) {
                break;
            }
            if (is_array($gifts)) {
                break;
            }
        }

        return is_array($gifts) ? $gifts : [];
    }

    public function getRecipientCountAttribute(): int
    {
        return count($this->recipients);
    }

    public function getGiftCountAttribute(): int
    {
        return count($this->gifts);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }
}
