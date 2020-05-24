<?php

namespace Tben\LaravelJsonAPI\Middleware;

use Closure;
use Tben\LaravelJsonAPI\JSONMeta;

class Handle
{
    protected $objectResponse = [
        'Illuminate\Http\JsonResponse' => 'Tben\\LaravelJsonAPI\\Transformer\\Response\\JsonResponse'
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
        $classType = \get_class($response);

        if ($classType == "Illuminate\Http\Response") {
            return $response;
        }

        if (array_key_exists($classType, $this->objectResponse)) {
            $class = $this->objectResponse[$classType];

            return (new $class())->handle($response);
        }

        return response()->json([
            "error" => "Cannot handle website"
        ]);
    }
}