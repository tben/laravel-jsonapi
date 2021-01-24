<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Exception;
use Tben\LaravelJsonAPI\JsonApiError;

class InvalidIncludes extends Exception
{
    protected $message = 'Parameters were incorrect';

    public function toJsonError()
    {
        return response()->jsonapierror([
            new JsonApiError(500, $this->getMessage()),
        ], 500);
    }
}
