<?php

namespace Tben\LaravelJsonAPI\Exceptions\Errors;

use Throwable;

class UnknownException
{
    public function render(Throwable $e)
    {
        return response()->json(
            [
                'errors' => [
                    [
                        "status" => 400,
                        "source" => ["pointer" => "data"],
                        "detail" => "Unknown error found",
                        "attribute" => "message",
                        "message" => $exception->getMessage(),
                    ],
                ],
            ],
            422
        );
    }
}