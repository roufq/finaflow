@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $asset->name }}</h1>
        <div>
            <a href="{{ route('assets.edit', $asset) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="{{ route('assets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Assets
            </a>
        </div>
    </div>

    <!-- Asset Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Current Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</div>
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
                                Depreciated Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($asset->depreciated_value, 0, ',', '.') }}</div>
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
                                Annual Income</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($asset->annual_income, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                                Age</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $asset->age_in_years }} years</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Asset Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $asset->name }}</p>
                            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $asset->type)) }}</p>
                            <p><strong>Purchase Value:</strong> Rp {{ number_format($asset->purchase_value, 0, ',', '.') }}</p>
                            <p><strong>Current Value:</strong> Rp {{ number_format($asset->current_value, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Purchase Date:</strong> {{ $asset->purchase_date->format('d M Y') }}</p>
                            <p><strong>Depreciation Rate:</strong> {{ $asset->depreciation_rate }}% per year</p>
                            <p><strong>Total Depreciation:</strong> Rp {{ number_format($asset->total_depreciation, 0, ',', '.') }}</p>
                            <p><strong>Monthly Income:</strong> Rp {{ number_format($asset->monthly_income, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($asset->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Description:</strong></p>
                            <p>{{ $asset->description }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-6">
                            @if($asset->location)
                            <p><strong>Location:</strong> {{ $asset->location }}</p>
                            @endif
                            @if($asset->serial_number)
                            <p><strong>Serial Number:</strong> {{ $asset->serial_number }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($asset->insurance_expiry)
                            <p><strong>Insurance Expiry:</strong>
                                {{ $asset->insurance_expiry->format('d M Y') }}
                                @if($asset->is_insurance_expired)
                                    <span class="badge badge-danger ml-2">Expired</span>
                                @elseif($asset->insurance_expires_soon)
                                    <span class="badge badge-warning ml-2">Expires Soon</span>
                                @else
                                    <span class="badge badge-success ml-2">Valid</span>
                                @endif
                            </p>
                            @endif
                        </div>
                    </div>

                    @if($asset->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Notes:</strong></p>
                            <p>{{ $asset->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
