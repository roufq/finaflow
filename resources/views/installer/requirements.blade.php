@extends('installer.layout')

@section('content')
<h4 class="mb-4 font-weight-bold text-gray-800">Server Requirements</h4>
<p class="text-muted mb-4">Please make sure your server meets all the minimum PHP extension requirements below to run the application.</p>

<ul class="list-group mb-4">
    @foreach($requirements as $ext => $enabled)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>
            @if($ext == 'php')
                <strong>PHP >= 8.2.0</strong>
            @else
                <strong>{{ $ext }}</strong> extension
            @endif
        </span>
        @if($enabled)
            <span class="badge badge-success badge-pill py-2 px-3"><i class="fas fa-check"></i> Passed</span>
        @else
            <span class="badge badge-danger badge-pill py-2 px-3"><i class="fas fa-times"></i> Failed</span>
        @endif
    </li>
    @endforeach
</ul>

<div class="text-right">
    @if($allPassed)
    <a href="{{ route('installer.permissions') }}" class="btn btn-primary">
        Next: File Permissions <i class="fas fa-arrow-right ml-1"></i>
    </a>
    @else
    <button class="btn btn-secondary" disabled>Please Fix Errors First</button>
    <a href="{{ route('installer.index') }}" class="btn btn-info"><i class="fas fa-sync"></i> Refresh</a>
    @endif
</div>
@endsection
