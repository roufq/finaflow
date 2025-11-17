@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Integration</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bank-integrations.update', $bankIntegration) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $bankIntegration->bank_name) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $bankIntegration->account_number) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Account Type</label>
                            <select name="account_type" class="form-control" required>
                                @foreach(['checking','savings','credit_card'] as $type)
                                    <option value="{{ $type }}" @selected($bankIntegration->account_type === $type)>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Integration Type</label>
                            <select name="integration_type" class="form-control" required>
                                @foreach(['api','csv','manual'] as $type)
                                    <option value="{{ $type }}" @selected($bankIntegration->integration_type === $type)>{{ strtoupper($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $bankIntegration->notes) }}</textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $bankIntegration->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Integration active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h6>Status</h6>
                    <p class="mb-1">Last Sync: {{ $bankIntegration->last_sync_at?->diffForHumans() ?? 'Never' }}</p>
                    <p class="mb-1">Current Balance: Rp {{ number_format($bankIntegration->current_balance ?? 0, 0, ',', '.') }}</p>
                    <p>Available Balance: Rp {{ number_format($bankIntegration->available_balance ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
