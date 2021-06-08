<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Tben\LaravelJsonAPI\JsonApiError;

class UnauthorizedHttpException
{
    public static function handle(Throwable $e)
    {
        return response()->jsonapierror([
            new JsonApiError(401, "Unauthorized"),
        ], 401);
    }
}
