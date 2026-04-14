@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Email Parser</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integrations.email-parser.store') }}">
                        @csrf
                        <div class="form-group">
                            <tags>Email Content</tags>
                            <textarea name="email_content" rows="10" class="form-control @error('email_content') is-invalid @enderror">{{ old('email_content') }}</textarea>
                            @error('email_content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Parse Email</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Parsed Result</h6>
                </div>
                <div class="card-body">
                    @if(session('parsed'))
                        <ul class="list-unstyled mb-0">
                            <li><strong>Vendor:</strong> {{ session('parsed.vendor') }}</li>
                            <li><strong>Due Date:</strong> {{ session('parsed.due_date') }}</li>
                            <li><strong>Amount:</strong> Rp {{ number_format(session('parsed.amount'), 0, ',', '.') }}</li>
                            <li><strong>Invoice #:</strong> {{ session('parsed.invoice_number') }}</li>
                        </ul>
                    @else
                        <p class="text-muted mb-0">Paste any bill or receipt email to extract structured data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
