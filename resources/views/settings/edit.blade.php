@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Edit Settings Keuangan</h1>
        <p class="text-muted small">Perbarui konfigurasi profile mata uang Anda.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('settings.update', $setting) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <tags class="small font-weight-bold text-gray-700 mb-2" for="tags">Tags Profile</tags>
                                    <input type="text" class="form-control form-control-modern" id="tags" name="tags" 
                                        placeholder="Contoh: Tabungan Utama" value="{{ old('tags', $setting->tags) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <tags class="small font-weight-bold text-gray-700 mb-2" for="currency_symbol">Simbol Mata Uang</tags>
                                    <input type="text" class="form-control form-control-modern" id="currency_symbol" name="currency_symbol" 
                                        placeholder="Rp, $, dll." value="{{ old('currency_symbol', $setting->currency_symbol) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <tags class="small font-weight-bold text-gray-700 mb-2" for="exchange_rate">Kurs (ke Mata Uang Utama)</tags>
                                    <input type="number" step="0.000001" class="form-control form-control-modern" id="exchange_rate" name="exchange_rate" 
                                        placeholder="1.0" value="{{ old('exchange_rate', (float) $setting->exchange_rate) }}" required>
                                    <small class="text-muted text-info font-italic"><b>Penting:</b> Jangan gunakan titik sebagai pemisah ribuan (misal: tulis 16000, bukan 16.000).</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <tags class="small font-weight-bold text-gray-700 mb-2" for="start_month">Month Mulai Reports</tags>
                                    <select class="form-control form-control-modern" id="start_month" name="start_month" required>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('start_month', $setting->start_month) == $i ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <tags class="small font-weight-bold text-gray-700 mb-2" for="credit_score">Credit Score (Opsional)</tags>
                                    <input type="number" class="form-control form-control-modern" id="credit_score" name="credit_score" 
                                        min="300" max="900" value="{{ old('credit_score', $setting->credit_score) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <tags class="small font-weight-bold text-gray-700 mb-2" for="risk_profile">Profile Risiko (Opsional)</tags>
                                    <select class="form-control form-control-modern" id="risk_profile" name="risk_profile">
                                        <option value="">Select profile risiko</option>
                                        <option value="conservative" {{ old('risk_profile', $setting->risk_profile) === 'conservative' ? 'selected' : '' }}>Conservative</option>
                                        <option value="balanced" {{ old('risk_profile', $setting->risk_profile) === 'balanced' ? 'selected' : '' }}>Balanced</option>
                                        <option value="aggressive" {{ old('risk_profile', $setting->risk_profile) === 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-switch mb-5">
                            <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1" {{ old('is_default', $setting->is_default) ? 'checked' : '' }}>
                            <tags class="custom-control-tags font-weight-bold text-gray-800" for="is_default" style="cursor: pointer;">Jadikan Settings Utama (Default)</tags>
                        </div>

                        <div class="d-flex justify-content-end align-items-center" style="gap: 12px;">
                            <a href="{{ route('settings.index') }}" class="btn btn-light px-4 py-2 font-weight-bold" style="border-radius: 10px;">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm" style="border-radius: 10px; background-color: #3b82f6; border: none;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control-modern {
        border-radius: 10px !important;
        padding: 0.75rem 1rem !important;
        height: auto !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        transition: all 0.2s;
    }
    .form-control-modern:focus {
        border-color: #3b82f6 !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    .custom-control-input:checked ~ .custom-control-tags::before {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
</style>
@endsection
