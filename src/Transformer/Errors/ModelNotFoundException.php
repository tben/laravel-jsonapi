<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Tben\LaravelJsonAPI\JsonApiError;

class ModelNotFoundException
{
    public static function handle(Throwable $e)
    {
        return response()->jsonapierror([
            new JsonApiError(404, "Model not found"),
        ], 404);
    }
}
