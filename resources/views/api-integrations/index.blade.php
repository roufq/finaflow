@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">External Data Protocols</h1>
            <p class="text-sm font-medium text-slate-500">Orchestrate and monitor your third-party data integration hooks</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('api-integrations.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plug mr-2"></i>
                Establish Integration
            </a>
        </div>
    </div>

    <!-- Integrations Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($integrations as $integration)
        <div class="group relative flex flex-col p-6 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">{{ ucfirst(str_replace('_', ' ', $integration->provider)) }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Data Engine</p>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-black uppercase ring-1 ring-inset {{ $integration->is_active ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-slate-100 text-slate-400 ring-slate-200' }}">
                    {{ $integration->is_active ? 'Online' : 'Dormant' }}
                </span>
            </div>

            <div class="space-y-4 flex-1">
                <div class="flex items-center justify-between pb-4 border-b border-slate-50">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Last Synchronization</span>
                    <span class="text-[10px] font-black text-slate-900 uppercase">{{ $integration->last_sync_at?->diffForHumans() ?? 'Inaugural Sync Pending' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">API Rate Capacity</span>
                    <span class="text-[10px] font-black {{ $integration->rate_limit_remaining < 100 ? 'text-rose-500' : 'text-emerald-500' }} tabular-nums">{{ $integration->rate_limit_remaining ?? 'UNLIMITED' }}</span>
                </div>
            </div>

            <!-- Controller Interaction Area -->
            <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button data-sync="{{ route('api-integrations.sync', $integration) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600 transition-all shadow-sm" title="Invoke Sync">
                        <i class="fas fa-sync text-xs"></i>
                    </button>
                    <a href="{{ route('api-integrations.edit', $integration) }}" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all shadow-sm" title="Edit Parameters">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                </div>
                <form method="POST" action="{{ route('api-integrations.destroy', $integration) }}" onsubmit="return confirm('Purge this integration hook?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all shadow-sm" title="Erase Protocol">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-white rounded-2xl shadow-premium border border-dashed border-slate-200 flex flex-col items-center gap-6">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-200">
                <i class="fas fa-plug text-2xl"></i>
            </div>
            <div class="text-center">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">No Active Protocols</h4>
                <p class="text-[10px] font-medium text-slate-400 mt-2 max-w-sm">Connect to external gateways for credit intelligence, market data, and predictive environmental patterns.</p>
            </div>
            <a href="{{ route('api-integrations.create') }}" class="text-xs font-black text-primary-600 uppercase hover:underline">Establish First Protocol</a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-sync]').forEach(btn => {
    btn.addEventListener('click', async () => {
        const icon = btn.querySelector('i');
        btn.disabled = true;
        icon.classList.add('fa-spin', 'text-primary-600');
        
        try {
            const response = await fetch(btn.dataset.sync, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
            });
            const data = await response.json();
            window.location.reload();
        } catch (error) {
            console.error('Protocol Error:', error);
            icon.classList.remove('fa-spin');
            btn.disabled = false;
        }
    });
});
</script>
@endpush
@endsection
