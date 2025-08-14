<?php

namespace Tben\LaravelJsonAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tben\LaravelJsonAPI\HandleErrors;

class Handle
{
    public function handle(Request $request, Closure $next): mixed
    {
        $acceptable = $request->getAcceptableContentTypes();

        if (isset($acceptable[0]) && 'application/vnd.api+json' === $acceptable[0]) {
            $handler = app()->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);
            if (! method_exists($handler, 'renderable')) {
                return $next($request);
            }
            $handler->renderable(new HandleErrors);
        }

        return $next($request);
    }
}
