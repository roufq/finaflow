<?php

namespace App\Jobs;

use App\Models\CommunityStory;
use App\Services\CommunityStoryModerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateCommunityStory implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public CommunityStory $story,
        public ?int $moderatorId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CommunityStoryModerator $moderator): void
    {
        $result = $moderator->review($this->story);

        $this->story->forceFill([
            'status' => $result['status'],
            'moderated_by' => $this->moderatorId,
            'moderated_at' => now(),
            'moderation_flags' => $result['flags'],
            'moderator_notes' => $result['notes'],
        ])->save();
    }
}
