<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class Exception
{
    public static function handle(Throwable $e)
    {
        return new JsonApiResponseError([
            new JsonApiError(500, $e->getMessage()),
        ], 500);
    }
}
