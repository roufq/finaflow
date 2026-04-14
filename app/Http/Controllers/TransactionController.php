<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\Finance\ReceiptScannerService;
use App\Services\Finance\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private ReceiptScannerService $receiptScannerService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'type' => 'nullable|in:income,expense',
            'account_id' => 'nullable|integer|exists:accounts,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $search = $validated['search'] ?? null;
        $type = $validated['type'] ?? null;
        $accountId = $validated['account_id'] ?? null;
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $userId = Auth::id();

        $transactions = Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('description', 'like', '%'.$search.'%')
                        ->orWhere('amount', 'like', '%'.$search.'%')
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('account', function ($accountQuery) use ($search) {
                            $accountQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($accountId, function ($query) use ($accountId) {
                $query->where('account_id', $accountId);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('transaction_date', '<=', $endDate);
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(25)
            ->withQueryString();
        $categories = Category::where('user_id', $userId)->get();
        $accounts = Account::active()->where('user_id', $userId)->get();

        return view('transactions.index', compact('transactions', 'categories', 'accounts'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        $accounts = Account::active()->get();

        return view('transactions.create', compact('categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', Auth::id()),
            ],
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', Auth::id()),
            ],
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $this->transactionService->create([
            'user_id' => Auth::id(),
            'account_id' => $request->account_id,
            'category_id' => $request->category_id,
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'amount' => (float) $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['category', 'account']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::where('user_id', Auth::id())->get();
        $accounts = Account::active()->where('user_id', Auth::id())->get();

        return view('transactions.edit', compact('transaction', 'categories', 'accounts'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', Auth::id()),
            ],
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', Auth::id()),
            ],
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $this->transactionService->update($transaction, [
            'category_id' => $request->category_id,
            'account_id' => $request->account_id,
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'amount' => (float) $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->transactionService->delete($transaction);

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }

    public function scanReceipt(Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $image = $request->file('receipt_image');
            $storedPath = $image->store('receipts/temp');
            $absolutePath = Storage::path($storedPath);
            $this->assertSafeImage($absolutePath);
            $extractedData = $this->receiptScannerService->extract($absolutePath);
            Storage::delete($storedPath);

            return response()->json([
                'success' => true,
                'date' => $extractedData['date'],
                'amount' => $extractedData['amount'],
                'merchant' => $extractedData['merchant'],
                'description' => $extractedData['description'],
                'raw_text' => $extractedData['raw_text'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing receipt: '.$e->getMessage(),
            ], 500);
        }
    }

    private function assertSafeImage(string $path): void
    {
        $info = @getimagesize($path);
        if ($info === false || empty($info['mime'])) {
            throw new \RuntimeException('Invalid image content.');
        }

        $allowedMimes = ['image/jpeg', 'image/png'];
        if (! in_array($info['mime'], $allowedMimes, true)) {
            throw new \RuntimeException('Unsupported image type.');
        }
    }
}
