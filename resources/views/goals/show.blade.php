@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ $goal->name }}</h1>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase ring-1 ring-inset {{ $goal->status === 'active' ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-200' }}">
                    {{ $goal->status_tags }}
                </span>
            </div>
            <p class="text-sm font-medium text-slate-500">Milestone velocity and accumulation manifest for your financial aspirations.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('goals.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('goals.edit', $goal) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-edit mr-2 text-primary-400"></i>
                Edit Goal
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Left: Accumulation Metrics -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Progress Visualization -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 text-center relative overflow-hidden">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary-50 blur-2xl opacity-50"></div>
                
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-10">Accumulation Velocity</h3>
                
                <div class="relative mx-auto flex h-48 w-48 items-center justify-center">
                    <svg class="h-full w-full -rotate-90 transform" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" class="text-slate-50"></circle>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" 
                                class="text-primary-500 transition-all duration-1000 ease-out"
                                stroke-dasharray="282.7"
                                stroke-dashoffset="{{ 282.7 - (min($goal->progress_percentage, 100) / 100 * 282.7) }}"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black tracking-tighter text-slate-900">{{ $goal->progress_percentage }}%</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Achieved</span>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-slate-50">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-left">
                            <p class="text-[9px] font-extrabold text-slate-400 uppercase">Stashed</p>
                            <p class="text-sm font-black text-slate-900">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-extrabold text-slate-400 uppercase">Aspiration</p>
                            <p class="text-sm font-black text-slate-900">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @if($goal->progress_percentage < 100)
                    <div class="mt-6 rounded-2xl bg-primary-50 p-3 ring-1 ring-primary-100">
                        <p class="text-[10px] font-bold text-primary-700">Rp {{ number_format($goal->target_amount - $goal->current_amount, 0, ',', '.') }} remaining until milestone.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Parameters Card -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-8">Mission Schema</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter">Thematic Focus</span>
                        <span class="text-xs font-black text-white italic capitalize">{{ str_replace('_', ' ', $goal->category) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter">Timeline Horizon</span>
                        <span class="text-xs font-black text-white italic capitalize">{{ str_replace('_', ' ', $goal->type) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-tighter">Target Deadline</span>
                        <span class="text-xs font-black text-white">{{ $goal->target_date->format('d M Y') }}</span>
                    </div>
                </div>
                @if($goal->description)
                <div class="mt-8 pt-8 border-t border-white/5">
                    <p class="text-[10px] font-medium text-slate-500 leading-relaxed italic line-clamp-3">{{ $goal->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Activity & Maintenance -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Progress Ledger -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/30">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Accumulation Ledger</h3>
                </div>
                <div class="overflow-x-auto">
                    @if($progressEntries->isEmpty())
                        <div class="py-20 flex flex-col items-center gap-4 text-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-200">
                                <i class="fas fa-layer-group text-2xl"></i>
                            </div>
                            <p class="text-xs font-medium text-slate-400 italic">No capital contributions recorded yet.</p>
                        </div>
                    @else
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-slate-50">
                                @foreach($progressEntries as $entry)
                                <tr class="group hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-900">{{ $entry->created_at->format('d M Y') }}</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">Transaction Log</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-medium text-slate-500 truncate max-w-[250px]">{{ $entry->note ?: 'Operational Contribution' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-black text-emerald-600 tracking-tight">+ Rp {{ number_format($entry->amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Maintenance Console -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 text-center md:text-left">Aspiration Maintenance</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <button @click="$dispatch('open-modal', 'add-progress')" class="flex items-center gap-4 rounded-2xl bg-primary-50 p-6 ring-1 ring-primary-100 transition-all hover:bg-primary-100 text-left">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-primary-500 shadow-sm">
                            <i class="fas fa-coins text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-primary-900 uppercase">Inject Capital</p>
                            <p class="text-[10px] font-medium text-primary-600 mt-1">Manual progress entry for historical tracking.</p>
                        </div>
                    </button>

                    <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="w-full" onsubmit="return confirm('Securely archive this aspiration?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex w-full items-center gap-4 rounded-2xl bg-rose-50 p-6 ring-1 ring-rose-100 transition-all hover:bg-rose-100 text-left">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-rose-500 shadow-sm">
                                <i class="fas fa-trash-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-rose-900 uppercase">Purge Protocol</p>
                                <p class="text-[10px] font-medium text-rose-600 mt-1">Permanently remove this financial target from core.</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Progress Modal -->
<div x-data="{ open: false }" @open-modal.window="if($event.detail === 'add-progress') open = true" class="relative z-50" x-show="open" x-cloak>
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Inject Capital</h3>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form id="addProgressForm" class="space-y-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Injection Volume (Rp)</label>
                                <input type="number" name="amount" min="1" step="1" required
                                       class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 focus:ring-4 focus:ring-primary-500/10">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Contextual Reference</label>
                                <input type="text" name="note" maxlength="255" placeholder="e.g. Monthly Surplus Allocation"
                                       class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end border-t border-slate-50 pt-6">
                            <button type="button" @click="open = false" class="px-6 py-3 text-sm font-bold text-slate-500">Cancel</button>
                            <button type="submit" class="rounded-xl bg-slate-900 px-8 py-3 text-sm font-extrabold text-white shadow-lg transition-all hover:bg-slate-800">Finalize Injection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProgressForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(`{{ route('goals.updateProgress', $goal) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during capital injection.');
            });
        });
    }
});
</script>
@endpush
@endsection
