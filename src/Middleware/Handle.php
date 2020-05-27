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
            'Tben\LaravelJsonAPI\Exceptions\ErrorHandler'
        );

        $response = $next($request);
        $class = get_class($response);

        if ($class == 'Illuminate\Http\Response' || $class == 'Illuminate\Http\JsonResponse') {
            return $response;
        }

        if (array_key_exists($class, $this->objectResponse)) {
            $class = $this->defaultClass . '\\' . $replacement;

            return (new $class())->handle($response)->header("Content-Type", "application/vnd.api+json");
        }


        foreach ($this->objectResponse as $check => $transformerClass) {
            if (is_subclass_of($class, $check)) {
                return (new $class())->handle($response)->header("Content-Type", "application/vnd.api+json");
            }
        }

        throw new \Exception("Don't know how to handle this!");
    }
}