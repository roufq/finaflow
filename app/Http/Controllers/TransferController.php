<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfers = Transfer::where('user_id', Auth::id())
            ->with(['fromAccount', 'toAccount'])
            ->latest('transfer_date')
            ->paginate(15);

        return view('transfers.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->active()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transfers.create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'fee' => 'nullable|numeric|min:0',
            'transfer_date' => 'required|date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        // Verify accounts belong to user
        $fromAccount = Account::where('id', $validated['from_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $toAccount = Account::where('id', $validated['to_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if from account has sufficient balance
        $totalAmount = $validated['amount'] + ($validated['fee'] ?? 0);
        if ($fromAccount->available_balance < $totalAmount) {
            return back()->withInput()
                ->withErrors(['amount' => 'Saldo akun pengirim tidak mencukupi.']);
        }

        DB::transaction(function () use ($validated) {
            $transfer = Transfer::create($validated);
            $transfer->processTransfer();
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer berhasil dilakukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        $this->authorize('view', $transfer);

        $transfer->load(['fromAccount', 'toAccount']);

        return view('transfers.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transfer $transfer)
    {
        $this->authorize('update', $transfer);

        // Only allow editing pending transfers
        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.index')
                ->with('error', 'Hanya transfer dengan status pending yang dapat diedit.');
        }

        $accounts = Account::where('user_id', Auth::id())
            ->active()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('transfers.edit', compact('transfer', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transfer $transfer)
    {
        $this->authorize('update', $transfer);

        // Only allow editing pending transfers
        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.index')
                ->with('error', 'Hanya transfer dengan status pending yang dapat diedit.');
        }

        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'fee' => 'nullable|numeric|min:0',
            'transfer_date' => 'required|date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255',
        ]);

        // Verify accounts belong to user
        $fromAccount = Account::where('id', $validated['from_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $toAccount = Account::where('id', $validated['to_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if from account has sufficient balance
        $totalAmount = $validated['amount'] + ($validated['fee'] ?? 0);
        if ($fromAccount->available_balance < $totalAmount) {
            return back()->withInput()
                ->withErrors(['amount' => 'Saldo akun pengirim tidak mencukupi.']);
        }

        $transfer->update($validated);

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transfer $transfer)
    {
        $this->authorize('delete', $transfer);

        // Only allow deleting pending or failed transfers
        if (! in_array($transfer->status, ['pending', 'failed'])) {
            return redirect()->route('transfers.index')
                ->with('error', 'Hanya transfer dengan status pending atau failed yang dapat dihapus.');
        }

        $transfer->delete();

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer berhasil dihapus.');
    }
}
