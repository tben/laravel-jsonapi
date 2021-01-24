<?php

namespace Tben\LaravelJsonAPI\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Tben\LaravelJsonAPI\JsonApiResponse;
use Tben\LaravelJsonAPI\JsonApiResponseError;
use Tben\LaravelJsonAPI\JsonMeta;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        ResponseFactory::macro('jsonapi', function ($data = [], $status = 200, array $headers = [], $options = 0) {
            return new JsonApiResponse($data, $status, $headers, $options);
        });

        ResponseFactory::macro('jsonapierror', function ($data = [], $status = 200, array $headers = [], $options = 0) {
            return new JsonApiResponseError($data, $status, $headers, $options);
        });
    }

    public function register()
    {
        App::bind('tben.laraveljsonapi.jsonmeta', function () {
            return new JsonMeta();
        });
    }
}
