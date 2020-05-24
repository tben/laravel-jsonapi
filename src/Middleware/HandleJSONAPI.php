<?php

namespace Tben\LaravelJsonAPI\Middleware;

use Closure;
use Tben\LaravelJsonAPI\JSONMeta;

class JSONAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->singleton(
            Illuminate\Contracts\Debug\ExceptionHandler::class,
            Tben\LaravelJsonAPI\Exceptions\JsonApiHandler::class
        );

        $response = $next($request);
    }
}