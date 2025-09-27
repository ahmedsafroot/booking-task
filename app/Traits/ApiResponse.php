<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{

    public function success($data = [], $message = 'Success', $status = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function error($message = 'Error', $status = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function validationError($errors, $message = 'Validation Error'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    public function notFound($message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    public function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, 401);
    }

}
