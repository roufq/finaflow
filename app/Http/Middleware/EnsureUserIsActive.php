<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->is_active) {
            $message = Auth::user()->deactivation_message ?: 'Akun Anda dinonaktifkan. Hubungi admin untuk mengaktifkan kembali.';
            session(['account_inactive_message' => $message]);
        }

        return $next($request);
    }
}
