<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installed = file_exists(storage_path('installed'));
        
        // If visiting installer but already installed
        if ($request->is('install*')) {
            if ($installed) {
                return redirect('/');
            }
            return $next($request);
        }

        // If not visiting installer and not installed
        if (! $installed) {
            return redirect('/install');
        }

        return $next($request);
    }
}
