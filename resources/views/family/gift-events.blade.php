@extends('layouts.app')

@section('content')
<div class="space-y-10 animate-fade-in pb-20" x-data="{ activeModal: null }">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-black text-amber-600 uppercase tracking-widest ring-1 ring-inset ring-amber-500/20">Cultural Milestones</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Gift Events Registry</h1>
            <p class="text-sm font-medium text-slate-500">Curate and manage collective celebration logistics</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('family.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-tachometer-alt mr-2 text-slate-400"></i>
                Dashboard
            </a>
            <a href="{{ route('family.gift-events.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-plus mr-2 text-amber-400"></i>
                Plan New Event
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-b-4 border-amber-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-3 italic">Total Events</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 leading-none tracking-tighter">{{ $events->count() }}</h3>
                <div class="h-10 w-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-400">
                    <i class="fas fa-calendar-star text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-b-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-3 italic">Settled Events</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 leading-none tracking-tighter">{{ $events->where('is_completed', true)->count() }}</h3>
                <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-400">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-slate-900 p-6 shadow-premium ring-1 ring-white/10 text-white">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest leading-none mb-3 italic">Allocated Budget</p>
            <div class="flex flex-col">
                <h3 class="text-xl font-black italic tracking-tighter leading-none mb-1">Rp {{ number_format($events->sum('budget_amount'), 0, ',', '.') }}</h3>
                <span class="text-[9px] font-bold text-white/30 uppercase tracking-widest">Total ceiling</span>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-t-4 border-red-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-3 italic">Operating Spent</p>
            <div class="flex flex-col">
                <h3 class="text-xl font-black text-slate-900 italic tracking-tighter leading-none mb-1">Rp {{ number_format($events->sum('spent_amount'), 0, ',', '.') }}</h3>
                <div class="h-1.5 w-full bg-slate-100 rounded-full mt-2 overflow-hidden">
                    @php $totalSpentProgress = $events->sum('budget_amount') > 0 ? ($events->sum('spent_amount') / $events->sum('budget_amount')) * 100 : 0; @endphp
                    <div class="h-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.4)]" style="width: {{ min($totalSpentProgress, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @forelse($events as $event)
        <div class="group relative rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden transition-all hover:scale-[1.01] hover:ring-amber-500/30">
            <div class="p-8">
                <div class="flex items-start justify-between mb-8">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-lg bg-amber-500 px-2.5 py-1 text-[9px] font-black uppercase text-white tracking-widest shadow-lg shadow-amber-500/20">{{ $event->event_type }}</span>
                            @if($event->is_completed)
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-0.5 text-[9px] font-black text-emerald-600 uppercase tracking-widest ring-1 ring-inset ring-emerald-500/20 italic">Fulfilled</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-primary-50 px-2 py-0.5 text-[9px] font-black text-primary-600 uppercase tracking-widest ring-1 ring-inset ring-primary-500/20 italic">Planning phase</span>
                            @endif
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight leading-tight">{{ $event->event_name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest py-1">{{ $event->event_date->format('M d, Y') }} execution window</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('family.gift-events.edit', $event) }}" class="h-9 w-9 rounded-xl bg-white flex items-center justify-center text-slate-400 ring-1 ring-slate-200 transition-all hover:text-amber-600 hover:ring-amber-500/30 shadow-sm">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form method="POST" action="{{ route('family.gift-events.destroy', $event) }}" onsubmit="return confirm('Abort this mission?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="h-9 w-9 rounded-xl bg-white flex items-center justify-center text-slate-400 ring-1 ring-slate-200 transition-all hover:text-red-600 hover:ring-red-500/30 shadow-sm">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Budget Dynamics -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-4">
                        <div class="flex items-end justify-between">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Asset Allocation</span>
                                <span class="text-xl font-black text-slate-900 tabular-nums">Rp {{ number_format($event->budget_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Burn rate: {{ $event->budget_used_percentage }}%</span>
                                <p class="text-sm font-black text-red-600 tabular-nums">Spent: Rp {{ number_format($event->spent_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full bg-amber-500 transition-all duration-700" style="width: {{ min($event->budget_used_percentage, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Meta Diagnostics -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-white shadow-soft flex items-center justify-center text-slate-400 border border-slate-100">
                                <i class="fas fa-users text-[10px]"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mb-0.5">Operatives</span>
                                <span class="text-xs font-black text-slate-900 uppercase italic">{{ $event->recipient_count }} targeted</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-xl bg-white shadow-soft flex items-center justify-center text-slate-400 border border-slate-100">
                                <i class="fas fa-box-open text-[10px]"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mb-0.5">Inventory</span>
                                <span class="text-xs font-black text-slate-900 uppercase italic">{{ $event->gift_count }} gifts planned</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <button @click="activeModal = 'gifts-{{ $event->id }}'" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-amber-700 active:scale-95">
                            <i class="fas fa-gift mr-2"></i> Manage Gifting
                        </button>
                        <button @click="activeModal = 'details-{{ $event->id }}'" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-600 ring-1 ring-slate-200 shadow-premium transition-all hover:bg-slate-50 active:scale-95">
                            <i class="fas fa-chart-line mr-2"></i> Protocol Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gifts Management Modal -->
        <div x-show="activeModal === 'gifts-{{ $event->id }}'" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="activeModal = null"></div>
            <div class="relative w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden ring-1 ring-white/20">
                <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none uppercase italic underline decoration-amber-500/20">Inventory Distribution</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest italic">{{ $event->event_name }}</p>
                    </div>
                    <button @click="activeModal = null" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white shadow-soft text-slate-400 hover:text-slate-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="p-8 max-h-[70vh] overflow-y-auto">
                    <div class="mb-8 flex items-center justify-between">
                        <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-widest italic">Allocated Assets</h4>
                        <button @click="activeModal = 'add-gift-{{ $event->id }}'" class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-premium">
                            <i class="fas fa-plus mr-2"></i> New Allocation
                        </button>
                    </div>

                    @php $giftList = collect($event->gifts ?? []); @endphp
                    <div class="space-y-4">
                        @forelse($giftList as $gift)
                        <div class="flex items-center justify-between p-5 rounded-2xl bg-slate-50 border border-slate-100 group transition-all hover:bg-white hover:shadow-soft">
                            <div class="flex items-center gap-5">
                                <div class="h-12 w-12 rounded-2xl bg-white shadow-soft flex items-center justify-center text-amber-500 border border-slate-100">
                                    <i class="fas fa-gift text-xl"></i>
                                </div>
                                <div>
                                    <h5 class="text-sm font-black text-slate-900 leading-none mb-1">{{ $gift['gift_name'] ?? 'N/A' }}</h5>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Recipient: <span class="text-slate-900">{{ $gift['recipient_name'] ?? 'Unknown' }}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-8">
                                <div class="text-right">
                                    <span class="text-sm font-black text-slate-900 tabular-nums">Rp {{ number_format($gift['amount'] ?? 0, 0, ',', '.') }}</span>
                                    <div class="mt-1">
                                        @if($gift['purchased'] ?? false)
                                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-0.5 text-[8px] font-black text-emerald-600 uppercase tracking-widest ring-1 ring-inset ring-emerald-500/20 italic">Procured</span>
                                        @else
                                            <span class="inline-flex items-center rounded-lg bg-amber-50 px-2 py-0.5 text-[8px] font-black text-amber-600 uppercase tracking-widest ring-1 ring-inset ring-amber-500/20 italic">Planned</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase italic tracking-widest leading-none mb-2">No inventory defined</p>
                            <p class="text-xs font-medium text-slate-400">Initialize gift allocations for this milestone.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple Detail Modal -->
        <div x-show="activeModal === 'details-{{ $event->id }}'" 
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
                    <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none uppercase italic underline decoration-amber-500/20">Protocol Diagnostics</h3>
                    <button @click="activeModal = null" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white shadow-soft text-slate-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-2 gap-8 text-[11px] font-black uppercase tracking-widest italic">
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <span class="text-slate-400 block mb-1">Operational ID</span>
                                <span class="text-slate-900">#FF-GE-{{ $event->id }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-400 block mb-1 underline decoration-primary-500/20">Execution Window</span>
                                <span class="text-slate-900">{{ $event->event_date->format('l, M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <span class="text-slate-400 block mb-1">Classification</span>
                                <span class="text-amber-600">{{ $event->event_type }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-400 block mb-1 underline decoration-primary-500/20">Budget Burn</span>
                                <span class="text-red-500">Rp {{ number_format($event->spent_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($event->notes)
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                        <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Internal Memoranda</h4>
                        <p class="text-xs font-medium text-slate-600 leading-relaxed">{{ $event->notes }}</p>
                    </div>
                    @endif

                    <div class="space-y-3">
                        <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Designated Personnel</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($event->recipient_details as $recipient)
                            <div class="flex items-center gap-2 rounded-xl bg-slate-900 px-3 py-1.5 shadow-lg border border-white/10">
                                <div class="h-1.5 w-1.5 rounded-full bg-amber-400"></div>
                                <span class="text-[9px] font-black text-white uppercase tracking-widest">{{ $recipient->name }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Gift Form Modal -->
        <div x-show="activeModal === 'add-gift-{{ $event->id }}'" 
             class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6"
             style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="activeModal = 'gifts-{{ $event->id }}'"></div>
            <div class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl p-8 ring-1 ring-white/20">
                <h3 class="text-lg font-black text-slate-900 tracking-tight leading-none mb-8 uppercase italic border-l-4 border-primary-500 pl-4">Asset Provisioning</h3>
                <form method="POST" action="{{ route('family.gift-events.gifts.store', $event) }}" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Target Recipient</label>
                        <select name="recipient_id" required class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-sm font-bold text-slate-900 ring-1 ring-slate-200">
                            <option value="">Select Operative</option>
                            @foreach($event->recipient_details as $recipient)
                            <option value="{{ $recipient->id }}">{{ $recipient->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Asset Descriptor</label>
                        <input type="text" name="gift_name" required class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-sm font-bold text-slate-900 ring-1 ring-slate-200" placeholder="e.g. Mechanical Metronome">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 italic">Valuation (Rp)</label>
                        <input type="number" name="amount" required class="w-full rounded-xl border-none bg-slate-50 px-4 py-3 text-sm font-bold text-slate-900 ring-1 ring-slate-200" placeholder="100000">
                    </div>
                    <div class="flex items-center gap-3 ml-1">
                        <input type="checkbox" name="purchased" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/20">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mark as Procured</span>
                    </div>
                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-premium">Initialize Provision</button>
                        <button type="button" @click="activeModal = 'gifts-{{ $event->id }}'" class="rounded-xl px-6 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Abort</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 rounded-3xl bg-slate-100/50 border-2 border-dashed border-slate-200 p-20 text-center">
            <div class="h-20 w-20 rounded-3xl bg-white shadow-premium flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fas fa-gift text-4xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest mb-2 italic underline decoration-amber-500/20">No active gift protocols</h3>
            <p class="text-sm font-medium text-slate-400 max-w-sm mx-auto mb-8">Initiate celebratory logistics to track collective generosity and household growth.</p>
            <a href="{{ route('family.gift-events.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-8 py-3.5 text-xs font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                Register First Event
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
