<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Pagination\Paginator;
use Tben\LaravelJsonAPI\Facades\JsonMeta;

class EloquentPagination
{
    public static function handle(Paginator $data): array
    {
        JsonMeta::addMetaNotation('page', [
            'current' => $data->currentPage(),
            'size' => $data->perPage(),
            'next' => $data->hasMorePages(),
        ]);
        
        return EloquentCollection::handle(collect($data->items()));
    }
}
