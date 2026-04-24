@extends('layouts.app')

@section('content')
<div class="space-y-10 animate-fade-in pb-20" x-data="{ activeModal: null }">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-black text-emerald-600 uppercase tracking-widest ring-1 ring-inset ring-emerald-500/20">Strategic Assets</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Family Goals</h1>
            <p class="text-sm font-medium text-slate-500">Synchronize your collective aspirations and growth milestones</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('family.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-tachometer-alt mr-2 text-slate-400"></i>
                Dashboard
            </a>
            <a href="{{ route('family.goals.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Add New Goal
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-b-4 border-primary-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-3 italic">Active Initiatives</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 leading-none tracking-tighter">{{ $goals->count() }}</h3>
                <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300">
                    <i class="fas fa-bullseye text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-b-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-3 italic">Completed Milestones</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 leading-none tracking-tighter">{{ $goals->where('is_completed', true)->count() }}</h3>
                <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300">
                    <i class="fas fa-check-double text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-slate-900 p-6 shadow-premium ring-1 ring-white/10 text-white">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest leading-none mb-3 italic">Aggregate Target</p>
            <div class="flex flex-col">
                <h3 class="text-xl font-black italic tracking-tighter leading-none mb-1">Rp {{ number_format($goals->sum('target_amount'), 0, ',', '.') }}</h3>
                <span class="text-[9px] font-bold text-white/30 uppercase tracking-widest">Collective valuation</span>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-t-4 border-amber-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-3 italic">Capital Accumulated</p>
            <div class="flex flex-col">
                <h3 class="text-xl font-black text-slate-900 italic tracking-tighter leading-none mb-1">Rp {{ number_format($goals->sum('current_amount'), 0, ',', '.') }}</h3>
                <div class="h-1.5 w-full bg-slate-100 rounded-full mt-2 overflow-hidden">
                    @php $totalProgress = $goals->sum('target_amount') > 0 ? ($goals->sum('current_amount') / $goals->sum('target_amount')) * 100 : 0; @endphp
                    <div class="h-full bg-amber-500" style="width: {{ $totalProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @forelse($goals as $goal)
        <div class="group relative rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden transition-all hover:scale-[1.01] hover:ring-primary-500/30 cursor-pointer"
             @click="activeModal = 'detail-{{ $goal->id }}'">
            <!-- Progress Bar Background -->
            <div class="absolute inset-0 bg-slate-50/50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="relative p-8">
                <div class="flex items-start justify-between mb-8">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-lg bg-slate-900 px-2.5 py-1 text-[9px] font-black uppercase text-white tracking-widest shadow-lg">{{ $goal->goal_type }}</span>
                            @if($goal->is_completed)
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-0.5 text-[9px] font-black text-emerald-600 uppercase tracking-widest ring-1 ring-inset ring-emerald-500/20 italic">Achieved</span>
                            @elseif($goal->days_remaining < 0)
                                <span class="inline-flex items-center rounded-lg bg-red-50 px-2 py-0.5 text-[9px] font-black text-red-600 uppercase tracking-widest ring-1 ring-inset ring-red-500/20 italic">Overdue</span>
                            @endif
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight underline decoration-primary-500/20 decoration-4 underline-offset-4 leading-relaxed">{{ $goal->goal_name }}</h3>
                    </div>
                    
                    <div class="flex gap-2" @click.stop="">
                        <a href="{{ route('family.goals.edit', $goal) }}" class="h-9 w-9 rounded-xl bg-white flex items-center justify-center text-slate-400 ring-1 ring-slate-200 transition-all hover:text-primary-600 hover:ring-primary-500/30 shadow-sm">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form method="POST" action="{{ route('family.goals.destroy', $goal) }}" onsubmit="return confirm('Are you sure you want to delete this strategic goal?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="h-9 w-9 rounded-xl bg-white flex items-center justify-center text-slate-400 ring-1 ring-slate-200 transition-all hover:text-red-600 hover:ring-red-500/30 shadow-sm">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Progress Dynamics -->
                    <div class="space-y-3">
                        <div class="flex items-end justify-between">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic leading-none mb-1">Accumulation progress</span>
                                <span class="text-3xl font-black text-slate-900 tracking-tighter leading-none">{{ number_format($goal->progress_percentage, 2) }}%</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter leading-none italic">Target Threshold</span>
                                <p class="text-sm font-black text-slate-900">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="h-3 w-full rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-200/50">
                            <div class="h-full bg-primary-500 transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(37,99,235,0.4)]" style="width: {{ $goal->progress_percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Meta Intelligence -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-white/60 backdrop-blur shadow-sm border border-slate-100 group-hover:bg-white transition-colors">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Temporal Window</p>
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-alt text-primary-600 text-[10px]"></i>
                                <span class="text-xs font-black text-slate-900 uppercase tracking-tighter">{{ $goal->target_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/60 backdrop-blur shadow-sm border border-slate-100 group-hover:bg-white transition-colors">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Execution Status</p>
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full {{ $goal->days_remaining > 0 ? 'bg-amber-500' : ($goal->days_remaining == 0 ? 'bg-primary-500' : 'bg-red-500') }}"></span>
                                <span class="text-xs font-black text-slate-900 uppercase tracking-tighter">
                                    @if($goal->days_remaining > 0) {{ $goal->days_remaining }} Days remaining
                                    @elseif($goal->days_remaining == 0) Initializing Today
                                    @else {{ abs($goal->days_remaining) }} Days Overdue
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Goal Detail Modal -->
        <div x-show="activeModal === 'detail-{{ $goal->id }}'" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="activeModal = null"></div>
            <div class="relative w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden ring-1 ring-white/20">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-0.5 text-[10px] font-black text-primary-600 uppercase tracking-widest ring-1 ring-inset ring-primary-500/20 mb-2">{{ $goal->goal_type }}</span>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none uppercase italic underline decoration-primary-500/20 underline-offset-4">Goal Intelligence</h3>
                    </div>
                    <button @click="activeModal = null" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white shadow-soft text-slate-400 hover:text-slate-900 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-10 space-y-10">
                    <div class="space-y-2">
                        <h4 class="text-2xl font-black text-slate-900 leading-tight uppercase italic tracking-tighter">{{ $goal->goal_name }}</h4>
                        <p class="text-sm font-medium text-slate-500 italic">{{ $goal->description ?: 'No operational description provided.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-8 border-y border-slate-50 py-10">
                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 italic">Capital Analysis</span>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase italic">Accumulated</span>
                                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase italic">Target</span>
                                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-slate-50 mt-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase italic">Gap</span>
                                    <span class="text-sm font-black text-primary-600">Rp {{ number_format($goal->target_amount - $goal->current_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 italic">Temporal Analysis</span>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase italic">Initiated</span>
                                    <span class="text-sm font-black text-slate-900">{{ $goal->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase italic">Target Date</span>
                                    <span class="text-sm font-black text-slate-900">{{ $goal->target_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-slate-50 mt-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase italic">Window</span>
                                    <span class="text-sm font-black text-amber-600">{{ $goal->days_remaining }} Days Left</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contributors Persona -->
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-widest italic">Assigned Personnel</h4>
                        <div class="flex flex-wrap gap-3">
                            @if(is_array($goal->contributors) && count($goal->contributors) > 0)
                                @foreach($goal->contributors as $contributorId)
                                    @php $contributor = \App\Models\FamilyMember::find($contributorId); @endphp
                                    @if($contributor)
                                        <div class="flex items-center gap-3 rounded-[1.25rem] bg-slate-50 px-4 py-2.5 ring-1 ring-slate-100 shadow-sm">
                                            <div class="h-6 w-6 rounded-lg bg-white shadow-soft flex items-center justify-center text-primary-500 border border-slate-100">
                                                <i class="fas fa-user text-[10px]"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[11px] font-black text-slate-900 leading-none italic">{{ $contributor->name }}</span>
                                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1">{{ $contributor->relationship }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="w-full p-4 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center">
                                    <p class="text-[10px] font-black text-slate-400 uppercase italic">Universal pool allocation (No specific contributors)</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6 flex gap-4">
                        <a href="{{ route('family.goals.edit', $goal) }}" class="flex-1 rounded-2xl bg-slate-900 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-premium text-center transition-all hover:bg-slate-800 active:scale-95">
                            Modify Protocol
                        </a>
                        <button @click="activeModal = null" class="flex-1 rounded-2xl bg-white px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 ring-1 ring-slate-100 shadow-premium transition-all hover:bg-slate-50 hover:text-slate-900">
                            Dismiss Intel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200 p-20 text-center">
            <div class="h-20 w-20 rounded-3xl bg-white shadow-premium flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fas fa-bullseye text-4xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest mb-2 italic underline decoration-primary-500/20">No Strategic Objectives Found</h3>
            <p class="text-sm font-medium text-slate-400 max-w-sm mx-auto mb-8">Define collaborative milestones to synchronize your household financial trajectory.</p>
            <a href="{{ route('family.goals.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-8 py-3.5 text-xs font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                Register First Goal
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
