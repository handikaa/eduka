<?php

trait ApiResponse
{
    /**
     * Standardized API response format.
     *
     * @param bool $success Indicates if the request was successful.
     * @param string $message A message describing the result of the request.
     * @param mixed|null $data The data to be returned in the response (optional).
     * @param int $statusCode The HTTP status code for the response (default: 200).
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse($success, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function apiSuccess($message, $data = null, $statusCode = 200)
    {
        return $this->apiResponse(true, $message, $data, $statusCode);
    }

    public function apiError($message, $data = null, $statusCode = 500)
    {
        return $this->apiResponse(false, $message, $data, $statusCode);
    }

    public function apiNotFound($message = 'Resource not found', $data = null)
    {
        return $this->apiResponse(false, $message, $data, 404);
    }

    public function apiValidationError($message = 'Validation error', $data = null)
    {
        return $this->apiResponse(false, $message, $data, 422);
    }

    public function apiUnauthorized($message = 'Unauthorized', $data = null)
    {
        return $this->apiResponse(false, $message, $data, 401);
    }

    public function apiForbidden($message = 'Forbidden', $data = null)
    {
        return $this->apiResponse(false, $message, $data, 403);
    }

    public function apiCreated($message = 'Resource created successfully', $data = null)
    {
        return $this->apiResponse(true, $message, $data, 201);
    }

    public function apiUpdated($message = 'Resource updated successfully', $data = null)
    {
        return $this->apiResponse(true, $message, $data, 200);
    }

    public function apiDeleted($message = 'Resource deleted successfully', $data = null)
    {
        return $this->apiResponse(true, $message, $data, 200);
    }

    public function apiRestored($message = 'Resource restored successfully', $data = null)
    {
        return $this->apiResponse(true, $message, $data, 200);
    }
}
