@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Setting Details</h1>
        <a href="{{ route('settings.edit', $setting) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Edit</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Setting Information</h6>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $setting->id }}</p>
            <p><strong>Currency Symbol:</strong> {{ $setting->currency_symbol }}</p>
            <p><strong>Start Month:</strong> {{ $setting->start_month }}</p>
            <p><strong>Credit Score:</strong> {{ $setting->credit_score ?? 'Not set' }}</p>
            <p><strong>Risk Profile:</strong> {{ $setting->risk_profile ? ucfirst($setting->risk_profile) : 'Not set' }}</p>
            <a href="{{ route('settings.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
