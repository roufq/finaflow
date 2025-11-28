<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningPath extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'recommendations' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(EducationModule::class, 'education_module_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markProgress(int $value): void
    {
        $this->progress = min(100, max(0, $value));
        if ($this->progress === 100 && ! $this->completed_at) {
            $this->completed_at = now();
        }
        $this->save();
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->progress >= 100;
    }

    public function getRecommendedNextModuleAttribute(): ?string
    {
        return $this->recommendations['next'] ?? null;
    }
}
