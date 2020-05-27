<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Http\JsonResponse;

class JsonApiResponse
{
    public function successful($data, $status = 200, $headers = [])
    {
        $json = [
            "data" => $data
        ];

        return new JsonResponse($json, $status, $headers);
    }

    public function error($data, $status = 400, $headers = [])
    {
        $json = [
            "error" => $data
        ];

        return new JsonResponse($json, $status, $headers);
    }
}