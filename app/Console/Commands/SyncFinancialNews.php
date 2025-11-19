<?php

namespace App\Console\Commands;

use App\Models\EducationModule;
use App\Services\FinancialNewsPipeline;
use Illuminate\Console\Command;

class SyncFinancialNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financial:sync-news 
        {--tag=* : Override preferred tags}
        {--category=* : Override preferred categories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Curate and sync financial news with tagging for personalization';

    public function __construct(
        private readonly FinancialNewsPipeline $pipeline
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tags = array_filter((array) $this->option('tag')) ?: $this->defaultTags();
        $categories = array_filter((array) $this->option('category')) ?: $this->defaultCategories();

        $result = $this->pipeline->sync([
            'tags' => $tags,
            'categories' => $categories,
        ]);

        $this->info("Synced {$result['total']} articles from {$result['source']} source.");
        $this->line("Created: {$result['created']}, Updated: {$result['updated']}, Skipped: {$result['skipped']}");

        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    protected function defaultTags(): array
    {
        return EducationModule::query()
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(6)
            ->values()
            ->toArray();
    }

    /**
     * @return string[]
     */
    protected function defaultCategories(): array
    {
        return EducationModule::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->take(6)
            ->toArray();
    }
}
