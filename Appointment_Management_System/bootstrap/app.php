<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | Validation Exception
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ): ?Response {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return null;
        });

        /*
        |--------------------------------------------------------------------------
        | Token Expired
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            TokenExpiredException $e,
            Request $request
        ): ?Response {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Token has expired'
                ], 401);
            }

            return null;
        });

        /*
        |--------------------------------------------------------------------------
        | Invalid Token
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            TokenInvalidException $e,
            Request $request
        ): ?Response {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            return null;
        });

        /*
        |--------------------------------------------------------------------------
        | Token Missing
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            JWTException $e,
            Request $request
        ): ?Response {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided'
                ], 401);
            }

            return null;
        });

        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ): ?Response {

            if ($request->is('api/*')) {

                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please log in again.'
                ], 401);
            }

            return null;
        });
    })

    ->create();
