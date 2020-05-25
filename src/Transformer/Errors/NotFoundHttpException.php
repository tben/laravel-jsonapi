<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class NotFoundHttpException
{
    public function handle(Throwable $e) : JsonResponse
    {
        return response()->json(
            [
                "errors" => [
                    [
                        "status" => 404,
                        "detail" => "Page not found!"
                    ]
                ]
            ],
            404
        );
    }
}