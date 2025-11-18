@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Family Goal</h1>
        <a href="{{ route('family.goals') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Goals
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Goal Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('family.goals.update', $goal) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="goal_name">Goal Name *</label>
                            <input type="text" class="form-control @error('goal_name') is-invalid @enderror"
                                   id="goal_name" name="goal_name"
                                   value="{{ old('goal_name', $goal->goal_name) }}" required>
                            @error('goal_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="goal_type">Goal Type *</label>
                            <select class="form-control @error('goal_type') is-invalid @enderror"
                                    id="goal_type" name="goal_type" required>
                                @php
                                    $types = ['Vacation','Education','Emergency Fund','Home Purchase','Vehicle','Wedding','Retirement','Investment','Other'];
                                    $selectedType = old('goal_type', $goal->goal_type);
                                @endphp
                                <option value="">Select Goal Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('goal_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="target_amount">Target Amount (Rp) *</label>
                                <input type="number" class="form-control @error('target_amount') is-invalid @enderror"
                                       id="target_amount" name="target_amount" min="0" step="1000"
                                       value="{{ old('target_amount', $goal->target_amount) }}" required>
                                @error('target_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="current_amount">Current Amount (Rp)</label>
                                <input type="number" class="form-control @error('current_amount') is-invalid @enderror"
                                       id="current_amount" name="current_amount" min="0" step="1000"
                                       value="{{ old('current_amount', $goal->current_amount) }}">
                                @error('current_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Track the progress collected so far.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="target_date">Target Date *</label>
                            <input type="date" class="form-control @error('target_date') is-invalid @enderror"
                                   id="target_date" name="target_date"
                                   value="{{ old('target_date', optional($goal->target_date)->format('Y-m-d')) }}" required>
                            @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $goal->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Contributors</label>
                            <p class="mb-2">Select family members contributing to this goal:</p>
                            @if($members->count() > 0)
                                @php($selectedContributors = old('contributors', $goal->contributors ?? []))
                                <div class="row">
                                    @foreach($members as $member)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   id="contributor_{{ $member->id }}" name="contributors[]"
                                                   value="{{ $member->id }}"
                                                   {{ in_array($member->id, $selectedContributors ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="contributor_{{ $member->id }}">
                                                {{ $member->name }} ({{ $member->relationship }})
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No active family members available.</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_achieved" name="is_achieved"
                                       value="1" {{ old('is_achieved', $goal->is_achieved) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_achieved">Mark goal as achieved</label>
                            </div>
                            <small class="form-text text-muted">Marking as achieved locks progress and records completion date.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Update Goal</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Progress</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-1">Progress towards target</p>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-info" role="progressbar"
                            style="width: {{ $goal->progress_percentage }}%"
                            aria-valuenow="{{ $goal->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ round($goal->progress_percentage, 0) }}%
                        </div>
                    </div>
                    <p class="mb-1">
                        Rp {{ number_format($goal->current_amount, 0, ',', '.') }}
                        <span class="text-muted">of</span>
                        Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                    </p>
                    <p class="mb-0">
                        <strong>{{ $goal->days_remaining >= 0 ? $goal->days_remaining . ' days' : 'Past due' }}</strong>
                        <span class="text-muted">remaining</span>
                    </p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Quick Calculator</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="calc_amount">Target Amount (Rp)</label>
                        <input type="number" class="form-control" id="calc_amount" placeholder="10000000">
                    </div>
                    <div class="form-group">
                        <label for="calc_months">Time Period (Months)</label>
                        <input type="number" class="form-control" id="calc_months" placeholder="12">
                    </div>
                    <button type="button" class="btn btn-success btn-block" onclick="calculateMonthly()">
                        Calculate Monthly Needed
                    </button>
                    <div id="calc_result" class="mt-3 text-center font-weight-bold text-success" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateMonthly() {
    const amount = parseFloat(document.getElementById('calc_amount').value);
    const months = parseFloat(document.getElementById('calc_months').value);

    if (amount && months && months > 0) {
        const monthly = amount / months;
        document.getElementById('calc_result').innerHTML =
            'Monthly Contribution: Rp ' + monthly.toLocaleString('id-ID', {maximumFractionDigits: 0});
        document.getElementById('calc_result').style.display = 'block';
    } else {
        alert('Please enter valid amount and months');
    }
}

const targetInput = document.getElementById('target_date');
if (targetInput) {
    const minDate = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    targetInput.min = minDate;
}
</script>
@endsection
