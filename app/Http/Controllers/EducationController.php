<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunityStoryRequest;
use App\Jobs\ModerateCommunityStory;
use App\Models\CommunityStory;
use App\Models\EducationModule;
use App\Models\FinancialNews;
use App\Models\LearningPath;
use App\Services\FinancialNewsPipeline;
use App\Services\LearningPathPersonalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EducationController extends Controller
{
    public function __construct(
        private readonly LearningPathPersonalizer $personalizer
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'category' => $this->cleanFilter($request->input('category')),
            'difficulty' => $this->cleanFilter($request->input('difficulty')),
            'language' => $this->cleanFilter($request->input('language')),
            'search' => $this->cleanFilter($request->input('search')),
            'tag' => $this->cleanFilter($request->input('tag')),
        ];
        $newsTag = $this->cleanFilter($request->input('news_tag'));

        $moduleQuery = EducationModule::query()->active();
        $allModules = (clone $moduleQuery)->get();
        $modules = $this->applyModuleFilters(clone $moduleQuery, $filters)->get();

        $userId = Auth::id();
        $learningPaths = LearningPath::with('module')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('education_module_id');
        $newsPreferences = $this->newsPreferences($filters, $learningPaths);
        $news = $this->loadNews($newsTag, $newsPreferences);

        $progress = $this->progressOverview($learningPaths, $allModules);
        $recommendations = $this->personalizer->recommend($allModules, $learningPaths, $userId);
        $communityHighlights = $this->communityHighlights();
        $filterOptions = $this->moduleFilterOptions();
        $newsTags = $this->newsTagOptions();

        return view('education.index', compact(
            'modules',
            'learningPaths',
            'news',
            'progress',
            'recommendations',
            'communityHighlights',
            'filters',
            'filterOptions',
            'newsTags',
            'newsTag'
        ));
    }

    public function showModule(EducationModule $module): View
    {
        if (! $module->is_active) {
            abort(404);
        }

        $path = LearningPath::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'education_module_id' => $module->id,
            ],
            [
                'started_at' => now(),
                'recommendations' => [],
            ]
        );

        $relatedModules = EducationModule::where('category', $module->category)
            ->where('id', '!=', $module->id)
            ->limit(3)
            ->get();

        return view('education.module', compact('module', 'path', 'relatedModules'));
    }

    public function updateProgress(Request $request, EducationModule $module): RedirectResponse
    {
        $data = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $path = LearningPath::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'education_module_id' => $module->id,
            ],
            [
                'started_at' => now(),
            ]
        );

        $path->markProgress($data['progress']);

        return back()->with('success', __('education.messages.progress_updated'));
    }

    public function news(Request $request): View
    {
        $newsTag = $this->cleanFilter($request->input('tag'));
        $newsQuery = FinancialNews::recent();

        if ($newsTag) {
            $newsQuery->whereJsonContains('tags', $newsTag);
        }

        $news = $newsQuery->paginate(10)->withQueryString();

        if ($news->isEmpty()) {
            app(FinancialNewsPipeline::class)->sync([
                'tags' => array_filter([$newsTag]),
                'categories' => $newsTag ? [$newsTag] : [],
            ]);

            $news = $newsQuery->paginate(10)->withQueryString();
        }

        $newsTags = $this->newsTagOptions();

        return view('education.news', compact('news', 'newsTags', 'newsTag'));
    }

    public function storeCommunityStory(StoreCommunityStoryRequest $request): RedirectResponse
    {
        $story = CommunityStory::create([
            'user_id' => Auth::id(),
            'display_name' => $request->input('display_name') ?: Auth::user()?->name,
            'title' => $request->input('title'),
            'achievement' => $request->input('achievement'),
            'tip' => $request->input('tip'),
            'status' => 'pending',
        ]);

        ModerateCommunityStory::dispatch($story, Auth::id());

        return back()->with('success', __('education.community.submitted'));
    }

    protected function progressOverview(Collection $learningPaths, Collection $modules): array
    {
        $completed = $learningPaths->where('progress', '>=', 100)->count();
        $inProgress = $learningPaths->where('progress', '>', 0)->where('progress', '<', 100)->count();

        return [
            'completed' => $completed,
            'in_progress' => $inProgress,
            'total_modules' => $modules->count(),
            'completion_rate' => $modules->count() ? round(($completed / max(1, $modules->count())) * 100) : 0,
        ];
    }

    protected function communityHighlights(): array
    {
        $stories = CommunityStory::approved()
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        if ($stories->isNotEmpty()) {
            return $stories->map(function (CommunityStory $story) {
                return [
                    'name' => $story->author_name,
                    'title' => $story->title,
                    'achievement' => $story->achievement,
                    'tip' => $story->tip,
                ];
            })->toArray();
        }

        return [
            [
                'name' => 'Alya',
                'achievement' => 'Closed 3 credit cards in 6 months',
                'tip' => 'Followed debt avalanche plan and negotiated lower interest.',
                'title' => 'Debt Freedom Momentum',
            ],
            [
                'name' => 'Dimas',
                'achievement' => 'Built 6 month emergency fund',
                'tip' => 'Automated savings transfers every payday with envelope budgeting.',
                'title' => 'Emergency Fund Win',
            ],
            [
                'name' => 'Sari & Budi',
                'achievement' => 'Invested consistently for child education',
                'tip' => 'Used goal-based investing module and monthly review rituals.',
                'title' => 'Education Investing Journey',
            ],
        ];
    }

    protected function applyModuleFilters($query, array $filters)
    {
        return $query
            ->when($filters['category'], fn ($q, $category) => $q->where('category', $category))
            ->when($filters['difficulty'], fn ($q, $difficulty) => $q->where('difficulty', $difficulty))
            ->when($filters['language'], fn ($q, $language) => $q->where('language', $language))
            ->when($filters['tag'], fn ($q, $tag) => $q->whereJsonContains('tags', $tag))
            ->when($filters['search'], function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            });
    }

    protected function moduleFilterOptions(): array
    {
        $categories = EducationModule::query()->select('category')->distinct()->orderBy('category')->pluck('category')->toArray();
        $difficulties = EducationModule::query()->select('difficulty')->distinct()->orderBy('difficulty')->pluck('difficulty')->toArray();
        $languages = EducationModule::query()->select('language')->distinct()->orderBy('language')->pluck('language')->toArray();
        $tags = EducationModule::query()
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return compact('categories', 'difficulties', 'languages', 'tags');
    }

    protected function newsTagOptions(): array
    {
        return FinancialNews::query()
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    protected function loadNews(?string $tag, array $preferences = []): Collection
    {
        $query = FinancialNews::recent();

        if ($tag) {
            $query->whereJsonContains('tags', $tag);
        }

        $news = $query->limit(4)->get();

        if ($news->isEmpty()) {
            app(FinancialNewsPipeline::class)->sync($preferences);

            $query = FinancialNews::recent();

            if ($tag) {
                $query->whereJsonContains('tags', $tag);
            }

            return $query->limit(4)->get();
        }

        return $news;
    }

    protected function newsPreferences(array $filters, Collection $learningPaths): array
    {
        $learningPaths->loadMissing('module');

        $tags = collect([
            $filters['category'] ?? null,
            $filters['tag'] ?? null,
        ])
            ->merge($learningPaths->pluck('module.tag_list')->flatten())
            ->filter()
            ->unique()
            ->take(6)
            ->values()
            ->toArray();

        $categories = collect([$filters['category'] ?? null])
            ->merge($learningPaths->pluck('module.category'))
            ->filter()
            ->unique()
            ->take(6)
            ->values()
            ->toArray();

        return [
            'tags' => $tags,
            'categories' => $categories,
        ];
    }

    protected function cleanFilter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
