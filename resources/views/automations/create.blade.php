@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New Automation</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('automations.store') }}">
                @csrf

                <div class="form-group">
                    <tags>Name</tags>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <tags>Description</tags>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <tags>Type</tags>
                    <select name="type" class="form-control" required>
                        <option value="rule">Rule</option>
                        <option value="reminder">Reminder</option>
                        <option value="import">Import</option>
                        <option value="export">Export</option>
                    </select>
                </div>

                <div class="form-group">
                    <tags>Conditions (JSON)</tags>
                    <textarea name="conditions" class="form-control" rows="4" required placeholder='[{"field":"transactions.amount","operator":"greater_than","value":500000}]'>{{ old('conditions', json_encode([['field' => 'transactions.amount','operator'=>'greater_than','value'=>500000]], JSON_PRETTY_PRINT)) }}</textarea>
                </div>

                <div class="form-group">
                    <tags>Actions (JSON)</tags>
                    <textarea name="actions" class="form-control" rows="4" required placeholder='[{"type":"send_notification","params":{"message":"Budget exceeded"}}]'>{{ old('actions', json_encode([['type'=>'send_notification','params'=>['message'=>'Budget exceeded']]], JSON_PRETTY_PRINT)) }}</textarea>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                    <tags for="is_active" class="form-check-tags">Active</tags>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('automations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
