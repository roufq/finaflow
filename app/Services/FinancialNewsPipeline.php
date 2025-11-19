<?php

namespace App\Services;

use App\Models\FinancialNews;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FinancialNewsPipeline
{
    public function __construct(
        private readonly ?string $endpoint = null,
        private readonly ?string $apiKey = null,
    ) {}

    public function sync(array $preferences = []): array
    {
        $endpoint = $this->endpoint ?? config('services.financial_news.endpoint');
        $apiKey = $this->apiKey ?? config('services.financial_news.api_key');

        $articles = $this->fetchFromApi($endpoint, $apiKey, $preferences);
        $source = 'api';

        if (empty($articles)) {
            $articles = $this->loadFromLocalSeed();
            $source = 'local';
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($articles as $article) {
            $payload = $this->preparePayload($article, $preferences);

            if (empty($payload['title']) || empty($payload['content'])) {
                $skipped++;

                continue;
            }

            $record = FinancialNews::updateOrCreate(
                [
                    'title' => $payload['title'],
                    'published_at' => $payload['published_at'],
                ],
                $payload
            );

            $record->wasRecentlyCreated ? $created++ : $updated++;
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'source' => $source,
            'total' => $created + $updated,
            'synced_at' => now(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fetchFromApi(?string $endpoint, ?string $apiKey, array $preferences): array
    {
        if (! $endpoint || ! $apiKey) {
            return [];
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->withHeaders(['Authorization' => $apiKey])
                ->get($endpoint, [
                    'limit' => 20,
                    'tags' => implode(',', $preferences['tags'] ?? []),
                    'categories' => implode(',', $preferences['categories'] ?? []),
                ]);

            if ($response->failed()) {
                report($response->toException());

                return [];
            }

            $body = $response->json();

            return data_get($body, 'articles', $body) ?? [];
        } catch (\Throwable $exception) {
            report($exception);

            return [];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function loadFromLocalSeed(): array
    {
        $path = resource_path('data/financial_news_seed.json');

        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }

    protected function preparePayload(array $article, array $preferences): array
    {
        $publishedAt = ! empty($article['published_at'])
            ? Carbon::parse($article['published_at'])
            : now();

        $tags = $this->tagArticle($article, $preferences);

        return [
            'title' => $article['title'] ?? '',
            'content' => $article['content'] ?? $article['summary'] ?? '',
            'source' => $article['source'] ?? $article['provider'] ?? 'Curated',
            'category' => $article['category'] ?? $preferences['categories'][0] ?? 'general',
            'published_at' => $publishedAt,
            'url' => $article['url'] ?? null,
            'tags' => array_values(array_unique($tags)),
        ];
    }

    /**
     * @return string[]
     */
    protected function tagArticle(array $article, array $preferences): array
    {
        $content = strtolower(strip_tags($article['content'] ?? $article['summary'] ?? ''));
        $tags = (array) ($article['tags'] ?? []);
        $topics = [
            'inflation' => ['inflation', 'cpi', 'consumer price'],
            'investment' => ['stock', 'equity', 'portfolio', 'dividend', 'yield'],
            'crypto' => ['crypto', 'bitcoin', 'ethereum'],
            'savings' => ['saving', 'deposit', 'interest rate'],
            'budgeting' => ['spending', 'budget', 'expense'],
            'policy' => ['government', 'policy', 'regulation'],
        ];

        foreach ($topics as $tag => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($content, $keyword)) {
                    $tags[] = $tag;

                    break;
                }
            }
        }

        $tags = array_merge($tags, $preferences['tags'] ?? []);

        if (! empty($article['category'])) {
            $tags[] = $article['category'];
        }

        return array_values(array_unique(array_filter($tags)));
    }
}
