@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Debt</h1>
        <a href="{{ route('debts.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Hutang</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('debts.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Nama Hutang *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="type">Tipe Hutang *</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                                    <option value="personal_loan" {{ old('type') == 'personal_loan' ? 'selected' : '' }}>Pinjaman Pribadi</option>
                                    <option value="student_loan" {{ old('type') == 'student_loan' ? 'selected' : '' }}>Pinjaman Pendidikan</option>
                                    <option value="mortgage" {{ old('type') == 'mortgage' ? 'selected' : '' }}>KPR</option>
                                    <option value="car_loan" {{ old('type') == 'car_loan' ? 'selected' : '' }}>Pinjaman Mobil</option>
                                    <option value="business_loan" {{ old('type') == 'business_loan' ? 'selected' : '' }}>Pinjaman Bisnis</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="lender">Pemberi Pinjaman *</label>
                                <input type="text" class="form-control @error('lender') is-invalid @enderror" id="lender" name="lender" value="{{ old('lender') }}" required>
                                @error('lender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="original_amount">Jumlah Asli (Rp) *</label>
                                <input type="number" class="form-control @error('original_amount') is-invalid @enderror" id="original_amount" name="original_amount" value="{{ old('original_amount') }}" min="0" required>
                                @error('original_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="current_balance">Saldo Saat Ini (Rp) *</label>
                                <input type="number" class="form-control @error('current_balance') is-invalid @enderror" id="current_balance" name="current_balance" value="{{ old('current_balance') }}" min="0" required>
                                @error('current_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="interest_rate">Suku Bunga (%) *</label>
                                <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" id="interest_rate" name="interest_rate" value="{{ old('interest_rate') }}" min="0" max="100" step="0.01" required>
                                @error('interest_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="minimum_payment">Pembayaran Minimum (Rp) *</label>
                                <input type="number" class="form-control @error('minimum_payment') is-invalid @enderror" id="minimum_payment" name="minimum_payment" value="{{ old('minimum_payment') }}" min="0" required>
                                @error('minimum_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="due_date">Tanggal Jatuh Tempo *</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payoff_strategy">Strategi Pelunasan *</label>
                            <select class="form-control @error('payoff_strategy') is-invalid @enderror" id="payoff_strategy" name="payoff_strategy" required>
                                <option value="">Pilih Strategi</option>
                                <option value="snowball" {{ old('payoff_strategy') == 'snowball' ? 'selected' : '' }}>Snowball (Hutang Terkecil Dulu)</option>
                                <option value="avalanche" {{ old('payoff_strategy') == 'avalanche' ? 'selected' : '' }}>Avalanche (Bunga Tertinggi Dulu)</option>
                                <option value="custom" {{ old('payoff_strategy') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('payoff_strategy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Debt</button>
                            <a href="{{ route('debts.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
