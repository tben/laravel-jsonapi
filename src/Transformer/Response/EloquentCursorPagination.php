<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Pagination\CursorPaginator;

/**
 * Undocumented class
 * 
 * @todo Set up meta
 */
class EloquentCursorPagination
{
    public static function handle(CursorPaginator $data): array
    {
        return EloquentCollection::handle(collect($data->items()));
    }
}
