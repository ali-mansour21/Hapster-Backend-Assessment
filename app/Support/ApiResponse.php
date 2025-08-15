<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data, ?string $message = null, int $statusCode = 200, array $extra = []): JsonResponse
    {
        $body = ['status' => 'success'];
        if (!is_null($data)) $body['data'] = $data;
        if ($message) $body['message'] = $message;
        if ($extra) $body = array_merge($body, $extra);
        return response()->json($body, $statusCode);
    }
    public static function fail(string $message, int $statusCode = 400, array $errors = []): JsonResponse
    {
        $body = ['status' => 'failed', 'message' => $message];
        if (!empty($errors)) $body['errors'] = $errors;
        return response()->json($body, $statusCode);
    }
}
