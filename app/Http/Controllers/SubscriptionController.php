<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::where('user_id', Auth::id())
            ->orderBy('next_billing_date', 'asc')
            ->get();

        $totalMonthlyCost = $subscriptions->sum(function ($subscription) {
            return $subscription->getMonthlyCost();
        });
        $renewingSoon = $subscriptions->filter->isDueForRenewal();
        $potentialSavings = $this->calculatePotentialSavings($subscriptions);

        return view('subscriptions.index', compact('subscriptions', 'totalMonthlyCost', 'renewingSoon', 'potentialSavings'));
    }

    public function create()
    {
        return view('subscriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'provider' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
            'next_billing_date' => 'required|date',
            'category' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Subscription::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'provider' => $request->provider,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'next_billing_date' => $request->next_billing_date,
            'category' => $request->category,
            'notes' => $request->notes,
            'auto_renewal' => true,
            'status' => 'active',
        ]);

        return redirect()->route('subscriptions.index')->with('success', __('subscriptions.messages.created'));
    }

    public function show(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        return view('subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        return view('subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string',
            'provider' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
            'next_billing_date' => 'required|date',
            'category' => 'nullable|string',
            'auto_renewal' => 'boolean',
            'status' => 'required|in:active,paused,cancelled',
            'notes' => 'nullable|string',
        ]);

        $subscription->update($request->only([
            'name', 'provider', 'amount', 'frequency', 'next_billing_date',
            'category', 'auto_renewal', 'status', 'notes'
        ]));

        return redirect()->route('subscriptions.index')->with('success', __('subscriptions.messages.updated'));
    }

    public function destroy(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->delete();

        return redirect()->route('subscriptions.index')->with('success', __('subscriptions.messages.deleted'));
    }

    public function pause(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->pause();

        return redirect()->back()->with('success', __('subscriptions.messages.paused'));
    }

    public function resume(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->resume();

        return redirect()->back()->with('success', __('subscriptions.messages.resumed'));
    }

    public function cancel(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->cancel();

        return redirect()->back()->with('success', __('subscriptions.messages.cancelled'));
    }

    public function analytics()
    {
        $subscriptions = Subscription::where('user_id', Auth::id())->get();

        $analytics = [
            'total_monthly' => $subscriptions->sum(function ($subscription) {
                return $subscription->getMonthlyCost();
            }),
            'total_yearly' => $subscriptions->sum(function ($subscription) {
                return $subscription->getMonthlyCost();
            }) * 12,
            'by_category' => $subscriptions->groupBy('category')->map(function ($group) {
                return $group->sum(function ($subscription) {
                    return $subscription->getMonthlyCost();
                });
            }),
            'by_provider' => $subscriptions->groupBy('provider')->map(function ($group) {
                return $group->sum(function ($subscription) {
                    return $subscription->getMonthlyCost();
                });
            }),
            'due_this_month' => $subscriptions->filter(function ($sub) {
                return $sub->next_billing_date->month === now()->month;
            })->sum('amount'),
            'active_count' => $subscriptions->where('status', 'active')->count(),
        ];

        return view('subscriptions.analytics', compact('analytics', 'subscriptions'));
    }

    public function detectFromTransactions()
    {
        // This would analyze transaction patterns to detect recurring subscriptions
        // For now, return a placeholder response
        return redirect()->route('subscriptions.index')->with('info', 'Automatic subscription detection feature coming soon.');
    }

    private function calculatePotentialSavings($subscriptions)
    {
        // Simple calculation for potential savings (e.g., bundling, cheaper alternatives)
        // This is a placeholder - in a real app, this would be more sophisticated
        $savings = 0;

        // Example: Assume 10% savings potential for subscriptions over certain amount
        foreach ($subscriptions as $subscription) {
            if ($subscription->getMonthlyCost() > 50000) { // Over Rp 50k/month
                $savings += $subscription->getMonthlyCost() * 0.1;
            }
        }

        return $savings;
    }
}
