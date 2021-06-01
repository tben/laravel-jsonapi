<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Support\Collection as CollectionObject;
use Tben\LaravelJsonAPI\Facades\JsonMeta;

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

        $response['meta'] = JsonMeta::viewMetaAll();

        if ($response['meta'] == null) {
            unset($response['meta']);
        }

        return $response;
    }
}
