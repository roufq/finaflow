@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('family.dashboard_title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Collaborative financial strategy for your household ecosystem</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('family.members.create') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-user-plus mr-2 text-slate-400"></i>
                {{ __('family.buttons.add_member') }}
            </a>
            <a href="{{ route('family.shared-expenses.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                {{ __('family.buttons.add_expense') }}
            </a>
        </div>
    </div>

    <!-- Family Infrastructure Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-primary-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('family.overview.members') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $familyMembers->count() }}</h3>
            <p class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Active Unit Members</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('family.overview.shared_expenses') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $sharedExpenses->count() }}</h3>
            <p class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Collective Obligations</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-blue-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('family.overview.goals') }}</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $familyGoals->count() }}</h3>
            <p class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Shared Aspirations</p>
        </div>
        <div class="rounded-2xl bg-slate-900 p-6 shadow-soft ring-1 ring-white/10 text-white">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">{{ __('family.overview.events') }}</p>
            <h3 class="text-2xl font-black text-white mt-2">{{ $upcomingEvents->count() }}</h3>
            <div class="mt-2 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500" style="width: 75%"></div>
            </div>
        </div>
    </div>

    <!-- Unit Management Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
        <!-- Family Members Ledger -->
        <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Unit Members</h2>
                <a href="{{ route('family.members') }}" class="text-[10px] font-black text-primary-600 uppercase hover:underline">Auditing View</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-50 bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ __('family.members.name') }}</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Persona</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Allowance</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($familyMembers as $member)
                        <tr class="group transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-bold text-slate-900 text-sm italic">{{ $member->name }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-[9px] font-black uppercase text-slate-500">{{ $member->relationship }}</span>
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums text-sm font-bold text-slate-600">Rp {{ number_format($member->monthly_allowance, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right tabular-nums text-sm font-black text-primary-600">Rp {{ number_format($member->current_balance, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center flex flex-col items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                    <i class="fas fa-users-slash text-xl"></i>
                                </div>
                                <p class="text-[10px] font-medium text-slate-400 italic">No members defined in your household.</p>
                                <a href="{{ route('family.members.create') }}" class="text-xs font-black text-primary-600 uppercase hover:underline">Add First Member</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Collective Liquidity Ledger (Expenses) -->
        <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">{{ __('family.shared_expenses.title') }}</h2>
                <a href="{{ route('family.shared-expenses') }}" class="text-[10px] font-black text-emerald-600 uppercase hover:underline">{{ __('family.buttons.view_all') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-50 bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Expense Identifier</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Aggregate</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Verification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($sharedExpenses as $expense)
                        <tr class="group transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900 leading-none">{{ $expense->expense_name }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ $expense->expense_date->format('M d, Y') }} execution window</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums text-sm font-black text-slate-900">Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-black uppercase ring-1 ring-inset {{ $expense->is_settled ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-amber-50 text-amber-600 ring-amber-100' }}">
                                    {{ $expense->is_settled ? __('family.statuses.settled') : __('family.statuses.pending') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-xs font-medium text-slate-400 italic">No shared obligations recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Strategic Aspirations & Events Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
        <!-- Joint Aspirations (Goals) -->
        <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('family.goals.title') }}</h3>
                <div class="flex items-center gap-3">
                    <a href="{{ route('family.goals') }}" class="text-[10px] font-black text-slate-400 uppercase hover:text-primary-600 transition-colors">Audit</a>
                    <a href="{{ route('family.goals.create') }}" class="h-8 px-3 flex items-center justify-center rounded-lg bg-primary-600 text-white text-[9px] font-black uppercase shadow-soft hover:bg-primary-500 transition-all">
                        {{ __('family.buttons.add_goal') }}
                    </a>
                </div>
            </div>
            <div class="space-y-8">
                @forelse($familyGoals as $goal)
                <div class="relative group">
                    <div class="flex items-end justify-between mb-3">
                        <div>
                            <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $goal->goal_name }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">Capital Accumulation: <span class="text-slate-900">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-black text-primary-600 tracking-tighter">{{ $goal->progress_percentage }}%</span>
                            <p class="text-[8px] font-bold text-slate-400 uppercase">of Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-primary-500 transition-all duration-1000 ease-out" style="width: {{ $goal->progress_percentage }}%"></div>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-xs font-medium text-slate-400 italic">Define joint milestones to synchronize your household growth.</div>
                @endforelse
            </div>
        </div>

        <!-- Household Milestones (Events) -->
        <div class="rounded-3xl bg-slate-50 p-8 border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('family.events.title') }}</h3>
                <a href="{{ route('family.gift-events.create') }}" class="h-8 px-3 flex items-center justify-center rounded-lg bg-slate-900 text-white text-[9px] font-black uppercase shadow-soft hover:bg-slate-800 transition-all">New Milestone</a>
            </div>
            <div class="grid grid-cols-1 gap-4">
                @forelse($upcomingEvents as $event)
                <div class="flex items-center gap-6 p-5 rounded-2xl bg-white shadow-soft transition-transform hover:scale-[1.02]">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 shadow-sm border border-amber-200">
                        <i class="fas fa-gift text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tighter leading-none">{{ $event->event_name }}</h4>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $event->event_date->format('M d, Y') }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                            <span class="text-[9px] font-black text-amber-600">Rp {{ number_format($event->budget_amount, 0, ',', '.') }} Allocated</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-xs font-medium text-slate-400 italic">No upcoming family events or gift registries.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
