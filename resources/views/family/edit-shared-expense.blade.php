@extends('layouts.app')

@php
    $defaultSelected = old('participants', $selectedParticipants);
    $initialShares = old('share_amount', []);
    if (empty($initialShares)) {
        foreach ($participantShares as $memberId => $amount) {
            if ($expense->split_method === 'percentage' && $expense->total_amount > 0) {
                $initialShares[$memberId] = round(($amount / $expense->total_amount) * 100, 2);
            } else {
                $initialShares[$memberId] = $amount;
            }
        }
    }
@endphp

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Shared Expense</h1>
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
                    <form method="POST" action="{{ route('family.shared-expenses.update', $expense) }}" id="sharedExpenseForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <tags for="expense_name">Expense Name *</tags>
                            <input type="text" class="form-control @error('expense_name') is-invalid @enderror"
                                   id="expense_name" name="expense_name" value="{{ old('expense_name', $expense->expense_name) }}" required>
                            @error('expense_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="form-group">
                        <tags for="description">Description</tags>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $expense->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                        <div class="form-group">
                            <tags for="total_amount">Total Amount (Rp) *</tags>
                            <input type="number" class="form-control @error('total_amount') is-invalid @enderror"
                                   id="total_amount" name="total_amount"
                                   value="{{ old('total_amount', $expense->total_amount) }}" min="0" step="1000" required>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="form-group">
                        <tags for="category">Category *</tags>
                        <select class="form-control @error('category') is-invalid @enderror"
                                id="category" name="category" required>
                            @php
                                $categories = ['Food & Dining', 'Transportation', 'Entertainment', 'Shopping', 'Utilities', 'Healthcare', 'Education', 'Travel', 'Other'];
                                $selectedCategory = old('category', $expense->category);
                            @endphp
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                        <div class="form-group">
                            <tags for="expense_date">Expense Date *</tags>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror"
                                   id="expense_date" name="expense_date"
                                   value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="split_method">Split Method *</tags>
                            @php
                                $splitMethod = old('split_method', $expense->split_method);
                            @endphp
                            <select class="form-control @error('split_method') is-invalid @enderror" id="split_method" name="split_method" onchange="toggleSplitOptions()" required>
                                <option value="equal" {{ $splitMethod === 'equal' ? 'selected' : '' }}>Equal</option>
                                <option value="percentage" {{ $splitMethod === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="custom" {{ $splitMethod === 'custom' ? 'selected' : '' }}>Custom Amount</option>
                            </select>
                            @error('split_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags>Participants *</tags>
                            <div class="border rounded p-3" id="participantsSection">
                                <p class="mb-3">Select family members who participated in this expense:</p>
                                @if($members->count() > 0)
                                    @foreach($members as $member)
                                    @php
                                        $isChecked = in_array($member->id, $defaultSelected ?? []);
                                        $shareValue = $initialShares[$member->id] ?? '';
                                    @endphp
                                    <div class="participant-item mb-2">
                                        <div class="form-check d-inline-block mr-3">
                                            <input class="form-check-input participant-checkbox" type="checkbox"
                                                   id="participant_{{ $member->id }}" name="participants[]"
                                                   value="{{ $member->id }}"
                                                   onchange="updateParticipantShares()"
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            <tags class="form-check-tags" for="participant_{{ $member->id }}">
                                                {{ $member->name }} ({{ $member->relationship }})
                                            </tags>
                                        </div>
                                        <div class="participant-share d-inline-block" id="share_{{ $member->id }}" style="display: none;">
                                            <input type="number" class="form-control form-control-sm d-inline-block w-25"
                                                   id="share_amount_{{ $member->id }}" name="share_amount[{{ $member->id }}]"
                                                   placeholder="Amount" min="0" step="1000"
                                                   value="{{ $shareValue }}"
                                                   onchange="validateShares()">
                                            <small class="text-muted d-block" id="share_tags_{{ $member->id }}"></small>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">No family members available. Please add members first.</p>
                                        <a href="{{ route('family.members.create') }}" class="btn btn-primary btn-sm">Add Member</a>
                                    </div>
                                @endif
                            </div>
                            @error('participants')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="validationMessage" class="alert alert-warning" style="display: none;"></div>

                        <div id="splitPreview" class="border rounded p-3 mb-3" style="display: none;">
                            <h6>Split Preview</h6>
                            <p>Total: <strong id="previewTotal">Rp 0</strong></p>
                            <div id="participantPreviews"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Shared Expense
                        </button>
                        <a href="{{ route('family.shared-expenses') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-3">
                        <li>Review participants before saving changes</li>
                        <li>Ensure the split method represents the actual agreement</li>
                        <li>Update descriptions to add important context</li>
                        <li>Settle expenses promptly when everyone has paid</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> All fields marked with * are required.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@php
    $selectedJson = collect($defaultSelected ?? [])->map(fn ($id) => (int) $id)->values();
@endphp

<script>
let selectedParticipants = {!! $selectedJson->toJson() !!};
const initialShareValues = {!! json_encode($initialShares) !!};

function toggleSplitOptions() {
    const method = document.getElementById('split_method').value;
    const participants = document.querySelectorAll('.participant-share');

    if (method === 'equal' || method === 'percentage' || method === 'custom') {
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
                const equalShare = selectedParticipants.length > 0 ? totalAmount / selectedParticipants.length : 0;
                document.getElementById(`share_amount_${memberId}`).value = equalShare.toFixed(0);
                document.getElementById(`share_tags_${memberId}`).textContent = 'Equal share';
            } else if (method === 'percentage') {
                document.getElementById(`share_tags_${memberId}`).textContent = '% of total';
            } else if (method === 'custom') {
                document.getElementById(`share_tags_${memberId}`).textContent = 'Custom amount';
            }
        } else {
            share.style.display = 'none';
        }
    });

    if (method === 'percentage') {
        validatePercentage(totalAmount);
    } else if (method === 'custom') {
        validateCustom(totalAmount);
    } else {
        document.getElementById('validationMessage').style.display = 'none';
    }

    updateSplitPreview();
}

function validatePercentage() {
    let totalShares = 0;
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
}

function validateCustom(totalAmount) {
    let totalShares = 0;
    selectedParticipants.forEach(memberId => {
        const amount = parseFloat(document.getElementById(`share_amount_${memberId}`).value) || 0;
        totalShares += amount;
    });

    const validationMsg = document.getElementById('validationMessage');
    if (Math.abs(totalShares - totalAmount) > 1) {
        validationMsg.innerHTML = `Total custom amounts must equal total expense. Current: Rp ${totalShares.toLocaleString('id-ID')} vs Rp ${totalAmount.toLocaleString('id-ID')}`;
        validationMsg.style.display = 'block';
    } else {
        validationMsg.style.display = 'none';
    }
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

document.addEventListener('DOMContentLoaded', function() {
    selectedParticipants.forEach(memberId => {
        const checkbox = document.getElementById(`participant_${memberId}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    Object.entries(initialShareValues).forEach(([memberId, value]) => {
        const input = document.getElementById(`share_amount_${memberId}`);
        if (input && value !== undefined && value !== null) {
            input.value = value;
        }
    });

    document.getElementById('total_amount').addEventListener('input', updateParticipantShares);
    document.getElementById('expense_date').max = new Date().toISOString().split('T')[0];
    toggleSplitOptions();
    updateSplitPreview();
});

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
            totalShares += amount;
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
