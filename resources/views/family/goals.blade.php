@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Family Goals</h1>
        <a href="{{ route('family.goals.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Goal
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All Family Goals</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('family.goals.create') }}">
                                <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                                Add Goal
                            </a>
                            <a class="dropdown-item" href="{{ route('family.index') }}">
                                <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($goals->count() > 0)
                        <div class="row">
                            @foreach($goals as $goal)
                            <div class="col-xl-6 col-lg-6 mb-4">
                                <div class="card border-left-info shadow h-100">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-info">{{ $goal->goal_name }}</h6>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink{{ $goal->id }}"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                aria-labelledby="dropdownMenuLink{{ $goal->id }}">
                                                <a class="dropdown-item" href="{{ route('family.goals.edit', $goal) }}">
                                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                                    Edit Goal
                                                </a>
                                                <form method="POST" action="{{ route('family.goals.destroy', $goal) }}" onsubmit="return confirm('Are you sure you want to delete this goal?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>
                                                        Delete Goal
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center mb-3">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Progress</div>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $goal->progress_percentage }}%</div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="progress progress-sm mr-2">
                                                            <div class="progress-bar bg-info" role="progressbar"
                                                                style="width: {{ $goal->progress_percentage }}%" aria-valuenow="{{ $goal->progress_percentage }}"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Target Amount</div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Current Amount</div>
                                                <div class="h6 mb-0 font-weight-bold text-success">
                                                    Rp {{ number_format($goal->current_amount, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Target Date</div>
                                                <div class="text-gray-800">{{ $goal->target_date->format('d M Y') }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-xs font-weight-bold text-uppercase mb-1">Days Left</div>
                                                <div class="text-gray-800">
                                                    @if($goal->days_remaining > 0)
                                                        {{ $goal->days_remaining }} days
                                                    @elseif($goal->days_remaining == 0)
                                                        <span class="text-warning">Today</span>
                                                    @else
                                                        <span class="text-danger">{{ abs($goal->days_remaining) }} days overdue</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($goal->description)
                                        <div class="mb-3">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Description</div>
                                            <p class="text-gray-800 small mb-0">{{ $goal->description }}</p>
                                        </div>
                                        @endif

                                        <div class="mb-3">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Goal Type</div>
                                            <span class="badge badge-primary">{{ $goal->goal_type }}</span>
                                        </div>

                                        @if($goal->contributors && count($goal->contributors) > 0)
                                        <div class="mb-3">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">Contributors</div>
                                            <div>
                                                @foreach($goal->contributors as $contributorId)
                                                    @php
                                                        $contributor = \App\Models\FamilyMember::find($contributorId);
                                                    @endphp
                                                    @if($contributor)
                                                        <span class="badge badge-light mr-1">{{ $contributor->name }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-6">
                                                <button class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#contributeModal{{ $goal->id }}">
                                                    <i class="fas fa-plus"></i> Contribute
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#detailsModal{{ $goal->id }}">
                                                    <i class="fas fa-eye"></i> Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contribute Modal -->
                            <div class="modal fade" id="contributeModal{{ $goal->id }}" tabindex="-1" role="dialog" aria-labelledby="contributeModalLabel{{ $goal->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="contributeModalLabel{{ $goal->id }}">Contribute to {{ $goal->goal_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="POST" action="#">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <tags for="contribution_amount{{ $goal->id }}">Contribution Amount (Rp)</tags>
                                                    <input type="number" class="form-control" id="contribution_amount{{ $goal->id }}" name="amount" min="0" step="1000" required>
                                                </div>
                                                <div class="form-group">
                                                    <tags for="contribution_date{{ $goal->id }}">Contribution Date</tags>
                                                    <input type="date" class="form-control" id="contribution_date{{ $goal->id }}" name="date" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <tags for="contribution_note{{ $goal->id }}">Note (Optional)</tags>
                                                    <textarea class="form-control" id="contribution_note{{ $goal->id }}" name="note" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Add Contribution</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Modal -->
                            <div class="modal fade" id="detailsModal{{ $goal->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $goal->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailsModalLabel{{ $goal->id }}">{{ $goal->goal_name }} - Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Goal Information</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td><strong>Type:</strong></td>
                                                            <td>{{ $goal->goal_type }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Target Amount:</strong></td>
                                                            <td>Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Current Amount:</strong></td>
                                                            <td>Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Progress:</strong></td>
                                                            <td>{{ $goal->progress_percentage }}%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Target Date:</strong></td>
                                                            <td>{{ $goal->target_date->format('d M Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Status:</strong></td>
                                                            <td>
                                                                @if($goal->is_completed)
                                                                    <span class="badge badge-success">Completed</span>
                                                                @elseif($goal->is_overdue)
                                                                    <span class="badge badge-danger">Overdue</span>
                                                                @else
                                                                    <span class="badge badge-warning">In Progress</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Contributors</h6>
                                                    @if($goal->contributors && count($goal->contributors) > 0)
                                                        <ul class="list-group list-group-flush">
                                                            @foreach($goal->contributors as $contributorId)
                                                                @php
                                                                    $contributor = \App\Models\FamilyMember::find($contributorId);
                                                                @endphp
                                                                @if($contributor)
                                                                    <li class="list-group-item px-0">{{ $contributor->name }}</li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-muted">No contributors assigned</p>
                                                    @endif

                                                    @if($goal->description)
                                                    <h6 class="mt-3">Description</h6>
                                                    <p>{{ $goal->description }}</p>
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
                            <i class="fas fa-bullseye fa-4x text-gray-300 mb-4"></i>
                            <h4 class="text-gray-500 mb-3">No Family Goals Yet</h4>
                            <p class="text-gray-500 mb-4">Start setting family goals to work towards your dreams together.</p>
                            <a href="{{ route('family.goals.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i>Create First Goal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($goals->count() > 0)
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Goals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $goals->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
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
                                Completed Goals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $goals->where('is_completed', true)->count() }}</div>
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
                                Total Target Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($goals->sum('target_amount'), 0, ',', '.') }}
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
                                Total Saved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($goals->sum('current_amount'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
