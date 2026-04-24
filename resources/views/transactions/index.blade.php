@extends('layouts.app')

@section('content')
<div class="space-y-8" x-data="{ showFilters: {{ request()->anyFilled(['search', 'type', 'account_id', 'start_date', 'end_date']) ? 'true' : 'false' }} }">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Transactions</h1>
            <p class="text-sm font-medium text-slate-500">Monitor your cash flow and financial activities with precision.</p>
            
            @if(request()->anyFilled(['search', 'type', 'account_id', 'start_date', 'end_date']))
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 self-center mr-1">Active Filters:</span>
                    @if(request('search')) 
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-2.5 py-1 text-xs font-bold text-primary-700 ring-1 ring-primary-100">
                            Search: {{ request('search') }}
                        </span> 
                    @endif
                    @if(request('type')) 
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-2.5 py-1 text-xs font-bold text-primary-700 ring-1 ring-primary-100 uppercase">
                            {{ request('type') }}
                        </span> 
                    @endif
                    @if(request('account_id')) 
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-2.5 py-1 text-xs font-bold text-primary-700 ring-1 ring-primary-100">
                            Account: {{ optional($accounts->firstWhere('id', request('account_id')))->name }}
                        </span> 
                    @endif
                    <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-red-500 hover:text-red-600 self-center ml-1">Clear All</a>
                </div>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <button @click="showFilters = !showFilters" 
                    :class="showFilters ? 'bg-slate-100 text-slate-900' : 'bg-white text-slate-600'"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-filter mr-2 text-slate-400"></i>
                Filters
            </button>
            <button type="button" data-toggle="modal" data-target="#receiptModal" 
                    class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-emerald-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-emerald-50">
                <i class="fas fa-camera mr-2"></i>
                Scan
            </button>
            <a href="{{ route('transactions.create') }}" 
               class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                New Transaction
            </a>
        </div>
    </div>

    <!-- Filter Bar (Expandable) -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6">
            <div class="space-y-1 lg:col-span-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Quick Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-xl border-none bg-slate-50 pl-9 py-2 text-sm focus:ring-2 focus:ring-primary-500/20" 
                           placeholder="Search keywords...">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Type</label>
                <select name="type" class="w-full rounded-xl border-none bg-slate-50 py-2 text-sm focus:ring-2 focus:ring-primary-500/20">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Account</label>
                <select name="account_id" class="w-full rounded-xl border-none bg-slate-50 py-2 text-sm focus:ring-2 focus:ring-primary-500/20">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ (string)request('account_id') === (string)$account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full rounded-xl border-none bg-slate-50 py-2 text-sm focus:ring-2 focus:ring-primary-500/20">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-slate-900 py-2 text-sm font-bold text-white transition-all hover:bg-slate-800">
                    Apply
                </button>
                <a href="{{ route('transactions.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200">
                    <i class="fas fa-undo text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions List -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Date & Info</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Type</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Amount</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $transaction)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900 leading-none mb-1">{{ $transaction->transaction_date->format('M d, Y') }}</span>
                                <span class="text-[10px] font-bold uppercase text-slate-400">{{ $transaction->account->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-1.5 rounded-full bg-primary-400"></div>
                                <span class="text-xs font-semibold text-slate-600">{{ $transaction->category->name ?? 'Other' }}</span>
                            </div>
                            @if($transaction->description)
                                <p class="mt-1 text-[10px] text-slate-400 truncate max-w-[200px]">{{ $transaction->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                <i class="fas {{ $transaction->type === 'income' ? 'fa-caret-up' : 'fa-caret-down' }}"></i>
                                {{ $transaction->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-extrabold tracking-tight {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-slate-900' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ $transaction->account->setting->currency_symbol ?? 'Rp' }} {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('transactions.show', $transaction) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('transactions.edit', $transaction) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                    <i class="fas fa-receipt text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-400 italic">No transactions found match your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="border-t border-slate-50 bg-slate-50/30 px-6 py-4">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Showing {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} of {{ $transactions->total() }}
                </div>
                <div class="pagination-custom">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Receipt Scan Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true" x-data="{ scanning: false, data: null, step: 1 }">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content overflow-hidden border-0 rounded-2xl shadow-soft">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h5 class="text-lg font-bold text-slate-900">Scan Smart Receipt</h5>
                <button type="button" class="close text-slate-400 hover:text-slate-900" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="receiptForm" class="p-0 m-0">
                @csrf
                <div class="p-8">
                    <!-- Step 1: Upload -->
                    <div x-show="step == 1" class="space-y-6">
                        <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-10 text-center transition-all hover:border-primary-300 hover:bg-slate-50">
                            <input type="file" id="receiptImage" name="receipt_image" accept="image/*" class="absolute inset-0 z-10 cursor-pointer opacity-0">
                            <div class="flex flex-col items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-primary-500 shadow-sm ring-1 ring-slate-100">
                                    <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-slate-900">Drop your receipt here</p>
                                    <p class="text-xs font-medium text-slate-400">JPG, PNG, TIFF up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        <div id="imagePreview" class="hidden rounded-xl overflow-hidden ring-1 ring-slate-200">
                            <img id="previewImg" src="" class="w-full max-h-[300px] object-contain bg-slate-100">
                        </div>
                    </div>

                    <!-- Step 2: Processing -->
                    <div x-show="scanning" class="py-12 flex flex-col items-center gap-6 text-center">
                        <div class="relative flex h-20 w-20 items-center justify-center">
                            <div class="absolute inset-0 rounded-full border-4 border-primary-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-primary-500 border-t-transparent animate-spin"></div>
                            <i class="fas fa-brain text-2xl text-primary-500"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">AI is Analyzing...</h4>
                            <p class="text-sm text-slate-400">Extracting merchant, date, and amounts using OCR technology.</p>
                        </div>
                    </div>

                    <!-- Step 3: Verify Data -->
                    <div x-show="step == 3" class="space-y-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Transaction Date</label>
                                <input type="date" id="transactionDate" class="w-full rounded-xl border-none bg-slate-50 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Amount</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" id="transactionAmount" class="w-full rounded-xl border-none bg-slate-50 pl-10 py-2.5 text-sm font-bold focus:ring-2 focus:ring-primary-500/20">
                                </div>
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Merchant / Description</label>
                                <input type="text" id="merchantName" class="w-full rounded-xl border-none bg-slate-50 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Category Selection</label>
                                <select id="transactionCategory" class="w-full rounded-xl border-none bg-slate-50 py-2.5 text-sm focus:ring-2 focus:ring-primary-500/20">
                                    <option value="">Choose a category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-3">
                    <button type="button" class="px-5 py-2 text-sm font-bold text-slate-500 hover:text-slate-900" data-dismiss="modal">Cancel</button>
                    <button type="button" id="processReceipt" class="hidden px-6 py-2 rounded-xl bg-primary-600 text-sm font-bold text-white shadow-premium transition-all hover:bg-primary-500">
                        Scan Receipt
                    </button>
                    <button type="button" id="saveTransaction" class="hidden px-6 py-2 rounded-xl bg-slate-900 text-sm font-bold text-white shadow-premium transition-all hover:bg-slate-800">
                        Save to Ledger
                    </button>
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
            const preview = document.getElementById('imagePreview');
            document.getElementById('previewImg').src = e.target.result;
            preview.classList.remove('hidden');
            document.getElementById('processReceipt').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('processReceipt').addEventListener('click', function() {
    const fileInput = document.getElementById('receiptImage');
    const file = fileInput.files[0];
    if (!file) return;

    const modal = document.querySelector('#receiptModal');
    // Using Alpine-like state manually since this is a separate script or we can trigger Alpine event
    const status = document.createElement('div'); // Mock for processing flow
    
    this.classList.add('hidden');
    document.getElementById('receiptForm').children[1].children[0].classList.add('hidden'); // Hide upload
    
    // We'll use the existing logic but with better loading UI
    const formData = new FormData();
    formData.append('receipt_image', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route('transactions.scanReceipt') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('transactionDate').value = data.date || '';
            document.getElementById('transactionAmount').value = data.amount || '';
            document.getElementById('merchantName').value = data.merchant || data.description || '';
            
            // Show result step
            document.getElementById('receiptForm').children[1].children[2].classList.remove('hidden'); // Show fields
            document.getElementById('saveTransaction').classList.remove('hidden');
        } else {
            alert('Scan failed: ' + data.message);
            location.reload();
        }
    })
    .catch(error => {
        console.error('OCR Error:', error);
        alert('Error processing document.');
        location.reload();
    });
});

document.getElementById('saveTransaction').addEventListener('click', function() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('category_id', document.getElementById('transactionCategory').value);
    formData.append('transaction_date', document.getElementById('transactionDate').value);
    formData.append('type', 'expense');
    formData.append('amount', document.getElementById('transactionAmount').value);
    formData.append('description', 'Auto-Scan: ' + document.getElementById('merchantName').value);
    // Assuming default account if one exists, or add account selector to modal
    // For demo/simplicity assuming the controller handles default account if missing or we add one field

    fetch('{{ route('transactions.store') }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            return response.json().then(data => alert('Save failed: ' + (data.message || 'Check fields')));
        }
    });
});
</script>
@endsection
