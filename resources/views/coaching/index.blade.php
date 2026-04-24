@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('coaching.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">{{ __('coaching.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('coaching.export') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-file-export mr-2"></i>
                {{ __('coaching.export.button') }}
            </a>
        </div>
    </div>

    <!-- Strategy Overview & Planning Hub -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Active Action Plan Core -->
        <div class="lg:col-span-8 space-y-8">
            <div class="rounded-3xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-soft">
                            <i class="fas fa-chess-knight text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest">{{ __('coaching.plan.current_plan') }}</h2>
                            @if($currentPlan)
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $currentPlan->month_tags }} Strategic Window</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="p-8 space-y-10">
                    @if($currentPlan)
                        <!-- Strategic Priorities -->
                        <div>
                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">{{ __('coaching.plan.priorities') }}</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($currentPlan->focus_priorities ?? [] as $priority)
                                    <span class="inline-flex items-center rounded-lg bg-primary-50 px-3 py-1.5 text-[10px] font-black text-primary-600 ring-1 ring-primary-100 uppercase tracking-tight">
                                        {{ Str::title(str_replace('_', ' ', $priority)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Executable Tactics (Recommended Actions) -->
                        <div>
                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">{{ __('coaching.plan.recommended_actions') }}</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($currentPlan->recommended_actions ?? [] as $action)
                                    <div class="group p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-100 transition-all">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="h-1.5 w-1.5 rounded-full bg-primary-500"></div>
                                            <h6 class="text-xs font-black text-slate-900 uppercase tracking-tighter">{{ $action['title'] }}</h6>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-relaxed font-medium">{{ $action['description'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Fiscal Targets -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-slate-100">
                            <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                                <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">{{ __('coaching.plan.savings_target') }}</p>
                                <p class="text-lg font-black text-slate-900 tabular-nums">Rp {{ number_format($currentPlan->savings_target, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100">
                                <p class="text-[9px] font-black text-rose-600 uppercase tracking-widest mb-1">{{ __('coaching.plan.debt_target') }}</p>
                                <p class="text-lg font-black text-slate-900 tabular-nums">Rp {{ number_format($currentPlan->debt_repayment_target, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">{{ __('coaching.plan.investment_target') }}</p>
                                <p class="text-lg font-black text-slate-900 tabular-nums">Rp {{ number_format($currentPlan->investment_target, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if($currentPlan->summary_notes)
                            <div class="mt-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 italic text-[11px] text-slate-500 font-medium leading-relaxed">
                                "{{ $currentPlan->summary_notes }}"
                            </div>
                        @endif
                    @else
                        <div class="py-12 text-center">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mb-4">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                            </div>
                            <p class="text-xs font-medium text-slate-400 italic">{{ __('coaching.plan.no_plan') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Task Progression Ledger -->
            @if($currentPlan)
            <div class="rounded-3xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100" x-data="{ showForm: false }">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest">{{ __('coaching.tasks.title') }}</h2>
                    <button @click="showForm = !showForm" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-4 py-2 text-xs font-black text-slate-900 hover:bg-slate-200 transition-all">
                        <i class="fas fa-plus mr-2"></i> {{ __('coaching.tasks.add_task') }}
                    </button>
                </div>
                
                <div class="p-8">
                    <!-- Dynamic Task Injector -->
                    <div x-show="showForm" x-collapse class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <form method="POST" action="{{ route('coaching.tasks.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="action_plan_id" value="{{ $currentPlan->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Task Identifier</label>
                                    <input type="text" name="title" required class="w-full h-11 rounded-xl bg-white border-slate-200 px-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Target Week</label>
                                    <input type="number" name="week_index" min="1" max="6" value="1" class="w-full h-11 rounded-xl bg-white border-slate-200 px-4 text-xs font-bold text-slate-900">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Deadline</label>
                                    <input type="date" name="due_date" class="w-full h-11 rounded-xl bg-white border-slate-200 px-4 text-xs font-bold text-slate-900">
                                </div>
                            </div>
                            <button type="submit" class="px-6 h-11 bg-primary-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-soft hover:bg-primary-500 transition-all">
                                Deploy Task
                            </button>
                        </form>
                    </div>

                    @if($currentPlan->tasks->isEmpty())
                        <p class="text-xs text-slate-400 italic text-center py-8">Generate strategic tasks to populate this ledger.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-slate-50 uppercase text-[10px] font-black text-slate-400 tracking-widest">
                                        <th class="pb-4 w-12 text-center">Wk</th>
                                        <th class="pb-4 px-4">Task Manifest</th>
                                        <th class="pb-4 px-4">Timeline</th>
                                        <th class="pb-4 px-4">Execution State</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($currentPlan->tasks->sortBy('due_date') as $task)
                                    <tr class="group transition-colors hover:bg-slate-50/50">
                                        <td class="py-5 text-center">
                                            <span class="text-[10px] font-black text-slate-400">0{{ $task->week_index }}</span>
                                        </td>
                                        <td class="py-5 px-4">
                                            <p class="text-sm font-black text-slate-900 uppercase tracking-tighter leading-tight">{{ $task->title }}</p>
                                            @if($task->notes)
                                                <p class="text-[10px] font-medium text-slate-400 mt-1 italic leading-tight">{{ $task->notes }}</p>
                                            @endif
                                        </td>
                                        <td class="py-5 px-4">
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tight">{{ optional($task->due_date)->format('M d, Y') ?? 'Flexible' }}</span>
                                        </td>
                                        <td class="py-5 px-4">
                                            <form method="POST" action="{{ route('coaching.tasks.update', $task) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" @change="this.form.submit()" class="h-8 rounded-lg bg-slate-50 border-transparent px-2 text-[9px] font-black uppercase text-slate-600 focus:ring-1 focus:ring-primary-500 appearance-none outline-none">
                                                    @foreach(['pending','in_progress','completed','skipped'] as $status)
                                                        <option value="{{ $status }}" @selected($task->status === $status)>{{ Str::title(str_replace('_',' ',$status)) }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="notes" value="{{ $task->notes }}">
                                                @if($task->status === 'completed')
                                                    <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar: Initialization & Tracking -->
        <div class="lg:col-span-4 space-y-10">
            <!-- New Strategy Initialization -->
            <div class="rounded-3xl bg-slate-900 p-8 shadow-soft ring-1 ring-white/10 text-white">
                <h3 class="text-xs font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-primary-400"></i>
                    {{ __('coaching.plan.create_new') }}
                </h3>
                <form method="POST" action="{{ route('coaching.action-plan.store') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-white/40 uppercase tracking-tighter mb-1.5 block">{{ __('coaching.plan.plan_month') }}</label>
                        <input type="month" name="plan_month" value="{{ old('plan_month', now()->format('Y-m')) }}" required class="w-full h-12 rounded-xl bg-white/5 border-transparent px-4 text-xs font-bold text-white focus:ring-1 focus:ring-primary-500 outline-none">
                    </div>
                    
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-white/40 uppercase tracking-tighter block">{{ __('coaching.plan.targets') }}</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-white/30">Rp</span>
                            <input type="number" name="savings_target" placeholder="Savings Target" class="w-full h-11 rounded-xl bg-white/5 border-transparent pl-10 pr-4 text-xs font-bold text-white outline-none focus:ring-1 focus:ring-primary-500">
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-white/30">Rp</span>
                            <input type="number" name="debt_repayment_target" placeholder="Debt Repayment" class="w-full h-11 rounded-xl bg-white/5 border-transparent pl-10 pr-4 text-xs font-bold text-white outline-none focus:ring-1 focus:ring-primary-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="generate_tasks" id="generateTasks" value="1" checked class="h-4 w-4 rounded border-white/20 bg-white/5 text-primary-500 focus:ring-0">
                        <label for="generateTasks" class="text-[10px] font-bold text-white/60 uppercase tracking-tight">{{ __('coaching.plan.generate_tasks') }}</label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-primary-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-soft hover:bg-primary-500 transition-all">
                        Initialize Strategy
                    </button>
                </form>
            </div>

            <!-- Behavioral Journaling -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">{{ __('coaching.journal.title') }}</h3>
                <form method="POST" action="{{ route('coaching.journal.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <textarea name="reflection" rows="2" placeholder="{{ __('coaching.journal.reflection') }}" required class="w-full rounded-2xl bg-slate-50 border-transparent p-4 text-[11px] font-medium text-slate-900 placeholder-slate-400 focus:ring-1 focus:ring-primary-500 outline-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="mood" placeholder="Psych Mood" class="w-full h-10 rounded-xl bg-slate-50 border-transparent px-3 text-[10px] font-bold text-slate-900 outline-none">
                        <select name="habit_id" class="w-full h-10 rounded-xl bg-slate-50 border-transparent px-3 text-[10px] font-bold text-slate-900 appearance-none outline-none">
                            <option value="">Link Habit</option>
                            @foreach($habits as $habit)
                                <option value="{{ $habit->id }}">{{ $habit->habit_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-soft hover:bg-slate-800 transition-all">
                        Log Entry
                    </button>
                </form>

                <div class="mt-8 space-y-4 divide-y divide-slate-50">
                    @foreach($journalEntries->take(3) as $entry)
                    <div class="pt-4 first:pt-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase">{{ $entry->logged_at->format('M d, Y') }}</span>
                            @if($entry->mood)
                                <span class="text-[8px] font-black text-primary-500 uppercase px-1.5 py-0.5 rounded-md bg-primary-50">{{ $entry->mood }}</span>
                            @endif
                        </div>
                        <p class="text-[10px] font-medium text-slate-600 line-clamp-2 italic leading-relaxed">"{{ $entry->reflection }}"</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Educational Integration (Lessons) -->
    <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-8">{{ __('coaching.lessons.title') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($lessons as $lesson)
            @php($progress = $lessonProgress->get($lesson->id))
            <div class="group p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-100 transition-all flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <span class="inline-flex items-center rounded-md bg-white px-2 py-1 text-[8px] font-black uppercase ring-1 ring-inset ring-slate-100 text-slate-500">
                        {{ ucfirst($lesson->format) }}
                    </span>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $lesson->duration_minutes }} MIN</span>
                </div>
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-2 leading-tight">{{ $lesson->title }}</h4>
                <p class="text-[10px] text-slate-400 font-medium mb-6 flex-grow leading-relaxed">{{ Str::limit($lesson->summary, 100) }}</p>
                
                <form method="POST" action="{{ route('coaching.lessons.update', $lesson) }}" class="space-y-3 pt-4 border-t border-slate-100">
                    @csrf
                    <div class="flex items-center gap-2">
                        <select name="status" class="flex-1 h-9 rounded-lg bg-white border-slate-200 px-2 text-[9px] font-black uppercase text-slate-600 outline-none focus:ring-1 focus:ring-primary-500">
                            @foreach(['not_started','in_progress','completed'] as $status)
                                <option value="{{ $status }}" @selected(optional($progress)->status === $status)>{{ Str::title(str_replace('_',' ',$status)) }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="comprehension_score" value="{{ optional($progress)->comprehension_score }}" min="0" max="100" placeholder="Score" class="w-16 h-9 rounded-lg bg-white border-slate-200 px-2 text-[9px] font-black text-slate-900 outline-none">
                    </div>
                    <button type="submit" class="w-full py-2 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">
                        Update State
                    </button>
                </form>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-xs font-medium text-slate-400 italic">No assigned curriculum.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
