<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Support\Arr;
use Tben\LaravelJsonAPI\Exceptions\CannotTransformException;
use Tben\LaravelJsonAPI\Facades\JsonMeta;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentCollection;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentModel;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentPagination;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentLengthAwarePagination;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentCursorPagination;
use Tben\LaravelJsonAPI\Transformer\Response\Collection;

class HandleResponse
{
    public static function make(mixed $data = [], int $status = 200, array $headers = [])
    {
        if (is_string($data)) {
            $data = collect(Arr::wrap($data));
        }

        // If it's a raw array, check if it's already structured or convert to collection
        if (is_array($data)) {
            $data = isset($data['data']) ? $data : collect($data);
        }

        // Transform various object types
        $response = match (true) {
            ($data === null) => ['data' => null],
            is_array($data) => $data, // Already structured array (e.g. custom error or pre-formatted payload)
            ($data instanceof JsonApiErrors) => $data->toArray(),
            ($data instanceof \Illuminate\Database\Eloquent\Collection) => EloquentCollection::handle($data),
            ($data instanceof \Illuminate\Database\Eloquent\Model) => EloquentModel::handle($data),
            ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) => EloquentLengthAwarePagination::handle($data),
            ($data instanceof \Illuminate\Pagination\Paginator) => EloquentPagination::handle($data),
            ($data instanceof \Illuminate\Pagination\CursorPaginator) => EloquentCursorPagination::handle($data),
            ($data instanceof \Illuminate\Support\Collection) => Collection::handle($data),
            default => throw new CannotTransformException()
        };

        // Add Metadata to response if present
        $meta = JsonMeta::viewMetaAll();
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        // Ensure proper JSON:API content-type header
        if (empty($headers['content-type'])) {
            $headers['content-type'] = 'application/vnd.api+json';
        }

        // Use clean JSON flags instead of 0
        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        return response()->json($response, $status, $headers, $jsonFlags);
    }
}
