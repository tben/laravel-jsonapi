<?php

/*
 * This file is part of laravel-jsonapi.
 *
 * (c) Ben Tidy <jsonapi@bentidy.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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