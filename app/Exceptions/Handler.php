<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Twilio\Exceptions\TwilioException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($request->expectsJson()) {
            if ($exception instanceof PostTooLargeException) {
                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => 'Size of attached file should be less '.ini_get('upload_max_filesize').'B',
                    ],
                    400
                );
            }

            if ($exception instanceof AuthenticationException) {
                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => 'Unauthenticated or Token Expired, Please Login',
                    ],
                    401
                );
            }
            if ($exception instanceof ThrottleRequestsException) {
                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => 'Too Many Requests,Please Slow Down',
                    ],
                    429
                );
            }
            if ($exception instanceof ModelNotFoundException) {

                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => 'Resource not found.',
                    ],
                    404
                );
            }
            if ($exception instanceof ValidationException) {

                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => $exception->getMessage(),
                        'errors' => $exception->errors(),
                    ],
                    422
                );
            }

            if ($exception instanceof TwilioException) {

                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => $exception->getMessage(),
                        'errors' => [
                            'code' => $exception->getCode(),
                        ],
                    ],
                    422
                );
            }

            if ($exception instanceof MethodNotAllowedHttpException) {

                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => $exception->getMessage(),
                        'errors' => $exception->getTrace(),
                    ],
                    405
                );
            }
            if ($exception instanceof QueryException) {

                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => 'There was Issue with the Query',
                        'exception' => $exception,

                    ],
                    500
                );
            }
            if ($exception instanceof \Error) {
                // $exception = $exception->getResponse();
                return $this->apiResponse(
                    [
                        'status' => false,
                        'message' => 'There was some internal error',
                        'exception' => $exception,
                    ],
                    500
                );
            }
        }

        return parent::render($request, $exception);
    }
}
