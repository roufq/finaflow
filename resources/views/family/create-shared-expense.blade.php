@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Shared Expense</h1>
        <a href="{{ route('family.shared-expenses') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Shared Expenses
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Shared Expense Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('family.shared-expenses.store') }}" id="sharedExpenseForm">
                        @csrf

                        <div class="form-group">
                            <label for="expense_name">Expense Name *</label>
                            <input type="text" class="form-control @error('expense_name') is-invalid @enderror"
                                   id="expense_name" name="expense_name" value="{{ old('expense_name') }}" required>
                            @error('expense_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="total_amount">Total Amount (Rp) *</label>
                            <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                   id="total_amount" name="total_amount"
                                   value="{{ old('total_amount') }}" min="0" step="1000" required>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select class="form-control @error('category') is-invalid @enderror"
                                    id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Food & Dining" {{ old('category') == 'Food & Dining' ? 'selected' : '' }}>Food & Dining</option>
                                <option value="Transportation" {{ old('category') == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="Shopping" {{ old('category') == 'Shopping' ? 'selected' : '' }}>Shopping</option>
                                <option value="Utilities" {{ old('category') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="Healthcare" {{ old('category') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Travel" {{ old('category') == 'Travel' ? 'selected' : '' }}>Travel</option>
                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expense_date">Expense Date *</label>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror"
                                   id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="split_method">Split Method *</label>
                            <select class="form-control @error('split_method') is-invalid @enderror"
                                    id="split_method" name="split_method" required onchange="toggleSplitOptions()">
                                <option value="">Select Split Method</option>
                                <option value="equal" {{ old('split_method') == 'equal' ? 'selected' : '' }}>Equal Split</option>
                                <option value="percentage" {{ old('split_method') == 'percentage' ? 'selected' : '' }}>Percentage Split</option>
                                <option value="custom" {{ old('split_method') == 'custom' ? 'selected' : '' }}>Custom Amount</option>
                            </select>
                            @error('split_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Participants *</label>
                            <div class="border rounded p-3" id="participantsSection">
                                <p class="mb-3">Select family members who participated in this expense:</p>
                                @if($members->count() > 0)
                                    @foreach($members as $member)
                                    <div class="participant-item mb-2">
                                        <div class="form-check d-inline-block mr-3">
                                            <input class="form-check-input participant-checkbox" type="checkbox"
                                                   id="participant_{{ $member->id }}" name="participants[]"
                                                   value="{{ $member->id }}"
                                                   onchange="updateParticipantShares()"
                                                   {{ in_array($member->id, old('participants', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="participant_{{ $member->id }}">
                                                {{ $member->name }} ({{ $member->relationship }})
                                            </label>
                                        </div>
                                        <div class="participant-share d-inline-block" id="share_{{ $member->id }}" style="display: none;">
                                            <input type="number" class="form-control form-control-sm d-inline-block w-25"
                                                   id="share_amount_{{ $member->id }}" name="share_amount[{{ $member->id }}]"
                                                   placeholder="Amount" min="0" step="1000" onchange="validateShares()">
                                            <small class="text-muted ml-2" id="share_tags_{{ $member->id }}"></small>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No active family members available. <a href="{{ route('family.members.create') }}">Add a family member first</a>.</p>
                                @endif
                            </div>
                            @error('participants')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Shared Expense
                        </button>
                        <a href="{{ route('family.shared-expenses') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Shared Expense Tips</h6>
                </div>
                <div class="card-body">
                    <h6>Split Methods</h6>
                    <ul class="mb-3">
                        <li><strong>Equal Split:</strong> Everyone pays the same amount</li>
                        <li><strong>Percentage Split:</strong> Each person pays a percentage of the total</li>
                        <li><strong>Custom Amount:</strong> Specify exact amount for each person</li>
                    </ul>

                    <h6>Best Practices</h6>
                    <ul class="mb-3">
                        <li>Choose appropriate split method for the expense type</li>
                        <li>Include all participants who benefited from the expense</li>
                        <li>Settle expenses promptly to avoid confusion</li>
                        <li>Keep receipts for shared expenses</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> All fields marked with * are required.
                    </div>
                </div>
            </div>

            <!-- Split Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Split Calculator</h6>
                </div>
                <div class="card-body">
                    <div id="splitPreview" class="mb-3" style="display: none;">
                        <h6>Total: <span id="previewTotal">Rp 0</span></h6>
                        <div id="participantPreviews"></div>
                    </div>
                    <div class="alert alert-warning" id="validationMessage" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
let selectedParticipants = [];

function toggleSplitOptions() {
    const method = document.getElementById('split_method').value;
    const participants = document.querySelectorAll('.participant-share');

    if (method === 'equal' || method === 'percentage') {
        participants.forEach(p => p.style.display = 'inline-block');
    } else if (method === 'custom') {
        participants.forEach(p => p.style.display = 'inline-block');
    } else {
        participants.forEach(p => p.style.display = 'none');
    }

    updateParticipantShares();
}

function updateParticipantShares() {
    selectedParticipants = Array.from(document.querySelectorAll('.participant-checkbox:checked')).map(cb => cb.value);
    const method = document.getElementById('split_method').value;
    const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;

    document.querySelectorAll('.participant-share').forEach(share => {
        const memberId = share.id.replace('share_', '');
        const isSelected = selectedParticipants.includes(memberId);

        if (isSelected && method) {
            share.style.display = 'inline-block';

            if (method === 'equal') {
                const equalShare = totalAmount / selectedParticipants.length;
                document.getElementById(`share_amount_${memberId}`).value = equalShare.toFixed(0);
                document.getElementById(`share_tags_${memberId}`).textContent = 'Equal share';
            } else if (method === 'percentage') {
                document.getElementById(`share_amount_${memberId}`).value = '';
                document.getElementById(`share_tags_${memberId}`).textContent = '% of total';
            } else if (method === 'custom') {
                document.getElementById(`share_amount_${memberId}`).value = '';
                document.getElementById(`share_tags_${memberId}`).textContent = 'Custom amount';
            }
        } else {
            share.style.display = 'none';
            document.getElementById(`share_amount_${memberId}`).value = '';
        }
    });

    updateSplitPreview();
}

function validateShares() {
    const method = document.getElementById('split_method').value;
    const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
    let totalShares = 0;

    if (method === 'percentage') {
        selectedParticipants.forEach(memberId => {
            const percentage = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
            totalShares += percentage;
        });

        const validationMsg = document.getElementById('validationMessage');
        if (totalShares !== 100) {
            validationMsg.innerHTML = `Total percentage must equal 100%. Current: ${totalShares}%`;
            validationMsg.style.display = 'block';
        } else {
            validationMsg.style.display = 'none';
        }
    } else if (method === 'custom') {
        selectedParticipants.forEach(memberId => {
            const amount = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
            totalShares += amount;
        });

        const validationMsg = document.getElementById('validationMessage');
        if (Math.abs(totalShares - totalAmount) > 1) { // Allow small rounding differences
            validationMsg.innerHTML = `Total custom amounts must equal total expense. Current: Rp ${totalShares.toLocaleString('id-ID')} vs Rp ${totalAmount.toLocaleString('id-ID')}`;
            validationMsg.style.display = 'block';
        } else {
            validationMsg.style.display = 'none';
        }
    } else {
        document.getElementById('validationMessage').style.display = 'none';
    }

    updateSplitPreview();
}

function updateSplitPreview() {
    const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
    const method = document.getElementById('split_method').value;

    if (selectedParticipants.length === 0 || !method || totalAmount === 0) {
        document.getElementById('splitPreview').style.display = 'none';
        return;
    }

    document.getElementById('previewTotal').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
    document.getElementById('splitPreview').style.display = 'block';

    const previewContainer = document.getElementById('participantPreviews');
    previewContainer.innerHTML = '';

    selectedParticipants.forEach(memberId => {
        const memberName = document.querySelector(`tags[for="participant_${memberId}"]`).textContent;
        let shareAmount = 0;
        let shareText = '';

        if (method === 'equal') {
            shareAmount = totalAmount / selectedParticipants.length;
            shareText = 'Rp ' + shareAmount.toLocaleString('id-ID');
        } else if (method === 'percentage') {
            const percentage = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
            shareAmount = (totalAmount * percentage) / 100;
            shareText = `${percentage}% (Rp ${shareAmount.toLocaleString('id-ID')})`;
        } else if (method === 'custom') {
            shareAmount = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
            shareText = 'Rp ' + shareAmount.toLocaleString('id-ID');
        }

        const previewItem = document.createElement('div');
        previewItem.className = 'd-flex justify-content-between';
        previewItem.innerHTML = `<span>${memberName}:</span><span>${shareText}</span>`;
        previewContainer.appendChild(previewItem);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('total_amount').addEventListener('input', updateParticipantShares);
    document.getElementById('expense_date').max = new Date().toISOString().split('T')[0];
    toggleSplitOptions();
});

// Form validation before submit
document.getElementById('sharedExpenseForm').addEventListener('submit', function(e) {
    const participants = document.querySelectorAll('.participant-checkbox:checked');
    if (participants.length === 0) {
        e.preventDefault();
        alert('Please select at least one participant.');
        return;
    }

    const method = document.getElementById('split_method').value;
    if (method === 'percentage' || method === 'custom') {
        const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
        let totalShares = 0;

        participants.forEach(cb => {
            const memberId = cb.value;
            const amount = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
            if (method === 'percentage') {
                totalShares += amount;
            } else {
                totalShares += amount;
            }
        });

        if (method === 'percentage' && totalShares !== 100) {
            e.preventDefault();
            alert('Total percentage must equal 100%.');
            return;
        }

        if (method === 'custom' && Math.abs(totalShares - totalAmount) > 1) {
            e.preventDefault();
            alert('Total custom amounts must equal the total expense amount.');
            return;
        }
    }
});
</script>
@endsection
