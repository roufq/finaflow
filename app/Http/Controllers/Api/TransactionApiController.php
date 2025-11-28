<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionApiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|in:income,expense',
            'account_id' => 'nullable|integer|exists:accounts,id',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $userId = Auth::id();
        $perPage = $request->input('per_page', 15);

        $transactions = Transaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('account_id'), fn ($query) => $query->where('account_id', $request->input('account_id')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->orderByDesc('transaction_date')
            ->paginate($perPage)
            ->withQueryString();

        return TransactionResource::collection($transactions);
    }
}
