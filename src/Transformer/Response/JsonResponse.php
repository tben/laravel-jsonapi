<?php

namespace Tben\LaravelJsonAPI\Transformer\Response;

class JsonResponse
{
    public function handle($request)
    {
        return response("Response", 201);
    }
}