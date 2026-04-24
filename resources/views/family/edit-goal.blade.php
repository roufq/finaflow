@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-0.5 text-[10px] font-black text-primary-600 uppercase tracking-widest ring-1 ring-inset ring-primary-500/20">Operational Refinement</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Asset ID: #{{ str_pad($goal->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Evolve Strategic Objective</h1>
            <p class="text-sm font-medium text-slate-500">Recalibrate parameters for an existing financial milestone</p>
        </div>
        <a href="{{ route('family.goals') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            Back to Strategic Assets
        </a>
    </div>

    <form method="POST" action="{{ route('family.goals.update', $goal) }}" x-data="goalCalculator({{ $goal->target_amount }}, {{ $goal->current_amount }})" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        @csrf
        @method('PUT')
        
        <div class="lg:col-span-2 space-y-8">
            <!-- Core Parameters Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden border-b-4 border-primary-500">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-white">
                        <i class="fas fa-bullseye text-xs"></i>
                    </div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Objective Evolution</h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label for="goal_name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Strategic Identifier *</label>
                        <input type="text" id="goal_name" name="goal_name" value="{{ old('goal_name', $goal->goal_name) }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('goal_name') ring-red-500 @enderror">
                        @error('goal_name') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="goal_type" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Asset Classification *</label>
                            <select id="goal_type" name="goal_type" required
                                    class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                                @php $types = ['Vacation','Education','Emergency Fund','Home Purchase','Vehicle','Wedding','Retirement','Investment','Other']; @endphp
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('goal_type', $goal->goal_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="target_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Temporal Deadline *</label>
                            <input type="date" id="target_date" name="target_date" value="{{ old('target_date', $goal->target_date->format('Y-m-d')) }}" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="target_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Valuation Threshold (Rp) *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 font-bold text-xs uppercase">Rp</div>
                                <input type="number" id="target_amount" name="target_amount" x-model.number="targetAmount" required min="0" step="1000"
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-black text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="current_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Manual Capital Injection (Rp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-emerald-400 font-bold text-xs uppercase">Rp</div>
                                <input type="number" id="current_amount" name="current_amount" x-model.number="currentAmount" min="0" step="1000"
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-black text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Contextual Brief</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                                  placeholder="Provide strategic context for this objective...">{{ old('description', $goal->description) }}</textarea>
                    </div>

                    <!-- Contributors Selection -->
                    <div class="pt-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-4 block">Unit Operatives involved</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php 
                                $members = \App\Models\FamilyMember::where('user_id', auth()->id())->active()->get();
                                $selectedContributors = old('contributors', $goal->contributors ?? []);
                            @endphp
                            @forelse($members as $member)
                            <label class="relative flex items-center p-4 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer transition-all hover:bg-white hover:shadow-soft group">
                                <input type="checkbox" name="contributors[]" value="{{ $member->id }}" 
                                       {{ in_array($member->id, $selectedContributors) ? 'checked' : '' }}
                                       class="h-5 w-5 rounded-lg border-slate-200 text-primary-600 focus:ring-primary-500/20 mr-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 leading-none mb-1 group-hover:text-primary-600 transition-colors">{{ $member->name }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $member->relationship }}</span>
                                </div>
                            </label>
                            @empty
                            <div class="col-span-2 p-4 rounded-2xl bg-amber-50 border border-amber-100 flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle text-amber-500 text-sm"></i>
                                <p class="text-[10px] font-bold text-amber-700 uppercase italic">No active operatives available.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50">
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" id="is_achieved" name="is_achieved" value="1" {{ old('is_achieved', $goal->is_achieved) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none ring-4 ring-transparent peer-checked:ring-emerald-500/10 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 transition-all"></div>
                            <span class="ms-3 text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-900 transition-colors italic">Set Protocol Status to Achieved</span>
                        </label>
                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-2 ml-14 italic tracking-tight italic">Activating this will lock the objective and archive completion delta.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Strategic Projection Card -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-6 italic underline decoration-primary-500/30">Strategic Projection</h3>
                <div class="space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-white/30 uppercase tracking-widest">Growth progress</span>
                            <span class="text-xs font-black text-primary-400" x-text="calculatePercentage() + '%'">0%</span>
                        </div>
                        <div class="h-2 w-full bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-primary-500 shadow-[0_0_10px_rgba(37,99,235,0.5)] transition-all duration-500" :style="'width: ' + calculatePercentage() + '%'"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-[8px] font-black text-white/40 uppercase mb-2">Remaining Delta</p>
                            <h4 class="text-xl font-black text-white italic tracking-tighter" x-text="formatCurrency(targetAmount - currentAmount)">Rp 0</h4>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10">
                            <p class="text-[8px] font-black text-white/40 uppercase mb-2">Temporal Density (Months)</p>
                            <input type="number" x-model.number="projectionMonths" min="1" max="120"
                                   class="w-full bg-transparent border-none p-0 text-lg font-black text-primary-400 focus:ring-0">
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[8px] font-black text-white/40 uppercase mb-2">Required Monthly Delta</p>
                        <h4 class="text-2xl font-black text-white italic tracking-tighter" x-text="formatCurrency(calculateMonthlyRequirement())">Rp 0</h4>
                    </div>
                </div>
            </div>

            <!-- Authorization Card -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 italic underline decoration-primary-500 decoration-2">Strategic Authorization</h3>
                <p class="text-[10px] text-slate-400 uppercase font-bold leading-relaxed mb-8">Evolving this strategy will recalibrate the entire household portfolio projection immediatey.</p>
                
                <div class="space-y-4">
                    <button type="submit" class="w-full rounded-2xl bg-primary-600 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-primary-700 active:scale-95">
                        Authorize Evolution
                    </button>
                    <a href="{{ route('family.goals') }}" class="block w-full text-center mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors italic">Discard Modifications</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function goalCalculator(initialTarget, initialCurrent) {
    return {
        targetAmount: initialTarget || 0,
        currentAmount: initialCurrent || 0,
        projectionMonths: 12,
        calculatePercentage() {
            if (this.targetAmount > 0) {
                let p = (this.currentAmount / this.targetAmount) * 100;
                return Math.min(Math.round(p), 100);
            }
            return 0;
        },
        calculateMonthlyRequirement() {
            let remaining = this.targetAmount - this.currentAmount;
            if (remaining > 0 && this.projectionMonths > 0) {
                return Math.ceil(remaining / this.projectionMonths);
            }
            return 0;
        },
        formatCurrency(value) {
            if (value < 0) value = 0;
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        }
    }
}
</script>
@endsection
