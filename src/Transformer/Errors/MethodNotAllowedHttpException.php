<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class MethodNotAllowedHttpException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_METHOD_NOT_ALLOWED,
                    code: 'METHOD_NOT_ALLOWED',
                    title: trans('jsonapi::errors.title.method_not_allowed'),
                    detail: trans('jsonapi::errors.description.method_not_allowed'),
                ),
            )
        )->setStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
