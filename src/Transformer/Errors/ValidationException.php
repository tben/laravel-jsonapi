<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Illuminate\Validation\ValidationException as Exception;
use Tben\LaravelJsonAPI\JsonApiError;
use Tben\LaravelJsonAPI\JsonApiResponseError;

class ValidationException
{
    public static function handle(Exception $e)
    {
        $errors = [];
        foreach ($e->errors() as $index => $message) {
            $errors[] = new JsonApiError(422, $message[0] ?? 'unknown', null, null, $index);
        }

        return new JsonApiResponseError($errors, 422);
    }
}
