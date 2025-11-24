<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($this->canBypassTwoFactor($request, $user)) {
                $user->logActivity('login', 'User logged in');

                return redirect()->intended(route('dashboard'));
            }

            session(['2fa:user_id' => $user->id, '2fa:remember' => $request->boolean('remember')]);
            Auth::logout();

            return redirect()->route('twofactor.challenge');
        }

        $request->session()->flash('auth_error', trans('auth.failed'));

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            $user->logActivity('logout', 'User logged out');
        }

        return redirect()->route('login');
    }

    private function canBypassTwoFactor(Request $request, $user): bool
    {
        if (! $user->hasValidTwoFactorSecret()) {
            return true;
        }

        $rememberToken = $request->cookie('remember_device');
        if (! $rememberToken) {
            return false;
        }

        $hashed = hash('sha256', $rememberToken);

        return \App\Models\TwoFactorRememberToken::where('user_id', $user->id)
            ->where('token_hash', $hashed)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
