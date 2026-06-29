<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    use AuthorizesRequests;
    //

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('goals.index', compact('goals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('goals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:emergency_fund,vacation,house_down_payment,car_purchase,education,retirement,investment,debt_payoff,business,other',
            'type' => 'required|in:short_term,medium_term,long_term',
            'target_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date|after:today',
        ]);

        Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'type' => $request->type,
            'target_amount' => $request->target_amount,
            'target_date' => $request->target_date,
        ]);

        return redirect()->route('goals.index')->with('success', 'Goal created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        $goal->load(['progressEntries.user']);

        return view('goals.show', [
            'goal' => $goal,
            'progressEntries' => $goal->progressEntries,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);

        return view('goals.edit', compact('goal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:emergency_fund,vacation,house_down_payment,car_purchase,education,retirement,investment,debt_payoff,business,other',
            'type' => 'required|in:short_term,medium_term,long_term',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date',
            'status' => 'required|in:active,completed,paused,cancelled',
        ]);

        $goal->update($request->only([
            'name', 'description', 'category', 'type',
            'target_amount', 'current_amount', 'target_date', 'status',
        ]));

        return redirect()->route('goals.index')->with('success', 'Goal updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully!');
    }

    /**
     * Update goal progress
     */
    public function updateProgress(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($goal, $request) {
            $goal->lockForUpdate();
            $goal->increment('current_amount', $request->amount);

            $goal->progressEntries()->create([
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'note' => $request->note,
            ]);
        });

        $goal->refresh();

        return response()->json(['success' => true, 'progress' => $goal->progress_percentage]);
    }
}
