<?php

namespace Tben\LaravelJsonAPI;

use Throwable;
use Tben\LaravelJsonAPI\Transformer\Errors\Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class JsonApiErrorHandling extends ExceptionHandler
{
    protected $errorResponse = [
        'Illuminate\Auth\Access\AuthorizationException' =>
            __NAMESPACE__ . '\Transformer\Errors\AuthorizationException',
        'Illuminate\Database\Eloquent\ModelNotFoundException' =>
            __NAMESPACE__ . '\Transformer\Errors\ModelNotFoundException',
        'Illuminate\Validation\ValidationException' =>
            __NAMESPACE__ . '\Transformer\Errors\ValidationException',
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' =>
            __NAMESPACE__ . '\Transformer\Errors\NotFoundHttpException',
        'Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException' =>
            __NAMESPACE__ . '\Transformer\Errors\UnauthorizedHttpException',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($r, Throwable $e)
    {
        // Check whether exception has toJsonError handling
        if (method_exists($e, "toJsonError")) {
            return $e->toJsonError();
        }

        // TODO:Check whether user defined responses exist

        // Check default error responses
        if (array_key_exists(get_class($e), $this->errorResponse)) {
            return $this->errorResponse[get_class($e)]::handle($e);
        }

        // If all else fails then throw them the standard errors
        return Exception::handle($e);
    }
}
