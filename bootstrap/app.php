<?php

use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1) Missing Eloquent model (direct)
        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                $model = $e->getModel() ? class_basename($e->getModel()) : 'Resource';
                return ApiResponse::fail(Str::lower($model) . ' not found.', 404);
            }
        });

        // 2) 404s – could be a wrapped ModelNotFoundException or a real bad route
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                $prev = $e->getPrevious();
                if ($prev instanceof ModelNotFoundException) {
                    $model = $prev->getModel() ? class_basename($prev->getModel()) : 'Resource';
                    return ApiResponse::fail(Str::lower($model) . ' not found.', 404);
                }
                return ApiResponse::fail('Endpoint not found.', 404);
            }
        });
    })->create();
