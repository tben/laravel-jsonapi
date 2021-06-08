<?php

namespace Tben\LaravelJsonAPI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void addMetaNotation(string $key, mixed $value)
 * @method static void addArray(array $array)
 * @method static null|array viewMetaAll()
 *
 * @see Tben\LaravelJsonAPI\JsonMeta
 */
class JsonMeta extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tben.laraveljsonapi.jsonmeta';
    }
}
