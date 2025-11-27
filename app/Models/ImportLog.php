<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'bank_integration_id',
        'status',
        'source',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function bankIntegration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
