@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-start justify-content-between flex-wrap mb-4">
        <div class="mb-2">
            <h1 class="h3 mb-0 text-gray-800">Transactions</h1>
            @if(request('search') || request('type') || request('account_id') || request('start_date') || request('end_date'))
                <div class="small text-muted">
                    Filter aktif:
                    @if(request('search')) <span class="badge badge-light">Cari: "{{ request('search') }}"</span> @endif
                    @if(request('type')) <span class="badge badge-light">Tipe: {{ request('type') }}</span> @endif
                    @if(request('account_id')) <span class="badge badge-light">Akun: {{ optional($accounts->firstWhere('id', request('account_id')))->name }}</span> @endif
                    @if(request('start_date')) <span class="badge badge-light">Dari: {{ request('start_date') }}</span> @endif
                    @if(request('end_date')) <span class="badge badge-light">Sampai: {{ request('end_date') }}</span> @endif
                </div>
            @endif
        </div>
        <div class="row align-items-start">
            <div class="col-lg-7 mb-3 mb-lg-0">
                <form class="form-row" method="GET" action="{{ route('transactions.index') }}">
                    <div class="col-md-6 mb-2">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Cari transaksi/kategori/nominal..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="type" class="form-control form-control-sm">
                            <option value="">Semua tipe</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="account_id" class="form-control form-control-sm">
                            <option value="">Semua akun</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (string)request('account_id') === (string)$account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" placeholder="Dari">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" placeholder="Sampai">
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-outline-secondary btn-sm btn-block" type="submit">Terapkan</button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-link btn-sm btn-block">Reset</a>
                    </div>
                </form>
            </div>
            <div class="col-lg-5 d-flex flex-column flex-lg-row align-items-stretch">
                <button type="button" class="btn btn-sm btn-success shadow-sm mb-2 mb-lg-0 mr-lg-2 w-100" data-toggle="modal" data-target="#receiptModal">
                    <i class="fas fa-camera fa-sm text-white-50"></i> Scan Receipt
                </button>
                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary shadow-sm w-100">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Add Transaction
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transactions List</h6>
        </div>
                <div class="card-body">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $transaction->account->name ?? '?' }}</td>
                            <td>{{ $transaction->category->name }}</td>
                            <td>{{ $transaction->type }}</td>
                            <td>{{ $transaction->amount }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
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
                                <div class="small text-muted">Kategori</div>
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
                    <p class="text-muted mb-0">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
</div>

<!-- Receipt Scan Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">Scan Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="receiptForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="receiptImage">Upload Receipt Image</label>
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
                                    <label for="transactionDate">Date</label>
                                    <input type="date" class="form-control" id="transactionDate" name="transaction_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transactionAmount">Amount</label>
                                    <input type="number" class="form-control" id="transactionAmount" name="amount" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="merchantName">Merchant</label>
                            <input type="text" class="form-control" id="merchantName" name="merchant" placeholder="e.g., Indomaret, Alfamart">
                        </div>
                        <div class="form-group">
                            <label for="transactionDescription">Description</label>
                            <input type="text" class="form-control" id="transactionDescription" name="description" placeholder="Transaction description">
                        </div>
                        <div class="form-group">
                            <label for="transactionCategory">Category</label>
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
