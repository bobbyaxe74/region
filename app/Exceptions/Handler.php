<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponderTrait;
use Illuminate\Auth\Access\AuthorizationException as AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException as ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException as AuthenticationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException as MethodNotAllowed;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException as HttpException;
use App\Exceptions\CustomException as CustomException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponderTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Get an ovsettings value
        $api_exception_handler = env('OV_EXCEPTION_HANDLER', false);

        if ($api_exception_handler) {

            // Thrown when an error occurs when a user makes an unauthenticated request
            if ($exception instanceof AuthenticationException) {
                return $this->authenticationFailure();
            }

            // Thrown when a user makes requests that Auth service does not validated
            if ($exception instanceof AuthorizationException) {
                return $this->forbiddenAccess();
            }

            // Thrown when the request fails Laravel FormValidator validation.
            if ($exception instanceof ValidationException) {
                return $this->formProcessingFailure($exception->errors(),'Inappropriate input');
            }

            // Thrown when HTTP Method is incorrect when requesting routing
            if ($exception instanceof MethodNotAllowed) {
                return $this->wrongRequestType($exception->getMessage());
            }

            // Thrown when the HTTP requested route can not be found
            if ($exception instanceof NotFoundHttpException) {
                return $this->notFound();
            }

            // Thrown when processing HTTP requests is unsuccessful
            if ($exception instanceof HttpException) {
                return $this->unavailableService();
            }

            // Thrown when a custom exception occurs.
            if ($exception instanceof CustomException) {
                return $this->internalServerError($exception->getMessage());
            }

            // Thrown when an exception occurs.
            if ($exception instanceof Exception) {
                return $this->internalServerError();
            }
        }

        return parent::render($request, $exception);
    }
}
