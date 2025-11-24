<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display the authenticated user's activity log.
     */
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(15);

        return view('activity-log.index', [
            'logs' => $logs,
        ]);
    }
}
