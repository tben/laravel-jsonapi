<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class Exception
{
    public function handle(Throwable $e) : JsonResponse
    {
        return response()->json(
            [
                'errors' => [
                    [
                        "status" => 422,
                        "source" => ["pointer" => "data"],
                        "detail" => "Unknown error found",
                        "attribute" => "message",
                        "message" => $e->getMessage(),
                    ],
                ],
            ],
            422
        );
    }
}