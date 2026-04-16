@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Gift Event</h1>
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
                    <form method="POST" action="{{ route('family.gift-events.update', $event) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="event_name">Event Name *</label>
                            <input type="text" class="form-control @error('event_name') is-invalid @enderror"
                                   id="event_name" name="event_name" value="{{ old('event_name', $event->event_name) }}" required>
                            @error('event_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="event_type">Event Type *</label>
                            <input type="text" class="form-control @error('event_type') is-invalid @enderror"
                                   id="event_type" name="event_type" value="{{ old('event_type', $event->event_type) }}" required>
                            @error('event_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="event_date">Event Date *</label>
                            <input type="date" class="form-control @error('event_date') is-invalid @enderror"
                                   id="event_date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                            @error('event_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="budget_amount">Budget Amount (Rp)</label>
                            <input type="number" class="form-control @error('budget_amount') is-invalid @enderror"
                                   id="budget_amount" name="budget_amount"
                                   value="{{ old('budget_amount', $event->budget_amount) }}" min="0" step="1000">
                            @error('budget_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty or 0 if no budget limit</small>
                        </div>

                        <div class="form-group">
                            <label>Recipients</label>
                            <div class="border rounded p-3">
                                <p class="mb-2">Select people who will receive gifts for this event:</p>
                                @if($members->count() > 0)
                                    @foreach($members as $member)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="recipient_{{ $member->id }}" name="recipients[]"
                                               value="{{ $member->id }}"
                                               {{ in_array($member->id, old('recipients', $event->recipients ?? [])) ? 'checked' : '' }}>
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
                                      id="notes" name="notes" rows="3">{{ old('notes', $event->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Gift Event
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
                    <h6>Planning Reminders</h6>
                    <ul class="mb-3">
                        <li>Review recipients after each update</li>
                        <li>Track spending vs. allocated budget</li>
                        <li>Document notes for future reference</li>
                        <li>Close events when all gifts are delivered</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Keep your events updated to reflect current plans.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
