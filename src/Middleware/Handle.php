<?php

namespace Tben\LaravelJsonAPI\Middleware;

use Closure;
use \Illuminate\Http\{Response, JsonResponse};
use Tben\LaravelJsonAPI\JSONMeta;

class Handle
{
    protected $defaultClass = "Tben\LaravelJsonAPI\Transformer\Response";

    protected $objectResponse = [
        'EloquentModel' => \Illuminate\Database\Eloquent\Model::class,
        'EloquentCollection' => \Illuminate\Database\Eloquent\Collection::class,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->bind(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            '\Tben\LaravelJsonAPI\Exceptions\ErrorHandler'
        );

        $response = $next($request);

        if ($response->original instanceof Response || $response->original instanceof JsonResponse) {
            return $response;
        }

        foreach ($this->objectResponse as $key => $check) {
            if ($response->original instanceof $check) {
                $class = $this->defaultClass . '\\' . $key;

                return (new $class())->handle($response)->header("Content-Type", "application/vnd.api+json");
            }
        }

        throw new \Exception("Don't know how to handle this!");
    }
}