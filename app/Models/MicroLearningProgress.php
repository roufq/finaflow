<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroLearningProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'micro_learning_id',
        'user_id',
        'status',
        'comprehension_score',
        'last_accessed_at',
        'completed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(MicroLearning::class, 'micro_learning_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
