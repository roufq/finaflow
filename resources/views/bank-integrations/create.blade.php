@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Connect Bank Account</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bank-integrations.store') }}">
                        @csrf

                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-control @error('bank_name') is-invalid @enderror" required>
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" value="{{ old('account_number') }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Account Type</label>
                            <select name="account_type" class="form-control @error('account_type') is-invalid @enderror" required>
                                <option value="checking">Checking</option>
                                <option value="savings">Savings</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Integration Type</label>
                            <select name="integration_type" class="form-control @error('integration_type') is-invalid @enderror" required>
                                <option value="api">API (instant)</option>
                                <option value="csv">CSV Upload</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Why Connect?</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Automatic transaction import</li>
                        <li>Real-time balance syncing</li>
                        <li>Duplicate detection and AI categorization</li>
                        <li>CSV upload for bank statements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
