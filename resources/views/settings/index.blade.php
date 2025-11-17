@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Settings</h1>
        <a href="{{ route('settings.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Setting
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Settings List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Currency Symbol</th>
                            <th>Start Month</th>
                            <th>Credit Score</th>
                            <th>Risk Profile</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings as $setting)
                        <tr>
                            <td>{{ $setting->id }}</td>
                            <td>{{ $setting->currency_symbol }}</td>
                            <td>{{ $setting->start_month }}</td>
                            <td>{{ $setting->credit_score ?? '-' }}</td>
                            <td>{{ $setting->risk_profile ? ucfirst($setting->risk_profile) : '-' }}</td>
                            <td>
                                <a href="{{ route('settings.show', $setting) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('settings.edit', $setting) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('settings.destroy', $setting) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
