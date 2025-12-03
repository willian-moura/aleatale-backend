<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Success response with data.
     */
    public static function success(mixed $data = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'total_pages' => $data->lastPage(),
            ];
        } elseif (is_string($data)) {
            $response['data']['message'] = $data;
        }
        elseif ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Error response.
     */
    public static function error(string $message, array $stack = [], int $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'stack' => $stack,
            ],
        ];

        return response()->json($response, $status);
    }

    /**
     * Validation error response.
     */
    public static function validationError(array $errors): JsonResponse
    {
        return self::error(
            message: 'Validation failed.',
            stack: $errors,
            status: 422
        );
    }

    /**
     * Not found error response.
     */
    public static function notFound(string $message = 'Resource not found.'): JsonResponse
    {
        return self::error($message, [], 404);
    }

    /**
     * Unauthorized error response.
     */
    public static function unauthorized(string $message = 'Unauthorized.'): JsonResponse
    {
        return self::error($message, [], 401);
    }
}

