<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ApiIntegration extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'api_key',
        'settings',
        'last_sync_at',
        'is_active',
        'rate_limit_remaining',
        'rate_limit_reset_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
        'rate_limit_remaining' => 'integer',
        'rate_limit_reset_at' => 'datetime',
        'api_key' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sync data from API provider
     */
    public function syncData(): array
    {
        if (! $this->is_active) {
            return ['success' => false, 'message' => 'Integration is not active'];
        }

        try {
            $data = [];

            switch ($this->provider) {
                case 'credit_score':
                    $data = $this->syncCreditScore();
                    break;
                case 'investment_data':
                    $data = $this->syncInvestmentData();
                    break;
                case 'news':
                    $data = $this->syncFinancialNews();
                    break;
                case 'weather':
                    $data = $this->syncWeatherData();
                    break;
                default:
                    return ['success' => false, 'message' => 'Unknown provider'];
            }

            $this->update(['last_sync_at' => now()]);

            return [
                'success' => true,
                'data' => $data,
                'message' => "Successfully synced {$this->provider}",
            ];

        } catch (\Exception $e) {
            \Log::error("API sync failed for {$this->provider}: ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Sync failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Sync credit score data
     */
    private function syncCreditScore(): array
    {
        // Placeholder for credit score API integration
        // Would integrate with services like Experian, Equifax, TransUnion

        $cacheKey = "credit_score_{$this->user_id}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        // Simulate API call
        $score = rand(300, 850); // Random score for demo

        $data = [
            'score' => $score,
            'date' => now()->format('Y-m-d'),
            'factors' => [
                'payment_history' => rand(1, 100),
                'credit_utilization' => rand(1, 100),
                'credit_age' => rand(1, 100),
            ],
        ];

        Cache::put($cacheKey, $data, now()->addHours(24));

        return $data;
    }

    /**
     * Sync investment data
     */
    private function syncInvestmentData(): array
    {
        // Placeholder for investment data API
        // Would integrate with Yahoo Finance, Alpha Vantage, etc.

        $cacheKey = "investment_data_{$this->user_id}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        // Simulate market data
        $data = [
            'indices' => [
                'sp500' => [
                    'value' => rand(4000, 5000),
                    'change' => rand(-50, 50),
                    'change_percent' => rand(-2, 2),
                ],
                'nasdaq' => [
                    'value' => rand(13000, 16000),
                    'change' => rand(-100, 100),
                    'change_percent' => rand(-2, 2),
                ],
            ],
            'currencies' => [
                'usd_idr' => rand(14000, 16000),
                'eur_usd' => rand(1, 2),
            ],
        ];

        Cache::put($cacheKey, $data, now()->addMinutes(15));

        return $data;
    }

    /**
     * Sync financial news
     */
    private function syncFinancialNews(): array
    {
        // Placeholder for financial news API
        // Would integrate with NewsAPI, Alpha Vantage News, etc.

        $cacheKey = "financial_news_{$this->user_id}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        // Simulate news data
        $news = [
            [
                'title' => 'Market Update: Tech Stocks Rally',
                'summary' => 'Technology stocks showed strong performance today...',
                'source' => 'Financial Times',
                'published_at' => now()->subHours(2)->format('Y-m-d H:i:s'),
                'url' => 'https://example.com/news/1',
            ],
            [
                'title' => 'Federal Reserve Signals Interest Rate Decision',
                'summary' => 'The Federal Reserve indicated potential changes...',
                'source' => 'Reuters',
                'published_at' => now()->subHours(4)->format('Y-m-d H:i:s'),
                'url' => 'https://example.com/news/2',
            ],
        ];

        Cache::put($cacheKey, $news, now()->addHours(1));

        return $news;
    }

    /**
     * Sync weather data for seasonal spending insights
     */
    private function syncWeatherData(): array
    {
        // Placeholder for weather API
        // Would integrate with OpenWeatherMap, WeatherAPI, etc.

        $cacheKey = "weather_data_{$this->user_id}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        // Simulate weather data for Jakarta (default location)
        $weatherConditions = ['Sunny', 'Cloudy', 'Rainy', 'Stormy'];
        $temperatures = [25, 28, 30, 32, 27];

        $data = [
            'location' => 'Jakarta, Indonesia',
            'current' => [
                'condition' => $weatherConditions[array_rand($weatherConditions)],
                'temperature' => $temperatures[array_rand($temperatures)],
                'humidity' => rand(60, 90),
                'last_updated' => now()->format('Y-m-d H:i:s'),
            ],
            'forecast' => [
                [
                    'date' => now()->addDay()->format('Y-m-d'),
                    'condition' => $weatherConditions[array_rand($weatherConditions)],
                    'temp_min' => rand(24, 28),
                    'temp_max' => rand(29, 33),
                ],
                [
                    'date' => now()->addDays(2)->format('Y-m-d'),
                    'condition' => $weatherConditions[array_rand($weatherConditions)],
                    'temp_min' => rand(24, 28),
                    'temp_max' => rand(29, 33),
                ],
            ],
        ];

        Cache::put($cacheKey, $data, now()->addHours(3));

        return $data;
    }

    /**
     * Check rate limits
     */
    public function checkRateLimit(): bool
    {
        if (! $this->rate_limit_reset_at || now()->isAfter($this->rate_limit_reset_at)) {
            // Reset rate limit
            $this->update([
                'rate_limit_remaining' => $this->getMaxRequests(),
                'rate_limit_reset_at' => now()->addHour(),
            ]);

            return true;
        }

        return $this->rate_limit_remaining > 0;
    }

    /**
     * Consume rate limit
     */
    public function consumeRateLimit(): void
    {
        if ($this->rate_limit_remaining > 0) {
            $this->decrement('rate_limit_remaining');
        }
    }

    /**
     * Get max requests per hour for this provider
     */
    private function getMaxRequests(): int
    {
        return match ($this->provider) {
            'credit_score' => 10,
            'investment_data' => 100,
            'news' => 50,
            'weather' => 1000,
            default => 100
        };
    }

    /**
     * Get integration status
     */
    public function getStatus(): array
    {
        return [
            'is_active' => $this->is_active,
            'provider' => $this->provider,
            'last_sync' => $this->last_sync_at?->diffForHumans(),
            'rate_limit_remaining' => $this->rate_limit_remaining,
            'rate_limit_reset' => $this->rate_limit_reset_at?->diffForHumans(),
        ];
    }
}
