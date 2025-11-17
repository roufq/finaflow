<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investments = Investment::where('user_id', Auth::id())->get();

        // Calculate portfolio summary
        $totalValue = $investments->sum('current_value');
        $totalPurchaseValue = $investments->sum('total_purchase_value');
        $totalGainLoss = $investments->sum('unrealized_gain_loss');
        $totalDividends = $investments->sum('dividends_received');
        $totalFees = $investments->sum('fees');

        return view('investments.index', compact(
            'investments',
            'totalValue',
            'totalPurchaseValue',
            'totalGainLoss',
            'totalDividends',
            'totalFees'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('investments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'symbol' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:stock,mutual_fund,crypto,bond,etf,other',
            'quantity' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date|before_or_equal:today',
            'dividends_received' => 'nullable|numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Investment::create($validated);

        return redirect()->route('investments.index')
            ->with('success', 'Investment added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Investment $investment)
    {
        $this->authorize('view', $investment);

        return view('investments.show', compact('investment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investment $investment)
    {
        $this->authorize('update', $investment);

        return view('investments.edit', compact('investment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investment $investment)
    {
        $this->authorize('update', $investment);

        $validated = $request->validate([
            'symbol' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:stock,mutual_fund,crypto,bond,etf,other',
            'quantity' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'required|date|before_or_equal:today',
            'dividends_received' => 'nullable|numeric|min:0',
            'fees' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $investment->update($validated);

        return redirect()->route('investments.index')
            ->with('success', 'Investment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investment $investment)
    {
        $this->authorize('delete', $investment);

        $investment->delete();

        return redirect()->route('investments.index')
            ->with('success', 'Investment deleted successfully.');
    }
}
