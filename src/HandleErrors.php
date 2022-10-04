<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Tben\LaravelJsonAPI\Transformer\Errors\Exception;
use Throwable;

class HandleErrors extends ExceptionHandler
{
    protected $responses = [
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
     * @inheritDoc
     */
    public function render($request, Throwable $exception)
    {
        // Function to custom Json:Api error response
        if (method_exists($exception, 'toJsonError')) {
            /** @var \Tben\LaravelJsonAPI\JsonException $exception */
            return $exception->toJsonError();
        }

        // TODO:Check whether user defined responses exist

        // Check default error responses
        $exceptionClass = get_class($exception);

        if (array_key_exists($exceptionClass, $this->responses)) {
            return call_user_func($this->responses[$exceptionClass] . '::handle', $exception);
        }

        // If all else fails then throw them the standard errors
        return Exception::handle($exception);
    }
}
