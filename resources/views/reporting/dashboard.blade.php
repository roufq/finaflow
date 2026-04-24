@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('reporting.dashboard.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">{{ __('reporting.dashboard.subtitle') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('reporting.builder') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-project-diagram mr-2"></i>
                {{ __('reporting.buttons.open_builder') }}
            </a>
        </div>
    </div>

    <!-- Core Analytical Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Income -->
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ __('reporting.metrics.income') }}</span>
                <i class="fas fa-arrow-up text-emerald-400 text-xs"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-4 tabular-nums">Rp {{ number_format($metrics['income'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold tracking-tighter">Verified Inflow</p>
        </div>

        <!-- Expenses -->
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-rose-500">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ __('reporting.metrics.expenses') }}</span>
                <i class="fas fa-arrow-down text-rose-400 text-xs"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-4 tabular-nums">Rp {{ number_format($metrics['expenses'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold tracking-tighter">Aggregated Outflow</p>
        </div>

        <!-- Net Flow -->
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Net Flux</span>
                <i class="fas fa-balance-scale text-blue-400 text-xs"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-4 tabular-nums">Rp {{ number_format($metrics['net_flow'] ?? 0, 0, ',', '.') }}</h3>
            <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold tracking-tighter">Retained Capital</p>
        </div>

        <!-- Active Context -->
        <div class="rounded-2xl bg-slate-900 p-6 shadow-soft ring-1 ring-white/10">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{ __('reporting.metrics.goals') }}</span>
                <i class="fas fa-bullseye text-primary-400 text-xs"></i>
            </div>
            <h3 class="text-2xl font-black text-white mt-4">{{ $metrics['active_goals'] ?? 0 }}</h3>
            <div class="mt-2 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500" style="width: 60%"></div>
            </div>
        </div>
    </div>

    <!-- Reporting Intelligence Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Main Reports Ledger -->
        <div class="lg:col-span-8 space-y-6">
            <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-widest">{{ __('reporting.dashboard.reports') }}</h2>
                    <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-0.5 text-[9px] font-black text-primary-600 ring-1 ring-primary-100 uppercase">{{ $metrics['reports'] ?? 0 }} Templates</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-50 bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Structure / Manifest</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Cadence</th>
                                <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Depth</th>
                                <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Export Portal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($reports as $report)
                            <tr class="group transition-colors hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white shadow-soft">
                                            <i class="fas fa-file-invoice text-[10px]"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 leading-none">{{ $report->name }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ Str::limit($report->config['description'] ?? 'No description', 40) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">
                                        {{ $report->schedule ? __('reporting.schedules.' . $report->schedule) : 'Ad-hoc' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-lg bg-slate-50 px-2 py-1 text-[9px] font-black text-slate-400 ring-1 ring-slate-100 uppercase">
                                        {{ $report->widgets_count }} Nodes
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('reporting.builder', $report) }}" class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('reporting.reports.export', $report) }}" method="POST" class="flex gap-1">
                                            @csrf
                                            <select name="format" class="h-8 rounded-lg bg-slate-50 border-transparent px-2 text-[9px] font-black uppercase text-slate-600 focus:ring-1 focus:ring-primary-500 transition-all outline-none">
                                                @foreach(trans('reporting.formats') as $formatKey => $formatLabel)
                                                    <option value="{{ $formatKey }}" @selected($formatKey === $report->format)>{{ $formatLabel }}</option>
                                                @endforeach
                                            </select>
                                            <button class="h-8 px-3 bg-primary-600 text-white rounded-lg text-[9px] font-black uppercase shadow-soft hover:bg-primary-500 transition-all">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-xs font-medium text-slate-400 italic">{{ __('reporting.dashboard.empty_reports') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Integration Panel -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Quick Build Form -->
            <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-t-4 border-slate-900" id="quick-report">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i>
                    {{ __('reporting.dashboard.create_quick') }}
                </h3>
                <form method="POST" action="{{ route('reporting.reports.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Report Identifier</label>
                        <input type="text" name="name" required class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 placeholder-slate-300 focus:ring-1 focus:ring-primary-500 outline-none transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Cadence</label>
                            <select name="schedule" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 appearance-none outline-none focus:ring-1 focus:ring-primary-500">
                                <option value="">{{ __('reporting.forms.choose_report') }}</option>
                                @foreach(trans('reporting.schedules') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter mb-1.5 block">Format</label>
                            <select name="format" class="w-full h-11 rounded-xl bg-slate-50 border-transparent px-4 text-xs font-bold text-slate-900 appearance-none outline-none focus:ring-1 focus:ring-primary-500">
                                @foreach(trans('reporting.formats') as $key => $label)
                                    <option value="{{ $key }}" @selected($key === 'pdf')>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase shadow-soft hover:bg-slate-800 transition-all mt-4">
                        {{ __('reporting.forms.submit') }}
                    </button>
                </form>
            </div>

            <!-- Visualization Nodes (Widgets) -->
            <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ __('reporting.dashboard.widgets') }}</h3>
                </div>
                <div class="space-y-3">
                    @forelse($widgets as $widget)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/50 border border-slate-50 group hover:border-slate-200 transition-all">
                        <div>
                            <p class="text-[10px] font-black text-slate-900 uppercase tracking-tighter leading-none">{{ $widget->config['title'] ?? ucfirst($widget->type) }}</p>
                            <p class="text-[8px] font-bold text-slate-400 mt-1 uppercase">{{ Str::limit(trans('reporting.widgets.' . $widget->type . '.description'), 25) }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-md bg-white px-1.5 py-0.5 text-[8px] font-black text-slate-400 border border-slate-100">
                            {{ $widget->size }}
                        </span>
                    </div>
                    @empty
                    <p class="text-[10px] text-slate-400 italic text-center py-4">{{ __('reporting.dashboard.empty_widgets') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
