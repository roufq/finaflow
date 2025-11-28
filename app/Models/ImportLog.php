<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

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
