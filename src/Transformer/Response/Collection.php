<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Support\Collection as CollectionObject;

class Collection
{
    public static function handle(CollectionObject $collection): array
    {
        $response = [];

        if (count($collection) == 0) {
            $response["data"] = [];
        } else {
            $response["data"] = $collection->toArray();
        }

        return $response;
    }
}
