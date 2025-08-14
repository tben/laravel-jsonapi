<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Illuminate\Auth\Access\AuthorizationException as AccessAuthorizationException;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class AuthorizationException
{
    public static function handle(AccessAuthorizationException $ex)
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: $ex->status(),
                    code: 'FORBIDDEN',
                    title: trans('jsonapi::errors.title.forbidden'),
                    detail: trans('jsonapi::errors.description.forbidden'),
                ),
            )
        )->setStatus($ex->status());
    }
}
