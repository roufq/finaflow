@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Automation Hub</h1>
            <p class="text-sm font-medium text-slate-500">Configure robotic workflows to manage your finances automatically</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('automations.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-robot mr-2"></i>
                New Automation
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Main Automation Ledger -->
        <div class="lg:col-span-8 flex flex-col rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Active Workflows</h2>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">System Operational</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-50 bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Workflow Name</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($automations as $automation)
                        <tr class="group transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">{{ $automation->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                    {{ $automation->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($automation->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black text-emerald-600 ring-1 ring-emerald-100 uppercase">
                                    Enabled
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-400 ring-1 ring-slate-200 uppercase">
                                    Disabled
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <button data-run="{{ route('automations.run', $automation) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-100 hover:scale-105 transition-all" title="Run Now">
                                        <i class="fas fa-play text-xs"></i>
                                    </button>
                                    <button data-toggle="{{ route('automations.toggle', $automation) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all" title="Toggle Status">
                                        <i class="fas fa-power-off text-xs"></i>
                                    </button>
                                    <a href="{{ route('automations.edit', $automation) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                        <i class="fas fa-robot text-2xl"></i>
                                    </div>
                                    <p class="text-xs text-slate-400 font-medium italic">No automation tasks configured yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar Components -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Toolbox -->
            <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">Integrations Toolbox</h2>
                <div class="space-y-4">
                    <a href="{{ route('integrations.voice-entry') }}" class="group flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600 transition-transform group-hover:scale-110">
                            <i class="fas fa-microphone"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Voice-to-Text Entry</p>
                            <p class="text-[10px] text-slate-400">Natural language processing</p>
                        </div>
                    </a>
                    <a href="{{ route('integrations.email-parser') }}" class="group flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition-transform group-hover:scale-110">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Email Parser</p>
                            <p class="text-[10px] text-slate-400">Automated invoice scanning</p>
                        </div>
                    </a>
                    <a href="{{ route('integrations.telegram-bot') }}" class="group flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 transition-transform group-hover:scale-110">
                            <i class="fab fa-telegram-plane"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Telegram Assistant</p>
                            <p class="text-[10px] text-slate-400">Conversational AI entry</p>
                        </div>
                    </a>
                    <a href="{{ route('integrations.reminders') }}" class="group flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition-transform group-hover:scale-110">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Bill Reminders</p>
                            <p class="text-[10px] text-slate-400">Prevent late payment fees</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Global Status Card -->
            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft ring-1 ring-white/10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">System Pulse</span>
                    <i class="fas fa-tachometer-alt text-primary-400"></i>
                </div>
                <div class="flex flex-col items-center py-4">
                    <span class="text-3xl font-black text-white">100%</span>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-tighter mt-1">Uptime Synchronized</span>
                </div>
                <p class="text-[10px] text-slate-500 font-medium leading-relaxed text-center">
                    All connected bank APIs and local crawlers are communicating correctly.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('[data-run]').forEach(button => {
    button.addEventListener('click', async () => {
        if(!confirm('Execute this automation immediately?')) return;
        try {
            const response = await fetch(button.dataset.run, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            alert(data.message || 'Automation sequence completed.');
        } catch (error) {
            alert('Execution failed. Please check logs.');
        }
    });
});

document.querySelectorAll('[data-toggle]').forEach(button => {
    button.addEventListener('click', async () => {
        try {
            const response = await fetch(button.dataset.toggle, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            window.location.reload();
        } catch (error) {
            alert('Status update failed.');
        }
    });
});
</script>
@endpush
@endsection
