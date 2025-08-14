<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Illuminate\Validation\ValidationException as ValidException;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\JsonApi;
use Tben\LaravelJsonAPI\JsonApiErrors;
use Tben\LaravelJsonAPI\JsonSingleError;

class ValidationException
{
    public static function handle(ValidException $e)
    {
        $errors = [];
        foreach ($e->errors() as $pointer => $message) {
            $errors[] = new JsonSingleError(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                code: 'UNPROCESSABLE_ENTITY',
                title: trans('jsonapi.errors.title.unprocessable_entity'),
                detail: $message[0] ?? trans('jsonapi.errors.description.unprocessable_entity'),
                source: [
                    'pointer' => self::convertPointToUrl((string) $pointer),
                ]
            );
        }

        return JsonApi::response(
            new JsonApiErrors($errors),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public static function convertPointToUrl(string $pointer)
    {
        return '/' . str_replace('.', '/', $pointer);
    }
}
