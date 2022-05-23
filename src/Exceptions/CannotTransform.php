<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Exception;
use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class CannotTransform extends Exception
{
    protected $message = 'Internal error';

    public function toJsonError()
    {
        return new JsonApiResponseError([
            new JsonApiError(500, $this->getMessage()),
        ], 500);
    }
}
