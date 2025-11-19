@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Family Members</h1>
        <a href="{{ route('family.members.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Family Member
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">All Family Members</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('family.members.create') }}">
                                <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                                Add Member
                            </a>
                            <a class="dropdown-item" href="{{ route('family.index') }}">
                                <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($members->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Relationship</th>
                                        <th>Age</th>
                                        <th>Monthly Allowance</th>
                                        <th>Current Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white mr-2" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $member->name }}</div>
                                                    @if($member->date_of_birth)
                                                        <small class="text-muted">{{ $member->date_of_birth->format('d/m/Y') }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $member->relationship }}</span>
                                        </td>
                                        <td>
                                            @if($member->date_of_birth)
                                                {{ $member->age }} years old
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            Rp {{ number_format($member->monthly_allowance, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <span class="font-weight-bold {{ $member->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($member->current_balance, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($member->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('family.members.edit', $member) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('family.members.update', $member) }}"
                                                      style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" name="is_active" value="{{ $member->is_active ? '0' : '1' }}"
                                                            class="btn btn-sm {{ $member->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                            onclick="return confirm('{{ $member->is_active ? 'Deactivate' : 'Activate' }} this family member?')">
                                                        <i class="fas fa-{{ $member->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-gray-300 mb-4"></i>
                            <h4 class="text-gray-500 mb-3">No Family Members Yet</h4>
                            <p class="text-gray-500 mb-4">Start building your family finance management by adding your first family member.</p>
                            <a href="{{ route('family.members.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i>Add First Family Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($members->count() > 0)
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Members</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $members->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Active Members</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $members->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                Total Monthly Allowance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($members->sum('monthly_allowance'), 0, ',', '.') }}
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
                                Total Current Balance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($members->sum('current_balance'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 0, "asc" ]],
        "pageLength": 25,
        "language": {
            "search": "Search members:",
            "lengthMenu": "Show _MENU_ members per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ members"
        }
    });
});
</script>
@endsection
