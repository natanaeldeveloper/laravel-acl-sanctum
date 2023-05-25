<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
     * @var array<string<string, int>>
     */
    protected $errorMappings = [
        ValidationException::class => ['validation_error', Response::HTTP_UNPROCESSABLE_ENTITY],
        NotFoundHttpException::class => ['not_found_error', Response::HTTP_NOT_FOUND],
        AuthorizationException::class => ['authorization_error', Response::HTTP_FORBIDDEN],
        AccessDeniedHttpException::class => ['authorization_error', Response::HTTP_UNAUTHORIZED],
        AuthenticationException::class => ['authentication_error', Response::HTTP_UNAUTHORIZED],
        MethodNotAllowedHttpException::class => ['method_not_allowed_error', Response::HTTP_METHOD_NOT_ALLOWED]
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {

            $errorClass = get_class($e);
            $errorKey   = $this->errorMappings[$errorClass][0] ?? 'internal_server_error';
            $statusCode = $this->errorMappings[$errorClass][1] ?? Response::HTTP_INTERNAL_SERVER_ERROR;
            $message    = __('exceptions.' . $errorKey) ?? 'Internal Server Error';
            $time       = Carbon::now();

            //erros esperados com formatação diferenciada
            if ($e instanceof ValidationException) {

                $response = response()->json([
                    'status'        => 'error',
                    'code'          => $statusCode,
                    'message'       => $message,
                    'time'          => $time,
                    'errors'        => $e->errors(),
                ], $statusCode);
            }
            else { //erros esperados com formatação comun


                $response = response()->json([
                    'status'        => 'error',
                    'code'          => $statusCode,
                    'message'       => $message,
                    'time'          => $time,
                ], $statusCode);
            }

            // adiciona parâmetros ao response quando é ambiente de desenvolvimento
            if (env('APP_DEBUG')) {

                $data = $response->getData();

                $data->message  = $e->getMessage();
                $data->trace    = $e->getTrace();

                $response->setData($data);
            }

            return $response;
        });
    }
}
