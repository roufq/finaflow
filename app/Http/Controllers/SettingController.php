<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();

        return view('settings.index', compact('settings'));
    }

    public function create()
    {
        return view('settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_symbol' => 'required|string|max:10',
            'start_month' => 'required|integer|min:1|max:12',
            'credit_score' => 'nullable|integer|min:300|max:900',
            'risk_profile' => 'nullable|in:aggressive,balanced,conservative',
        ]);

        $payload = [
            'currency_symbol' => $request->currency_symbol,
            'start_month' => $request->start_month,
            'credit_score' => $request->credit_score,
            'risk_profile' => $request->risk_profile,
        ];

        $setting = Setting::updateOrCreate(
            ['user_id' => Auth::id()],
            $payload + ['user_id' => Auth::id()]
        );

        $message = $setting->wasRecentlyCreated
            ? 'Setting created successfully.'
            : 'Setting updated successfully.';

        return redirect()->route('settings.index')->with('success', $message);
    }

    public function show(Setting $setting)
    {
        return view('settings.show', compact('setting'));
    }

    public function edit(Setting $setting)
    {
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'currency_symbol' => 'required|string|max:10',
            'start_month' => 'required|integer|min:1|max:12',
            'credit_score' => 'nullable|integer|min:300|max:900',
            'risk_profile' => 'nullable|in:aggressive,balanced,conservative',
        ]);

        $setting->update($request->only(['currency_symbol', 'start_month', 'credit_score', 'risk_profile']));

        return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('settings.index')->with('success', 'Setting deleted successfully.');
    }
}
