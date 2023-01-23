<?php

namespace Tben\LaravelJsonAPI\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Tben\LaravelJsonAPI\HandleResponse;
use Tben\LaravelJsonAPI\MetaStore;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        ResponseFactory::macro('jsonapi', function ($data = [], $status = 200, array $headers = []) {
            // TODO: change to response::json();
            return HandleResponse::make($data, $status, $headers, 0);
        });

        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'jsonapi');
 
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../lang' => $this->app->langPath('vendor/jsonapi'),
            ], 'laravel-jsonapi');
        }
    }

    public function register()
    {
        $this->app->bind('tben.laraveljsonapi.jsonmeta', function () {
            return new MetaStore();
        });
    }
}
