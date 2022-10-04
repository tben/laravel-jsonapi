<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Illuminate\Validation\ValidationException as ValidException;
use Symfony\Component\HttpFoundation\Response;
use Tben\LaravelJsonAPI\HandleResponse;
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
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
                title: 'Validation Error',
                detail: $message[0] ?? 'unknown',
                source: [
                    'pointer' => self::convertPointToUrl((string) $pointer),
                ]
            );
        }

        return HandleResponse::make(
            new JsonApiErrors($errors),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public static function convertPointToUrl(string $pointer)
    {
        return '/' . str_replace('.', '/', $pointer);
    }
}
