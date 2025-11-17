<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Gamification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'level',
        'badges',
        'achievements',
        'streak_days',
        'last_activity',
        'progress',
    ];

    protected $casts = [
        'points' => 'integer',
        'level' => 'integer',
        'badges' => 'array',
        'achievements' => 'array',
        'streak_days' => 'integer',
        'last_activity' => 'date',
        'progress' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add points to user
     */
    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
        $this->checkLevelUp();
        $this->updateStreak();
        $this->save();
    }

    /**
     * Check if user should level up
     */
    public function checkLevelUp(): void
    {
        $pointsPerLevel = 1000; // Adjust as needed
        $newLevel = floor($this->points / $pointsPerLevel) + 1;

        if ($newLevel > $this->level) {
            $this->level = $newLevel;
            $this->unlockLevelBadges();
        }
    }

    /**
     * Update activity streak
     */
    public function updateStreak(): void
    {
        $today = Carbon::today();

        if ($this->last_activity && $this->last_activity->isYesterday()) {
            $this->increment('streak_days');
        } elseif (!$this->last_activity || !$this->last_activity->isToday()) {
            $this->streak_days = 1;
        }

        $this->last_activity = $today;
    }

    /**
     * Unlock badges based on level
     */
    public function unlockLevelBadges(): void
    {
        $badges = $this->badges ?? [];

        $levelBadges = [
            1 => 'Financial Beginner',
            5 => 'Budget Master',
            10 => 'Investment Guru',
            15 => 'Wealth Builder',
            20 => 'Financial Expert',
        ];

        if (isset($levelBadges[$this->level])) {
            $badges[] = $levelBadges[$this->level];
            $this->badges = array_unique($badges);
        }
    }

    /**
     * Award achievement
     */
    public function awardAchievement(string $achievement): void
    {
        $achievements = $this->achievements ?? [];
        if (!in_array($achievement, $achievements)) {
            $achievements[] = $achievement;
            $this->achievements = $achievements;
            $this->addPoints(500); // Bonus points for achievements
            $this->save();
        }
    }

    /**
     * Get points needed for next level
     */
    public function getPointsToNextLevel(): int
    {
        $pointsPerLevel = 1000;
        $currentLevelPoints = ($this->level - 1) * $pointsPerLevel;
        $nextLevelPoints = $this->level * $pointsPerLevel;

        return $nextLevelPoints - $this->points;
    }

    /**
     * Get level progress percentage
     */
    public function getLevelProgress(): float
    {
        $pointsPerLevel = 1000;
        $currentLevelPoints = ($this->level - 1) * $pointsPerLevel;
        $progressPoints = $this->points - $currentLevelPoints;

        return ($progressPoints / $pointsPerLevel) * 100;
    }
}
