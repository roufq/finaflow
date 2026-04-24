@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('insights.predictions.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Predictive data model for future cash-flow trajectory and budgetary overhead</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                {{ __('common.back') }}
            </a>
            <form method="POST" action="{{ route('insights.generate') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                    <i class="fas fa-sync-alt mr-2"></i>
                    {{ __('insights.generate_insights') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Data Table Ecosystem -->
    <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.predictions.category') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.predictions.predicted_amount') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">{{ __('insights.predictions.confidence') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">{{ __('insights.predictions.period') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.predictions.prediction_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($predictions as $prediction)
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600 shadow-sm border border-primary-100">
                                    <i class="fas fa-tag text-[10px]"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-900">{{ $prediction->category }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-black text-slate-900">Rp {{ number_format($prediction->predicted_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                <div class="h-1.5 w-16 rounded-full bg-slate-100 overflow-hidden">
                                    @php
                                        $confidenceColor = $prediction->confidence >= 0.8 ? 'bg-emerald-500' : ($prediction->confidence >= 0.6 ? 'bg-amber-500' : 'bg-rose-500');
                                    @endphp
                                    <div class="h-full {{ $confidenceColor }} transition-all" style="width: {{ $prediction->confidence_percentage }}%"></div>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $prediction->confidence_percentage }}% Reliability</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2.5 py-0.5 text-[10px] font-bold text-indigo-600 uppercase tracking-tight">
                                {{ $prediction->period_tags }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-500">{{ $prediction->prediction_date->format('M d, Y') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                    <i class="fas fa-chart-line text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">{{ __('insights.no_predictions') }}</h4>
                                <p class="text-xs text-slate-400 max-w-[200px]">{{ __('insights.generate_predictions_hint') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($predictions->hasPages())
        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            {{ $predictions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
