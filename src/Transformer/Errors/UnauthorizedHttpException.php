<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class UnauthorizedHttpException
{
    public function handle(Throwable $e) : JsonResponse
    {
        return response()->json(
            [
                "errors" => [
                    [
                        "status" => "401",
                        "title"=> "Unauthorized"
                    ]
                ]
            ],
            401
        );
    }
}