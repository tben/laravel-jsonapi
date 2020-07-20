<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class UnknownException
{
    public function handle(Throwable $e) : JsonResponse
    {
        return response()->json(
            [
                'errors' => [
                    [
                        "status" => 500,
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