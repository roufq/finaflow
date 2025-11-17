<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->active()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $totalBalance = $accounts->sum('balance');

        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accountTypes = [
            'bank' => 'Bank Account',
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'e_wallet' => 'E-Wallet',
            'investment' => 'Investment',
            'loan' => 'Loan',
            'savings' => 'Savings Account',
        ];

        return view('accounts.create', compact('accountTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,credit_card,e_wallet,investment,loan,savings',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $recentTransactions = $account->transactions()
            ->with('category')
            ->latest('transaction_date')
            ->take(10)
            ->get();

        $transfersFrom = $account->transfersFrom()
            ->with('toAccount')
            ->latest('transfer_date')
            ->take(5)
            ->get();

        $transfersTo = $account->transfersTo()
            ->with('fromAccount')
            ->latest('transfer_date')
            ->take(5)
            ->get();

        return view('accounts.show', compact('account', 'recentTransactions', 'transfersFrom', 'transfersTo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $accountTypes = [
            'bank' => 'Bank Account',
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'e_wallet' => 'E-Wallet',
            'investment' => 'Investment',
            'loan' => 'Loan',
            'savings' => 'Savings Account',
        ];

        return view('accounts.edit', compact('account', 'accountTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,credit_card,e_wallet,investment,loan,savings',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'balance' => 'required|numeric',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if account has transactions or transfers
        if ($account->transactions()->count() > 0 || $account->transfersFrom()->count() > 0 || $account->transfersTo()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Tidak dapat menghapus akun yang memiliki transaksi atau transfer.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
