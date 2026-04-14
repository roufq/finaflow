@extends('installer.layout')

@section('content')
<h4 class="mb-4 font-weight-bold text-gray-800">Folder Permissions</h4>
<p class="text-muted mb-4">Please ensure that the following directories have <code>chmod 775</code> or <code>chmod 777</code> permissions so the application can write files (logs, cache, uploads).</p>

<ul class="list-group mb-4">
    @foreach($permissions as $folder => $isWritable)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span><code>{{ $folder }}</code></span>
        @if($isWritable)
            <span class="badge badge-success badge-pill py-2 px-3"><i class="fas fa-check"></i> Writable</span>
        @else
            <span class="badge badge-danger badge-pill py-2 px-3"><i class="fas fa-times"></i> Not Writable</span>
        @endif
    </li>
    @endforeach
</ul>

<div class="d-flex justify-content-between">
    <a href="{{ route('installer.index') }}" class="btn btn-light"><i class="fas fa-arrow-left"></i> Back</a>
    @if($allPassed)
    <a href="{{ route('installer.environment') }}" class="btn btn-primary">
        Next: Database Setup <i class="fas fa-arrow-right ml-1"></i>
    </a>
    @else
    <div>
        <a href="{{ route('installer.permissions') }}" class="btn btn-info mr-2"><i class="fas fa-sync"></i> Re-Check</a>
        <button class="btn btn-secondary" disabled>Please Fix Permissions First</button>
    </div>
    @endif
</div>
@endsection
