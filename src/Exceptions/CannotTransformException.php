<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\HandleResponse;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class CannotTransformException extends Exception
{
    protected $message = 'Unable to convert object into JSON:API response!';

    public function toJsonError()
    {
        return HandleResponse::make(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_INTERNAL_SERVER_ERROR,
                    code: Response::HTTP_INTERNAL_SERVER_ERROR,
                ),
            ),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
