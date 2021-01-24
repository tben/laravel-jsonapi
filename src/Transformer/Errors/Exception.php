<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Tben\LaravelJsonAPI\JsonApiError;

class Exception
{
    public static function handle(Throwable $e)
    {
        return response()->jsonapierror([
            new JsonApiError(500, $e->getMessage()),
        ], 500);
    }
}
