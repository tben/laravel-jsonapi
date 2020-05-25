<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\Response;

class UnknownException
{
    public function render(Throwable $e) : Response
    {
        return response()->json(
            [
                'errors' => [
                    [
                        "status" => 400,
                        "source" => ["pointer" => "data"],
                        "detail" => "Unknown error found",
                        "attribute" => "message",
                        "message" => $e->getMessage(),
                    ],
                ],
            ],
            422
        );
    }
}