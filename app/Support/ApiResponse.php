<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Operación exitosa.',
        int $status = 200,
        array $meta = [],
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    public static function error(
        string $message,
        int $status = 400,
        ?string $code = null,
        mixed $errors = null,
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($code !== null) {
            $payload['error']['code'] = $code;
        }

        if ($errors !== null) {
            $payload['error']['details'] = $errors;
        }

        return response()->json($payload, $status);
    }

    public static function resource(
        JsonResource|ResourceCollection $resource,
        string $message = 'Operación exitosa.',
        int $status = 200,
    ): JsonResponse {
        $data = $resource->response()->getData(true);

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data['data'] ?? $data,
        ];

        if (isset($data['meta'])) {
            $response['meta'] = $data['meta'];
        }

        if (isset($data['links'])) {
            $response['links'] = $data['links'];
        }

        return response()->json($response, $status);
    }

    public static function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Listado obtenido correctamente.',
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ], $status);
    }
}
