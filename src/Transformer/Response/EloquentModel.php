<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

class EloquentModel
{
    public function handle($request)
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