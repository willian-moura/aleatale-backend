<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle validation exceptions
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Validation failed.',
                    stack: $e->errors(),
                    status: 422
                );
            }
        });

        // Handle authentication exceptions
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Unauthenticated.',
                    stack: [],
                    status: 401
                );
            }
        });

        // Handle model not found exceptions
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Resource not found.',
                    stack: [],
                    status: 404
                );
            }
        });

        // Handle not found exceptions
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Route not found.',
                    stack: [],
                    status: 404
                );
            }
        });

        // Handle all other exceptions
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $stack = config('app.debug') ? [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->take(5)->toArray(),
                ] : [];

                return ApiResponse::error(
                    message: $e->getMessage() ?: 'An unexpected error occurred.',
                    stack: $stack,
                    status: 500
                );
            }
        });
    })->create();
