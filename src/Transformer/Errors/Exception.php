<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

use Throwable;
use Illuminate\Http\Response;

class Exception
{
    public function handle(Throwable $e) : Response
    {
        return response()->json(
            [
                'errors' => [
                    [
                        "status" => 422,
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