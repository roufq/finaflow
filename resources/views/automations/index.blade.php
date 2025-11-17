@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Automations</h1>
        <a href="{{ route('automations.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-robot"></i> New Automation
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Active Automations</h6>
                </div>
                <div class="card-body">
                    @if($automations->count())
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($automations as $automation)
                                        <tr>
                                            <td>{{ $automation->name }}</td>
                                            <td>{{ ucfirst($automation->type) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $automation->is_active ? 'success' : 'secondary' }}">
                                                    {{ $automation->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('automations.edit', $automation) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-primary" data-run="{{ route('automations.run', $automation) }}">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" data-toggle="{{ route('automations.toggle', $automation) }}">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No automations yet.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Integrations Toolbox</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="{{ route('integrations.voice-entry') }}"><i class="fas fa-microphone"></i> Voice-to-Text Entry</a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('integrations.email-parser') }}"><i class="fas fa-envelope-open-text"></i> Email Parser</a>
                        </li>
                        <li>
                            <a href="{{ route('integrations.reminders') }}"><i class="fas fa-bell"></i> Bill Reminders</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-run]').forEach(button => {
    button.addEventListener('click', async () => {
        try {
            const response = await fetch(button.dataset.run, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
            });
            const data = await response.json();
            alert(data.message || 'Automation executed');
        } catch (error) {
            alert('Automation failed');
        }
    });
});

document.querySelectorAll('[data-toggle]').forEach(button => {
    button.addEventListener('click', async () => {
        try {
            const response = await fetch(button.dataset.toggle, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
            });
            const data = await response.json();
            alert(data.message || 'Status updated');
            window.location.reload();
        } catch (error) {
            alert('Toggle failed');
        }
    });
});
</script>
@endsection
