@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $investment->name }} ({{ $investment->symbol }})</h1>
        <div>
            <a href="{{ route('investments.edit', $investment) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="{{ route('investments.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Investments
            </a>
        </div>
    </div>

    <!-- Investment Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Current Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($investment->current_value, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Gain/Loss</div>
                            <div class="h5 mb-0 font-weight-bold {{ $investment->unrealized_gain_loss >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($investment->unrealized_gain_loss, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                ROI</div>
                            <div class="h5 mb-0 font-weight-bold {{ $investment->roi_percentage >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($investment->roi_percentage, 2) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Return</div>
                            <div class="h5 mb-0 font-weight-bold {{ $investment->total_return >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($investment->total_return, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Investment Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Symbol:</strong> {{ $investment->symbol }}</p>
                            <p><strong>Name:</strong> {{ $investment->name }}</p>
                            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $investment->type)) }}</p>
                            <p><strong>Quantity:</strong> {{ number_format($investment->quantity, 4) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Purchase Price:</strong> Rp {{ number_format($investment->purchase_price, 2, ',', '.') }}</p>
                            <p><strong>Current Price:</strong> Rp {{ number_format($investment->current_price, 2, ',', '.') }}</p>
                            <p><strong>Purchase Date:</strong> {{ $investment->purchase_date->format('d M Y') }}</p>
                            <p><strong>Total Purchase Value:</strong> Rp {{ number_format($investment->total_purchase_value, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($investment->dividends_received > 0)
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Dividends Received:</strong> Rp {{ number_format($investment->dividends_received, 2, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Transaction Fees:</strong> Rp {{ number_format($investment->fees, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($investment->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Notes:</strong></p>
                            <p>{{ $investment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
