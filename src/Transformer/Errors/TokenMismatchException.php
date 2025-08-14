<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;
use Tben\LaravelJsonAPI\JsonApi;

class TokenMismatchException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: 419,
                    code: 'TOKEN_MISMATCH',
                    title: trans('jsonapi::errors.title.token_mismatch'),
                    detail: trans('jsonapi::errors.description.token_mismatch'),
                ),
            )
        )->setStatus(419);
    }
}
