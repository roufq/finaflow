@extends('layouts.app')

@section('title', 'Edit Bank Integration')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Bank Integration</h4>
                    <p class="card-subtitle mb-0">Update your bank integration settings</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('bank-integrations.update', $bankIntegration) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">Bank Account <span class="text-danger">*</span></label>
                                    <select class="form-select @error('account_id') is-invalid @enderror" id="account_id" name="account_id" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts ?? [] as $account)
                                        <option value="{{ $account->id }}" {{ (old('account_id') ?? $bankIntegration->account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} ({{ $account->account_number ?: 'No number' }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $bankIntegration->bank_name) }}" required>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number', $bankIntegration->account_number) }}">
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_type" class="form-label">Account Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('account_type') is-invalid @enderror" id="account_type" name="account_type" required>
                                        <option value="">Select Type</option>
                                        <option value="checking" {{ (old('account_type') ?? $bankIntegration->account_type) == 'checking' ? 'selected' : '' }}>Checking Account</option>
                                        <option value="savings" {{ (old('account_type') ?? $bankIntegration->account_type) == 'savings' ? 'selected' : '' }}>Savings Account</option>
                                        <option value="credit_card" {{ (old('account_type') ?? $bankIntegration->account_type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    </select>
                                    @error('account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="integration_type" class="form-label">Integration Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('integration_type') is-invalid @enderror" id="integration_type" name="integration_type" required>
                                <option value="">Select Integration Type</option>
                                <option value="csv" {{ (old('integration_type') ?? $bankIntegration->integration_type) == 'csv' ? 'selected' : '' }}>CSV Upload</option>
                                <option value="ofx" {{ (old('integration_type') ?? $bankIntegration->integration_type) == 'ofx' ? 'selected' : '' }}>OFX Upload</option>
                                <option value="api" {{ (old('integration_type') ?? $bankIntegration->integration_type) == 'api' ? 'selected' : '' }}>API Integration</option>
                                <option value="manual" {{ (old('integration_type') ?? $bankIntegration->integration_type) == 'manual' ? 'selected' : '' }}>Manual Entry</option>
                            </select>
                            @error('integration_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="api_credentials" style="{{ (old('integration_type') ?? $bankIntegration->integration_type) === 'api' ? '' : 'display: none;' }}">
                            <label class="form-label">API Credentials</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="credentials[api_key]" value="{{ old('credentials.api_key', $bankIntegration->credentials['api_key'] ?? '') }}" placeholder="API Key">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="credentials[client_id]" value="{{ old('credentials.client_id', $bankIntegration->credentials['client_id'] ?? '') }}" placeholder="Client ID">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $bankIntegration->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $bankIntegration->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Integration
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Integration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('integration_type').addEventListener('change', function() {
    const apiCredentials = document.getElementById('api_credentials');
    if (this.value === 'api') {
        apiCredentials.style.display = 'block';
    } else {
        apiCredentials.style.display = 'none';
    }
});
</script>
@endsection
