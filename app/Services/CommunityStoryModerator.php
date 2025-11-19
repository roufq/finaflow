<?php

namespace App\Services;

use App\Models\CommunityStory;
use Illuminate\Support\Str;

class CommunityStoryModerator
{
    /**
     * Automatically review a community story and decide its status.
     *
     * @return array{status:string, flags:array<int,string>, notes:string}
     */
    public function review(CommunityStory $story): array
    {
        $flags = [];
        $content = Str::of($story->achievement.' '.$story->tip)->lower();

        foreach ($this->bannedKeywords() as $keyword) {
            if ($content->contains($keyword)) {
                $flags[] = "keyword:{$keyword}";
            }
        }

        if (Str::length($story->achievement) < 60) {
            $flags[] = 'insufficient_detail';
        }

        if (! $story->tip || Str::length($story->tip) < 20) {
            $flags[] = 'missing_tip';
        }

        if ($content->contains('http://') || $content->contains('https://')) {
            $flags[] = 'external_link';
        }

        $status = 'approved';

        if (! empty($flags)) {
            $status = count($flags) >= 3 ? 'rejected' : 'pending';
        }

        return [
            'status' => $status,
            'flags' => $flags,
            'notes' => empty($flags)
                ? 'Auto-approved by heuristic moderation.'
                : 'Flagged: '.implode(', ', $flags),
        ];
    }

    /**
     * @return string[]
     */
    protected function bannedKeywords(): array
    {
        return [
            'promo',
            'sponsor',
            'discount',
            'whatsapp',
            'clickbait',
            'crypto scheme',
        ];
    }
}
