@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gift Events</h1>
        <a href="{{ route('family.gift-events.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Plan New Event
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All Gift Events</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('family.gift-events.create') }}">
                                <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                                Plan Event
                            </a>
                            <a class="dropdown-item" href="{{ route('family.index') }}">
                                <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="row">
                            @foreach($events as $event)
                            <div class="col-xl-6 col-lg-6 mb-4">
                                <div class="card border-left-warning shadow h-100">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-warning">{{ $event->event_name }}</h6>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink{{ $event->id }}"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                aria-labelledby="dropdownMenuLink{{ $event->id }}">
                                                <a class="dropdown-item" href="#">
                                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                                    Edit Event
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="return confirm('Are you sure you want to delete this event?')">
                                                    <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>
                                                    Delete Event
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Event Type</div>
                                                <span class="badge badge-warning">{{ $event->event_type }}</span>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Event Date</div>
                                                <div class="text-gray-800">{{ $event->event_date->format('d M Y') }}</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Budget</div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    Rp {{ number_format($event->budget_amount, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Spent</div>
                                                <div class="h6 mb-0 font-weight-bold text-danger">
                                                    Rp {{ number_format($event->spent_amount, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        @if($event->budget_amount > 0)
                                        <div class="row no-gutters align-items-center mb-3">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Budget Used</div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $event->budget_used_percentage }}%</div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="progress progress-sm mr-2">
                                                            <div class="progress-bar bg-warning" role="progressbar"
                                                                style="width: {{ $event->budget_used_percentage }}%" aria-valuenow="{{ $event->budget_used_percentage }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Recipients</div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $event->recipient_count }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Gifts Planned</div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $event->gift_count }}</div>
                                            </div>
                                        </div>

                                        @if($event->recipients && $event->recipients->count() > 0)
                                        <div class="mb-3">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Recipients</div>
                                            <div>
                                                @foreach($event->recipients as $recipient)
                                                <span class="badge badge-light mr-1">{{ $recipient->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        @if($event->notes)
                                        <div class="mb-3">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Notes</div>
                                            <p class="text-gray-800 small mb-0">{{ $event->notes }}</p>
                                        </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-6">
                                                <button class="btn btn-warning btn-sm btn-block" data-toggle="modal" data-target="#giftModal{{ $event->id }}">
                                                    <i class="fas fa-gift"></i> Manage Gifts
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#detailsModal{{ $event->id }}">
                                                    <i class="fas fa-eye"></i> Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gift Management Modal -->
                            <div class="modal fade" id="giftModal{{ $event->id }}" tabindex="-1" role="dialog" aria-labelledby="giftModalLabel{{ $event->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="giftModalLabel{{ $event->id }}">Manage Gifts - {{ $event->event_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addGiftModal{{ $event->id }}">
                                                    <i class="fas fa-plus"></i> Add Gift
                                                </button>
                                            </div>

                                            @if($event->gifts && $event->gifts->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Recipient</th>
                                                                <th>Gift</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($event->gifts as $gift)
                                                            <tr>
                                                                <td>{{ $gift['recipient_name'] ?? 'Unknown' }}</td>
                                                                <td>{{ $gift['gift_name'] ?? 'N/A' }}</td>
                                                                <td>Rp {{ number_format($gift['amount'] ?? 0, 0, ',', '.') }}</td>
                                                                <td>
                                                                    @if($gift['purchased'] ?? false)
                                                                        <span class="badge badge-success">Purchased</span>
                                                                    @else
                                                                        <span class="badge badge-warning">Planned</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                                                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">No gifts planned yet.</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Gift Modal -->
                            <div class="modal fade" id="addGiftModal{{ $event->id }}" tabindex="-1" role="dialog" aria-labelledby="addGiftModalLabel{{ $event->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addGiftModalLabel{{ $event->id }}">Add Gift for {{ $event->event_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="POST" action="#">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="recipient{{ $event->id }}">Recipient</label>
                                                    <select class="form-control" id="recipient{{ $event->id }}" name="recipient_id" required>
                                                        <option value="">Select Recipient</option>
                                                        @if($event->recipients)
                                                            @foreach($event->recipients as $recipient)
                                                            <option value="{{ $recipient->id }}">{{ $recipient->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="gift_name{{ $event->id }}">Gift Name</label>
                                                    <input type="text" class="form-control" id="gift_name{{ $event->id }}" name="gift_name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="gift_amount{{ $event->id }}">Gift Amount (Rp)</label>
                                                    <input type="number" class="form-control" id="gift_amount{{ $event->id }}" name="amount" min="0" step="1000" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="gift_description{{ $event->id }}">Description (Optional)</label>
                                                    <textarea class="form-control" id="gift_description{{ $event->id }}" name="description" rows="2"></textarea>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="purchased{{ $event->id }}" name="purchased">
                                                    <label class="form-check-label" for="purchased{{ $event->id }}">Already purchased</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Add Gift</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Modal -->
                            <div class="modal fade" id="detailsModal{{ $event->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $event->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailsModalLabel{{ $event->id }}">{{ $event->event_name }} - Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Event Information</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td><strong>Type:</strong></td>
                                                            <td>{{ $event->event_type }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Date:</strong></td>
                                                            <td>{{ $event->event_date->format('d M Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Budget:</strong></td>
                                                            <td>Rp {{ number_format($event->budget_amount, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Spent:</strong></td>
                                                            <td>Rp {{ number_format($event->spent_amount, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Status:</strong></td>
                                                            <td>
                                                                @if($event->is_completed)
                                                                    <span class="badge badge-success">Completed</span>
                                                                @else
                                                                    <span class="badge badge-warning">Planning</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Recipients ({{ $event->recipient_count }})</h6>
                                                    @if($event->recipients && $event->recipients->count() > 0)
                                                        <ul class="list-group list-group-flush">
                                                            @foreach($event->recipients as $recipient)
                                                            <li class="list-group-item px-0">{{ $recipient->name }} ({{ $recipient->relationship }})</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-muted">No recipients assigned</p>
                                                    @endif

                                                    @if($event->notes)
                                                    <h6 class="mt-3">Notes</h6>
                                                    <p>{{ $event->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-gift fa-4x text-gray-300 mb-4"></i>
                            <h4 class="text-gray-500 mb-3">No Gift Events Yet</h4>
                            <p class="text-gray-500 mb-4">Start planning gift events for special occasions and holidays.</p>
                            <a href="{{ route('family.gift-events.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i>Plan First Event
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($events->count() > 0)
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Events</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $events->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Events</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $events->where('is_completed', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Budget</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($events->sum('budget_amount'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Spent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($events->sum('spent_amount'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
