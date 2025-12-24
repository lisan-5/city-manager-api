<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * successResponse
     *
     * @param  mixed  $data
     */
    protected function successResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * errorResponse
     */
    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    /**
     * paginatedResponse
     *
     * @param  array{data: mixed, meta: mixed}  $paginatedData
     * @param  mixed  $resourceClass
     */
    protected function paginatedResponse(array $paginatedData, string $message = 'Success', $resourceClass = null): JsonResponse
    {
        $data = $resourceClass ? $resourceClass::collection($paginatedData['data']) : $paginatedData['data'];

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => $paginatedData['meta'],
        ]);
    }
}
