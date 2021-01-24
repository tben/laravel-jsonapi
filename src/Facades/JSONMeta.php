<?php

namespace Tben\LaravelJsonAPI\Facades;

use Illuminate\Support\Facades\Facade;

class JsonMeta extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tben.laraveljsonapi.jsonmeta';
    }
}
