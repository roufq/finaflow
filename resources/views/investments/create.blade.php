@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Investment</h1>
        <a href="{{ route('investments.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Investments
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Investment Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('investments.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <tags for="symbol">Symbol *</tags>
                            <input type="text" class="form-control @error('symbol') is-invalid @enderror"
                                   id="symbol" name="symbol" value="{{ old('symbol') }}" required maxlength="10">
                            @error('symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Stock ticker, crypto symbol, etc.</small>
                        </div>

                        <div class="form-group">
                            <tags for="name">Name *</tags>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Full name of the investment</small>
                        </div>

                        <div class="form-group">
                            <tags for="type">Type *</tags>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="stock" {{ old('type') == 'stock' ? 'selected' : '' }}>Stock</option>
                                <option value="mutual_fund" {{ old('type') == 'mutual_fund' ? 'selected' : '' }}>Mutual Fund</option>
                                <option value="crypto" {{ old('type') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                                <option value="bond" {{ old('type') == 'bond' ? 'selected' : '' }}>Bond</option>
                                <option value="etf" {{ old('type') == 'etf' ? 'selected' : '' }}>ETF</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="quantity">Quantity *</tags>
                                    <input type="number" step="0.0001" class="form-control @error('quantity') is-invalid @enderror"
                                           id="quantity" name="quantity" value="{{ old('quantity') }}" required min="0">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="purchase_price">Purchase Price per Unit *</tags>
                                    <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror"
                                           id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required min="0">
                                    @error('purchase_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="current_price">Current Price per Unit</tags>
                                    <input type="number" step="0.01" class="form-control @error('current_price') is-invalid @enderror"
                                           id="current_price" name="current_price" value="{{ old('current_price') }}" min="0">
                                    @error('current_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty if unknown</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="purchase_date">Purchase Date *</tags>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                                           id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="dividends_received">Dividends Received</tags>
                                    <input type="number" step="0.01" class="form-control @error('dividends_received') is-invalid @enderror"
                                           id="dividends_received" name="dividends_received" value="{{ old('dividends_received', 0) }}" min="0">
                                    @error('dividends_received')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="fees">Transaction Fees</tags>
                                    <input type="number" step="0.01" class="form-control @error('fees') is-invalid @enderror"
                                           id="fees" name="fees" value="{{ old('fees', 0) }}" min="0">
                                    @error('fees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <tags for="notes">Notes</tags>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Save Investment</button>
                        <a href="{{ route('investments.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
