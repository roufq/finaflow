<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MicroLearning extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'format',
        'language',
        'duration_minutes',
        'difficulty',
        'summary',
        'content',
        'content_url',
        'persona_tags',
        'is_active',
    ];

    protected $casts = [
        'persona_tags' => 'array',
        'is_active' => 'boolean',
    ];

    public function progresses(): HasMany
    {
        return $this->hasMany(MicroLearningProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
