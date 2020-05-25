<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException
{
    public function handle(Throwable $e) : JsonResponse
    {
        return response()->json(
            ["errors" => "Model not found!"],
            404
        );
    }
}