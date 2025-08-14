<?php

namespace Tben\LaravelJsonAPI\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Tben\LaravelJsonAPI\JsonApi;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        ResponseFactory::macro(
            'jsonapi',
            fn (mixed $data = [], $status = 200, array $headers = []) => JsonApi::response($data)
                ->setStatus($status)
                ->withHeaders($headers)
        );

        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'jsonapi');
 
        $this->publishes([
            __DIR__.'/../../lang' => $this->app->langPath('vendor/jsonapi'),
        ]);
    }
}
