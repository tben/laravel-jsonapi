<?php

namespace Tben\LaravelJsonAPI\Tests\Units;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tben\LaravelJsonAPI\HandleErrors;
use Tben\LaravelJsonAPI\Tests\TestCase;
use Throwable;

class ErrorHandlingTest extends TestCase
{

    /**
     * Setup Error handler
     *
     * @param Throwable $exception
     * @return Response
     */
    public function setupErrorHandler(Throwable $exception)
    {
        $container = new Container;
        $errorHandler = new HandleErrors($container);
        $request = new Request;

        return $errorHandler->render($request, $exception);
    }

    /**
     * Assert whether response is valid json
     *
     * @param Response $response
     * @param array $expectedResponse
     * @return void
     */
    public function assertApiJsonResponse(Response $response, $expectedResponse = [], $expectedStatus = 200)
    {
        $this->assertEquals($response->headers->get('content-type', ''), 'application/vnd.api+json');
        $this->assertEquals($response->getContent(), json_encode($expectedResponse));
        $this->assertEquals($response->getStatusCode(), $expectedStatus);
    }

    /**
     * whether the authorization exceptions formatting is correct
     *
     * @return void
     */
    public function testAuthorizationException()
    {
        $response = $this->setupErrorHandler(
            new AuthorizationException('This action is unauthorized.')
        );

        $this->assertApiJsonResponse(
            $response,
            [
                'errors' => [
                    [
                        'id' => null,
                        'status' => 401,
                        'code' => '401',
                        'links' => null,
                        'title' => trans('jsonapi::errors.title.unauthorised'),
                        'detail' => trans('jsonapi::errors.description.unauthorised'),
                        'source' => [],
                        'meta' => null,
                    ]
                ]
            ],
            401
        );
    }

    /**
     * Test whether the exception formatting is correct
     *
     * @return void
     */
    public function testException()
    {
        $response = $this->setupErrorHandler(
            new Exception('Test')
        );

        $this->assertApiJsonResponse(
            $response,
            [
                'errors' => [
                    [
                        'id' => null,
                        'status' => 500,
                        'code' => '500',
                        'links' => null,
                        'title' => trans('jsonapi::errors.title.unhandled_exception'),
                        'detail' => null,
                        'source' => [],
                        'meta' => null,
                    ],
                ]
            ],
            500
        );
    }

    /**
     * Test whether the exception formatting is correct
     *
     * @return void
     */
    public function testModelNotFoundException()
    {
        $response = $this->setupErrorHandler(
            (new ModelNotFoundException)->setModel('\Model\test', 1)
        );

        $this->assertApiJsonResponse(
            $response,
            [
                'errors' => [
                    [
                        'id' => null,
                        'status' => 404,
                        'code' => '404',
                        'links' => null,
                        'title' => trans('jsonapi::errors.title.model_not_found'),
                        'detail' => null,
                        'source' => [],
                        'meta' => null,
                    ]
                ]
            ],
            404
        );
    }

    /**
     * Test whether the exception formatting is correct
     *
     * @return void
     */
    public function testNotFoundHttpException()
    {
        $response = $this->setupErrorHandler(new NotFoundHttpException);

        $this->assertApiJsonResponse(
            $response,
            [
                'errors' => [
                    [
                        'id' => null,
                        'status' => 404,
                        'code' => '404',
                        'links' => null,
                        'title' => trans('jsonapi::errors.title.page_not_found'),
                        'detail' => null,
                        'source' => [],
                        'meta' => null,
                    ]
                ]
            ],
            404
        );
    }

    /**
     * Test whether the validation exceptions formatting is correct
     *
     * @return void
     */
    public function testValidationExceptions()
    {
        $response = $this->setupErrorHandler(
            ValidationException::withMessages([
                'field_name_1' => ['1-1'],
                'field_name_2' => ['1-2'],
            ])
        );

        $this->assertApiJsonResponse(
            $response,
            [
                'errors' => [
                    [
                        'id' => null,
                        'status' => 422,
                        'code' => '422',
                        'links' => null,
                        'title' => 'Validation Error',
                        'detail' => '1-1',
                        'source' => [
                            'pointer' => '/field_name_1'
                        ],
                        'meta' => null,
                    ],
                    [
                        'id' => null,
                        'status' => 422,
                        'code' => '422',
                        'links' => null,
                        'title' => 'Validation Error',
                        'detail' => '1-2',
                        'source' => [
                            'pointer' => '/field_name_2'
                        ],
                        'meta' => null,
                    ]
                ]
            ],
            422
        );
    }
}