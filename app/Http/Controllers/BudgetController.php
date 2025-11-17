<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BudgetController extends Controller
{
    use AuthorizesRequests;
    //

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('budgets.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:zero_based,envelope,percentage_based,fixed_amount',
            'period' => 'required|in:weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'category_allocations' => 'nullable|array',
        ]);

        $budget = Budget::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_budget' => $request->total_budget,
            'category_allocations' => $request->category_allocations,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        return view('budgets.show', compact('budget'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        $categories = Category::where('user_id', Auth::id())->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:zero_based,envelope,percentage_based,fixed_amount',
            'period' => 'required|in:weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'spent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,completed,paused,cancelled',
            'category_allocations' => 'nullable|array',
        ]);

        $budget->update($request->only([
            'name', 'description', 'type', 'period', 'start_date',
            'end_date', 'total_budget', 'spent_amount', 'status', 'category_allocations'
        ]));

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully!');
    }

    /**
     * Update budget spent amount
     */
    public function updateSpent(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $request->validate([
            'spent_amount' => 'required|numeric|min:0',
        ]);

        $budget->update(['spent_amount' => $request->spent_amount]);

        return response()->json([
            'success' => true,
            'spent_percentage' => $budget->spent_percentage,
            'remaining_amount' => $budget->remaining_amount
        ]);
    }
}
