<?php

namespace Tben\LaravelJsonAPI\Transformer\Errors;

class Exception
{
    public function handle($e)
    {
        return response("Error", 201);
    }
}