<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class NotFoundHttpException
{
    public static function handle()
    {
        return new JsonApiResponseError([
            new JsonApiError(404, 'Page not found!'),
        ], 404);
    }
}
