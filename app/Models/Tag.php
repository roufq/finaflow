<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use \App\Models\Scopes\UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'color' => 'string',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'transaction_tags')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getFormattedColorAttribute()
    {
        return $this->color ?? '#007bff';
    }

    public function getTransactionCountAttribute()
    {
        return $this->transactions()->count();
    }

    // Methods
    public function getTransactionsCount()
    {
        return $this->transactions()->count();
    }
}
