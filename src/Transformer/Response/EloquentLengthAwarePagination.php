<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Pagination\LengthAwarePaginator;
use Tben\LaravelJsonAPI\Facades\JsonMeta;

class EloquentLengthAwarePagination
{
    public static function handle(LengthAwarePaginator $data): array
    {
        JsonMeta::addMetaNotation('page', [
            'current' => $data->currentPage(),
            'last' => $data->lastPage(),
            'size' => $data->perPage(),
            'total' => $data->total(),
        ]);

        return EloquentCollection::handle(collect($data->items()));
    }
}
