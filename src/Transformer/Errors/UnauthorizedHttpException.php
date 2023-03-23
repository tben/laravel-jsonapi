<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\HandleResponse;

class UnauthorizedHttpException
{
    public static function handle()
    {
        return HandleResponse::make(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_UNAUTHORIZED,
                    code: Response::HTTP_UNAUTHORIZED,
                    title: trans('jsonapi::errors.title.unauthorised'),
                    detail: trans('jsonapi::errors.description.unauthorised'),
                ),
            ),
            Response::HTTP_UNAUTHORIZED
        );
    }
}
