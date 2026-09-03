<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\Finance\ReceiptScannerService;
use App\Services\Finance\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TransactionApiController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private ReceiptScannerService $receiptScannerService
    ) {}

    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|in:income,expense',
            'account_id' => 'nullable|integer|exists:accounts,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'search' => 'nullable|string|max:100',
        ]);

        $userId = Auth::id();
        $perPage = $request->input('per_page', 15);

        $transactions = Transaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('account_id'), fn ($query) => $query->where('account_id', $request->input('account_id')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('description', 'like', '%'.$search.'%');
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return TransactionResource::collection($transactions);
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
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = $this->transactionService->create(array_merge($request->all(), [
            'user_id' => Auth::id(),
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created successfully',
            'data' => new TransactionResource($transaction->load(['category', 'account'])),
        ], 201);
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return new TransactionResource($transaction->load(['category', 'account']));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

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
            'description' => 'nullable|string|max:255',
        ]);

        $this->transactionService->update($transaction, $request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction updated successfully',
            'data' => new TransactionResource($transaction->load(['category', 'account'])),
        ]);
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $this->transactionService->delete($transaction);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction deleted successfully',
        ]);
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

            // Periksa keamanan gambar
            $info = @getimagesize($absolutePath);
            if ($info === false || empty($info['mime'])) {
                Storage::delete($storedPath);

                return response()->json(['success' => false, 'message' => 'Invalid image content'], 422);
            }

            $extractedData = $this->receiptScannerService->extract($absolutePath);
            Storage::delete($storedPath);

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $extractedData['date'],
                    'amount' => $extractedData['amount'],
                    'merchant' => $extractedData['merchant'],
                    'description' => $extractedData['description'],
                    'raw_text' => $extractedData['raw_text'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OCR Error: '.$e->getMessage(),
            ], 500);
        }
    }
}
