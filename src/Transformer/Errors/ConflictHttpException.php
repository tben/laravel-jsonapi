<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class ConflictHttpException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_CONFLICT,
                    code: 'CONFLICT',
                    title: trans('jsonapi::errors.title.conflict'),
                    detail: trans('jsonapi::errors.description.conflict'),
                ),
            )
        )->setStatus(Response::HTTP_CONFLICT);
    }
}
