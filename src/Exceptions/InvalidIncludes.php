<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Exception;
use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class InvalidIncludes extends Exception
{
    protected $message = 'Parameters were incorrect';

    public function toJsonError()
    {
        return new JsonApiResponseError([
            new JsonApiError(500, $this->getMessage()),
        ], 500);
    }
}
