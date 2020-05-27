<?php

namespace Tben\LaravelJsonAPI\Facades;

use Illuminate\Support\Facades\Facade;

class JSONMeta extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tben.jsonapi.meta';
    }
}