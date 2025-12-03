<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    /**
     * Success response with data.
     */
    protected function success(mixed $data = null, int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $status);
    }

    /**
     * Error response.
     */
    protected function error(string $message, array $stack = [], int $status = 400): JsonResponse
    {
        return ApiResponse::error($message, $stack, $status);
    }

    /**
     * Validation error response.
     */
    protected function validationError(array $errors): JsonResponse
    {
        return ApiResponse::validationError($errors);
    }

    /**
     * Not found error response.
     */
    protected function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    /**
     * Unauthorized error response.
     */
    protected function unauthorized(string $message = 'Unauthorized.'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }
}

