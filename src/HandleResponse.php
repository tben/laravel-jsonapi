<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Support\Arr;
use Tben\LaravelJsonAPI\Exceptions\CannotTransformException;
use Tben\LaravelJsonAPI\Facades\JsonMeta;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentCollection;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentModel;
use Tben\LaravelJsonAPI\Transformer\Response\EloquentPagination;
use Tben\LaravelJsonAPI\Transformer\Response\Collection;

class HandleResponse
{
    public static function make(mixed $data = [], $status = 200, $headers = [])
    {
        if (is_string($data)) {
            $data = collect(Arr::wrap($data));
        }

        if (is_array($data)) {
            $data = collect($data);
        }

        // Transform various object type
        $response = match(true) {
            ($data === null) => ['data' => null],
            ($data instanceof JsonApiErrors) => $data->toArray(),
            ($data instanceof \Illuminate\Database\Eloquent\Collection) => EloquentCollection::handle($data),
            ($data instanceof \Illuminate\Database\Eloquent\Model) => EloquentModel::handle($data),
            ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) => EloquentPagination::handle($data),
            ($data instanceof \Illuminate\Support\Collection) => Collection::handle($data),
            default => throw new CannotTransformException()
        };

        // Add Metadata to response
        $response['meta'] = JsonMeta::viewMetaAll();
        if ($response['meta'] == null) {
            unset($response['meta']);
        }

        return response()->json($response, $status, $headers, 0);
    }
}
