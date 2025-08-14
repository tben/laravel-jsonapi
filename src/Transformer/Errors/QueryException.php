<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class QueryException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_INTERNAL_SERVER_ERROR,
                    code: 'DATABASE_ERROR',
                    title: trans('jsonapi::errors.title.database_error'),
                    detail: trans('jsonapi::errors.description.database_error'),
                ),
            )
        )->setStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
