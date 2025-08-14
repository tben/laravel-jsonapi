<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class ServiceUnavailableHttpException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_SERVICE_UNAVAILABLE,
                    code: 'SERVICE_UNAVAILABLE',
                    title: trans('jsonapi.errors.title.service_unavailable'),
                    detail: trans('jsonapi.errors.description.service_unavailable'),
                ),
            )
        )->setStatus(Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
