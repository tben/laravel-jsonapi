<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class ValidationException
{
    public function handle(Throwable $e) : JsonResponse
    {
        $errors = [];
        foreach ($e->errors() as $index => $message) {
            $errors[] = [
                "source" => [
                    "pointer" => '/data/attributes/' . str_replace('.', '/', $index),
                ],
                "detail" => $message,
                "title" => "Invalid Attribute",
                "status" =>  422,
            ];
        }

        return response()->json(
            ["errors" => $errors],
            422
        );
    }
}