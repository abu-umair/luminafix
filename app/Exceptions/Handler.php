<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\JSON;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            if ($request->is('api/*'))
                return JSON::error(
                    'Only logged in users have access to this resource!',
                    null,
                    Response::HTTP_FORBIDDEN
                );
        }

        if ($e instanceof AuthorizationException) {
            if ($request->is('api/*'))
                return JSON::error(
                    'You don\'t have rights to access this resource!',
                    null,
                    Response::HTTP_UNAUTHORIZED
                );
        }

        if ($e instanceof NotFoundHttpException) {
            if ($request->is('api/*'))
                return JSON::error(
                    'The requested resource was not found!',
                    null,
                    Response::HTTP_NOT_FOUND
                );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            if ($request->is('api/*'))
                return JSON::error(
                    sprintf(
                        'The requested route does not support %s method!',
                        Str::upper($request->method())
                    ),
                    null,
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
        }

        return parent::render($request, $e);
    }
}
