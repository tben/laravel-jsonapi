<?php

namespace Tben\LaravelJsonAPI\Exceptions;

use App\Exceptions\Handler;

class JsonApiHandler extends Handler
{
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
        return response()
            ->view('hello', $data, 200)
            ->header('Content-Type', $type);

        return parent::render($request, $e);
    }
}