<?php

use Tben\LaravelJsonAPI\JsonApiResponse;

if (! function_exists('jsonapi')) {
    /**
     * Return a new response from the application.
     *
     * @param  \Illuminate\View\View|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function jsonapi()
    {
        return new JsonApiResponse();
    }
}