@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Shared Expenses</h1>
        <a href="{{ route('family.shared-expenses.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Shared Expense
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All Shared Expenses</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('family.shared-expenses.create') }}">
                                <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                                Add Expense
                            </a>
                            <a class="dropdown-item" href="{{ route('family.index') }}">
                                <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($expenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Expense Name</th>
                                        <th>Category</th>
                                        <th>Total Amount</th>
                                        <th>Split Method</th>
                                        <th>Participants</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $expense)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $expense->expense_name }}</div>
                                            @if($expense->description)
                                                <small class="text-muted">{{ Str::limit($expense->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $expense->category }}</span>
                                        </td>
                                        <td class="font-weight-bold text-primary">
                                            Rp {{ number_format($expense->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($expense->split_method) }}</span>
                                        </td>
                                        <td>
                                            {{ $expense->participant_count }} participants
                                            @if($expense->split_method === 'equal')
                                                <br><small class="text-muted">Rp {{ number_format($expense->average_share, 0, ',', '.') }} each</small>
                                            @endif
                                        </td>
                                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                        <td>
                                            @if($expense->is_settled)
                                                <span class="badge badge-success">Settled</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#detailsModal{{ $expense->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#settleModal{{ $expense->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('family.shared-expenses.edit', $expense) }}">
                                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                                            Edit
                                                        </a>
                                                        <a class="dropdown-item" href="#" onclick="return confirm('Are you sure you want to delete this expense?')">
                                                            <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>
                                                            Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-4x text-gray-300 mb-4"></i>
                            <h4 class="text-gray-500 mb-3">No Shared Expenses Yet</h4>
                            <p class="text-gray-500 mb-4">Start tracking shared expenses with your family members.</p>
                            <a href="{{ route('family.shared-expenses.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i>Add First Expense
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($expenses->count() > 0)
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expenses->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                                Settled Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expenses->where('is_settled', true)->count() }}</div>
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
                                Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($expenses->sum('total_amount'), 0, ',', '.') }}
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
                                Average per Expense</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($expenses->avg('total_amount'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@if($expenses->count() > 0)
    @foreach($expenses as $expense)
    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal{{ $expense->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $expense->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel{{ $expense->id }}">{{ $expense->expense_name }} - Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Expense Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $expense->expense_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $expense->category }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Split Method:</strong></td>
                                    <td>{{ ucfirst($expense->split_method) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($expense->is_settled)
                                            <span class="badge badge-success">Settled</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Participants ({{ $expense->participant_count }})</h6>
                            @if(!empty($expense->participant_details))
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Share</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expense->participant_details as $participant)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $participant['name'] }}</strong>
                                                        @if(!empty($participant['relationship']))
                                                            <br>
                                                            <small class="text-muted">{{ ucfirst($participant['relationship']) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>Rp {{ number_format($participant['share'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">{{ __('family.shared_expenses.unknown_participant') }}</p>
                            @endif

                            @if($expense->description)
                            <h6 class="mt-3">Description</h6>
                            <p>{{ $expense->description }}</p>
                            @else
                            <p class="text-muted mt-3">No additional description provided.</p>
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

    <!-- Settle Modal -->
    <div class="modal fade" id="settleModal{{ $expense->id }}" tabindex="-1" role="dialog" aria-labelledby="settleModalLabel{{ $expense->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settleModalLabel{{ $expense->id }}">Settle Expense - {{ $expense->expense_name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('family.shared-expenses.settle') }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="expense_id" value="{{ $expense->id }}">
                    <div class="modal-body">
                        <p>Mark this shared expense as settled? This indicates that all participants have paid their shares.</p>
                        <div class="form-group">
                            <label for="settlement_date{{ $expense->id }}">Settlement Date</label>
                            <input type="date" class="form-control" id="settlement_date{{ $expense->id }}" name="settlement_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Settled</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

<script>
// Initialize DataTable if expenses exist
@if($expenses->count() > 0)
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 5, "desc" ]], // Sort by date descending
        "pageLength": 10,
        "language": {
            "search": "Search expenses:",
            "lengthMenu": "Show _MENU_ expenses per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ expenses"
        }
    });
});
@endif
</script>
@endsection
