<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Account;
use App\Models\Goal;
use App\Models\Setting;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $settings = Setting::query()
            ->where('user_id', $user->id)
            ->first();

        $profileStats = [
            'accounts' => Account::query()->where('user_id', $user->id)->count(),
            'goals' => Goal::query()->where('user_id', $user->id)->count(),
            'subscriptions' => Subscription::query()->where('user_id', $user->id)->count(),
        ];

        $completionSegments = [
            filled($user->name),
            filled($user->email),
            filled(optional($settings)->currency_symbol),
            filled(optional($settings)->risk_profile),
        ];

        $profileCompletion = (int) round((collect($completionSegments)->filter()->count() / count($completionSegments)) * 100);

        return view('profile.show', [
            'user' => $user,
            'settings' => $settings,
            'profileStats' => $profileStats,
            'profileCompletion' => $profileCompletion,
        ]);
    }

    /**
     * Update profile data such as name and email.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return redirect()
            ->route('profile.show')
            ->with('profileUpdated', __('profile.messages.profile_updated'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->validated()['new_password']),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('passwordUpdated', __('profile.messages.password_updated'));
    }
}
