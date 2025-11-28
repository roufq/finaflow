<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalProgress extends Model
{
    protected $table = 'goal_progresses';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
