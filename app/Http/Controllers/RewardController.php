<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyProgram;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $rewards = Reward::where('user_id', $userId)->get();
        $loyaltyPrograms = LoyaltyProgram::where('user_id', $userId)->get();

        $totalPoints = $rewards->sum('points_earned') + $loyaltyPrograms->sum('points_balance');
        $totalCashback = $rewards->sum('cashback_amount');

        // Simple logic to determine best card for next purchase
        $bestCard = $rewards->where('status', 'active')->sortByDesc('points_earned')->first()?->card_type ?? 'None';

        return view('rewards.index', compact('rewards', 'loyaltyPrograms', 'totalPoints', 'totalCashback', 'bestCard'));
    }

    public function createReward()
    {
        return view('rewards.create');
    }

    public function storeReward(Request $request)
    {
        $request->validate([
            'card_type' => 'required|string',
            'reward_type' => 'required|in:points,cashback',
            'expiry_date' => 'nullable|date',
        ]);

        Reward::create([
            'user_id' => Auth::id(),
            'card_type' => $request->card_type,
            'reward_type' => $request->reward_type,
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
        ]);

        return redirect()->route('rewards.index')->with('success', __('rewards.messages.reward_created'));
    }

    public function createLoyaltyProgram()
    {
        return view('rewards.create-loyalty');
    }

    public function storeLoyaltyProgram(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string',
            'program_type' => 'required|in:airline,hotel,credit_card,retail',
            'membership_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        LoyaltyProgram::create([
            'user_id' => Auth::id(),
            'program_name' => $request->program_name,
            'program_type' => $request->program_type,
            'membership_number' => $request->membership_number,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('rewards.index')->with('success', __('rewards.messages.loyalty_created'));
    }

    public function showReward(Reward $reward)
    {
        if ($reward->user_id !== Auth::id()) {
            abort(403);
        }

        return view('rewards.show-reward', compact('reward'));
    }

    public function showLoyaltyProgram(LoyaltyProgram $loyaltyProgram)
    {
        if ($loyaltyProgram->user_id !== Auth::id()) {
            abort(403);
        }

        return view('rewards.show-loyalty', compact('loyaltyProgram'));
    }

    public function editReward(Reward $reward)
    {
        if ($reward->user_id !== Auth::id()) {
            abort(403);
        }

        return view('rewards.edit-reward', compact('reward'));
    }

    public function updateReward(Request $request, Reward $reward)
    {
        if ($reward->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'card_type' => 'required|string',
            'reward_type' => 'required|in:points,cashback',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,expired,redeemed',
        ]);

        $reward->update($request->only(['card_type', 'reward_type', 'expiry_date', 'status']));

        return redirect()->route('rewards.index')->with('success', __('rewards.messages.reward_updated'));
    }

    public function editLoyaltyProgram(LoyaltyProgram $loyaltyProgram)
    {
        if ($loyaltyProgram->user_id !== Auth::id()) {
            abort(403);
        }

        return view('rewards.edit-loyalty', compact('loyaltyProgram'));
    }

    public function updateLoyaltyProgram(Request $request, LoyaltyProgram $loyaltyProgram)
    {
        if ($loyaltyProgram->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'program_name' => 'required|string',
            'program_type' => 'required|in:airline,hotel,credit_card,retail',
            'tier_level' => 'nullable|string',
            'membership_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        $loyaltyProgram->update($request->only([
            'program_name', 'program_type', 'tier_level', 'membership_number', 'expiry_date',
        ]));

        return redirect()->route('rewards.index')->with('success', __('rewards.messages.loyalty_updated'));
    }

    public function redeemReward(Reward $reward, Request $request)
    {
        if ($reward->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'points' => 'required|numeric|min:1',
        ]);

        if ($reward->redeemPoints($request->points)) {
            return redirect()->back()->with('success', __('rewards.messages.points_redeemed'));
        }

        return redirect()->back()->with('error', 'Insufficient points balance.');
    }

    public function redeemLoyaltyPoints(LoyaltyProgram $loyaltyProgram, Request $request)
    {
        if ($loyaltyProgram->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'points' => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        if ($loyaltyProgram->redeemPoints($request->points, $request->description)) {
            return redirect()->back()->with('success', __('rewards.messages.points_redeemed'));
        }

        return redirect()->back()->with('error', 'Insufficient points balance.');
    }

    public function optimizeRewards()
    {
        $rewards = Reward::where('user_id', Auth::id())->active()->get();

        // Simple optimization logic - suggest best card for spending
        $optimization = [
            'best_for_groceries' => $rewards->where('card_type', 'visa')->first() ?? $rewards->first(),
            'best_for_travel' => $rewards->where('card_type', 'amex')->first() ?? $rewards->first(),
            'best_for_online' => $rewards->where('card_type', 'mastercard')->first() ?? $rewards->first(),
        ];

        return view('rewards.optimize', compact('optimization', 'rewards'));
    }

    public function destroyReward(Reward $reward)
    {
        if ($reward->user_id !== Auth::id()) {
            abort(403);
        }

        $reward->delete();

        return redirect()->route('rewards.index')->with('success', __('rewards.messages.reward_deleted'));
    }

    public function destroyLoyaltyProgram(LoyaltyProgram $loyaltyProgram)
    {
        if ($loyaltyProgram->user_id !== Auth::id()) {
            abort(403);
        }

        $loyaltyProgram->delete();

        return redirect()->route('rewards.index')->with('success', __('rewards.messages.loyalty_deleted'));
    }
}
