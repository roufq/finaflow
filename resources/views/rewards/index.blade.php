@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('rewards.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Monitor and maximize your financial incentive ecosystem</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('rewards.create') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-amber-400 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                {{ __('rewards.buttons.add_reward') }}
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Points -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('rewards.summary.total_points') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="fas fa-star text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalPoints, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Accumulated rewards</p>
            </div>
        </div>

        <!-- Cashback -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('rewards.summary.cashback_earned') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalCashback, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Direct savings</p>
            </div>
        </div>

        <!-- Cards -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('rewards.summary.cards_tracked') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="fas fa-credit-card text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $rewards->count() }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Registered instruments</p>
            </div>
        </div>

        <!-- Best Card -->
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('rewards.summary.best_card') }}</span>
                <i class="fas fa-trophy text-primary-400"></i>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-bold text-white truncate">{{ $bestCard ?: __('rewards.summary.none') }}</h3>
                <div class="mt-2 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full bg-primary-500" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optimization Insights Section -->
    <div class="rounded-2xl bg-slate-50/50 p-8 border border-slate-100 ring-1 ring-slate-200/50">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-8 flex items-center gap-2">
            <i class="fas fa-lightbulb text-amber-500"></i>
            Reward Optimization Strategy
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach (['cashback', 'loyalty'] as $type)
                @php($tip = trans('rewards.tips.' . $type))
                <div class="space-y-4">
                    <h6 class="text-xs font-black text-slate-900 uppercase tracking-tighter">{{ $tip['title'] }}</h6>
                    <ul class="space-y-3">
                        @foreach ($tip['items'] as $item)
                        <li class="flex items-start gap-3 p-3 rounded-xl bg-white shadow-sm border border-slate-100/50 text-[11px] font-bold text-slate-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0 mt-1.5"></span>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Tracking Cards -->
        <div class="lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">{{ __('rewards.cards.section_title') }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($rewards as $reward)
                <div class="group relative flex flex-col p-6 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-[10px] font-black text-amber-600 uppercase tracking-tighter">{{ $reward->card_type }}</span>
                            <h4 class="text-base font-black text-slate-900 mt-1">{{ $reward->reward_type }}</h4>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="viewDetails({{ $reward->id }})" class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                            <button onclick="deleteReward({{ $reward->id }})" class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-50">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Points Pool</p>
                            <p class="text-sm font-black text-slate-900 tabular-nums">{{ number_format($reward->points_earned, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Cashback</p>
                            <p class="text-sm font-black text-emerald-600 tabular-nums">Rp {{ number_format($reward->cashback_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <span class="text-[9px] font-bold text-slate-300 uppercase">Expires: {{ $reward->expiry_date ? $reward->expiry_date->format('M Y') : 'Never' }}</span>
                        <div class="flex h-1.5 w-12 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-amber-400" style="width: 70%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-12 text-center bg-white rounded-2xl border-2 border-dashed border-slate-100">
                    <p class="text-xs font-medium text-slate-400 italic">{{ __('rewards.cards.empty_body') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Loyalty Programs Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">{{ __('rewards.loyalty.section_title') }}</h2>
            <div class="space-y-4">
                @forelse($loyaltyPrograms as $program)
                <div class="p-6 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 text-center relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-3">
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[8px] font-black text-blue-600 ring-1 ring-blue-100 uppercase tracking-tighter">
                            {{ $program->tier_level }}
                        </span>
                    </div>
                    <h6 class="text-xs font-black text-slate-900 mb-4 group-hover:text-primary-600 transition-colors">{{ $program->program_name }}</h6>
                    <div class="text-3xl font-black text-slate-900 tracking-tighter mb-1">{{ number_format($program->points_balance, 0, ',', '.') }}</div>
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('rewards.loyalty.points_balance') }}</div>
                    
                    @if($program->benefits)
                    <div class="mt-6 pt-4 border-t border-slate-50">
                        <p class="text-[10px] text-slate-500 font-medium leading-relaxed italic">"{{ Str::limit($program->benefits, 60) }}"</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ __('rewards.loyalty.empty') }}</p>
                    <a href="{{ route('rewards.create-loyalty') }}" class="text-[10px] font-extrabold text-primary-600 uppercase hover:underline mt-2 inline-block">Enroll Program</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewDetails(id) {
    alert(@json(__('rewards.messages.view_placeholder')));
}
function deleteReward(id) {
    if (confirm(@json(__('rewards.messages.delete_confirm')))) {
        alert(@json(__('rewards.messages.delete_placeholder')));
    }
}
</script>
@endpush
@endsection
