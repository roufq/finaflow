<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActionPlan extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'plan_month' => 'date',
        'focus_priorities' => 'array',
        'recommended_actions' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ActionPlanTask::class);
    }

    public function getMonthLabelAttribute(): string
    {
        return $this->plan_month?->translatedFormat('F Y') ?? '';
    }

    public function scopeForMonth($query, string $month)
    {
        return $query->whereDate('plan_month', '=', $month);
    }
}
