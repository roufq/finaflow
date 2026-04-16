<?php

namespace App\Http\Controllers;

use App\Models\TwoFactorRememberToken;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function showSetup(TotpService $totp): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        $secret = $user->two_factor_secret ?? $totp->generateSecret();
        $otpAuth = $totp->getOtpAuthUri(config('app.name'), $user->email, $secret);

        return view('auth.twofactor-setup', [
            'secret' => $secret,
            'otpAuth' => $otpAuth,
            'backupCodes' => $user->two_factor_backup_codes ?? [],
            'twoFactorEnabled' => $user->two_factor_enabled,
        ]);
    }

    public function enable(Request $request, TotpService $totp): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'secret' => 'required|string',
        ]);

        $user = $request->user();
        if (! $totp->verify($request->secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid TOTP code.']);
        }

        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_secret' => $request->secret,
            'two_factor_backup_codes' => $backupCodes,
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        $user->logActivity('two_factor_enabled', 'Enabled TOTP 2FA');

        return redirect()->route('dashboard')->with('success', '2FA has been enabled. Please save your backup codes.');
    }

    public function disable(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $user->update([
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
        ]);

        TwoFactorRememberToken::where('user_id', $user->id)->delete();
        $user->logActivity('two_factor_disabled', 'Disabled TOTP 2FA');

        return redirect()->route('dashboard')->with('success', '2FA has been disabled.');
    }

    public function regenerateBackupCodes(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $backupCodes = $this->generateBackupCodes();
        $user->update(['two_factor_backup_codes' => $backupCodes]);
        $user->logActivity('two_factor_backup_regenerated', 'Regenerated 2FA backup codes');

        return back()->with('success', 'New backup codes have been generated. Store them securely.');
    }

    public function showChallenge(): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $pendingUserId = session('2fa:user_id');
        if (! $pendingUserId) {
            return redirect()->route('login');
        }

        return view('auth.twofactor-challenge');
    }

    public function verifyChallenge(Request $request, TotpService $totp): \Illuminate\Http\RedirectResponse
    {
        $pendingUserId = session('2fa:user_id');
        if (! $pendingUserId) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'required|string',
            'remember_device' => 'nullable|boolean',
        ]);

        $user = User::findOrFail($pendingUserId);
        $code = trim($request->code);

        $backupCodes = $user->two_factor_backup_codes ?? [];
        $isBackup = in_array($code, $backupCodes, true);

        if (! $isBackup && ! $totp->verify($user->two_factor_secret, $code)) {
            return back()->withErrors(['code' => 'Invalid code.']);
        }

        if ($isBackup) {
            $remaining = array_values(array_diff($backupCodes, [$code]));
            $user->update(['two_factor_backup_codes' => $remaining]);
        }

        Auth::login($user, session('2fa:remember', false));
        session()->forget(['2fa:user_id', '2fa:remember']);
        $user->logActivity('two_factor_passed', 'Passed 2FA challenge');

        if ($request->boolean('remember_device')) {
            $this->rememberDevice($request, $user);
        }

        return redirect()->intended(route('dashboard'));
    }

    private function rememberDevice(Request $request, User $user): void
    {
        $token = Str::random(64);
        $expires = now()->addDays(30);

        TwoFactorRememberToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'user_agent' => substr($request->userAgent(), 0, 255),
            'ip_address' => $request->ip(),
            'expires_at' => $expires,
        ]);

        cookie()->queue('remember_device', $token, 60 * 24 * 30, null, null, false, true, false, 'Strict');
    }

    private function generateBackupCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }

        return $codes;
    }
}
