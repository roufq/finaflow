<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardChartService;
use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private DashboardChartService $chartService, private CacheManager $cacheManager) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $currentMonth = $validated['month'] ?? now()->month;
        $currentYear = $validated['year'] ?? now()->year;
        $periodDate = Carbon::createFromDate($currentYear, $currentMonth, 1);
        $userId = Auth::id();

        $cacheKey = sprintf('dashboard:%d:%d-%02d', $userId, $periodDate->year, $periodDate->month);

        $cacheStore = $this->resolveCacheStore();

        $dashboardData = $cacheStore->remember(
            $cacheKey,
            now()->addMinutes(15),
            fn () => $this->chartService->buildDashboardData($userId, $periodDate)
        );

        return view('dashboard', $dashboardData);
    }

    private function resolveCacheStore(): CacheRepository
    {
        if (class_exists(\Redis::class)) {
            return $this->cacheManager->store('redis');
        }

        return $this->cacheManager->store(config('cache.default'));
    }
}
