<?php

namespace App\Http\Controllers;

use App\Models\UserPrivacySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivacyController extends Controller
{
    /**
     * Display the user's privacy settings.
     */
    public function index()
    {
        $privacySettings = UserPrivacySetting::where('user_id', Auth::id())->first();

        if (! $privacySettings) {
            // Create default privacy settings for the user
            $privacySettings = UserPrivacySetting::create([
                'user_id' => Auth::id(),
                'data_analytics' => true,
                'behavioral_insights' => true,
                'third_party_sharing' => false,
                'data_anonymization' => true,
                'account_deletion' => false,
                'custom_settings' => [],
            ]);
        }

        return view('privacy.settings', compact('privacySettings'));
    }

    /**
     * Update the user's privacy settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'data_analytics' => 'boolean',
            'behavioral_insights' => 'boolean',
            'third_party_sharing' => 'boolean',
            'data_anonymization' => 'boolean',
            'account_deletion' => 'boolean',
            'custom_settings' => 'nullable|array',
        ]);

        $privacySettings = UserPrivacySetting::where('user_id', Auth::id())->first();

        if ($privacySettings) {
            $privacySettings->update($request->only([
                'data_analytics',
                'behavioral_insights',
                'third_party_sharing',
                'data_anonymization',
                'account_deletion',
                'custom_settings',
            ]));
        } else {
            UserPrivacySetting::create(array_merge($request->only([
                'data_analytics',
                'behavioral_insights',
                'third_party_sharing',
                'data_anonymization',
                'account_deletion',
                'custom_settings',
            ]), ['user_id' => Auth::id()]));
        }

        return redirect()->back()->with('success', 'Privacy settings updated successfully.');
    }

    /**
     * Request account deletion.
     */
    public function requestDeletion()
    {
        $privacySettings = UserPrivacySetting::where('user_id', Auth::id())->first();

        if ($privacySettings) {
            $privacySettings->update(['account_deletion' => true]);
        } else {
            UserPrivacySetting::create([
                'user_id' => Auth::id(),
                'account_deletion' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Account deletion request submitted. You will receive a confirmation email within 24 hours.');
    }

    /**
     * Cancel account deletion request.
     */
    public function cancelDeletion()
    {
        $privacySettings = UserPrivacySetting::where('user_id', Auth::id())->first();

        if ($privacySettings) {
            $privacySettings->update(['account_deletion' => false]);
        }

        return redirect()->back()->with('success', 'Account deletion request cancelled.');
    }

    /**
     * Export user data.
     */
    public function exportData()
    {
        $user = Auth::user();

        // This would typically generate a comprehensive data export
        // For now, we'll return a simple JSON response
        $data = [
            'user' => $user->toArray(),
            'export_date' => now()->toISOString(),
            'note' => 'This is a sample data export. Full implementation would include all user data.',
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="user-data-export.json"',
        ]);
    }
}
