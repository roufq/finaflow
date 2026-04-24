@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Log shared obligation</h1>
            <p class="text-sm font-medium text-slate-500">Initialize a collaborative financial event within your household unit</p>
        </div>
        <a href="{{ route('family.shared-expenses') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
            Back to Audit
        </a>
    </div>

    <form method="POST" action="{{ route('family.shared-expenses.store') }}" id="sharedExpenseForm" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        @csrf
        
        <div class="lg:col-span-2 space-y-8">
            <!-- Core Metadata Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-white">
                        <i class="fas fa-info-circle text-xs"></i>
                    </div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Metadata Infrastructure</h2>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label for="expense_name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Event Identifier *</label>
                        <input type="text" id="expense_name" name="expense_name" value="{{ old('expense_name') }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('expense_name') ring-red-500 @enderror"
                               placeholder="e.g., Monthly Grocery Run, Utility Settlement">
                        @error('expense_name') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="total_amount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Principal Amount *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 font-bold text-xs uppercase">Rp</div>
                                <input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" min="0" step="1000" required
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-black text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('total_amount') ring-red-500 @enderror">
                            </div>
                            @error('total_amount') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="category" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Capital Corridor *</label>
                            <select id="category" name="category" required
                                    class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                                <option value="">Select Corridor</option>
                                @foreach(['Food & Dining', 'Transportation', 'Entertainment', 'Shopping', 'Utilities', 'Healthcare', 'Education', 'Travel', 'Other'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="expense_date" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Execution Date *</label>
                            <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                        </div>

                        <div class="space-y-2">
                            <label for="split_method" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Distribution Protocol *</label>
                            <select id="split_method" name="split_method" required onchange="toggleSplitOptions()"
                                    class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                                <option value="">Select Logic</option>
                                <option value="equal" {{ old('split_method') == 'equal' ? 'selected' : '' }}>Equitable Balance (Equal)</option>
                                <option value="percentage" {{ old('split_method') == 'percentage' ? 'selected' : '' }}>Weighted Ratio (%)</option>
                                <option value="custom" {{ old('split_method') == 'custom' ? 'selected' : '' }}>Discretionary Audit (Custom)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Contextual Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-medium text-slate-600 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                                  placeholder="Provide optional strategic context for this event...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Unit Participants Card -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden border-b-4 border-primary-500">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-xl bg-primary-500 flex items-center justify-center text-white">
                            <i class="fas fa-users text-xs"></i>
                        </div>
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Unit Participant Registry</h2>
                    </div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Validation Required</span>
                </div>
                <div class="p-8">
                    @if($members->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($members as $member)
                            <div class="participant-item p-4 rounded-2xl ring-1 ring-slate-100 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 flex-1">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" id="participant_{{ $member->id }}" name="participants[]" value="{{ $member->id }}"
                                                   class="participant-checkbox h-5 w-5 rounded-lg border-slate-300 text-primary-600 focus:ring-primary-500/20"
                                                   onchange="updateParticipantShares()"
                                                   {{ in_array($member->id, old('participants', [])) ? 'checked' : '' }}>
                                        </div>
                                        <label for="participant_{{ $member->id }}" class="flex flex-col cursor-pointer">
                                            <span class="text-sm font-black text-slate-900 leading-tight italic">{{ $member->name }}</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $member->relationship }}</span>
                                        </label>
                                    </div>

                                    <div class="participant-share hidden" id="share_{{ $member->id }}">
                                        <div class="relative">
                                            <input type="number" id="share_amount_{{ $member->id }}" name="share_amount[{{ $member->id }}]"
                                                   class="w-24 rounded-lg border-none bg-slate-100 px-3 py-2 text-xs font-black text-slate-900 ring-1 ring-slate-200 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                                                   placeholder="Value" min="0" step="1000" onchange="validateShares()">
                                            <div class="absolute -top-1.5 -right-1.5">
                                                <span class="inline-flex items-center rounded-full bg-slate-900 px-1.5 py-0.5 text-[7px] font-black text-white uppercase" id="share_tags_{{ $member->id }}"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center space-y-4">
                            <div class="h-16 w-16 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-200">
                                <i class="fas fa-users-slash text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 uppercase italic">No active unit members found</p>
                                <p class="text-xs text-slate-400 mt-1">Initialize your household directory to enable collaborative auditing.</p>
                            </div>
                            <a href="{{ route('family.members.create') }}" class="text-xs font-black text-primary-600 uppercase hover:underline">Add First Member</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Split Manifest Card (Calculator) -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10" id="splitPreviewCard" style="display: none;">
                <h3 class="text-xs font-black uppercase tracking-widest text-white/40 mb-6">Liquidity Manifest</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <span class="text-[10px] font-bold text-white/60 uppercase">Gross Aggregate</span>
                        <span class="text-xl font-black text-primary-400" id="previewTotal">Rp 0</span>
                    </div>
                    <div id="participantPreviews" class="space-y-4"></div>
                    <div id="validationMessage" class="hidden rounded-xl bg-red-500/10 border border-red-500/20 p-4 text-[10px] font-bold text-red-400 leading-relaxed uppercase tracking-tighter italic"></div>
                </div>
            </div>

            <!-- Strategy Card -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 italic underline decoration-primary-500 decoration-2">Tactical Guidance</h3>
                <ul class="space-y-4">
                    <li class="flex gap-4">
                        <div class="h-5 w-5 shrink-0 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center">
                            <i class="fas fa-check text-[8px]"></i>
                        </div>
                        <p class="text-xs text-slate-600 font-medium leading-relaxed italic"><span class="font-black text-slate-900 uppercase text-[10px] not-italic tracking-tighter">Equal Split:</span> Standardized balance for mutual benefits.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="h-5 w-5 shrink-0 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-[8px]">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="text-xs text-slate-600 font-medium leading-relaxed italic"><span class="font-black text-slate-900 uppercase text-[10px] not-italic tracking-tighter">Percentage:</span> Proportional contribution based on earning capacity.</p>
                    </li>
                </ul>
                <div class="mt-8 pt-8 border-t border-slate-50">
                    <button type="submit" class="w-full rounded-2xl bg-primary-600 px-6 py-4 text-xs font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-primary-700 active:scale-95">
                        Initialize Expense Audit
                    </button>
                    <a href="{{ route('family.shared-expenses') }}" class="block w-full text-center mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors italic">Abort Transaction</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let selectedParticipants = [];

function toggleSplitOptions() {
    const method = document.getElementById('split_method').value;
    const participants = document.querySelectorAll('.participant-share');

    participants.forEach(p => p.classList.add('hidden'));

    if (method) {
        updateParticipantShares();
    }
}

function updateParticipantShares() {
    selectedParticipants = Array.from(document.querySelectorAll('.participant-checkbox:checked')).map(cb => cb.value);
    const method = document.getElementById('split_method').value;
    const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;

    document.querySelectorAll('.participant-share').forEach(share => {
        const memberId = share.id.replace('share_', '');
        const isSelected = selectedParticipants.includes(memberId);
        const tag = document.getElementById(`share_tags_${memberId}`);
        const input = document.getElementById(`share_amount_${memberId}`);

        if (isSelected && method) {
            share.classList.remove('hidden');

            if (method === 'equal') {
                const equalShare = selectedParticipants.length > 0 ? totalAmount / selectedParticipants.length : 0;
                input.value = Math.round(equalShare);
                input.readOnly = true;
                tag.textContent = 'VAL';
            } else if (method === 'percentage') {
                input.readOnly = false;
                tag.textContent = '%';
            } else if (method === 'custom') {
                input.readOnly = false;
                tag.textContent = 'Rp';
            }
        } else {
            share.classList.add('hidden');
        }
    });

    updateSplitPreview();
}

function validateShares() {
    updateSplitPreview();
}

function updateSplitPreview() {
    const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
    const method = document.getElementById('split_method').value;
    const manifestCard = document.getElementById('splitPreviewCard');

    if (selectedParticipants.length === 0 || !method || totalAmount === 0) {
        manifestCard.style.display = 'none';
        return;
    }

    manifestCard.style.display = 'block';
    document.getElementById('previewTotal').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');

    const previewContainer = document.getElementById('participantPreviews');
    previewContainer.innerHTML = '';
    
    let totalComputedShares = 0;

    selectedParticipants.forEach(memberId => {
        const memberNameLabel = document.querySelector(`label[for="participant_${memberId}"] span`).textContent;
        let shareAmount = 0;
        let shareText = '';
        let inputVal = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;

        if (method === 'equal') {
            shareAmount = Math.round(totalAmount / selectedParticipants.length);
            shareText = 'Rp ' + shareAmount.toLocaleString('id-ID');
            totalComputedShares += shareAmount;
        } else if (method === 'percentage') {
            shareAmount = (totalAmount * inputVal) / 100;
            shareText = `${inputVal}% (Rp ${Math.round(shareAmount).toLocaleString('id-ID')})`;
            totalComputedShares += inputVal;
        } else if (method === 'custom') {
            shareAmount = inputVal;
            shareText = 'Rp ' + shareAmount.toLocaleString('id-ID');
            totalComputedShares += shareAmount;
        }

        const previewItem = document.createElement('div');
        previewItem.className = 'flex justify-between items-end';
        previewItem.innerHTML = `<span class='text-[11px] font-black uppercase text-white/40 tracking-tighter italic font-serif leading-none'>${memberNameLabel}</span><span class='text-sm font-black text-white tabular-nums'>${shareText}</span>`;
        previewContainer.appendChild(previewItem);
    });

    const validationMsg = document.getElementById('validationMessage');
    validationMsg.classList.add('hidden');

    if (method === 'percentage' && totalComputedShares !== 100) {
        validationMsg.innerHTML = `<i class='fas fa-exclamation-triangle mr-1'></i> Weighted ratio imbalance: current total ${totalComputedShares}%. Verification requires precisely 100%.`;
        validationMsg.classList.remove('hidden');
    } else if (method === 'custom' && Math.abs(totalComputedShares - totalAmount) > 10) {
        validationMsg.innerHTML = `<i class='fas fa-exclamation-triangle mr-1'></i> Discretionary audit mismatch: total Rp ${totalComputedShares.toLocaleString('id-ID')} vs principal Rp ${totalAmount.toLocaleString('id-ID')}.`;
        validationMsg.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('total_amount').addEventListener('input', updateParticipantShares);
    document.getElementById('expense_date').max = new Date().toISOString().split('T')[0];
    toggleSplitOptions();
});

document.getElementById('sharedExpenseForm').addEventListener('submit', function(e) {
    const participants = document.querySelectorAll('.participant-checkbox:checked');
    if (participants.length === 0) {
        e.preventDefault();
        alert('Unit participation registry is empty. Protocol requires at least one operative.');
        return;
    }

    const method = document.getElementById('split_method').value;
    if (method === 'percentage' || method === 'custom') {
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        let totalShares = 0;

        participants.forEach(cb => {
            const memberId = cb.value;
            const amount = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
            totalShares += amount;
        });

        if (method === 'percentage' && Math.abs(totalShares - 100) > 0.01) {
            e.preventDefault();
            alert('Weighted ratio imbalance detected. Distribution must equal 100%.');
            return;
        }

        if (method === 'custom' && Math.abs(totalShares - totalAmount) > 10) {
            e.preventDefault();
            alert('Audit mismatch detected. Participant aggregate must equal total principal.');
            return;
        }
    }
});
</script>
@endsection
