@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Gift Event</h1>
        <a href="{{ route('family.gift-events') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Gift Events
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Gift Event Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('family.gift-events.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="event_name">Event Name *</label>
                            <input type="text" class="form-control @error('event_name') is-invalid @enderror"
                                   id="event_name" name="event_name" value="{{ old('event_name') }}" required>
                            @error('event_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="event_type">Event Type *</label>
                            <select class="form-control @error('event_type') is-invalid @enderror"
                                    id="event_type" name="event_type" required>
                                <option value="">Select Event Type</option>
                                <option value="Birthday" {{ old('event_type') == 'Birthday' ? 'selected' : '' }}>Birthday</option>
                                <option value="Wedding" {{ old('event_type') == 'Wedding' ? 'selected' : '' }}>Wedding</option>
                                <option value="Graduation" {{ old('event_type') == 'Graduation' ? 'selected' : '' }}>Graduation</option>
                                <option value="Holiday" {{ old('event_type') == 'Holiday' ? 'selected' : '' }}>Holiday</option>
                                <option value="Anniversary" {{ old('event_type') == 'Anniversary' ? 'selected' : '' }}>Anniversary</option>
                                <option value="Baby Shower" {{ old('event_type') == 'Baby Shower' ? 'selected' : '' }}>Baby Shower</option>
                                <option value="Retirement" {{ old('event_type') == 'Retirement' ? 'selected' : '' }}>Retirement</option>
                                <option value="Other" {{ old('event_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('event_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="event_date">Event Date *</label>
                            <input type="date" class="form-control @error('event_date') is-invalid @enderror"
                                   id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                            @error('event_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="budget_amount">Budget Amount (Rp)</label>
                            <input type="number" class="form-control @error('budget_amount') is-invalid @enderror"
                                   id="budget_amount" name="budget_amount"
                                   value="{{ old('budget_amount', 0) }}" min="0" step="1000">
                            @error('budget_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty or 0 if no budget limit</small>
                        </div>

                        <div class="form-group">
                            <label>Recipients</label>
                            <div class="border rounded p-3">
                                <p class="mb-2">Select people who will receive gifts for this event:</p>
                                @php
                                    $members = \App\Models\FamilyMember::where('user_id', auth()->id())->active()->get();
                                @endphp
                                @if($members->count() > 0)
                                    @foreach($members as $member)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="recipient_{{ $member->id }}" name="recipients[]"
                                               value="{{ $member->id }}"
                                               {{ in_array($member->id, old('recipients', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recipient_{{ $member->id }}">
                                            {{ $member->name }} ({{ $member->relationship }})
                                        </label>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No active family members available. <a href="{{ route('family.members.create') }}">Add a family member first</a>.</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Gift Event
                        </button>
                        <a href="{{ route('family.gift-events') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Gift Event Planning Tips</h6>
                </div>
                <div class="card-body">
                    <h6>Planning Your Gift Event</h6>
                    <ul class="mb-3">
                        <li>Set a realistic budget for gifts</li>
                        <li>Plan ahead for special occasions</li>
                        <li>Consider multiple recipients for group events</li>
                        <li>Track spending against your budget</li>
                    </ul>

                    <h6>Event Types</h6>
                    <p>Different events may require different gift strategies:</p>
                    <ul class="mb-3">
                        <li><strong>Birthday:</strong> Personal gifts based on interests</li>
                        <li><strong>Wedding:</strong> Consider couple's preferences and registry</li>
                        <li><strong>Graduation:</strong> Practical gifts for new beginnings</li>
                        <li><strong>Holiday:</strong> Traditional or family-oriented gifts</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Tip:</strong> Start planning gift events well in advance to find the best gifts within your budget.
                    </div>
                </div>
            </div>

            <!-- Budget Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Budget Calculator</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="calc_recipients">Number of Recipients</label>
                        <input type="number" class="form-control" id="calc_recipients" placeholder="1" min="1">
                    </div>
                    <div class="form-group">
                        <label for="calc_avg_gift">Average Gift Amount (Rp)</label>
                        <input type="number" class="form-control" id="calc_avg_gift" placeholder="100000" min="0" step="1000">
                    </div>
                    <button type="button" class="btn btn-success btn-block" onclick="calculateBudget()">
                        Calculate Total Budget
                    </button>
                    <div id="budget_result" class="mt-3 text-center font-weight-bold text-success" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function calculateBudget() {
    const recipients = parseInt(document.getElementById('calc_recipients').value) || 0;
    const avgGift = parseFloat(document.getElementById('calc_avg_gift').value) || 0;

    if (recipients > 0 && avgGift > 0) {
        const total = recipients * avgGift;
        document.getElementById('budget_result').innerHTML =
            'Total Budget: Rp ' + total.toLocaleString('id-ID', {maximumFractionDigits: 0});
        document.getElementById('budget_result').style.display = 'block';
    } else {
        alert('Please enter valid numbers for recipients and gift amount');
    }
}

// Set minimum date to today
document.getElementById('event_date').min = new Date().toISOString().split('T')[0];
</script>
@endsection
