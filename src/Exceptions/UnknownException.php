<?php

namespace Tben\LaravelJsonAPI\Exceptions;

class UnknownException
{
    public function render()
    {
        return response()->json(
            [
                'errors' => [
                    [
                        "status" => 400,
                        "source" => [
                            "pointer" => "data"
                        ],
                        "detail" => "Unknown error found",
                        "attribute" => "message",
                        "message" => $exception->getMessage(),
                    ],
                ],
            ],
            422
        )->header("Content-Type", "application/vnd.api+json");
    }
}