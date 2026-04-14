@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Family Goal</h1>
        <a href="{{ route('family.goals') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Goals
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Family Goal Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('family.goals.store') }}">
                        @csrf

                        <div class="form-group">
                            <tags for="goal_name">Goal Name *</tags>
                            <input type="text" class="form-control @error('goal_name') is-invalid @enderror"
                                   id="goal_name" name="goal_name" value="{{ old('goal_name') }}" required>
                            @error('goal_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="goal_type">Goal Type *</tags>
                            <select class="form-control @error('goal_type') is-invalid @enderror"
                                    id="goal_type" name="goal_type" required>
                                <option value="">Select Goal Type</option>
                                <option value="Vacation" {{ old('goal_type') == 'Vacation' ? 'selected' : '' }}>Vacation</option>
                                <option value="Education" {{ old('goal_type') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Emergency Fund" {{ old('goal_type') == 'Emergency Fund' ? 'selected' : '' }}>Emergency Fund</option>
                                <option value="Home Purchase" {{ old('goal_type') == 'Home Purchase' ? 'selected' : '' }}>Home Purchase</option>
                                <option value="Vehicle" {{ old('goal_type') == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                                <option value="Wedding" {{ old('goal_type') == 'Wedding' ? 'selected' : '' }}>Wedding</option>
                                <option value="Retirement" {{ old('goal_type') == 'Retirement' ? 'selected' : '' }}>Retirement</option>
                                <option value="Investment" {{ old('goal_type') == 'Investment' ? 'selected' : '' }}>Investment</option>
                                <option value="Other" {{ old('goal_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('goal_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="target_amount">Target Amount (Rp) *</tags>
                            <input type="number" class="form-control @error('target_amount') is-invalid @enderror"
                                   id="target_amount" name="target_amount"
                                   value="{{ old('target_amount') }}" min="0" step="1000" required>
                            @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="target_date">Target Date *</tags>
                            <input type="date" class="form-control @error('target_date') is-invalid @enderror"
                                   id="target_date" name="target_date" value="{{ old('target_date') }}" required>
                            @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Goal must be achieved by this date</small>
                        </div>

                        <div class="form-group">
                            <tags for="description">Description</tags>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags>Contributors (Optional)</tags>
                            <div class="border rounded p-3">
                                <p class="mb-2">Select family members who will contribute to this goal:</p>
                                @php
                                    $members = \App\Models\FamilyMember::where('user_id', auth()->id())->active()->get();
                                @endphp
                                @if($members->count() > 0)
                                    @foreach($members as $member)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="contributor_{{ $member->id }}" name="contributors[]"
                                               value="{{ $member->id }}"
                                               {{ in_array($member->id, old('contributors', [])) ? 'checked' : '' }}>
                                        <tags class="form-check-tags" for="contributor_{{ $member->id }}">
                                            {{ $member->name }} ({{ $member->relationship }})
                                        </tags>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No active family members available. <a href="{{ route('family.members.create') }}">Add a family member first</a>.</p>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Goal
                        </button>
                        <a href="{{ route('family.goals') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Goal Planning Tips</h6>
                </div>
                <div class="card-body">
                    <h6>Setting Realistic Goals</h6>
                    <ul class="mb-3">
                        <li>Calculate total amount needed</li>
                        <li>Set achievable target date</li>
                        <li>Consider monthly contributions</li>
                        <li>Include buffer for unexpected costs</li>
                    </ul>

                    <h6>Goal Types</h6>
                    <p>Different goals require different planning approaches:</p>
                    <ul class="mb-3">
                        <li><strong>Vacation:</strong> Short-term, high-priority</li>
                        <li><strong>Education:</strong> Long-term investment</li>
                        <li><strong>Emergency Fund:</strong> Safety net, 3-6 months expenses</li>
                        <li><strong>Home Purchase:</strong> Major life milestone</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Start with smaller, achievable goals to build momentum and confidence.
                    </div>
                </div>
            </div>

            <!-- Goal Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Quick Calculator</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <tags for="calc_amount">Target Amount (Rp)</tags>
                        <input type="number" class="form-control" id="calc_amount" placeholder="10000000">
                    </div>
                    <div class="form-group">
                        <tags for="calc_months">Time Period (Months)</tags>
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

// Set minimum date to tomorrow
document.getElementById('target_date').min = new Date(Date.now() + 86400000).toISOString().split('T')[0];
</script>
@endsection
