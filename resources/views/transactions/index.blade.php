@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Transactions</h1>
        <div>
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#receiptModal">
                <i class="fas fa-camera fa-sm text-white-50"></i> Scan Receipt
            </button>
            <a href="{{ route('transactions.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Transaction
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transactions List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
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
