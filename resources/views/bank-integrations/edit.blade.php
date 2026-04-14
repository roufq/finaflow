@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Integrasi Bank</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Integrasi Bank</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('bank-integrations.update', $bankIntegration) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="account_id">Pilih Akun <span class="text-danger">*</span></label>
                    <select name="account_id" id="account_id" class="form-control @error('account_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach($accounts as $account)
                            <option
                                value="{{ $account->id }}"
                                data-bank-name="{{ $account->bank_name }}"
                                data-account-number="{{ $account->account_number }}"
                                {{ old('account_id', $bankIntegration->account_id) == $account->id ? 'selected' : '' }}
                            >
                                {{ $account->name }} ({{ $account->account_number ?? 'No Account Number' }})
                            </option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                           value="{{ old('bank_name', $bankIntegration->bank_name) }}" placeholder="Contoh: BCA, Mandiri, BRI" required>
                    @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_number">Nomor Rekening</label>
                    <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror"
                           value="{{ old('account_number', $bankIntegration->account_number) }}" placeholder="Contoh: 1234567890">
                    @error('account_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_type">Tipe Akun <span class="text-danger">*</span></label>
                    <select name="account_type" id="account_type" class="form-control @error('account_type') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe Akun --</option>
                        <option value="checking" {{ old('account_type', $bankIntegration->account_type) == 'checking' ? 'selected' : '' }}>Checking/Giro</option>
                        <option value="savings" {{ old('account_type', $bankIntegration->account_type) == 'savings' ? 'selected' : '' }}>Savings/Tabungan</option>
                        <option value="credit_card" {{ old('account_type', $bankIntegration->account_type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                    </select>
                    @error('account_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="integration_type">Metode Integrasi <span class="text-danger">*</span></label>
                    <select name="integration_type" id="integration_type" class="form-control @error('integration_type') is-invalid @enderror" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="manual" {{ old('integration_type', $bankIntegration->integration_type) == 'manual' ? 'selected' : '' }}>Manual Entry</option>
                        <option value="csv" {{ old('integration_type', $bankIntegration->integration_type) == 'csv' ? 'selected' : '' }}>Upload CSV</option>
                        <option value="ofx" {{ old('integration_type', $bankIntegration->integration_type) == 'ofx' ? 'selected' : '' }}>Upload OFX</option>
                        <option value="api" {{ old('integration_type', $bankIntegration->integration_type) == 'api' ? 'selected' : '' }}>Bank API</option>
                    </select>
                    @error('integration_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- API Settings (shown for API) -->
                <div id="api-settings" class="form-group" style="display: none;">
                    <label>API Settings</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="credentials[api_key]" class="form-control" placeholder="API Key" value="{{ old('credentials.api_key', $bankIntegration->credentials['api_key'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="credentials[client_id]" class="form-control" placeholder="Client ID" value="{{ old('credentials.client_id', $bankIntegration->credentials['client_id'] ?? '') }}">
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Masukkan kredensial API dari bank Anda untuk integrasi otomatis.
                    </small>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"
                              placeholder="Catatan tambahan tentang integrasi ini">{{ old('notes', $bankIntegration->notes) }}</textarea>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $bankIntegration->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Aktifkan integrasi ini</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Integrasi</button>
                <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const integrationTypeSelect = document.getElementById('integration_type');
    const apiSettings = document.getElementById('api-settings');
    const accountSelect = document.getElementById('account_id');
    const bankNameInput = document.getElementById('bank_name');
    const accountNumberInput = document.getElementById('account_number');

    function toggleApiSettings() {
        apiSettings.style.display = integrationTypeSelect.value === 'api' ? 'block' : 'none';
    }

    function autofillAccountDetails(force = false) {
        const selectedOption = accountSelect.options[accountSelect.selectedIndex];
        if (!selectedOption) {
            return;
        }

        const bankName = selectedOption.dataset.bankName || '';
        const accountNumber = selectedOption.dataset.accountNumber || '';

        if (force || !bankNameInput.value) {
            bankNameInput.value = bankName;
        }
        if (force || !accountNumberInput.value) {
            accountNumberInput.value = accountNumber;
        }
    }

    integrationTypeSelect.addEventListener('change', toggleApiSettings);
    accountSelect.addEventListener('change', function() {
        autofillAccountDetails(true);
    });

    toggleApiSettings();
    autofillAccountDetails();
});
</script>
@endsection
