<?php

namespace Tben\LaravelJsonAPI;

class JsonApi
{
    public static function builder(...$args): JsonApiBuilder
    {
        return JsonApiBuilder::for(...$args);
    }

    public static function relationship(...$args): JsonApiRelationship
    {
        return new JsonApiRelationship(...$args);
    }

    public static function response(...$args): JsonApiResponse
    {
        return new JsonApiResponse(...$args);
    }
}
