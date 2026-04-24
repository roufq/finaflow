@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Bank Integrations</h1>
            <p class="text-sm font-medium text-slate-500">Manage real-time data synchronization with your financial institutions</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('bank-integrations.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Add Integration
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Financial Health Overview -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-slate-900">
            <div class="flex items-center justify-between uppercase tracking-widest text-slate-400 font-bold text-[10px]">
                Total Channels
                <i class="fas fa-university text-slate-200 text-base"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-900 mt-4">{{ $integrations->count() }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 font-bold uppercase tracking-tighter">Established gateways</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between uppercase tracking-widest text-slate-400 font-bold text-[10px]">
                Operational
                <i class="fas fa-check-circle text-emerald-400 text-base"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-900 mt-4">{{ $integrations->where('is_active', true)->count() }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 font-bold uppercase tracking-tighter">Live & Synchronized</p>
        </div>
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft ring-1 ring-white/10">
            <div class="flex items-center justify-between uppercase tracking-widest text-slate-400 font-bold text-[10px]">
                Aggregate Balance
                <i class="fas fa-wallet text-primary-400 text-base"></i>
            </div>
            <h3 class="text-3xl font-black text-white mt-4 tracking-tighter">
                Rp {{ number_format($integrations->where('is_active', true)->sum('current_balance'), 0, ',', '.') }}
            </h3>
            <div class="mt-2 h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500" style="width: 85%"></div>
            </div>
        </div>
    </div>

    <!-- Integrations Ledger -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Connected Gateways</h2>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bank-Grade Middleware</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Financial Institution</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Account Mapping</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Valuation</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Last Sync</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($integrations as $integration)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-white group-hover:shadow-sm transition-all border border-transparent group-hover:border-slate-100 uppercase font-black text-[10px]">
                                    {{ substr($integration->bank_name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900 leading-none">{{ $integration->bank_name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ $integration->account_number ?: 'Encrypted Number' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700">{{ $integration->account->name ?? 'N/A' }}</span>
                                <span class="text-[9px] font-bold text-primary-500 uppercase tracking-widest mt-0.5">{{ str_replace('_', ' ', $integration->account_type) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($integration->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black text-emerald-600 ring-1 ring-emerald-100 uppercase tracking-tighter">Active</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-400 ring-1 ring-slate-200 uppercase tracking-tighter">Disabled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($integration->current_balance !== null)
                            <p class="text-sm font-black text-slate-900 tabular-nums">Rp {{ number_format($integration->current_balance, 0, ',', '.') }}</p>
                            @if($integration->available_balance)
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Avail: Rp {{ number_format($integration->available_balance, 0, ',', '.') }}</p>
                            @endif
                            @else
                            <span class="text-xs font-medium text-slate-300 italic">No balance data</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter/50">
                                {{ $integration->last_sync_at ? $integration->last_sync_at->diffForHumans() : 'Never' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('bank-integrations.show', $integration) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600" title="View Full Ledger">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('bank-integrations.edit', $integration) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Configure Integration">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if($integration->is_active)
                                <button type="button" class="sync-btn flex h-8 w-8 items-center justify-center rounded-lg bg-white border border-emerald-200 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" data-sync-url="{{ route('bank-integrations.sync', $integration) }}" title="Immediate Sync">
                                    <i class="fas fa-sync text-xs"></i>
                                </button>
                                @endif
                                <form action="{{ route('bank-integrations.destroy', $integration) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this bank integration securely?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="Terminate Channel">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                    <i class="fas fa-university text-2xl"></i>
                                </div>
                                <p class="text-xs text-slate-400 font-medium italic">No bank integrations established yet.</p>
                                <a href="{{ route('bank-integrations.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-8 py-2.5 text-xs font-bold text-white shadow-premium hover:bg-slate-800">
                                    Begin Onboarding
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modern Sync Feedback UI (replacing old modal) -->
<div id="syncFeedback" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300">
    <div class="bg-white rounded-3xl p-8 shadow-2xl max-w-sm w-full mx-4 transform scale-90 transition-transform duration-300" id="feedbackCard">
        <div class="flex flex-col items-center text-center">
            <div id="feedbackIcon" class="h-16 w-16 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mb-6">
                <i class="fas fa-sync fa-spin text-2xl"></i>
            </div>
            <h4 id="feedbackTitle" class="text-lg font-black text-slate-900 mb-2">Synchronizing Channel</h4>
            <p id="feedbackMessage" class="text-xs text-slate-500 leading-relaxed">Please wait while we establish a secure handshake with the financial provider...</p>
            
            <div id="feedbackActions" class="mt-8 w-full hidden">
                <button onclick="hideFeedback()" class="w-full py-3 bg-slate-900 text-white rounded-xl text-xs font-bold shadow-premium hover:bg-slate-800">Acknowledgment</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const feedbackOverlay = document.getElementById('syncFeedback');
const feedbackCard = document.getElementById('feedbackCard');
const feedbackTitle = document.getElementById('feedbackTitle');
const feedbackMessage = document.getElementById('feedbackMessage');
const feedbackIcon = document.getElementById('feedbackIcon');
const feedbackActions = document.getElementById('feedbackActions');

function showFeedback(title, message, spinning = true) {
    feedbackTitle.textContent = title;
    feedbackMessage.textContent = message;
    feedbackIcon.innerHTML = spinning ? '<i class="fas fa-sync fa-spin text-2xl"></i>' : '<i class="fas fa-check text-2xl text-emerald-600"></i>';
    feedbackActions.classList.add('hidden');
    
    feedbackOverlay.classList.remove('opacity-0', 'pointer-events-none');
    feedbackCard.classList.remove('scale-90');
    feedbackCard.classList.add('scale-100');
}

function updateFeedback(title, message, success = true) {
    feedbackTitle.textContent = title;
    feedbackMessage.textContent = message;
    feedbackIcon.classList.remove('bg-primary-50', 'text-primary-600');
    feedbackIcon.classList.add(success ? 'bg-emerald-50' : 'bg-rose-50');
    feedbackIcon.innerHTML = success ? '<i class="fas fa-check text-2xl text-emerald-600"></i>' : '<i class="fas fa-exclamation-triangle text-2xl text-rose-500"></i>';
    feedbackActions.classList.remove('hidden');
}

function hideFeedback() {
    feedbackOverlay.classList.add('opacity-0', 'pointer-events-none');
    feedbackCard.classList.remove('scale-100');
    feedbackCard.classList.add('scale-90');
    if(window.needsReload) location.reload();
}

document.querySelectorAll('.sync-btn').forEach(button => {
    button.addEventListener('click', async () => {
        const url = button.dataset.syncUrl;
        showFeedback('Bank Handshake', 'Initializing secure communications with the provider...');
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                updateFeedback('Sync Confirmed', data.message || 'Legacy data successfully imported to vault.', true);
                window.needsReload = true;
            } else {
                updateFeedback('Sync Rejected', data.message || 'The provider refused the handshake at this time.', false);
            }
        } catch (e) {
            updateFeedback('Network Error', 'The secure gateway is currently unreachable. Check your uplink.', false);
        }
    });
});
</script>
@endpush
@endsection
