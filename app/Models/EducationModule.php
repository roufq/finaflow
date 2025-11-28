<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationModule extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'tags' => 'array',
        'learning_objectives' => 'array',
        'resource_links' => 'array',
    ];

    public function learningPaths(): HasMany
    {
        return $this->hasMany(LearningPath::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getDifficultyLabelAttribute(): string
    {
        return ucfirst($this->difficulty);
    }

    public function getLanguageLabelAttribute(): string
    {
        return strtoupper($this->language ?? 'ID');
    }

    public function getTagListAttribute(): array
    {
        return $this->tags ?? $this->metadata['tags'] ?? [];
    }

    public function getObjectiveListAttribute(): array
    {
        return $this->learning_objectives ?? $this->metadata['learning_objectives'] ?? [];
    }

    public function getResourceListAttribute(): array
    {
        return $this->resource_links ?? $this->metadata['resources'] ?? [];
    }
}
