@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Activity Log</h1>
            <p class="mb-0 text-muted">Review your recent account activity.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($logs->isEmpty())
                <p class="text-muted mb-0">No activity recorded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Time</th>
                                <th scope="col">Action</th>
                                <th scope="col">Detail</th>
                                <th scope="col">IP</th>
                                <th scope="col">Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') ?? '—' }}</td>
                                    <td class="text-capitalize">{{ str_replace('_', ' ', $log->action) }}</td>
                                    <td>{{ $log->description ?? '—' }}</td>
                                    <td>{{ $log->ip_address ?? '—' }}</td>
                                    <td class="text-truncate" style="max-width: 240px;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
