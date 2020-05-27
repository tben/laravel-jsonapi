<?php

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