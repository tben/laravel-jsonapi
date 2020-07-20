<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException
{
    public function handle(Throwable $e) : JsonResponse
    {
        return response()->json(
            [
                "errors" => [
                    [
                        "status" => 404,
                        "sources" => [
                            "pointer" => "",
                        ],
                        "title" => "Model not found",
                        "detail" => "The model cannot be found"
                    ]
                ]
            ],
            404
        );
    }
}