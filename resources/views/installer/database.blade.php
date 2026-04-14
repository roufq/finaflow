@extends('installer.layout')

@section('content')
<div class="text-center">
    <div class="mb-4 text-primary">
        <i class="fas fa-database fa-4x"></i>
    </div>
    <h4 class="mb-3 font-weight-bold text-gray-800">Ready to Install Database</h4>
    <p class="text-muted mb-4">Your <code>.env</code> configuration has been successfully saved. The next step is to build the database tables, set security keys, and import demo data.</p>
    
    <div class="alert alert-info text-left small mb-4" style="border-radius: 8px;">
        <i class="fas fa-info-circle"></i> This process will run the <code>php artisan migrate --seed</code> command in the background. Please wait a few moments and do not close this page after clicking install.
    </div>

    <form method="POST" action="{{ route('installer.runDatabase') }}" id="installForm">
        @csrf
        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
            <a href="{{ route('installer.environment') }}" class="btn btn-light"><i class="fas fa-arrow-left"></i> Back</a>
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" id="installBtn">
                Run Installation <i class="fas fa-cogs ml-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('installForm').addEventListener('submit', function() {
        var btn = document.getElementById('installBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Installing... Please Wait.';
        btn.classList.add('disabled');
        btn.disabled = true;
    });
</script>
@endsection
