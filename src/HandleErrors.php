<?php

namespace Tben\LaravelJsonAPI;

use Illuminate\Http\Request;
use Tben\LaravelJsonAPI\Transformer\Errors;
use Symfony\Component\HttpKernel\Exception;
use Throwable;

class HandleErrors
{
    protected $responses = [
        \Illuminate\Auth\Access\AuthorizationException::class => Errors\AuthorizationException::class,
        \Illuminate\Auth\AuthenticationException::class => Errors\AuthenticationException::class,
        \Illuminate\Contracts\Filesystem\FileNotFoundException::class => Errors\FileNotFoundException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class => Errors\ModelNotFoundException::class,
        \Illuminate\Database\RecordsNotFoundException::class => Errors\NotFoundHttpException::class,
        \Illuminate\Session\TokenMismatchException::class => Errors\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class => Errors\ValidationException::class,
        Exception\ConflictHttpException::class => Errors\ConflictHttpException::class,
        Exception\GoneHttpException::class => Errors\GoneHttpException::class,
        Exception\MethodNotAllowedHttpException::class => Errors\MethodNotAllowedHttpException::class,
        Exception\NotFoundHttpException::class => Errors\NotFoundHttpException::class,
        Exception\ServiceUnavailableHttpException::class => Errors\ServiceUnavailableHttpException::class,
        Exception\TooManyRequestsHttpException::class => Errors\TooManyRequestsHttpException::class,
        Exception\UnauthorizedHttpException::class => Errors\AuthenticationException::class,
        \Symfony\Component\Routing\Exception\RouteNotFoundException::class => Errors\NotFoundHttpException::class,
        Throwable::class => Errors\Exception::class,
    ];

    public function __invoke(Throwable $ex, Request $request)
    {
        if (method_exists($ex, 'render')) {
            return $ex->render($request);
        }

        $responses = array_merge($this->responses, config('jsonapi.error_responses', []));

        // Check default error responses
        foreach ($responses as $to => $from) {
            if (class_exists($from) && is_a($ex, $to)) {
                return call_user_func([$from, 'handle'], $ex);
            }
        }
    }
}
