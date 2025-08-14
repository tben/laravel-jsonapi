<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class AuthenticationException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_UNAUTHORIZED,
                    code: 'UNAUTHORIZED',
                    title: trans('jsonapi::errors.title.unauthorised'),
                    detail: trans('jsonapi::errors.description.unauthorised'),
                ),
            )
        )->setStatus(Response::HTTP_UNAUTHORIZED);
    }
}
