<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\HandleResponse;

class NotFoundHttpException
{
    public static function handle()
    {
        return HandleResponse::make(
            new JsonApiErrors(
                new JsonSingleError(
                    status: Response::HTTP_NOT_FOUND,
                    code: Response::HTTP_NOT_FOUND,
                    title: trans('jsonapi::errors.title.page_not_found'),
                ),
            ),
            Response::HTTP_NOT_FOUND
        );
    }
}
