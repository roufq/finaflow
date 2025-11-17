<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'habit_name',
        'category',
        'target_amount',
        'current_streak',
        'best_streak',
        'start_date',
        'last_achieved',
        'is_active',
        'progress_data',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'start_date' => 'date',
        'last_achieved' => 'date',
        'is_active' => 'boolean',
        'progress_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if habit was achieved today
     */
    public function isAchievedToday(): bool
    {
        return $this->last_achieved && $this->last_achieved->isToday();
    }

    /**
     * Mark habit as achieved for today
     */
    public function markAchieved(): void
    {
        $today = Carbon::today();

        if (!$this->isAchievedToday()) {
            if ($this->last_achieved && $this->last_achieved->isYesterday()) {
                $this->increment('current_streak');
            } else {
                $this->current_streak = 1;
            }

            $this->last_achieved = $today;

            if ($this->current_streak > $this->best_streak) {
                $this->best_streak = $this->current_streak;
            }

            $this->save();
        }
    }

    /**
     * Reset streak if habit not achieved consecutively
     */
    public function checkStreak(): void
    {
        if ($this->last_achieved && !$this->last_achieved->isToday() && !$this->last_achieved->isYesterday()) {
            $this->current_streak = 0;
            $this->save();
        }
    }

    /**
     * Get habit progress percentage
     */
    public function getProgressPercentage(): float
    {
        $totalDays = Carbon::parse($this->start_date)->diffInDays(Carbon::today()) + 1;
        $achievedDays = $this->progress_data ? count(array_filter($this->progress_data)) : 0;

        return $totalDays > 0 ? ($achievedDays / $totalDays) * 100 : 0;
    }
}
