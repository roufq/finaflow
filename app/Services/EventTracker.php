<?php

namespace App\Services;

use App\Models\EventLog;
use Illuminate\Http\Request;

class EventTracker
{
    public function log(string $name, array $properties = [], ?Request $request = null, ?int $userId = null): void
    {
        $ip = $request?->ip();
        $userAgent = $request?->header('User-Agent');

        EventLog::create([
            'user_id' => $userId,
            'name' => $name,
            'source' => $this->detectSource(),
            'properties' => $properties,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    private function detectSource(): string
    {
        if (app()->runningInConsole()) {
            return 'console';
        }

        return 'http';
    }
}
