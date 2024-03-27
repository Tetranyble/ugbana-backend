<?php

namespace App\Helpers;

use App\Http\Resources\Ghost\EmptyResource;
use App\Http\Resources\Ghost\EmptyResourceCollection;
use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    protected function respondWithResource(JsonResource $resource, $message = null, int $statusCode = 200, array $headers = []): JsonResponse
    {
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource

        return $this->apiResponse(
            [
                'status' => true,
                'message' => $message,
                'data' => $resource,
            ], $statusCode, $headers
        );
    }

    /**
     * @param  array  $data
     * @param  int  $statusCode
     * @param  array  $headers
     */
    public function parseGivenData($data = [], $statusCode = 200, $headers = []): array
    {
        $responseStructure = [
            'status' => $data['status'],
            'message' => $data['message'] ?? 'failed',
            'data' => $data['data'] ?? [],
        ];
        if (isset($data['errors'])) {
            $responseStructure['errors'] = $data['errors'];
        }

        if (isset($data['exception']) && ($data['exception'] instanceof Error || $data['exception'] instanceof Exception)) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'message' => $data['exception']->getMessage(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            }

            if ($statusCode === 200) {
                $statusCode = 500;
            }
        }

        return ['content' => $responseStructure, 'statusCode' => $statusCode, 'headers' => $headers];
    }

    /*
     *
     * Just a wrapper to facilitate abstract
     */

    /**
     * Return generic json response with the given data.
     */
    protected function apiResponse(array $data = [], int $statusCode = 200, array $headers = []): JsonResponse
    {
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource

        $data = $this->parseGivenData($data, $statusCode, $headers);

        return response()->json(
            $data['content'],
            $data['statusCode'],
            $data['headers']
        );
    }

    /**
     * @param  int  $statusCode
     * @param  array  $headers
     */
    protected function respondWithResourceCollection(ResourceCollection $resourceCollection, $message = null, $statusCode = 200, $headers = []): JsonResponse
    {

        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource

        return $this->apiResponse(
            [
                'status' => true,
                'data' => $resourceCollection->response()->getData(),
            ], $statusCode, $headers
        );
    }

    /**
     * Respond with status.
     */
    protected function respondstatus(string $message = ''): JsonResponse
    {
        return $this->apiResponse(['status' => true, 'message' => $message]);
    }

    /**
     * Respond with created.
     */
    protected function respondCreated(mixed $data): JsonResponse
    {
        return $this->apiResponse($data, 201);
    }

    /**
     * Respond with no content.
     */
    protected function respondNoContent(string $message = 'No Content Found'): JsonResponse
    {
        return $this->apiResponse(['status' => false, 'message' => $message]);
    }

    /**
     * Respond with no content.
     */
    protected function respondNoContentResource(string $message = 'No Content Found'): JsonResponse
    {
        return $this->respondWithResource(new EmptyResource([]), $message);
    }

    /**
     * Respond with no content.
     */
    protected function respondNoContentResourceCollection(string $message = 'No Content Found'): JsonResponse
    {
        return $this->respondWithResourceCollection(new EmptyResourceCollection([]), $message);
    }

    /**
     * Respond with unauthorized.
     */
    protected function respondUnAuthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with error.
     */
    protected function respondError($message, int $statusCode = 400, ?Exception $exception = null): JsonResponse
    {
        return $this->apiResponse(
            [
                'status' => false,
                'message' => $message ?? 'There was an internal error, Pls try again later',
                'exception' => $exception,
            ], $statusCode
        );
    }

    /**
     * Respond with forbidden.
     */
    protected function respondForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     *
     * @param  string  $message
     */
    protected function respondNotFound($message = 'Not Found'): JsonResponse
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     */
    protected function respondInternalError(string $message = 'Internal Error'): JsonResponse
    {
        return $this->respondError($message, 500);
    }

    protected function respondValidationErrors(ValidationException $exception): JsonResponse
    {
        return $this->apiResponse(
            [
                'status' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ],
            422
        );
    }
}
