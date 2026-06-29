<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    use AuthorizesRequests;
    //

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $debts = Debt::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('debts.index', compact('debts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('debts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:credit_card,personal_loan,student_loan,mortgage,car_loan,business_loan,other',
            'lender' => 'required|string|max:255',
            'original_amount' => 'required|numeric|min:0',
            'current_balance' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'minimum_payment' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'payoff_strategy' => 'required|in:snowball,avalanche,custom',
        ]);

        $debt = Debt::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'lender' => $request->lender,
            'original_amount' => $request->original_amount,
            'current_balance' => $request->current_balance,
            'interest_rate' => $request->interest_rate,
            'minimum_payment' => $request->minimum_payment,
            'due_date' => $request->due_date,
            'payoff_strategy' => $request->payoff_strategy,
        ]);

        return redirect()->route('debts.index')->with('success', 'Debt created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        $this->authorize('view', $debt);

        return view('debts.show', compact('debt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        $this->authorize('update', $debt);

        return view('debts.edit', compact('debt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:credit_card,personal_loan,student_loan,mortgage,car_loan,business_loan,other',
            'lender' => 'required|string|max:255',
            'original_amount' => 'required|numeric|min:0',
            'current_balance' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'minimum_payment' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:active,paid_off,defaulted,settled',
            'payoff_strategy' => 'required|in:snowball,avalanche,custom',
        ]);

        $debt->update($request->only([
            'name', 'description', 'type', 'lender', 'original_amount',
            'current_balance', 'interest_rate', 'minimum_payment', 'due_date',
            'status', 'payoff_strategy',
        ]));

        return redirect()->route('debts.index')->with('success', 'Debt updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        $this->authorize('delete', $debt);
        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Debt deleted successfully!');
    }

    /**
     * Update debt balance
     */
    public function updateBalance(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $request->validate([
            'current_balance' => 'required|numeric|min:0',
        ]);

        $debt->update(['current_balance' => $request->current_balance]);

        return response()->json([
            'success' => true,
            'payoff_progress' => $debt->payoff_progress,
            'remaining_balance' => $debt->current_balance,
        ]);
    }

    /**
     * Calculate debt payoff plan
     */
    public function calculatePayoff(Debt $debt)
    {
        $this->authorize('view', $debt);

        $payoffData = [
            'estimated_months' => $debt->estimated_payoff_months,
            'monthly_interest' => $debt->monthly_interest,
            'total_interest' => $debt->total_interest_paid,
            'next_payment_date' => $debt->next_payment_date,
        ];

        return response()->json($payoffData);
    }

    /**
     * Add payment to debt
     */
    public function addPayment(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);

        $request->validate([
            'payment_amount' => 'required|numeric|min:0|max:'.$debt->current_balance,
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $newBalance = $debt->current_balance - $request->payment_amount;

        // Update payment history
        $paymentHistory = $debt->payment_history ?? [];
        $paymentHistory[] = [
            'amount' => $request->payment_amount,
            'date' => $request->payment_date,
            'notes' => $request->notes,
            'balance_after' => max(0, $newBalance),
        ];

        $debt->update([
            'current_balance' => max(0, $newBalance),
            'payment_history' => $paymentHistory,
            'status' => $newBalance <= 0 ? 'paid_off' : $debt->status,
        ]);

        return response()->json([
            'success' => true,
            'new_balance' => $debt->current_balance,
            'payoff_progress' => $debt->payoff_progress,
            'status' => $debt->status,
        ]);
    }
}
