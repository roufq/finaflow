@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-start justify-content-between flex-wrap mb-4">
        <div class="mb-2">
            <h1 class="h3 mb-2 text-gray-800 font-weight-bold">Transactions</h1>
            <p class="text-muted small mb-2">Pantau setiap alur uang logout dan masuk Anda dengan mudah.</p>
            @if(request('search') || request('type') || request('account_id') || request('start_date') || request('end_date'))
                <div class="small">
                    <span class="text-muted mr-1">Filter aktif:</span>
                    @if(request('search')) <span class="badge badge-pill badge-light text-primary px-2 py-1 bg-primary bg-opacity-10">Search: "{{ request('search') }}"</span> @endif
                    @if(request('type')) <span class="badge badge-pill badge-light text-primary px-2 py-1 bg-primary bg-opacity-10">Type: {{ request('type') }}</span> @endif
                    @if(request('account_id')) <span class="badge badge-pill badge-light text-primary px-2 py-1 bg-primary bg-opacity-10">Account: {{ optional($accounts->firstWhere('id', request('account_id')))->name }}</span> @endif
                    @if(request('start_date')) <span class="badge badge-pill badge-light text-primary px-2 py-1 bg-primary bg-opacity-10">Dari: {{ request('start_date') }}</span> @endif
                    @if(request('end_date')) <span class="badge badge-pill badge-light text-primary px-2 py-1 bg-primary bg-opacity-10">Sampai: {{ request('end_date') }}</span> @endif
                </div>
            @endif
        </div>
        <div class="row align-items-start w-100">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <form class="form-row" method="GET" action="{{ route('transactions.index') }}">
                    <div class="col-md-5 mb-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search transactions..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="type" class="form-control">
                            <option value="">All type</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="account_id" class="form-control">
                            <option value="">All account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (string)request('account_id') === (string)$account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Dari Date">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Sampai Date">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary border-0 btn-block shadow-sm font-weight-bold" type="submit" style="background-color: #3b82f6;">Filter</button>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-light btn-block text-muted">Clear</a>
                    </div>
                </form>
            </div>
            <div class="col-lg-4 d-flex justify-content-lg-end">
                <button type="button" class="btn text-white shadow-sm mb-lg-0 mr-2" style="background-color: #10b981; border: none; border-radius: 8px;" data-toggle="modal" data-target="#receiptModal">
                    <i class="fas fa-camera mr-1"></i> Scan
                </button>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-plus mr-1"></i> Add Transaction
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-4 d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-gray-800">Transaction List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-borderless text-gray-700" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="py-3">Info Account & Category</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Nominal</th>
                            <th class="py-3">Description</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr class="border-bottom border-light hover-bg-light">
                            <td class="px-4 py-3 align-middle">
                                <div class="font-weight-bold text-gray-800">{{ $transaction->transaction_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-3 align-middle">
                                <span class="font-weight-bold text-gray-800">{{ $transaction->account->name ?? '?' }}</span><br>
                                <span class="small text-muted">{{ $transaction->category->name }}</span>
                            </td>
                            <td class="py-3 align-middle">
                                @if($transaction->type === 'income')
                                    <span class="badge badge-pill font-weight-normal px-2 py-1" style="background-color: rgba(34, 197, 94, 0.1); color: #16a34a;"><i class="fas fa-arrow-up text-xs mr-1"></i> Masuk</span>
                                @else
                                    <span class="badge badge-pill font-weight-normal px-2 py-1" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;"><i class="fas fa-arrow-down text-xs mr-1"></i> Logout</span>
                                @endif
                            </td>
                            <td class="py-3 align-middle {{ $transaction->type === 'income' ? 'text-success' : 'text-gray-800' }} font-weight-bold">
                                {{ $transaction->type === 'income' ? '+' : '-' }} {{ $transaction->account->setting->currency_symbol ?? 'Rp' }} {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 align-middle text-muted">{{ Str::limit($transaction->description, 30) ?: '-' }}</td>
                            <td class="px-4 py-3 align-middle text-right">
                                <div class="btn-group shadow-sm rounded-lg" role="group">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-light btn-sm text-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-light btn-sm text-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-md-none">
                @forelse($transactions as $transaction)
                    <div class="card border mb-3 shadow-sm">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="font-weight-bold">{{ $transaction->transaction_date->format('Y-m-d') }}</div>
                                    <div class="small text-muted">{{ $transaction->account->name ?? '?' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold">{{ $transaction->amount }}</div>
                                    <span class="badge badge-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">{{ $transaction->type }}</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="small text-muted">Category</div>
                                <div class="font-weight-semibold">{{ $transaction->category->name }}</div>
                            </div>
                            @if($transaction->description)
                                <p class="mb-3 text-muted">{{ $transaction->description }}</p>
                            @endif
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-info btn-sm mr-sm-2 mb-2 w-100 w-sm-auto">View</a>
                                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning btn-sm mr-sm-2 mb-2 w-100 w-sm-auto">Edit</a>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="w-100 w-sm-auto mb-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-4 text-center">
                        <i class="fas fa-receipt fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">Belum ada transactions.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-white border-top-0 px-4 py-3">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="text-muted small mb-3 mb-md-0">
                    Menampilkan <strong>{{ $transactions->firstItem() }}</strong> - <strong>{{ $transactions->lastItem() }}</strong> dari <strong>{{ $transactions->total() }}</strong> transactions
                </div>
                <div class="pagination-scandinavian">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Receipt Scan Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">Scan Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="receiptForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <tags for="receiptImage">Upload Receipt Image</tags>
                        <input type="file" class="form-control-file" id="receiptImage" name="receipt_image" accept="image/*" required>
                        <small class="form-text text-muted">Supported formats: JPG, PNG, JPEG. Max size: 5MB</small>
                    </div>
                    <div id="imagePreview" class="mt-3" style="display: none;">
                        <img id="previewImg" src="" alt="Receipt Preview" class="img-fluid" style="max-height: 300px;">
                    </div>
                    <div id="processingStatus" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-spinner fa-spin"></i> Processing receipt... Please wait.
                        </div>
                    </div>
                    <div id="extractedData" class="mt-3" style="display: none;">
                        <h6>Extracted Information:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="transactionDate">Date</tags>
                                    <input type="date" class="form-control" id="transactionDate" name="transaction_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <tags for="transactionAmount">Amount</tags>
                                    <input type="number" class="form-control" id="transactionAmount" name="amount" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <tags for="merchantName">Merchant</tags>
                            <input type="text" class="form-control" id="merchantName" name="merchant" placeholder="e.g., Indomaret, Alfamart">
                        </div>
                        <div class="form-group">
                            <tags for="transactionDescription">Description</tags>
                            <input type="text" class="form-control" id="transactionDescription" name="description" placeholder="Transaction description">
                        </div>
                        <div class="form-group">
                            <tags for="transactionCategory">Category</tags>
                            <select class="form-control" id="transactionCategory" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="processReceipt" style="display: none;">Process Receipt</button>
                    <button type="submit" class="btn btn-primary" id="saveTransaction" style="display: none;">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('receiptImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';

            // Show process button when image is selected
            document.getElementById('processReceipt').style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
    }
});

function processReceipt(file) {
    const formData = new FormData();
    formData.append('receipt_image', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    const processingStatus = document.getElementById('processingStatus');
    const extractedData = document.getElementById('extractedData');
    const saveButton = document.getElementById('saveTransaction');

    processingStatus.style.display = 'block';
    extractedData.style.display = 'none';
    saveButton.style.display = 'none';

    fetch('/transactions/scan-receipt', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        processingStatus.style.display = 'none';

        if (data.success) {
            // Populate extracted data
            document.getElementById('transactionDate').value = data.date || '';
            document.getElementById('transactionAmount').value = data.amount || '';
            document.getElementById('merchantName').value = data.merchant || '';
            document.getElementById('transactionDescription').value = data.description || '';

            extractedData.style.display = 'block';
            saveButton.style.display = 'block';
        } else {
            alert('Error processing receipt: ' + data.message);
        }
    })
    .catch(error => {
        processingStatus.style.display = 'none';
        alert('Error processing receipt. Please try again.');
        console.error('Error:', error);
    });
}

document.getElementById('receiptForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Form submission is now handled automatically when file is selected
});

// Handle process receipt button
document.getElementById('processReceipt').addEventListener('click', function() {
    const fileInput = document.getElementById('receiptImage');
    const file = fileInput.files[0];
    if (file) {
        processReceipt(file);
    } else {
        alert('Please select a receipt image first.');
    }
});

// Handle save transaction
document.getElementById('saveTransaction').addEventListener('click', function() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('category_id', document.getElementById('transactionCategory').value);
    formData.append('transaction_date', document.getElementById('transactionDate').value);
    formData.append('type', 'expense'); // Receipt scans are typically expenses
    formData.append('amount', document.getElementById('transactionAmount').value);
    formData.append('description', document.getElementById('transactionDescription').value);

    fetch('/transactions', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            $('#receiptModal').modal('hide');
            location.reload(); // Refresh page to show new transaction
        } else {
            return response.json().then(data => {
                alert('Error saving transaction: ' + (data.message || 'Unknown error'));
            });
        }
    })
    .catch(error => {
        alert('Error saving transaction. Please try again.');
        console.error('Error:', error);
    });
});
</script>
@endsection
