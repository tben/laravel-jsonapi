<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Http\JsonResponse;

class JsonApiResponse extends JsonResponse
{
    public function successful($data, $status = 200, $headers = [])
    {
        $json = [
            "data" => $data
        ];

        return parent::__construct($json, $status, $headers);
    }

    public function error($data, $status = 400, $headers = [])
    {
        $errors = [
            "error" => $data
        ];

        return parent::__construct($json, $status, $headers);
    }
}