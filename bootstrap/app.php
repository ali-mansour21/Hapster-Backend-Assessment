<?php

use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->renderable(function (ModelNotFoundException $e, $request) {
        if ($request->is('api/*')) {
            $name = strtolower(class_basename($e->getModel() ?? 'Resource'));
            return ApiResponse::fail("{$name} not found.", 404);
        }
    });

    // 404 for bad endpoints (wrong route)
    $exceptions->renderable(function (NotFoundHttpException $e, $request) {
        if ($request->is('api/*')) {
            return ApiResponse::fail('Endpoint not found.', 404);
        }
    });

    // Uniform validation errors (if any slip past your FormRequests)
    $exceptions->renderable(function (ValidationException $e, $request) {
        if ($request->is('api/*')) {
            return ApiResponse::fail('Validation failed.', 422, $e->errors());
        }
    });
    })->create();
