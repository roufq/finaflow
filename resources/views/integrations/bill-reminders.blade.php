@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create Bill Reminder</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.reminders.store') }}">
                        @csrf
                        <div class="form-group">
                            <tags>Name</tags>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <tags>Vendor</tags>
                            <input type="text" name="vendor" class="form-control" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <tags>Amount (Rp)</tags>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <tags>Due Date</tags>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <tags>Remind me (days before)</tags>
                            <input type="number" name="reminder_days" class="form-control" min="1" max="30" value="3" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Reminder</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Reminders</h6>
                </div>
                <div class="card-body">
                    @if($reminders->count())
                        <ul class="list-group">
                            @foreach($reminders as $reminder)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $reminder->name }}</strong>
                                            <div class="text-muted">Due {{ data_get($reminder->conditions, '0.value') }}</div>
                                        </div>
                                        <span class="badge badge-{{ $reminder->is_active ? 'success' : 'secondary' }}">{{ $reminder->is_active ? 'Active' : 'Paused' }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No reminders yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
