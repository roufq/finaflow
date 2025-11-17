@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Setting</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Setting</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.update', $setting) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="currency_symbol">Currency Symbol</label>
                    <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $setting->currency_symbol) }}" required>
                </div>
                <div class="form-group">
                    <label for="start_month">Start Month</label>
                    <input type="number" class="form-control" id="start_month" name="start_month" min="1" max="12" value="{{ old('start_month', $setting->start_month) }}" required>
                </div>
                <div class="form-group">
                    <label for="credit_score">Credit Score (optional)</label>
                    <input type="number" class="form-control" id="credit_score" name="credit_score" min="300" max="900" value="{{ old('credit_score', $setting->credit_score) }}">
                    <small class="form-text text-muted">Masukkan credit score terbaru Anda jika tersedia (manual input, tidak terhubung ke biro kredit).</small>
                </div>
                <div class="form-group">
                    <label for="risk_profile">Risk Profile (optional)</label>
                    <select class="form-control" id="risk_profile" name="risk_profile">
                        <option value="">Pilih profil risiko</option>
                        <option value="conservative" {{ old('risk_profile', $setting->risk_profile) === 'conservative' ? 'selected' : '' }}>Conservative</option>
                        <option value="balanced" {{ old('risk_profile', $setting->risk_profile) === 'balanced' ? 'selected' : '' }}>Balanced</option>
                        <option value="aggressive" {{ old('risk_profile', $setting->risk_profile) === 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('settings.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
