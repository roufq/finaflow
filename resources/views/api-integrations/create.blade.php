@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">New API Integration</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('api-integrations.store') }}">
                        @csrf

                        <div class="form-group">
                            <tags>Provider</tags>
                            <select name="provider" class="form-control" required>
                                <option value="credit_score">Credit Score</option>
                                <option value="investment_data">Investment Data</option>
                                <option value="news">Financial News</option>
                                <option value="weather">Weather</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <tags>API Key (optional)</tags>
                            <input type="text" name="api_key" class="form-control" value="{{ old('api_key') }}">
                        </div>

                        <div class="form-group">
                            <tags>Notes / Settings</tags>
                            <textarea name="settings[notes]" class="form-control" rows="3">{{ old('settings.notes') }}</textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                            <tags class="form-check-tags" for="is_active">Activate integration</tags>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('api-integrations.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h6>Available Providers</h6>
                    <ul class="mb-0">
                        <li><strong>Credit Score:</strong> Monitor credit health</li>
                        <li><strong>Investment Data:</strong> Track portfolio movements</li>
                        <li><strong>Financial News:</strong> Curated headlines</li>
                        <li><strong>Weather:</strong> Seasonal spending insights</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
