<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    protected function ok(mixed $data = null, ?string $message = null, array $extra = [])
    {
        return ApiResponse::success($data, $message, 200, $extra);
    }
    protected function created(mixed $data = null, ?string $message = null, array $extra = [])
    {
        return ApiResponse::success($data, $message, 201);
    }
    protected function fail(string $message, int $status = 400, array $errors = [])
    {
        return ApiResponse::fail($message, $status, $errors);
    }
}
