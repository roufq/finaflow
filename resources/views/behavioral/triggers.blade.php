@extends('layouts.app')

@section('content')
<div class="w-full">

    <!-- Page Heading -->
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('behavioral.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('behavioral.triggers.title') ?? 'Spending Triggers' }}</h1>
        </div>
        <a href="{{ route('behavioral.triggers.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-600 active:scale-95">
            <i class="fas fa-plus"></i> {{ __('behavioral.triggers.add_trigger') ?? 'Add Trigger' }}
        </a>
    </div>

    <!-- Triggers List -->
    <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 mb-8">
        <h2 class="text-lg font-bold text-slate-900 mb-6">{{ __('behavioral.triggers.title') ?? 'Spending Triggers' }}</h2>
        
        @if($triggers->count() > 0)
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm text-slate-600" id="dataTable">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">No</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">{{ __('behavioral.triggers.trigger_type') ?? 'Type' }}</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">{{ __('behavioral.triggers.description') ?? 'Description' }}</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">{{ __('behavioral.triggers.frequency') ?? 'Frequency' }}</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">{{ __('behavioral.triggers.amount_threshold') ?? 'Threshold' }}</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold">{{ __('behavioral.triggers.last_detected') ?? 'Last Detected' }}</th>
                            <th class="whitespace-nowrap px-4 py-3 font-bold text-center">{{ __('behavioral.triggers.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($triggers as $trigger)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="whitespace-nowrap px-4 py-4">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4 font-medium text-slate-900 capitalize">{{ $trigger->trigger_type }}</td>
                            <td class="px-4 py-4 min-w-[200px]">{{ $trigger->description }}</td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="inline-flex items-center rounded-lg bg-amber-50 px-2 py-1 text-xs font-bold text-amber-600 ring-1 ring-inset ring-amber-500/20">
                                    {{ $trigger->frequency }} times
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-medium">Rp {{ number_format($trigger->amount_threshold, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $trigger->updated_at->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-500 transition-colors hover:bg-red-500 hover:text-white" onclick="deleteTrigger({{ $trigger->id }})">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-amber-500">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900">{{ __('behavioral.triggers.no_triggers') ?? 'No Spending Triggers Detected' }}</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-md">{{ __('behavioral.triggers.auto_detection') ?? 'Spending triggers will be automatically detected as you add transactions.' }}</p>
                <a href="{{ route('behavioral.triggers.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-slate-800 active:scale-95">
                    <i class="fas fa-plus"></i> {{ __('behavioral.triggers.add_manual') ?? 'Add Manual Trigger' }}
                </a>
            </div>
        @endif
    </div>

    <!-- Insights Card -->
    <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-500 shadow-sm">
                <i class="fas fa-brain"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">{{ __('behavioral.insights.title') ?? 'Behavioral Insights' }}</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-900 mb-3">{{ __('behavioral.insights.common_triggers') ?? 'Common Triggers' }}</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.triggers.emotional_spending') ?? 'Emotional' }}:</strong> {{ __('behavioral.insights.emotional_desc') ?? 'Shopping when stressed or bored.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.triggers.social_pressure') ?? 'Social Pressure' }}:</strong> {{ __('behavioral.insights.social_desc') ?? 'Keeping up with friends or family.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.triggers.impulse_buying') ?? 'Impulse Buying' }}:</strong> {{ __('behavioral.insights.impulse_desc') ?? 'Online shopping temptations.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.triggers.reward_shopping') ?? 'Reward Shopping' }}:</strong> {{ __('behavioral.insights.reward_desc') ?? 'Treating yourself after achievements.' }}</span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-3">{{ __('behavioral.insights.tips_title') ?? 'Tips to Avoid Triggers' }}</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1 shrink-0"></i>
                        <span>{{ __('behavioral.insights.tip_1') ?? 'Set a 24-hour waiting period for non-essential purchases.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1 shrink-0"></i>
                        <span>{{ __('behavioral.insights.tip_2') ?? 'Use cash instead of cards for discretionary spending.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1 shrink-0"></i>
                        <span>{{ __('behavioral.insights.tip_3') ?? 'Identify your emotional triggers and find alternatives.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-green-500 mt-1 shrink-0"></i>
                        <span>{{ __('behavioral.insights.tip_4') ?? 'Track your spending patterns to stay aware.' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<script>
function deleteTrigger(id) {
    if (confirm('{{ __('behavioral.triggers.confirm_delete') ?? 'Are you sure you want to delete this trigger?' }}')) {
        // Implement delete functionality
        alert('{{ __('behavioral.triggers.delete_implement') ?? 'Delete functionality will be implemented here.' }}');
    }
}
</script>
@endsection
