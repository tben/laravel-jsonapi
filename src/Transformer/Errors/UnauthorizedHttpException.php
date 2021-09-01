<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class UnauthorizedHttpException
{
    public static function handle()
    {
        return new JsonApiResponseError([
            new JsonApiError(401, 'Unauthorized'),
        ], 401);
    }
}
