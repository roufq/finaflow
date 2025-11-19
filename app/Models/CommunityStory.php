<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'title',
        'achievement',
        'tip',
        'status',
        'moderated_by',
        'moderated_at',
        'moderation_flags',
        'moderator_notes',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'moderated_at' => 'datetime',
        'moderation_flags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function markApproved(): void
    {
        $this->status = 'approved';
        $this->save();
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->display_name ?: optional($this->user)->name ?: 'Anonymous';
    }

    public function getFlagSummaryAttribute(): string
    {
        $flags = $this->moderation_flags ?? [];

        return implode(', ', $flags);
    }
}
