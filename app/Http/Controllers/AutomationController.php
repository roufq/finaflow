<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $automations = Automation::where('user_id', Auth::id())->get();

        return view('automations.index', compact('automations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('automations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:rule,reminder,import,export',
            'conditions' => 'required',
            'actions' => 'required',
            'is_active' => 'boolean',
        ]);

        $conditions = $this->decodeStructuredField($request->input('conditions'));
        $actions = $this->decodeStructuredField($request->input('actions'));

        Automation::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'conditions' => $conditions,
            'actions' => $actions,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('automations.index')->with('success', 'Automation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Automation $automation)
    {
        $this->authorize('view', $automation);

        return view('automations.show', compact('automation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Automation $automation)
    {
        $this->authorize('update', $automation);

        return view('automations.edit', compact('automation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Automation $automation)
    {
        $this->authorize('update', $automation);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:rule,reminder,import,export',
            'conditions' => 'required',
            'actions' => 'required',
            'is_active' => 'boolean',
        ]);

        $conditions = $this->decodeStructuredField($request->input('conditions'));
        $actions = $this->decodeStructuredField($request->input('actions'));

        $data = $request->only(['name', 'description', 'type', 'is_active']);
        $data['conditions'] = $conditions;
        $data['actions'] = $actions;

        $automation->update($data);

        return redirect()->route('automations.index')->with('success', 'Automation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Automation $automation)
    {
        $this->authorize('delete', $automation);
        $automation->delete();

        return redirect()->route('automations.index')->with('success', 'Automation deleted successfully.');
    }

    /**
     * Run automation manually
     */
    public function run(Automation $automation)
    {
        $this->authorize('view', $automation);

        if (! $automation->is_active) {
            return response()->json(['error' => 'Automation is not active'], 400);
        }

        try {
            $context = $this->buildContext($automation);
            $results = $automation->executeActions($context);

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Automation executed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to execute automation: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle automation active status
     */
    public function toggle(Automation $automation)
    {
        $this->authorize('update', $automation);

        $automation->update(['is_active' => ! $automation->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $automation->is_active,
            'message' => 'Automation '.($automation->is_active ? 'activated' : 'deactivated'),
        ]);
    }

    /**
     * Build context data for automation execution
     */
    private function buildContext(Automation $automation): array
    {
        $user = Auth::user();

        return [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'current_date' => now()->format('Y-m-d'),
            'current_time' => now()->format('H:i:s'),
            'automation_type' => $automation->type,
            // Add more context data as needed
        ];
    }

    private function decodeStructuredField($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return $decoded ?: [];
    }
}
