@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Family Member</h1>
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
                    <form method="POST" action="{{ route('family.members.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="relationship">Relationship *</label>
                            <select class="form-control @error('relationship') is-invalid @enderror"
                                    id="relationship" name="relationship" required>
                                <option value="">Select Relationship</option>
                                <option value="Spouse" {{ old('relationship') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                <option value="Child" {{ old('relationship') == 'Child' ? 'selected' : '' }}>Child</option>
                                <option value="Parent" {{ old('relationship') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Sibling" {{ old('relationship') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="Grandparent" {{ old('relationship') == 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                                <option value="Grandchild" {{ old('relationship') == 'Grandchild' ? 'selected' : '' }}>Grandchild</option>
                                <option value="Other" {{ old('relationship') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                   id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="monthly_allowance">Monthly Allowance (Rp)</label>
                            <input type="number" class="form-control @error('monthly_allowance') is-invalid @enderror"
                                   id="monthly_allowance" name="monthly_allowance"
                                   value="{{ old('monthly_allowance', 0) }}" min="0" step="1000">
                            @error('monthly_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty or 0 if no monthly allowance</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active Member</label>
                            </div>
                            <small class="form-text text-muted">Uncheck if this member is no longer active</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Member
                        </button>
                        <a href="{{ route('family.members') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Information</h6>
                </div>
                <div class="card-body">
                    <h5>What is Family Member?</h5>
                    <p class="mb-3">Family members can be assigned monthly allowances and tracked for their spending within the family finance system.</p>

                    <h6>Monthly Allowance</h6>
                    <p>The monthly allowance will be automatically credited to their balance at the beginning of each month.</p>

                    <h6>Current Balance</h6>
                    <p>Tracks the current spending balance for each family member. This can be used for allowance management.</p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> All fields marked with * are required.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
