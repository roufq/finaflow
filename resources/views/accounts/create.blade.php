@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Account</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Add Account</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('accounts.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Name Account *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">Type Account *</label>
                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Select Type Account</option>
                        @foreach($accountTypes as $key => $label)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="setting_id">Profile Mata Uang *</label>
                    <select class="form-control @error('setting_id') is-invalid @enderror" id="setting_id" name="setting_id" required>
                        <option value="">Select Profile Mata Uang</option>
                        @foreach($settings as $setting)
                        <option value="{{ $setting->id }}" data-symbol="{{ $setting->currency_symbol }}" {{ old('setting_id') == $setting->id ? 'selected' : '' }}>
                            {{ $setting->tags }} ({{ $setting->currency_symbol }})
                        </option>
                        @endforeach
                    </select>
                    @error('setting_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_number">Nomor Account</label>
                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number') }}">
                    @error('account_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bank_name">Name Bank</label>
                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                    @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="balance">Balance Awal *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text currency-symbol">Rp</span>
                        </div>
                        <input type="number" step="0.01" class="form-control @error('balance') is-invalid @enderror" id="balance" name="balance" value="{{ old('balance', 0) }}" required>
                    </div>
                    @error('balance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" id="credit_limit_group" style="display: none;">
                    <label for="credit_limit">Limit Kredit</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text currency-symbol">Rp</span>
                        </div>
                        <input type="number" step="0.01" class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit" name="credit_limit" value="{{ old('credit_limit') }}">
                    </div>
                    @error('credit_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="opening_date">Date Pembukaan</label>
                    <input type="date" class="form-control @error('opening_date') is-invalid @enderror" id="opening_date" name="opening_date" value="{{ old('opening_date') }}">
                    @error('opening_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Account Aktif</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Account</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const creditLimitGroup = document.getElementById('credit_limit_group');
    if (this.value === 'credit_card') {
        creditLimitGroup.style.display = 'block';
        document.getElementById('credit_limit').required = true;
    } else {
        creditLimitGroup.style.display = 'none';
        document.getElementById('credit_limit').required = false;
    }
});

document.getElementById('setting_id').addEventListener('change', function() {
    const symbol = this.options[this.selectedIndex].dataset.symbol || 'Rp';
    document.querySelectorAll('.currency-symbol').forEach(el => {
        el.innerText = symbol;
    });
});

// Trigger change event on page load to handle pre-selected values
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('type').dispatchEvent(new Event('change'));
    document.getElementById('setting_id').dispatchEvent(new Event('change'));
});
</script>
@endsection
