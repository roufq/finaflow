@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Family Member</h1>
        <a href="{{ route('family.members') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Members
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Family Member Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('family.members.update', $member) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $member->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="relationship">Relationship *</label>
                            <select class="form-control @error('relationship') is-invalid @enderror"
                                    id="relationship" name="relationship" required>
                                @php
                                    $relationships = ['Spouse', 'Child', 'Parent', 'Sibling', 'Grandparent', 'Grandchild', 'Other'];
                                @endphp
                                <option value="">Select Relationship</option>
                                @foreach($relationships as $relationship)
                                    <option value="{{ $relationship }}" {{ old('relationship', $member->relationship) === $relationship ? 'selected' : '' }}>
                                        {{ $relationship }}
                                    </option>
                                @endforeach
                            </select>
                            @error('relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                   id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($member->date_of_birth)->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="monthly_allowance">Monthly Allowance (Rp)</label>
                            <input type="number" class="form-control @error('monthly_allowance') is-invalid @enderror"
                                   id="monthly_allowance" name="monthly_allowance"
                                   value="{{ old('monthly_allowance', $member->monthly_allowance ?? 0) }}" min="0" step="1000">
                            @error('monthly_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty or 0 if no monthly allowance</small>
                        </div>

                        <div class="form-group">
                            <label for="current_balance">Current Balance (Rp)</label>
                            <input type="number" class="form-control @error('current_balance') is-invalid @enderror"
                                   id="current_balance" name="current_balance"
                                   value="{{ old('current_balance', $member->current_balance ?? 0) }}" min="0" step="1000">
                            @error('current_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $member->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active Member</label>
                            </div>
                            <small class="form-text text-muted">Uncheck if this member is no longer active</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Member
                        </button>
                        <a href="{{ route('family.members') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Member Snapshot</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $member->name }}</h5>
                    <p class="mb-3 text-muted">{{ $member->relationship }}</p>

                    <ul class="list-unstyled mb-4">
                        <li><strong>Allowance:</strong> Rp {{ number_format($member->monthly_allowance ?? 0, 0, ',', '.') }}</li>
                        <li><strong>Balance:</strong> Rp {{ number_format($member->current_balance ?? 0, 0, ',', '.') }}</li>
                        <li><strong>Status:</strong>
                            @if($member->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Update this member's profile to keep family insights accurate.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
