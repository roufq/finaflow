@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('insights.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Advanced AI-driven intelligence for your financial ecosystem</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <form id="generate-insights-form" method="POST" action="{{ route('insights.generate') }}">
                @csrf
                <button type="submit" id="generate-insights-btn" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-sync-alt mr-2" id="sync-icon"></i>
                    <span id="btn-text">{{ __('insights.generate_insights') }}</span>
                </button>
            </form>
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

    <div id="feedback-anchor"></div>

    <!-- Insights Intelligence Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
        
        <!-- Recommendations (Strategy) -->
        <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 text-white shadow-soft">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('insights.recommendations.title') }}</h3>
                </div>
                <a href="{{ route('insights.recommendations') }}" class="text-[10px] font-black text-primary-600 uppercase hover:underline">{{ __('common.view_all') }}</a>
            </div>
            <div class="p-6 space-y-4 flex-1">
                @forelse($recommendations as $recommendation)
                    <div class="relative p-5 rounded-2xl {{ $recommendation->priority >= 3 ? 'bg-amber-50 border-amber-100' : 'bg-blue-50 border-blue-100' }} border group">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[9px] font-black uppercase tracking-tighter {{ $recommendation->priority >= 3 ? 'text-amber-600' : 'text-blue-600' }}">
                                {{ __('insights.recommendations.priority_' . $recommendation->priority) }}
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-900 leading-relaxed mb-3">{{ $recommendation->content }}</p>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">{{ $recommendation->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="py-10 text-center flex flex-col items-center gap-3">
                        <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                            <i class="fas fa-ghost"></i>
                        </div>
                        <p class="text-[10px] font-medium text-slate-400 italic">{{ __('insights.no_recommendations') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Anomalies (Risk Management) -->
        <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500 text-white shadow-soft">
                        <i class="fas fa-exclamation-triangle text-sm"></i>
                    </div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('insights.anomalies.title') }}</h3>
                </div>
                <a href="{{ route('insights.anomalies') }}" class="text-[10px] font-black text-primary-600 uppercase hover:underline">{{ __('common.view_all') }}</a>
            </div>
            <div class="p-6 space-y-4 flex-1">
                @forelse($anomalies as $anomaly)
                    <div class="relative p-5 rounded-2xl {{ $anomaly->severity >= 3 ? 'bg-rose-50 border-rose-100' : ($anomaly->severity >= 2 ? 'bg-amber-50 border-amber-100' : 'bg-slate-50 border-slate-100') }} border">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[9px] font-black uppercase tracking-tighter {{ $anomaly->severity >= 3 ? 'text-rose-600' : ($anomaly->severity >= 2 ? 'text-amber-600' : 'text-slate-600') }}">
                                {{ $anomaly->severity_tags }}
                            </span>
                        </div>
                        <p class="text-xs font-bold text-slate-900 leading-relaxed mb-3">{{ $anomaly->description }}</p>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">{{ $anomaly->detected_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="py-10 text-center flex flex-col items-center gap-3">
                        <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <p class="text-[10px] font-medium text-slate-400 italic">{{ __('insights.no_anomalies') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Predictions (Forecasting) -->
        <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-soft">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('insights.predictions.title') }}</h3>
                </div>
                <a href="{{ route('insights.predictions') }}" class="text-[10px] font-black text-primary-600 uppercase hover:underline">{{ __('common.view_all') }}</a>
            </div>
            <div class="p-6 space-y-4 flex-1">
                @forelse($predictions as $prediction)
                    <div class="relative p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">{{ $prediction->category }}</span>
                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ __('insights.predictions.confidence') }}: {{ $prediction->confidence_percentage }}%</span>
                        </div>
                        <p class="text-xs font-bold text-slate-900 leading-relaxed mb-1">
                            Expected: <span class="text-sm font-black tracking-tight tabular-nums">Rp {{ number_format($prediction->predicted_amount, 0, ',', '.') }}</span>
                        </p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $prediction->period_tags }} projection window</p>
                    </div>
                @empty
                    <div class="py-10 text-center flex flex-col items-center gap-3">
                        <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                            <i class="fas fa-crystal-ball"></i>
                        </div>
                        <p class="text-[10px] font-medium text-slate-400 italic">{{ __('insights.no_predictions') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('generate-insights-form');
    const button = document.getElementById('generate-insights-btn');
    const syncIcon = document.getElementById('sync-icon');
    const btnText = document.getElementById('btn-text');
    const anchor = document.getElementById('feedback-anchor');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show Loading State
        button.disabled = true;
        syncIcon.classList.add('fa-spin');
        btnText.innerText = 'Calculating Matrix...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (response.headers.get('content-type')?.includes('application/json')) {
                return response.json();
            }
            window.location.reload();
        })
        .then(data => {
            if (data && data.success) {
                renderAlert('emerald', 'Success', data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else if (data && !data.success) {
                throw new Error(data.message || 'Anomaly detected during generation');
            }
        })
        .catch(error => {
            console.error('Core Logic Error:', error);
            renderAlert('rose', 'System Alert', error.message);
            button.disabled = false;
            syncIcon.classList.remove('fa-spin');
            btnText.innerText = '{{ __("insights.generate_insights") }}';
        });
    });

    function renderAlert(color, title, message) {
        const div = document.createElement('div');
        div.className = `relative overflow-hidden rounded-2xl bg-${color}-50 border border-${color}-100 p-4 shadow-sm flex items-center gap-4 mb-6 transition-all duration-500`;
        div.innerHTML = `
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-${color}-500 shadow-sm ring-1 ring-${color}-100">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase text-${color}-600 tracking-widest">${title}</p>
                <p class="text-xs font-bold text-${color}-900">${message}</p>
            </div>
        `;
        anchor.prepend(div);
    }
});
</script>
@endpush
