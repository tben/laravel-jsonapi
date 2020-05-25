<?php

namespace Tben\LaravelJsonAPI\Middleware;

use Closure;
use Tben\LaravelJsonAPI\JSONMeta;

class Handle
{
    protected $defaultClass = "Tben\LaravelJsonAPI\Transformer\Response";

    protected $objectResponse = [
        'Illuminate\Database\Eloquent\Model' => 'EloquentModel',
        'Illuminate\Database\Eloquent\Collection' => 'EloquentCollection',
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
        $classType = get_class($response);

        if ($classType == "Illuminate\Http\Response" || $classType == "Illuminate\Http\JsonResponse") {
            return $response;
        }

        if (array_key_exists($classType, $this->objectResponse)) {
            $class = $this->objectResponse[$classType];
            return (new $class())->handle($response)->header("Content-Type", "application/vnd.api+json");
        }

        throw new \Exception("Don't know how to handle this!");
    }
}