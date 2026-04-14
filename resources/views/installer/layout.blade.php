<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinaFlow Setup Wizard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .installer-container { max-width: 800px; margin: 40px auto; }
        .card { border: none; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .card-header { background-color: #fff; border-bottom: 1px solid #f1f5f9; border-radius: 12px 12px 0 0 !important; padding: 1.5rem; text-align: center; }
        .card-body { padding: 2rem; }
        .step-progress { display: flex; justify-content: space-between; margin-bottom: 2rem; position: relative; }
        .step-progress::before { content: ''; position: absolute; top: 15px; left: 0; width: 100%; height: 2px; background: #e2e8f0; z-index: 0; }
        .step { position: relative; z-index: 1; background: #f8fafc; padding: 0 10px; text-align: center; width: 25%; }
        .step-circle { width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; color: #64748b; line-height: 32px; margin: 0 auto 8px; font-weight: 600; }
        .step.active .step-circle { background: #3b82f6; color: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2); }
        .step.completed .step-circle { background: #10b981; color: #fff; }
        .step-label { font-size: 0.8rem; color: #64748b; font-weight: 500; }
        .step.active .step-label { color: #3b82f6; font-weight: 600; }
        .btn-primary { background-color: #3b82f6; border-color: #3b82f6; padding: 0.6rem 1.5rem; font-weight: 500; border-radius: 8px; }
        .btn-primary:hover { background-color: #2563eb; }
        .form-control { border-radius: 8px; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; }
        .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25); }
    </style>
</head>
<body>
    <div class="container installer-container">
        
        <div class="text-center mb-4">
            <h2 class="font-weight-bold text-gray-800">FinaFlow Setup Wizard</h2>
            <p class="text-muted">Follow the steps below to setup your application.</p>
        </div>

        @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 8px;">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="step-progress">
                    <div class="step {{ request()->routeIs('installer.index') ? 'active' : 'completed' }}">
                        <div class="step-circle">1</div>
                        <div class="step-label">Requirements</div>
                    </div>
                    <div class="step {{ request()->routeIs('installer.permissions') ? 'active' : (request()->routeIs('installer.index') ? '' : 'completed') }}">
                        <div class="step-circle">2</div>
                        <div class="step-label">Permissions</div>
                    </div>
                    <div class="step {{ request()->routeIs('installer.environment') ? 'active' : (request()->routeIs('installer.index', 'installer.permissions') ? '' : 'completed') }}">
                        <div class="step-circle">3</div>
                        <div class="step-label">Database</div>
                    </div>
                    <div class="step {{ request()->routeIs('installer.database') ? 'active' : (request()->routeIs('installer.finish') ? 'completed' : '') }}">
                        <div class="step-circle">4</div>
                        <div class="step-label">Install</div>
                    </div>
                    <div class="step {{ request()->routeIs('installer.finish') ? 'active' : '' }}">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">Finish</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @yield('content')
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted small">
            &copy; {{ date('Y') }} FinaFlow HQ. All Rights Reserved.
        </div>
    </div>
</body>
</html>
