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
            'label' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'start_month' => 'required|integer|min:1|max:12',
            'credit_score' => 'nullable|integer|min:300|max:900',
            'risk_profile' => 'nullable|in:aggressive,balanced,conservative',
        ]);

        $isFirst = Setting::where('user_id', Auth::id())->count() === 0;
        $isDefault = $request->boolean('is_default') || $isFirst;

        $payload = [
            'label' => $request->label,
            'currency_symbol' => $request->currency_symbol,
            'exchange_rate' => $request->exchange_rate,
            'is_default' => $isDefault,
            'start_month' => $request->start_month,
            'credit_score' => $request->credit_score,
            'risk_profile' => $request->risk_profile,
            'user_id' => Auth::id(),
        ];

        if ($isDefault) {
            Setting::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $setting = Setting::create($payload);

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
            'label' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'start_month' => 'required|integer|min:1|max:12',
            'credit_score' => 'nullable|integer|min:300|max:900',
            'risk_profile' => 'nullable|in:aggressive,balanced,conservative',
        ]);

        $data = $request->only(['label', 'currency_symbol', 'exchange_rate', 'start_month', 'credit_score', 'risk_profile']);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Setting::where('user_id', Auth::id())->where('id', '!=', $setting->id)->update(['is_default' => false]);
        }

        $setting->update($data);

        return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('settings.index')->with('success', 'Setting deleted successfully.');
    }
}
