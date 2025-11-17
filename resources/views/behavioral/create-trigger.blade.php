@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('behavioral.triggers.create_title') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.triggers.trigger_details') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('behavioral.triggers.store') }}">
                        @csrf

                        <div class="form-group">
                            @php
                                $triggerOptions = [
                                    'emotional' => __('behavioral.triggers.emotional'),
                                    'social' => __('behavioral.triggers.social_pressure'),
                                    'impulse' => __('behavioral.triggers.impulse_buying'),
                                    'reward' => __('behavioral.triggers.reward_shopping'),
                                    'boredom' => __('behavioral.triggers.boredom_shopping'),
                                    'stress' => __('behavioral.triggers.stress_relief'),
                                    'other' => __('behavioral.triggers.other'),
                                ];
                            @endphp
                            <label for="trigger_type">{{ __('behavioral.triggers.trigger_type') }}</label>
                            <select class="form-control @error('trigger_type') is-invalid @enderror" id="trigger_type" name="trigger_type" required>
                                <option value="">{{ __('behavioral.triggers.select_trigger_type') }}</option>
                                @foreach ($triggerOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('trigger_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('trigger_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('behavioral.triggers.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="{{ __('behavioral.triggers.describe_trigger') }}" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount_threshold">{{ __('behavioral.triggers.amount_threshold') }}</label>
                            <input type="number" class="form-control @error('amount_threshold') is-invalid @enderror" id="amount_threshold" name="amount_threshold" value="{{ old('amount_threshold') }}" placeholder="{{ __('behavioral.triggers.minimum_amount') }}" required>
                            @error('amount_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="frequency">{{ __('behavioral.triggers.initial_frequency') }}</label>
                            <input type="number" class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency" value="{{ old('frequency', 1) }}" min="1" required>
                            @error('frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('behavioral.triggers.save_trigger') }}</button>
                        <a href="{{ route('behavioral.triggers') }}" class="btn btn-secondary">{{ __('behavioral.triggers.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.triggers.help') }}</h6>
                </div>
                <div class="card-body">
                    <h6>{{ __('behavioral.triggers.what_is_trigger') }}</h6>
                    <p class="small">{{ __('behavioral.triggers.trigger_explanation') }}</p>

                    <h6>{{ __('behavioral.triggers.common_examples') }}</h6>
                    <ul class="small">
                        <li><strong>{{ __('behavioral.triggers.emotional') }}:</strong> {{ __('behavioral.insights.emotional_desc') }}</li>
                        <li><strong>{{ __('behavioral.triggers.social_pressure') }}:</strong> {{ __('behavioral.insights.social_desc') }}</li>
                        <li><strong>{{ __('behavioral.triggers.impulse_buying') }}:</strong> {{ __('behavioral.insights.impulse_desc') }}</li>
                        <li><strong>{{ __('behavioral.triggers.reward_shopping') }}:</strong> {{ __('behavioral.insights.reward_desc') }}</li>
                    </ul>

                    <h6>{{ __('behavioral.triggers.why_track') }}</h6>
                    <p class="small">{{ __('behavioral.triggers.why_track_description') }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
