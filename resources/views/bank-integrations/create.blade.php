@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Buat Integrasi Bank Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('bank-integrations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Account Selection -->
                        <div class="mb-3">
                            <label for="account_id" class="form-label">Pilih Akun <span class="text-danger">*</span></label>
                            <select name="account_id" id="account_id" class="form-select @error('account_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->account_number ?? 'No Account Number' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bank Name -->
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                                   value="{{ old('bank_name') }}" placeholder="Contoh: BCA, Mandiri, BRI" required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Number -->
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Nomor Rekening</label>
                            <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror"
                                   value="{{ old('account_number') }}" placeholder="Contoh: 1234567890">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Type -->
                        <div class="mb-3">
                            <label for="account_type" class="form-label">Tipe Akun <span class="text-danger">*</span></label>
                            <select name="account_type" id="account_type" class="form-select @error('account_type') is-invalid @enderror" required>
                                <option value="">-- Pilih Tipe Akun --</option>
                                <option value="checking" {{ old('account_type') == 'checking' ? 'selected' : '' }}>Checking/Giro</option>
                                <option value="savings" {{ old('account_type') == 'savings' ? 'selected' : '' }}>Savings/Tabungan</option>
                                <option value="credit_card" {{ old('account_type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            </select>
                            @error('account_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Integration Type -->
                        <div class="mb-3">
                            <label for="integration_type" class="form-label">Metode Integrasi <span class="text-danger">*</span></label>
                            <select name="integration_type" id="integration_type" class="form-select @error('integration_type') is-invalid @enderror" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="manual" {{ old('integration_type') == 'manual' ? 'selected' : '' }}>Manual Entry</option>
                                <option value="csv" {{ old('integration_type') == 'csv' ? 'selected' : '' }}>Upload CSV</option>
                                <option value="ofx" {{ old('integration_type') == 'ofx' ? 'selected' : '' }}>Upload OFX</option>
                                <option value="api" {{ old('integration_type') == 'api' ? 'selected' : '' }}>Bank API</option>
                            </select>
                            @error('integration_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Section (shown for CSV/OFX) -->
                        <div id="file-upload-section" class="mb-3" style="display: none;">
                            <label class="form-label">Upload File</label>

                            <!-- CSV Upload -->
                            <div id="csv-upload" style="display: none;">
                                <input type="file" name="csv_file" id="csv_file" class="form-control @error('csv_file') is-invalid @enderror"
                                       accept=".csv,.txt">
                                <div class="form-text">
                                    Upload file CSV dari bank Anda. Format yang didukung: .csv, .txt
                                </div>
                                @error('csv_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- OFX Upload -->
                            <div id="ofx-upload" style="display: none;">
                                <input type="file" name="ofx_file" id="ofx_file" class="form-control @error('ofx_file') is-invalid @enderror"
                                       accept=".ofx,.qfx">
                                <div class="form-text">
                                    Upload file OFX/QFX dari bank Anda. Format yang didukung: .ofx, .qfx
                                </div>
                                @error('ofx_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- API Settings (shown for API) -->
                        <div id="api-settings" class="mb-3" style="display: none;">
                            <label class="form-label">Pengaturan API</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="credentials[api_key]" class="form-control" placeholder="API Key">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="credentials[client_id]" class="form-control" placeholder="Client ID">
                                </div>
                            </div>
                            <div class="form-text">
                                Masukkan kredensial API dari bank Anda untuk integrasi otomatis.
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                      placeholder="Catatan tambahan tentang integrasi ini">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">
                                Aktifkan integrasi ini
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Integrasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const integrationTypeSelect = document.getElementById('integration_type');
    const fileUploadSection = document.getElementById('file-upload-section');
    const csvUpload = document.getElementById('csv-upload');
    const ofxUpload = document.getElementById('ofx-upload');
    const apiSettings = document.getElementById('api-settings');

    function toggleUploadFields() {
        const selectedType = integrationTypeSelect.value;

        // Hide all sections first
        fileUploadSection.style.display = 'none';
        csvUpload.style.display = 'none';
        ofxUpload.style.display = 'none';
        apiSettings.style.display = 'none';

        // Show relevant section
        if (selectedType === 'csv') {
            fileUploadSection.style.display = 'block';
            csvUpload.style.display = 'block';
        } else if (selectedType === 'ofx') {
            fileUploadSection.style.display = 'block';
            ofxUpload.style.display = 'block';
        } else if (selectedType === 'api') {
            apiSettings.style.display = 'block';
        }
    }

    integrationTypeSelect.addEventListener('change', toggleUploadFields);

    // Initialize on page load
    toggleUploadFields();
});
</script>
@endsection
