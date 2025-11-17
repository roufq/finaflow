<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::where('user_id', Auth::id())->get();

        // Calculate asset summary
        $totalValue = $assets->sum('current_value');
        $totalPurchaseValue = $assets->sum('purchase_value');
        $totalDepreciation = $assets->sum('total_depreciation');
        $totalIncome = $assets->sum('annual_income');

        // Check for expired or expiring insurance
        $expiredInsurance = $assets->where('is_insurance_expired', true);
        $expiringInsurance = $assets->where('insurance_expires_soon', true)->where('is_insurance_expired', false);

        return view('assets.index', compact(
            'assets',
            'totalValue',
            'totalPurchaseValue',
            'totalDepreciation',
            'totalIncome',
            'expiredInsurance',
            'expiringInsurance'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:real_estate,vehicle,electronics,jewelry,collectibles,insurance,other',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_value' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'purchase_date' => 'required|date|before_or_equal:today',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'monthly_income' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Asset::create($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Asset added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $this->authorize('view', $asset);

        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);

        return view('assets.edit', compact('asset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);

        $validated = $request->validate([
            'type' => 'required|in:real_estate,vehicle,electronics,jewelry,collectibles,insurance,other',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_value' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'purchase_date' => 'required|date|before_or_equal:today',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'monthly_income' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }
}
