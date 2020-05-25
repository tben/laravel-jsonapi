<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

class EloquentCollection
{
    public function handle($request)
    {
        return response()->json(
            [
                "data" => $request->original->map(function ($collection) {
                    if (\is_a($collection, '\Illuminate\Database\Eloquent\Model')) {
                        return $collection->toJsonApiArray();
                    }
                }),
                "included" => $request->original->flatMap(function ($collection) {
                    if (\is_a($collection, '\Illuminate\Database\Eloquent\Model')) {
                        return $collection->getJsonApiIncludes();
                    }
                }),
            ],
            200
        );
    }
}