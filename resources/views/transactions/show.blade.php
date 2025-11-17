@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Transaction Details</h1>
        <a href="{{ route('transactions.edit', $transaction) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Edit</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transaction Information</h6>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $transaction->id }}</p>
            <p><strong>Date:</strong> {{ $transaction->transaction_date->format('Y-m-d') }}</p>
            <p><strong>Category:</strong> {{ $transaction->category->name }}</p>
            <p><strong>Type:</strong> {{ $transaction->type }}</p>
            <p><strong>Amount:</strong> {{ $transaction->amount }}</p>
            <p><strong>Description:</strong> {{ $transaction->description }}</p>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
