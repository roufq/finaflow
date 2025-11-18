<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anomaly extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'anomaly_type',
        'description',
        'severity',
        'is_resolved',
        'metadata',
        'detected_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'metadata' => 'array',
        'severity' => 'integer',
        'detected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('anomaly_type', $type);
    }

    public function markAsResolved()
    {
        $this->update(['is_resolved' => true]);
    }

    public function getSeverityLabelAttribute()
    {
        return match ($this->severity) {
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            default => 'Unknown'
        };
    }
}
