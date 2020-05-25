<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Throwable;
use App\Exceptions\Handler;

class ErrorHandler extends Handler
{
    protected $defaultClass = "Tben\LaravelJsonAPI\Transformer\Errors";

    protected $errorResponse = [
        'Exception' => 'Exception',
        'Illuminate\Auth\Access\AuthorizationException' => 'AuthorizationException',
        'Illuminate\Database\Eloquent\ModelNotFoundException' => 'ModelNotFoundException',
        'Illuminate\Validation\ValidationException' => 'ValidationException',

    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        $classType = get_class($e);

        if (array_key_exists($classType, $this->errorResponse)) {
            $class = $this->defaultClass . '\\' . $this->errorResponse[$classType];
        } else {
            $class = $this->defaultClass . '\\' . 'UnknownException';
        }

        return (new $class())->handle($e)->header("Content-Type", "application/vnd.api+json");
    }
}