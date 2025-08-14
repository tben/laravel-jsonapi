<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class GoneHttpException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_GONE,
                    code: 'GONE',
                    title: trans('jsonapi::errors.title.gone'),
                    detail: trans('jsonapi::errors.description.gone'),
                ),
            )
        )->setStatus(Response::HTTP_GONE);
    }
}
