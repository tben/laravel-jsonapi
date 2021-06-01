<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Illuminate\Validation\ValidationException as Exception;
use Tben\LaravelJsonAPI\JsonApiError;

class ValidationException
{
    public static function handle(Exception $e)
    {
        $errors = [];
        foreach ($e->errors() as $index => $message) {
            $messages = collect($message);

            $errors[] = new JsonApiError(422, $message[0] ?? 'unknown', null, null, $index);
        }

        return response()->jsonapierror(
            $errors,
            422
        );
    }
}
