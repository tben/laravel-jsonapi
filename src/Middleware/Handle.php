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

        if ($response->exception !== null) {
            throw $response->exception;
        } else if (is_array($response->original)) {
            return $response->header("Content-Type", "application/vnd.api+json");
        } else if (is_object($response->original)) {
            $className = get_class($response->original);

            if (array_key_exists($className, $this->objectResponse)) {
                $class = $this->defaultClass . '\\' .  $this->objectResponse[$className];
    
                return (new $class())->handle($response)->header("Content-Type", "application/vnd.api+json");
            }
    
            foreach ($this->objectResponse as $check => $transformerClass) {
                $class = $this->defaultClass . '\\' . $transformerClass;
    
                if (is_subclass_of($className, $check)) {
                    return (new $class())->handle($response)->header("Content-Type", "application/vnd.api+json");
                }
            }
    
            throw new \Exception("Don't know how to handle this!");
        } else {
            return $response->header("Content-Type", "application/vnd.api+json");
        }
    }
}