@extends('installer.layout')

@section('content')
<div class="text-center py-4">
    <div class="mb-4 text-success">
        <i class="fas fa-check-circle fa-5x"></i>
    </div>
    <h3 class="font-weight-bold text-gray-800 mb-3">FinaFlow Installed Successfully! 🎉</h3>
    <p class="text-muted mb-4">Your web application is now ready to use and successfully connected to the database with sample data.</p>

    <div class="card bg-light border-0 text-left mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold mb-3"><i class="fas fa-user-shield text-primary mr-2"></i> Default Administrator Login:</h6>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><strong>Email:</strong> admin@finaflow.test</li>
                <li><strong>Password:</strong> password</li>
            </ul>
        </div>
    </div>
    
    <div class="alert alert-warning small text-left" style="border-radius: 8px;">
        <i class="fas fa-lock"></i> It is highly recommended that you log in immediately and change the default admin password for security purposes.
    </div>

    <a href="{{ url('/login') }}" class="btn btn-success btn-lg mt-3 px-5 shadow-sm">
        Go to Login Page <i class="fas fa-sign-in-alt ml-2"></i>
    </a>
</div>
@endsection
