@extends('layouts.app')

@section('content')
<div class="w-full">

    <!-- Page Heading -->
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('behavioral.triggers') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('behavioral.triggers.create_title') ?? 'Add New Trigger' }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                <h2 class="text-lg font-bold text-slate-900 mb-6">{{ __('behavioral.triggers.trigger_details') ?? 'Trigger Details' }}</h2>
                
                <form method="POST" action="{{ route('behavioral.triggers.store') }}">
                    @csrf

                    <div class="mb-5">
                        @php
                            $triggerOptions = [
                                'emotional' => __('behavioral.triggers.emotional') ?? 'Emotional',
                                'social' => __('behavioral.triggers.social_pressure') ?? 'Social Pressure',
                                'impulse' => __('behavioral.triggers.impulse_buying') ?? 'Impulse Buying',
                                'reward' => __('behavioral.triggers.reward_shopping') ?? 'Reward Shopping',
                                'boredom' => __('behavioral.triggers.boredom_shopping') ?? 'Boredom',
                                'stress' => __('behavioral.triggers.stress_relief') ?? 'Stress Relief',
                                'other' => __('behavioral.triggers.other') ?? 'Other',
                            ];
                        @endphp
                        <label for="trigger_type" class="block text-sm font-bold text-slate-700">{{ __('behavioral.triggers.trigger_type') ?? 'Trigger Type' }}</label>
                        <select id="trigger_type" name="trigger_type" required 
                                class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('trigger_type') ring-red-500 focus:ring-red-500 @enderror">
                            <option value="">{{ __('behavioral.triggers.select_trigger_type') ?? 'Select Trigger Type' }}</option>
                            @foreach ($triggerOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('trigger_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('trigger_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="description" class="block text-sm font-bold text-slate-700">{{ __('behavioral.triggers.description') ?? 'Description' }}</label>
                        <textarea id="description" name="description" rows="3" required placeholder="{{ __('behavioral.triggers.describe_trigger') ?? 'Describe when and why this occurs...' }}" 
                                  class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 custom-scrollbar @error('description') ring-red-500 focus:ring-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="amount_threshold" class="block text-sm font-bold text-slate-700">{{ __('behavioral.triggers.amount_threshold') ?? 'Amount Threshold (Rp)' }}</label>
                        <input type="number" id="amount_threshold" name="amount_threshold" value="{{ old('amount_threshold') }}" required placeholder="{{ __('behavioral.triggers.minimum_amount') ?? 'Minimum amount that triggers this' }}" 
                               class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('amount_threshold') ring-red-500 focus:ring-red-500 @enderror">
                        @error('amount_threshold')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label for="frequency" class="block text-sm font-bold text-slate-700">{{ __('behavioral.triggers.initial_frequency') ?? 'Initial Frequency' }}</label>
                        <input type="number" id="frequency" name="frequency" value="{{ old('frequency', 1) }}" min="1" required 
                               class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('frequency') ring-red-500 focus:ring-red-500 @enderror">
                        @error('frequency')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-600 hover:shadow active:scale-95">
                            {{ __('behavioral.triggers.save_trigger') ?? 'Save Trigger' }}
                        </button>
                        <a href="{{ route('behavioral.triggers') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-95">
                            {{ __('behavioral.triggers.cancel') ?? 'Cancel' }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="lg:col-span-1">
            <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-500 shadow-sm">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ __('behavioral.triggers.help') ?? 'Help' }}</h3>
                </div>
                
                <div class="space-y-6 text-sm text-slate-600">
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">{{ __('behavioral.triggers.what_is_trigger') ?? 'What is a Spending Trigger?' }}</h4>
                        <p class="leading-relaxed">{{ __('behavioral.triggers.trigger_explanation') ?? 'A spending trigger is a situation, emotion, or circumstance that prompts you to make unplanned purchases.' }}</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 mb-2">{{ __('behavioral.triggers.common_examples') ?? 'Common Examples:' }}</h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                                <span><strong class="text-slate-800">{{ __('behavioral.triggers.emotional') ?? 'Emotional Spending' }}:</strong> {{ __('behavioral.insights.emotional_desc') ?? 'Shopping when stressed or bored.' }}</span>
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

                    <div class="pt-4 border-t border-slate-200/60">
                        <h4 class="font-bold text-slate-900 mb-1">{{ __('behavioral.triggers.why_track') ?? 'Why Track Triggers?' }}</h4>
                        <p class="leading-relaxed">{{ __('behavioral.triggers.why_track_description') ?? 'By identifying your spending triggers, you can develop strategies to avoid or manage them effectively.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
