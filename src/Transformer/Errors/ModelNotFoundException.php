<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class ModelNotFoundException
{
    public static function handle()
    {
        return JsonApi::response(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_NOT_FOUND,
                    code: 'MODEL_NOT_FOUND',
                    title: trans('jsonapi::errors.title.model_not_found'),
                    detail: trans('jsonapi::errors.description.model_not_found'),
                ),
            )
        )->setStatus(Response::HTTP_NOT_FOUND);
    }
}
