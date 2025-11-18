<?php

namespace App\Http\Controllers;

use App\Models\FamilyGoal;
use App\Models\FamilyMember;
use App\Models\GiftEvent;
use App\Models\SharedExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FamilyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $familyMembers = FamilyMember::where('user_id', $user->id)->active()->get();
        $sharedExpenses = SharedExpense::where('user_id', $user->id)->latest()->get();
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
            'current_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $member->update($request->only(['name', 'relationship', 'date_of_birth', 'monthly_allowance', 'current_balance', 'is_active']));

        return redirect()->route('family.members')->with('success', __('family.messages.member_updated'));
    }

    // Shared Expenses
    public function sharedExpenses()
    {
        $userId = Auth::id();
        $expenses = SharedExpense::where('user_id', $userId)->latest()->get();
        $members = FamilyMember::where('user_id', $userId)->get()->keyBy('id');

        $expenses = $expenses->map(function (SharedExpense $expense) use ($members) {
            $participants = collect($expense->participants ?? []);

            $expense->participant_details = $participants->map(function ($value, $key) use ($members, $expense) {
                if (is_int($key)) {
                    $memberId = (int) $value;
                    $shareAmount = $expense->average_share;
                } else {
                    $memberId = (int) $key;
                    $shareAmount = (float) $value;
                }

                $member = $members->get($memberId);

                return [
                    'id' => $memberId,
                    'name' => $member?->name ?? __('family.shared_expenses.unknown_participant'),
                    'relationship' => $member?->relationship,
                    'share' => (float) $shareAmount,
                ];
            })->values()->all();

            return $expense;
        });

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

        $participantShares = $this->formatParticipants(
            $request->participants,
            $request->split_method,
            $request->input('share_amount', []),
            (float) $request->total_amount
        );

        SharedExpense::create([
            'user_id' => Auth::id(),
            'expense_name' => $request->expense_name,
            'description' => $request->description,
            'total_amount' => $request->total_amount,
            'category' => $request->category,
            'expense_date' => $request->expense_date,
            'participants' => $participantShares,
            'split_method' => $request->split_method,
            'is_settled' => false,
        ]);

        return redirect()->route('family.shared-expenses')->with('success', __('family.messages.expense_created'));
    }

    public function editSharedExpense(SharedExpense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $members = FamilyMember::where('user_id', Auth::id())->active()->get();
        $selectedParticipants = [];
        $participantShares = [];

        foreach ($expense->participants ?? [] as $key => $value) {
            if (is_int($key)) {
                $selectedParticipants[] = (int) $value;
            } else {
                $memberId = (int) $key;
                $selectedParticipants[] = $memberId;
                $participantShares[$memberId] = (float) $value;
            }
        }

        $selectedParticipants = array_unique($selectedParticipants);

        return view('family.edit-shared-expense', compact('expense', 'members', 'selectedParticipants', 'participantShares'));
    }

    public function updateSharedExpense(Request $request, SharedExpense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'participants' => 'required|array',
            'split_method' => 'required|in:equal,percentage,custom',
            'description' => 'nullable|string',
        ]);

        $participantShares = $this->formatParticipants(
            $validated['participants'],
            $validated['split_method'],
            $request->input('share_amount', []),
            (float) $validated['total_amount']
        );

        $expense->update([
            'expense_name' => $validated['expense_name'],
            'description' => $validated['description'] ?? null,
            'total_amount' => $validated['total_amount'],
            'category' => $validated['category'],
            'expense_date' => $validated['expense_date'],
            'participants' => $participantShares,
            'split_method' => $validated['split_method'],
        ]);

        return redirect()->route('family.shared-expenses')->with('success', __('family.messages.expense_updated'));
    }

    public function settleSharedExpense(Request $request, ?SharedExpense $expense = null)
    {
        $rules = [
            'settlement_date' => ['required', 'date'],
        ];

        if ($expense === null) {
            $rules['expense_id'] = ['required', 'integer', 'exists:shared_expenses,id'];
        }

        $data = $request->validate($rules);

        if ($expense !== null) {
            if ($expense->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            $expense = SharedExpense::where('user_id', Auth::id())->findOrFail($data['expense_id']);
        }

        $expense->update([
            'is_settled' => true,
            'settlement_date' => $data['settlement_date'],
        ]);

        return redirect()
            ->route('family.shared-expenses')
            ->with('success', __('family.messages.expense_settled'));
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

    public function editFamilyGoal(FamilyGoal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $members = FamilyMember::where('user_id', Auth::id())->active()->get();

        return view('family.edit-goal', compact('goal', 'members'));
    }

    public function updateFamilyGoal(Request $request, FamilyGoal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'goal_name' => 'required|string|max:255',
            'goal_type' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date|after:today',
            'description' => 'nullable|string',
            'contributors' => 'nullable|array',
            'is_achieved' => 'nullable|boolean',
        ]);

        $goal->goal_name = $data['goal_name'];
        $goal->goal_type = $data['goal_type'];
        $goal->target_amount = $data['target_amount'];
        $goal->target_date = $data['target_date'];
        $goal->description = $data['description'] ?? null;
        $goal->contributors = $data['contributors'] ?? [];

        $currentAmount = $data['current_amount'] ?? $goal->current_amount;
        $goal->current_amount = min($currentAmount, $goal->target_amount);

        $isAchieved = $request->boolean('is_achieved');
        $goal->is_achieved = $isAchieved;
        $goal->achieved_date = $isAchieved ? ($goal->achieved_date ?? now()) : null;

        $goal->save();

        return redirect()->route('family.goals')->with('success', __('family.messages.goal_updated'));
    }

    public function destroyFamilyGoal(FamilyGoal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return redirect()->route('family.goals')->with('success', __('family.messages.goal_deleted'));
    }

    // Gift Events
    public function giftEvents()
    {
        $userId = Auth::id();
        $members = FamilyMember::where('user_id', $userId)->active()->get()->keyBy('id');

        $events = GiftEvent::where('user_id', $userId)
            ->orderBy('event_date')
            ->get()
            ->map(function (GiftEvent $event) use ($members) {
                $event->recipient_details = collect($event->recipients ?? [])
                    ->map(function ($recipientId) use ($members) {
                        return $members->get((int) $recipientId);
                    })
                    ->filter()
                    ->values();

                return $event;
            });

        return view('family.gift-events', [
            'events' => $events,
            'members' => $members->values(),
        ]);
    }

    public function createGiftEvent()
    {
        $members = FamilyMember::where('user_id', Auth::id())->active()->get();

        return view('family.create-gift-event', compact('members'));
    }

    public function storeGiftEvent(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'budget_amount' => 'nullable|numeric|min:0',
            'recipients' => 'nullable|array',
            'notes' => 'nullable|string',
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

    public function editGiftEvent(GiftEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $members = FamilyMember::where('user_id', Auth::id())->active()->get();

        return view('family.edit-gift-event', compact('event', 'members'));
    }

    public function updateGiftEvent(Request $request, GiftEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'budget_amount' => 'nullable|numeric|min:0',
            'recipients' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $event->update([
            'event_name' => $validated['event_name'],
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'budget_amount' => $validated['budget_amount'] ?? 0,
            'recipients' => $validated['recipients'] ?? [],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('family.gift-events')->with('success', __('family.messages.event_updated'));
    }

    public function destroyGiftEvent(GiftEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();

        return redirect()->route('family.gift-events')->with('success', __('family.messages.event_deleted'));
    }

    public function storeGift(Request $request, GiftEvent $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'recipient_id' => 'required|integer',
            'gift_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'purchased' => 'nullable|boolean',
        ]);

        $recipientIds = collect($event->recipients ?? [])->map(fn ($id) => (int) $id)->all();
        if (! in_array((int) $validated['recipient_id'], $recipientIds, true)) {
            return back()->with('error', __('family.messages.recipient_invalid'));
        }

        $members = FamilyMember::where('user_id', Auth::id())->get()->keyBy('id');
        $recipient = $members->get((int) $validated['recipient_id']);

        $giftCollection = collect($event->gifts ?? []);
        $giftCollection->push([
            'id' => (string) Str::uuid(),
            'recipient_id' => (int) $validated['recipient_id'],
            'recipient_name' => $recipient?->name ?? __('family.shared_expenses.unknown_participant'),
            'gift_name' => $validated['gift_name'],
            'amount' => (float) $validated['amount'],
            'description' => $validated['description'] ?? null,
            'purchased' => $request->boolean('purchased'),
            'created_at' => now()->toDateTimeString(),
        ]);

        $event->update([
            'gifts' => $giftCollection->values()->all(),
            'spent_amount' => $giftCollection->sum(fn ($gift) => (float) ($gift['amount'] ?? 0)),
        ]);

        return back()->with('success', __('family.messages.gift_added'));
    }

    /**
     * Format participant payload based on split method.
     *
     * @param  array<int|string>  $participantIds
     * @param  array<int|string, mixed>  $rawShares
     * @return array<int, float>
     */
    protected function formatParticipants(array $participantIds, string $method, array $rawShares, float $totalAmount): array
    {
        $participantIds = array_map('intval', $participantIds);
        $participantIds = array_filter($participantIds);

        if (empty($participantIds)) {
            return [];
        }

        if ($method === 'equal') {
            $share = count($participantIds) > 0 ? $totalAmount / count($participantIds) : 0;

            return collect($participantIds)->mapWithKeys(function ($id) use ($share) {
                return [$id => round($share, 2)];
            })->all();
        }

        return collect($participantIds)->mapWithKeys(function ($id) use ($method, $rawShares, $totalAmount) {
            $inputValue = isset($rawShares[$id]) ? (float) $rawShares[$id] : 0;
            $amount = $method === 'percentage'
                ? ($totalAmount * $inputValue) / 100
                : $inputValue;

            return [$id => round($amount, 2)];
        })->all();
    }
}
