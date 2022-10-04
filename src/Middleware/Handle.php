<?php

namespace Tben\LaravelJsonAPI\Middleware;

use Closure;

class Handle
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
        if ($request->header('Accept') == 'application/vnd.api+json') {
            app()->bind(
                'Illuminate\Contracts\Debug\ExceptionHandler',
                'Tben\LaravelJsonAPI\HandleErrors'
            );
        }

        return $next($request);
    }
}
