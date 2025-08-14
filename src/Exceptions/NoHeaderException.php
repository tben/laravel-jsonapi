<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class NoHeaderException extends Exception
{
    protected $message = 'The Accept header must be set to "application/vnd.api+json"';

    public function render()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_BAD_REQUEST,
                    code: 'BAD_REQUEST',
                    title: $this->message,
                    detail: $this->getMessage(),
                ),
            )
        )
        ->setStatus(Response::HTTP_BAD_REQUEST);
    }
}
