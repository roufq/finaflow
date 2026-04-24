@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-black text-amber-600 uppercase tracking-widest ring-1 ring-inset ring-amber-500/20">Logistical Planning</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Plan Gift Protocol</h1>
            <p class="text-sm font-medium text-slate-500">Initialize a commemorative event and asset allocation</p>
        </div>
        <a href="{{ route('family.gift-events') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            Back to Gift Registry
        </a>
    </div>

    <form method="POST" action="{{ route('family.gift-events.store') }}" x-data="giftBudgetCalculator()" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        @csrf
        
        <div class="lg:col-span-2 space-y-8">
            <!-- Core Intelligence Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden border-b-4 border-amber-500">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-amber-400 shadow-lg">
                        <i class="fas fa-gift text-xs"></i>
                    </div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest leading-none">Event Parameters</h2>
                </div>
                
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label for="event_name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Event Identifier *</label>
                        <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-amber-500/10 @error('event_name') ring-red-500 @enderror"
                               placeholder="e.g. Eleanor's 12th Anniversary Portfolio">
                        @error('event_name') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="event_type" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Protocol Classification *</label>
                            <select id="event_type" name="event_type" required
                                    class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-amber-500/10 @error('event_type') ring-red-500 @enderror">
                                <option value="">Select Protocol</option>
                                @php $types = ['Birthday','Wedding','Graduation','Holiday','Anniversary','Baby Shower','Retirement','Other']; @endphp
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('event_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="event_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Temporal Window *</label>
                            <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-amber-500/10">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="budget_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 italic leading-none">Resource Allocation (Rp)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">Rp</div>
                            <input type="number" id="budget_amount" name="budget_amount" x-model.number="budgetAmount" min="0" step="1000"
                                   class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-4 text-xl font-black text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-amber-500/10">
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-2 ml-1 italic">Financial ceiling for this specific protocol window.</p>
                    </div>

                    <!-- Operative Selection -->
                    <div class="pt-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-4 block">Designated Recipients</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php $members = \App\Models\FamilyMember::where('user_id', auth()->id())->active()->get(); @endphp
                            @forelse($members as $member)
                            <label class="relative flex items-center p-4 rounded-xl bg-slate-50 border border-slate-100 cursor-pointer transition-all hover:bg-white hover:ring-2 hover:ring-amber-500/20 group">
                                <input type="checkbox" name="recipients[]" value="{{ $member->id }}" 
                                       {{ in_array($member->id, old('recipients', [])) ? 'checked' : '' }}
                                       class="h-5 w-5 rounded-lg border-slate-200 text-amber-500 focus:ring-amber-500/20 mr-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 leading-none mb-1 group-hover:text-amber-600 transition-colors">{{ $member->name }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $member->relationship }}</span>
                                </div>
                            </label>
                            @empty
                            <div class="col-span-2 p-6 rounded-2xl bg-amber-50 border border-dashed border-amber-200 text-center">
                                <p class="text-[10px] font-bold text-amber-700 uppercase italic">No active operatives in database. <a href="{{ route('family.members.create') }}" class="underline decoration-amber-500/30">Incorporate personnel</a>.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="notes" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Internal Memoranda</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-amber-500/10"
                                  placeholder="Document specific preferences or logistical constraints...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Strategic Buffer Card -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-6 italic underline decoration-amber-500/30 italic">Strategic Projection</h3>
                <div class="space-y-6">
                    <div class="p-5 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-white/40 uppercase mb-2">Calculated Threshold</span>
                            <h4 class="text-2xl font-black text-white italic tracking-tighter leading-none" x-text="formatCurrency(calculateTotalProjection())">Rp 0</h4>
                        </div>
                        <div class="pt-4 border-t border-white/5 grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[8px] font-black text-white/30 uppercase block mb-1 underline decoration-white/10">Volume</span>
                                <span class="text-sm font-black italic" x-text="recipientCount + ' Recps'">1</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[8px] font-black text-white/30 uppercase block mb-1 underline decoration-white/10">Avg Unit cost</span>
                                <span class="text-sm font-black italic" x-text="formatCurrency(avgPerRecipient)">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Controller -->
                    <div class="space-y-4 pt-4 border-t border-white/10">
                        <div class="space-y-2">
                            <label class="text-[8px] font-black text-white/40 uppercase block ml-1">Tweak Recipient Volume</label>
                            <input type="range" x-model.number="recipientCount" min="1" max="10" class="w-full accent-amber-500 h-1.5 bg-white/10 rounded-full appearance-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[8px] font-black text-white/40 uppercase block ml-1 italic leading-none">Tweak Avg Unit Capital (Rp)</label>
                            <input type="number" x-model.number="avgPerRecipient" step="10000" min="0" 
                                   class="w-full bg-transparent border-none p-0 text-lg font-black text-amber-400 focus:ring-0 leading-none">
                        </div>
                        <button type="button" @click="budgetAmount = calculateTotalProjection()" class="w-full rounded-xl bg-white/10 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-white transition-all hover:bg-white/20 active:scale-95 italic">Inject into Protocol</button>
                    </div>
                </div>
            </div>

            <!-- Protocol Authorization -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 italic underline decoration-amber-500 decoration-2">Mission Authorization</h3>
                <p class="text-[10px] text-slate-400 uppercase font-bold leading-relaxed mb-8">Authorization will lock asset allocation for this temporal milestone.</p>
                
                <div class="space-y-4">
                    <button type="submit" class="w-full rounded-2xl bg-slate-900 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                        Authorize protocol
                    </button>
                    <a href="{{ route('family.gift-events') }}" class="block w-full text-center mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors italic">Abort operation</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function giftBudgetCalculator() {
    return {
        budgetAmount: 0,
        recipientCount: 1,
        avgPerRecipient: 100000,
        calculateTotalProjection() {
            return this.recipientCount * this.avgPerRecipient;
        },
        formatCurrency(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('event_date');
    if (dateInput) {
        dateInput.min = new Date().toISOString().split('T')[0];
    }
});
</script>
@endsection
