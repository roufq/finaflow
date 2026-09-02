<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalApiController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $goals,
        ]);
    }

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

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'type' => $request->type,
            'target_amount' => $request->target_amount,
            'target_date' => $request->target_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Goal created successfully',
            'data' => $goal,
        ], 201);
    }

    public function show(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->load(['progressEntries.user']);

        return response()->json([
            'status' => 'success',
            'data' => $goal,
        ]);
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|in:emergency_fund,vacation,house_down_payment,car_purchase,education,retirement,investment,debt_payoff,business,other',
            'type' => 'sometimes|required|in:short_term,medium_term,long_term',
            'target_amount' => 'sometimes|required|numeric|min:0',
            'current_amount' => 'sometimes|required|numeric|min:0',
            'target_date' => 'sometimes|required|date|after:today',
            'status' => 'sometimes|required|in:active,completed,paused,cancelled',
        ]);

        $goal->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Goal updated successfully',
            'data' => $goal->refresh(),
        ]);
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Goal deleted successfully',
        ]);
    }

    public function updateProgress(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

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

        return response()->json([
            'status' => 'success',
            'message' => 'Progress updated successfully',
            'data' => $goal->refresh(),
        ]);
    }
}
