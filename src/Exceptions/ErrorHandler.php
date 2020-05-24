<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use Throwable;
use App\Exceptions\Handler;

class ErrorHandler extends Handler
{
    protected $errorResponse = [
        'Exception' => 'Tben\\LaravelJsonAPI\\Transformer\\Errors\\Exception'
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
        if ($this->shouldReport($e)) {
            parent::report($e);
        }
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
        $classType = \get_class($e);

        if (array_key_exists($classType, $this->errorResponse)) {
            $class = $this->errorResponse[$classType];

            return (new $class())->handle($e);
        }

        return response()->json([
            "error" => $e->getMessage()
        ]);
    }
}