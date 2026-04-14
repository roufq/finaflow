@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Manage Users</h1>
            <p class="text-muted mb-0">Set roles and active/inactive status.</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $isAdmin = $user->hasRole('admin');
                                $adminLimitReached = $adminCount >= 2 && ! $isAdmin;
                            @endphp
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <form class="form-inline justify-content-end" method="POST" action="{{ route('admin.users.update', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-control form-control-sm mr-2">
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}"
                                                    {{ $user->hasRole($role) ? 'selected' : '' }}
                                                    @if($role === 'admin' && $adminLimitReached) disabled @endif
                                                >{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary ml-2">Detail</a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3 text-muted small">
                    <div>Maximum 2 admins. Non-admin users cannot be promoted if the limit is reached.</div>
                    <div>Deactivate users to block access (will be redirected to login with notification).</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
