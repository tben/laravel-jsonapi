<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

use Illuminate\Http\JsonResponse;

class EloquentModel
{
    public function handle($request) : JsonResponse
    {
        return response()->json(
            [
                'data' => $request->original->toJsonApiArray(),
                'included' => $request->original->getJsonApiIncludes(),
            ],
            200
        );
    }
}