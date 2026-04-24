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
        $search = $request->query('search');

        $logs = ActivityLog::query()
            ->where('user_id', $request->user()->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(15);

        return view('activity-log.index', [
            'logs' => $logs->appends(['search' => $search]),
        ]);
    }
}
