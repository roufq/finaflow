@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('behavioral.habits.create_title') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.habits.habit_details') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('behavioral.habits.store') }}">
                        @csrf

                        <div class="form-group">
                            <tags for="habit_name">{{ __('behavioral.habits.habit_name') }}</tags>
                            <input type="text" class="form-control @error('habit_name') is-invalid @enderror" id="habit_name" name="habit_name" value="{{ old('habit_name') }}" placeholder="{{ __('forms.placeholders.enter_name') }}" required>
                            @error('habit_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="category">{{ __('behavioral.habits.category') }}</tags>
                            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">{{ __('forms.placeholders.select_category') }}</option>
                                @foreach(trans('behavioral.habits.categories_options') as $value => $label)
                                    <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="target_amount">{{ __('behavioral.habits.target_amount') }}</tags>
                            <input type="number" class="form-control @error('target_amount') is-invalid @enderror" id="target_amount" name="target_amount" value="{{ old('target_amount') }}" placeholder="{{ __('behavioral.habits.target_amount') }}">
                            @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="start_date">{{ __('behavioral.habits.start_date') }}</tags>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success">{{ __('behavioral.habits.add_habit') }}</button>
                        <a href="{{ route('behavioral.habits') }}" class="btn btn-secondary">{{ __('forms.labels.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.habits.examples_title') }}</h6>
                </div>
                <div class="card-body">
                    @foreach (['saving', 'spending_control', 'budgeting'] as $category)
                        <h6>{{ trans('behavioral.habits.categories_options.' . $category) }}:</h6>
                        <ul class="small mb-3">
                            @foreach (trans('behavioral.habits.examples.' . $category) as $example)
                                <li>{{ $example }}</li>
                            @endforeach
                        </ul>
                    @endforeach

                    <div class="alert alert-info small">
                        <strong>{{ __('behavioral.habits.tip') }}</strong> {{ __('behavioral.habits.tip_description') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
