<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class ModelNotFoundException
{
    public static function handle()
    {
        return new JsonApiResponseError([
            new JsonApiError(404, 'Model not found'),
        ], 404);
    }
}
