<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['admin' => EnsureUserIsAdmin::class]);
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));

        // Pengecualian CSRF untuk endpoint API tertentu.
        $middleware->validateCsrfTokens(except: [
            'api/v1/tanaman-tanaman',
            'api/v1/jadwal-perawatan/*/complete',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Respons untuk pengguna yang belum login.
        $exceptions->render(function (
            AuthenticationException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return null;
        });

        // Respons validasi khusus API.
        $exceptions->render(function (
            ValidationException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Data yang diberikan tidak valid.',
                    'errors' => $exception->errors(),
                ], 422);
            }

            return null;
        });
    })
    ->create();
