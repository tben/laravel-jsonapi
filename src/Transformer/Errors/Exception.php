<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\HandleResponse;

class Exception
{
    public static function handle()
    {
        return HandleResponse::make(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_INTERNAL_SERVER_ERROR,
                    code: Response::HTTP_INTERNAL_SERVER_ERROR,
                    title: trans('jsonapi::errors.title.unhandled_exception'),
                ),
            ),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
