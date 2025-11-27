@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Manage Education Modules</h1>
            <p class="text-muted small mb-0">Create, edit, and publish learning modules for users.</p>
        </div>
        <a href="{{ route('admin.education.create') }}" class="btn btn-sm btn-primary">Create Module</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-header">Modules</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Difficulty</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $module)
                            <tr>
                                <td>{{ $module->title }}</td>
                                <td>{{ $module->category ?? '-' }}</td>
                                <td>{{ ucfirst($module->difficulty ?? 'beginner') }}</td>
                                <td>{{ $module->order }}</td>
                                <td>
                                    <span class="badge badge-{{ $module->is_active ? 'success' : 'secondary' }}">
                                        {{ $module->is_active ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.education.show', $module) }}" class="btn btn-sm btn-outline-secondary">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.education.edit', $module) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.education.destroy', $module) }}" class="d-inline ml-2" onsubmit="return confirm('Delete module?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No modules yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($modules->hasPages())
            <div class="card-footer">
                {{ $modules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
