@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Edit Automation</h6>
            <span class="badge badge-{{ $automation->is_active ? 'success' : 'secondary' }}">{{ $automation->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('automations.update', $automation) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <tags>Name</tags>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $automation->name) }}">
                </div>

                <div class="form-group">
                    <tags>Description</tags>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $automation->description) }}</textarea>
                </div>

                <div class="form-group">
                    <tags>Type</tags>
                    <select name="type" class="form-control" required>
                        @foreach(['rule','reminder','import','export'] as $type)
                            <option value="{{ $type }}" @selected($automation->type === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <tags>Conditions (JSON)</tags>
                    <textarea name="conditions" class="form-control" rows="4" required>{{ old('conditions', json_encode($automation->conditions, JSON_PRETTY_PRINT)) }}</textarea>
                </div>

                <div class="form-group">
                    <tags>Actions (JSON)</tags>
                    <textarea name="actions" class="form-control" rows="4" required>{{ old('actions', json_encode($automation->actions, JSON_PRETTY_PRINT)) }}</textarea>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $automation->is_active ? 'checked' : '' }}>
                    <tags for="is_active" class="form-check-tags">Active</tags>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('automations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
