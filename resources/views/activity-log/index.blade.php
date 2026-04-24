@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Activity Log</h1>
            <p class="text-sm font-medium text-slate-500">Track and monitor all system interactions and account activities.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400 text-xs"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100/50">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-500">
                    <i class="fas fa-list-ul"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Total Events</p>
                    <h3 class="text-xl font-bold text-slate-900">{{ number_format($logs->total()) }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100/50">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400">Last Activity</p>
                    <h3 class="text-xl font-bold text-slate-900">{{ optional($logs->first())->created_at ? $logs->first()->created_at->diffForHumans() : 'No activity' }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-3xl bg-slate-900 p-6 shadow-soft text-white">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-primary-400">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-white/40">Security Status</p>
                    <h3 class="text-xl font-bold text-white uppercase tracking-tight">Active MONITORING</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Table -->
    <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/30">
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Date & Time</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">IP Address</th>
                        <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Device / Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="group transition-colors hover:bg-slate-50/30">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900">{{ $log->created_at ? $log->created_at->format('M d, Y') : '-' }}</span>
                                <span class="text-[11px] font-medium text-slate-400">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            @php
                                $actionType = strtolower($log->action);
                                $badgeClass = match(true) {
                                    str_contains($actionType, 'login') => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                                    str_contains($actionType, 'logout') => 'bg-slate-100 text-slate-600 ring-slate-200',
                                    str_contains($actionType, 'security') => 'bg-rose-50 text-rose-600 ring-rose-100',
                                    str_contains($actionType, 'update') || str_contains($actionType, 'edit') => 'bg-amber-50 text-amber-600 ring-amber-100',
                                    str_contains($actionType, 'create') => 'bg-primary-50 text-primary-600 ring-primary-100',
                                    default => 'bg-slate-50 text-slate-600 ring-slate-100'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-black uppercase tracking-tight ring-1 {{ $badgeClass }}">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm font-medium text-slate-600 line-clamp-2 max-w-xs">{{ $log->description ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="font-mono text-xs font-semibold text-slate-400 bg-slate-50 px-2 py-1 rounded-md">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-5 max-w-xs truncate" title="{{ $log->user_agent }}">
                            @php
                                $agent = $log->user_agent ?? '-';
                                if (str_contains(strtolower($agent), 'chrome')) $icon = 'fab fa-chrome';
                                elseif (str_contains(strtolower($agent), 'firefox')) $icon = 'fab fa-firefox';
                                elseif (str_contains(strtolower($agent), 'safari')) $icon = 'fab fa-safari';
                                elseif (str_contains(strtolower($agent), 'edge')) $icon = 'fab fa-edge';
                                else $icon = 'fas fa-desktop';
                            @endphp
                            <div class="flex items-center gap-2">
                                <i class="{{ $icon }} text-slate-300 text-xs"></i>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter truncate">{{ $agent }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="h-16 w-16 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-200">
                                    <i class="fas fa-fingerprint text-2xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-400 italic">No activity recorded on your ledger yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-6 border-t border-slate-50 bg-slate-50/20">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} Events
                </p>
                <div class="pagination-tailwind">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

