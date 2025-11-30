<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $exception) {
            $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

            Log::channel('daily')->error('Unhandled exception', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'status' => $status,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        });

        $exceptions->render(function (\Throwable $exception, $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $message = $status >= 500
                ? 'Terjadi kesalahan pada server. Silakan coba lagi.'
                : ($exception->getMessage() ?: 'Permintaan tidak dapat diproses.');

            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        });

        $exceptions->render(function (AuthorizationException|HttpExceptionInterface $e, $request) {
            $isForbidden = $e instanceof AuthorizationException
                || ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 403);

            if (! $isForbidden || $request->expectsJson()) {
                return null;
            }

            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')->with('error', 'Sesi Anda berakhir karena aksi tidak diizinkan.');
        });
    })->create();
