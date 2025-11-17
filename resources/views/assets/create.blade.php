@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Asset</h1>
        <a href="{{ route('assets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assets
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('assets.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Asset Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="type">Asset Type *</label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="real_estate" {{ old('type') == 'real_estate' ? 'selected' : '' }}>Real Estate</option>
                                <option value="vehicle" {{ old('type') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                                <option value="equipment" {{ old('type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="furniture" {{ old('type') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                <option value="jewelry" {{ old('type') == 'jewelry' ? 'selected' : '' }}>Jewelry</option>
                                <option value="artwork" {{ old('type') == 'artwork' ? 'selected' : '' }}>Artwork</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_value">Purchase Value *</label>
                                    <input type="number" step="0.01" class="form-control @error('purchase_value') is-invalid @enderror"
                                           id="purchase_value" name="purchase_value" value="{{ old('purchase_value') }}" required min="0">
                                    @error('purchase_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="current_value">Current Value</label>
                                    <input type="number" step="0.01" class="form-control @error('current_value') is-invalid @enderror"
                                           id="current_value" name="current_value" value="{{ old('current_value') }}" min="0">
                                    @error('current_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty if same as purchase value</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date *</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                                           id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="depreciation_rate">Depreciation Rate (% per year)</label>
                                    <input type="number" step="0.01" class="form-control @error('depreciation_rate') is-invalid @enderror"
                                           id="depreciation_rate" name="depreciation_rate" value="{{ old('depreciation_rate', 0) }}" min="0" max="100">
                                    @error('depreciation_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Annual depreciation percentage</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monthly_income">Monthly Income</label>
                                    <input type="number" step="0.01" class="form-control @error('monthly_income') is-invalid @enderror"
                                           id="monthly_income" name="monthly_income" value="{{ old('monthly_income', 0) }}" min="0">
                                    @error('monthly_income')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Rental income, etc.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="insurance_expiry">Insurance Expiry Date</label>
                                    <input type="date" class="form-control @error('insurance_expiry') is-invalid @enderror"
                                           id="insurance_expiry" name="insurance_expiry" value="{{ old('insurance_expiry') }}">
                                    @error('insurance_expiry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                           id="location" name="location" value="{{ old('location') }}" maxlength="255">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="serial_number">Serial Number</label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror"
                                           id="serial_number" name="serial_number" value="{{ old('serial_number') }}" maxlength="255">
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Save Asset</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
