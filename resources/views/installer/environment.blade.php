@extends('installer.layout')

@section('content')
<h4 class="mb-4 font-weight-bold text-gray-800">Database Configuration</h4>
<p class="text-muted mb-4">Please enter your MySQL database connection details. These will be securely saved into your <code>.env</code> file.</p>

<form method="POST" action="{{ route('installer.saveEnvironment') }}">
    @csrf
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <tags class="font-weight-bold text-gray-700">Application Name</tags>
            <input type="text" name="app_name" class="form-control" value="FinaFlow" required>
        </div>
        
        <div class="col-md-8 mb-3">
            <tags class="font-weight-bold text-gray-700">Database Host</tags>
            <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
        </div>
        
        <div class="col-md-4 mb-3">
            <tags class="font-weight-bold text-gray-700">Port</tags>
            <input type="number" name="db_port" class="form-control" value="3306" required>
        </div>

        <div class="col-md-12 mb-3">
            <tags class="font-weight-bold text-gray-700">Database Name</tags>
            <input type="text" name="db_database" class="form-control" placeholder="finaflow_db" required>
        </div>

        <div class="col-md-6 mb-3">
            <tags class="font-weight-bold text-gray-700">Database Username</tags>
            <input type="text" name="db_username" class="form-control" placeholder="root" required>
        </div>

        <div class="col-md-6 mb-3">
            <tags class="font-weight-bold text-gray-700">Database Password</tags>
            <input type="password" name="db_password" class="form-control" placeholder="Leave empty if none">
        </div>
    </div>

    <hr class="mt-4 mb-4">

    <div class="d-flex justify-content-between">
        <a href="{{ route('installer.permissions') }}" class="btn btn-light"><i class="fas fa-arrow-left"></i> Back</a>
        <button type="submit" class="btn btn-primary">Save Configuration <i class="fas fa-save ml-1"></i></button>
    </div>
</form>
@endsection
