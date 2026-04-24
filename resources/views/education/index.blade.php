@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('education.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">{{ __('education.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('education.news') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-newspaper mr-2"></i>
                {{ __('education.news.view_all') }}
            </a>
        </div>
    </div>

    <!-- Learning Metrics Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('education.metrics.completed') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $progress['completed'] }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Modules Mastery</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-primary-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('education.metrics.in_progress') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $progress['in_progress'] }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Active Learning</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-blue-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('education.metrics.available') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $progress['total_modules'] }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Library Depth</p>
        </div>
        <div class="rounded-2xl bg-slate-900 p-6 shadow-soft ring-1 ring-white/10 text-white">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">{{ __('education.metrics.completion_rate') }}</p>
            <h3 class="text-2xl font-black text-white mt-2">{{ $progress['completion_rate'] }}%</h3>
            <div class="mt-2 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500" style="width: {{ $progress['completion_rate'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Filtering Ecosystem -->
    <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
        <form method="GET" action="{{ route('education.index') }}" class="grid grid-cols-1 gap-6 md:grid-cols-5">
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">{{ __('education.filters.category') }}</label>
                <select name="category" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 appearance-none outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">{{ __('education.filters.all') }}</option>
                    @foreach($filterOptions['categories'] ?? [] as $category)
                    <option value="{{ $category }}" {{ ($filters['category'] ?? null) === $category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">{{ __('education.filters.difficulty') }}</label>
                <select name="difficulty" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 appearance-none outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">{{ __('education.filters.all') }}</option>
                    @foreach($filterOptions['difficulties'] ?? [] as $difficulty)
                    <option value="{{ $difficulty }}" {{ ($filters['difficulty'] ?? null) === $difficulty ? 'selected' : '' }}>{{ ucfirst($difficulty) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">{{ __('education.filters.language') }}</label>
                <select name="language" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 appearance-none outline-none focus:ring-1 focus:ring-primary-500">
                    <option value="">{{ __('education.filters.all') }}</option>
                    @foreach($filterOptions['languages'] ?? [] as $language)
                    <option value="{{ $language }}" {{ ($filters['language'] ?? null) === $language ? 'selected' : '' }}>{{ strtoupper($language) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">{{ __('education.filters.search') }}</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('education.filters.search_placeholder') }}" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 placeholder-slate-300 focus:ring-1 focus:ring-primary-500 outline-none transition-all">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 h-11 bg-slate-900 text-white rounded-xl text-xs font-black uppercase shadow-soft hover:bg-slate-800 transition-all">
                    {{ __('education.filters.apply') }}
                </button>
                <a href="{{ route('education.index') }}" class="h-11 px-4 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">
                    <i class="fas fa-redo text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Main Learning Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Sidebar: Recommendations & News -->
        <div class="lg:col-span-4 space-y-10">
            <!-- AI Recommendation -->
            @if($recommendations['next_module'])
            <div class="rounded-2xl bg-white p-8 shadow-premium ring-1 ring-slate-100 border-t-8 border-primary-500">
                <span class="text-[10px] font-black text-primary-500 uppercase tracking-widest mb-2 block">AI Recommends</span>
                <h3 class="text-xl font-black text-slate-900 leading-tight mb-2">{{ $recommendations['next_module']->title }}</h3>
                <p class="text-xs text-slate-500 font-medium mb-6">{{ ucfirst($recommendations['next_module']->category) }} · {{ $recommendations['next_module']->estimated_time }} min</p>
                <a href="{{ route('education.module', $recommendations['next_module']) }}" class="w-full inline-flex items-center justify-center py-3.5 bg-primary-600 text-white rounded-xl text-xs font-black uppercase shadow-soft hover:bg-primary-500 transition-all">
                    {{ __('education.modules.start') }}
                </a>
            </div>
            @endif

            <!-- News Wire -->
            <div class="rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('education.news.title') }}</h3>
                </div>
                <div class="p-6 space-y-8 divide-y divide-slate-50">
                    @forelse($news as $article)
                    <div class="pt-8 first:pt-0 group">
                        <a href="{{ $article->url ?? '#' }}" target="_blank" class="text-sm font-bold text-slate-900 leading-snug group-hover:text-primary-600 transition-colors block mb-2">{{ $article->title }}</a>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[9px] font-black text-slate-400 capitalize">{{ $article->source ?? 'Financial Media' }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                            <span class="text-[9px] font-black text-slate-300 uppercase">{{ optional($article->published_at)->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 italic">"{{ strip_tags($article->content) }}"</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic py-4 text-center">{{ __('education.news.empty') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Community Story Box -->
            <div class="rounded-2xl bg-slate-50 p-6 border border-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">{{ __('education.community.title') }}</h3>
                <div class="space-y-4">
                    @foreach($communityHighlights as $story)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 group">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $story['name'] }}</span>
                            <span class="text-[8px] font-bold text-primary-500 uppercase px-1.5 py-0.5 rounded-md bg-primary-50 ring-1 ring-primary-100">Verified Peer</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-900 leading-tight mb-1">{{ $story['achievement'] }}</p>
                        <p class="text-[9px] text-slate-500 italic">{{ Str::limit($story['tip'], 80) }}</p>
                    </div>
                    @endforeach
                </div>
                <button @click="showModal = true" class="w-full mt-6 py-3 border border-slate-200 bg-white text-slate-600 rounded-xl text-[10px] font-black uppercase hover:bg-slate-50 transition-all">Share Your Journey</button>
            </div>
        </div>

        <!-- Learning Modules Ledger -->
        <div class="lg:col-span-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($modules as $module)
                @php($path = $learningPaths->get($module->id))
                <div class="group flex flex-col p-8 rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft overflow-hidden">
                    <div class="flex items-start justify-between mb-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-soft transition-transform group-hover:scale-110">
                            <i class="fas {{ $path && $path->progress > 0 ? 'fa-play' : 'fa-book' }} text-[18px]"></i>
                        </div>
                        <span class="inline-flex items-center rounded-md bg-slate-50 px-2.5 py-1 text-[10px] font-black uppercase ring-1 ring-inset ring-slate-200 text-slate-500">
                            {{ ucfirst($module->difficulty) }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <h4 class="text-lg font-black text-slate-900 tracking-tight group-hover:text-primary-600 transition-colors mb-2">{{ $module->title }}</h4>
                        <div class="flex items-center gap-2 mb-6">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ ucfirst($module->category) }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-primary-500">{{ $module->estimated_time }} Min Session</span>
                        </div>

                        @if(!empty($module->objective_list))
                        <ul class="space-y-2 mb-8">
                            @foreach(array_slice($module->objective_list, 0, 2) as $objective)
                            <li class="flex items-start gap-2 text-[10px] font-bold text-slate-500 leading-relaxed">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-200 shrink-0 mt-1.5"></span>
                                {{ $objective }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-end justify-between text-[10px] font-black uppercase tracking-tighter text-slate-400">
                            <span>Module Progress</span>
                            <span class="text-slate-900">{{ $path->progress ?? 0 }}%</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-emerald-500 transition-all duration-1000 ease-out" style="width: {{ $path->progress ?? 0 }}%"></div>
                        </div>
                        <a href="{{ route('education.module', $module) }}" class="w-full inline-flex items-center justify-center rounded-xl bg-slate-50 py-3 text-xs font-black text-slate-900 ring-1 ring-slate-200 transition-all hover:bg-slate-100">
                            {{ $path && $path->progress > 0 ? __('education.modules.continue') : __('education.modules.start') }}
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center">
                    <p class="text-xs font-medium text-slate-400 italic">No modules match your current filter parameters.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
