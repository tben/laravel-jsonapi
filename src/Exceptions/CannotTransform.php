<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Exception;
use Tben\LaravelJsonAPI\JsonApiError;

class CannotTransform extends Exception
{
    protected $message = 'Internal error';

    public function toJsonError()
    {
        return response()->jsonapierror([
            new JsonApiError(500, $this->getMessage()),
        ], 500);
    }
}
