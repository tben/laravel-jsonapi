<?php

/*
 * This file is part of laravel-jsonapi.
 *
 * (c) Ben Tidy <jsonapi@bentidy.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tben\LaravelJsonAPI\Providers;



abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('tben.jsonapi.meta', JWTAuth::class);
    }
}