<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\SharedExpense;
use App\Models\FamilyGoal;
use App\Models\GiftEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $familyMembers = FamilyMember::where('user_id', $user->id)->active()->get();
        $sharedExpenses = SharedExpense::where('user_id', $user->id)->latest()->take(5)->get();
        $familyGoals = FamilyGoal::where('user_id', $user->id)->active()->get();
        $upcomingEvents = GiftEvent::where('user_id', $user->id)->upcoming()->orderBy('event_date')->take(3)->get();

        return view('family.index', compact('familyMembers', 'sharedExpenses', 'familyGoals', 'upcomingEvents'));
    }

    // Family Members
    public function members()
    {
        $members = FamilyMember::where('user_id', Auth::id())->get();
        return view('family.members', compact('members'));
    }

    public function createMember()
    {
        return view('family.create-member');
    }

    public function storeMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'monthly_allowance' => 'nullable|numeric|min:0',
        ]);

        FamilyMember::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'relationship' => $request->relationship,
            'date_of_birth' => $request->date_of_birth,
            'monthly_allowance' => $request->monthly_allowance ?? 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('family.members')->with('success', __('family.messages.member_created'));
    }

    public function editMember(FamilyMember $member)
    {
        if ($member->user_id !== Auth::id()) {
            abort(403);
        }
        return view('family.edit-member', compact('member'));
    }

    public function updateMember(Request $request, FamilyMember $member)
    {
        if ($member->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'monthly_allowance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $member->update($request->only(['name', 'relationship', 'date_of_birth', 'monthly_allowance', 'is_active']));

        return redirect()->route('family.members')->with('success', __('family.messages.member_updated'));
    }

    // Shared Expenses
    public function sharedExpenses()
    {
        $expenses = SharedExpense::where('user_id', Auth::id())->latest()->paginate(10);
        return view('family.shared-expenses', compact('expenses'));
    }

    public function createSharedExpense()
    {
        $members = FamilyMember::where('user_id', Auth::id())->active()->get();
        return view('family.create-shared-expense', compact('members'));
    }

    public function storeSharedExpense(Request $request)
    {
        $request->validate([
            'expense_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'participants' => 'required|array',
            'split_method' => 'required|in:equal,percentage,custom',
        ]);

        SharedExpense::create([
            'user_id' => Auth::id(),
            'expense_name' => $request->expense_name,
            'description' => $request->description,
            'total_amount' => $request->total_amount,
            'category' => $request->category,
            'expense_date' => $request->expense_date,
            'participants' => $request->participants,
            'split_method' => $request->split_method,
            'is_settled' => false,
        ]);

        return redirect()->route('family.shared-expenses')->with('success', __('family.messages.expense_created'));
    }

    // Family Goals
    public function familyGoals()
    {
        $goals = FamilyGoal::where('user_id', Auth::id())->get();
        return view('family.goals', compact('goals'));
    }

    public function createFamilyGoal()
    {
        return view('family.create-goal');
    }

    public function storeFamilyGoal(Request $request)
    {
        $request->validate([
            'goal_name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'target_date' => 'required|date|after:today',
            'goal_type' => 'required|string|max:255',
            'contributors' => 'nullable|array',
        ]);

        FamilyGoal::create([
            'user_id' => Auth::id(),
            'goal_name' => $request->goal_name,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'current_amount' => 0,
            'target_date' => $request->target_date,
            'goal_type' => $request->goal_type,
            'contributors' => $request->contributors ?? [],
            'is_achieved' => false,
        ]);

        return redirect()->route('family.goals')->with('success', __('family.messages.goal_created'));
    }

    // Gift Events
    public function giftEvents()
    {
        $events = GiftEvent::where('user_id', Auth::id())->orderBy('event_date')->get();
        return view('family.gift-events', compact('events'));
    }

    public function createGiftEvent()
    {
        return view('family.create-gift-event');
    }

    public function storeGiftEvent(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'budget_amount' => 'nullable|numeric|min:0',
            'recipients' => 'nullable|array',
        ]);

        GiftEvent::create([
            'user_id' => Auth::id(),
            'event_name' => $request->event_name,
            'event_type' => $request->event_type,
            'event_date' => $request->event_date,
            'budget_amount' => $request->budget_amount ?? 0,
            'spent_amount' => 0,
            'recipients' => $request->recipients ?? [],
            'gifts' => [],
            'notes' => $request->notes,
            'is_completed' => false,
        ]);

        return redirect()->route('family.gift-events')->with('success', __('family.messages.event_created'));
    }
}
