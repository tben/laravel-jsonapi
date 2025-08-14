<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class TooManyRequestsHttpException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_TOO_MANY_REQUESTS,
                    code: 'TOO_MANY_REQUESTS',
                    title: trans('jsonapi.errors.title.too_many_requests'),
                    detail: trans('jsonapi.errors.description.too_many_requests'),
                ),
            )
        )->setStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
