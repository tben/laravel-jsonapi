<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Tben\LaravelJsonAPI\JsonApiError;

class NotFoundHttpException
{
    public static function handle(Throwable $e)
    {
        return response()->jsonapierror([
            new JsonApiError(404, "Page not found!"),
        ], 404);
    }
}
