<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetApiController extends Controller
{
    public function index()
    {
        $budgets = Budget::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $budgets,
        ]);
    }

    public function show(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $budget,
        ]);
    }

    public function updateSpent(Request $request, Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'spent_amount' => 'required|numeric|min:0',
        ]);

        $budget->update(['spent_amount' => $request->spent_amount]);

        return response()->json([
            'status' => 'success',
            'message' => 'Budget updated successfully',
            'data' => $budget,
        ]);
    }
}
