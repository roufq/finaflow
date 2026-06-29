@extends('layouts.app')

@section('content')
<div class="w-full">

    <!-- Page Heading -->
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('behavioral.habits') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('behavioral.habits.create_title') ?? 'Add New Habit' }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                <h2 class="text-lg font-bold text-slate-900 mb-6">{{ __('behavioral.habits.habit_details') ?? 'Habit Details' }}</h2>
                
                <form method="POST" action="{{ route('behavioral.habits.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="habit_name" class="block text-sm font-bold text-slate-700">{{ __('behavioral.habits.habit_name') ?? 'Habit Name' }}</label>
                        <input type="text" id="habit_name" name="habit_name" value="{{ old('habit_name') }}" placeholder="{{ __('forms.placeholders.enter_name') ?? 'Enter name...' }}" required 
                               class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('habit_name') ring-red-500 focus:ring-red-500 @enderror">
                        @error('habit_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="category" class="block text-sm font-bold text-slate-700">{{ __('behavioral.habits.category') ?? 'Category' }}</label>
                        <select id="category" name="category" required 
                                class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('category') ring-red-500 focus:ring-red-500 @enderror">
                            <option value="">{{ __('forms.placeholders.select_category') ?? 'Select Category' }}</option>
                            @php
                                $categories = trans('behavioral.habits.categories_options');
                                if (!is_array($categories)) {
                                    $categories = [
                                        'saving' => 'Saving & Investing',
                                        'spending_control' => 'Spending Control',
                                        'budgeting' => 'Budgeting & Tracking'
                                    ];
                                }
                            @endphp
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="target_amount" class="block text-sm font-bold text-slate-700">{{ __('behavioral.habits.target_amount') ?? 'Target Amount (Optional)' }}</label>
                        <input type="number" id="target_amount" name="target_amount" value="{{ old('target_amount') }}" placeholder="{{ __('behavioral.habits.target_amount') ?? 'e.g. 100000' }}" 
                               class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('target_amount') ring-red-500 focus:ring-red-500 @enderror">
                        @error('target_amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label for="start_date" class="block text-sm font-bold text-slate-700">{{ __('behavioral.habits.start_date') ?? 'Start Date' }}</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required 
                               class="mt-2 block w-full rounded-xl border-0 py-3 px-4 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-primary-500 sm:text-sm sm:leading-6 @error('start_date') ring-red-500 focus:ring-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-600 hover:shadow active:scale-95">
                            {{ __('behavioral.habits.add_habit') ?? 'Add Habit' }}
                        </button>
                        <a href="{{ route('behavioral.habits') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-95">
                            {{ __('forms.labels.cancel') ?? 'Cancel' }}
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
                    <h3 class="text-lg font-bold text-slate-900">{{ __('behavioral.habits.examples_title') ?? 'Habit Examples' }}</h3>
                </div>
                
                <div class="space-y-6 text-sm text-slate-600">
                    @php
                        $examplesData = [
                            'saving' => ['Transfer 10% of salary to savings', 'Save all 50k bills', 'No spend weekend'],
                            'spending_control' => ['Pack lunch for work', 'Wait 24h before buying', 'Cancel unused subscriptions'],
                            'budgeting' => ['Review budget weekly', 'Track every expense', 'Stick to grocery list']
                        ];
                    @endphp
                    @foreach (['saving', 'spending_control', 'budgeting'] as $category)
                        <div>
                            @php
                                $catLabel = trans('behavioral.habits.categories_options.' . $category);
                                if (!is_string($catLabel) || empty($catLabel)) $catLabel = ucfirst(str_replace('_', ' ', $category));
                            @endphp
                            <h4 class="font-bold text-slate-900 mb-2">{{ $catLabel }}:</h4>
                            <ul class="space-y-2">
                                @php
                                    $examples = trans('behavioral.habits.examples.' . $category);
                                    if (!is_array($examples)) $examples = $examplesData[$category];
                                @endphp
                                @foreach ($examples as $example)
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                                        <span>{{ $example }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    <div class="pt-4 border-t border-slate-200/60">
                        <div class="rounded-xl bg-blue-50 p-4 border border-blue-100">
                            <h4 class="font-bold text-blue-900 mb-1 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                {{ __('behavioral.habits.tip') ?? 'Pro Tip' }}
                            </h4>
                            <p class="leading-relaxed text-blue-800">{{ __('behavioral.habits.tip_description') ?? 'Start small! It takes an average of 66 days to form a new habit.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
