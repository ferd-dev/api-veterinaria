<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

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

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Maneja excepciones para respuestas de API
     */
    private function handleApiException(Throwable $exception, $request)
    {
        if ($exception instanceof ValidationException) {
            return $this->validationErrorResponse(
                $exception->errors(),
                'Los datos proporcionados no son válidos'
            );
        }

        if ($exception instanceof AuthenticationException) {
            return $this->authenticationErrorResponse(
                'No autenticado',
                'Se requiere autenticación para acceder a este recurso'
            );
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return $this->errorResponse(
                null,
                'Acceso denegado',
                'FORBIDDEN',
                403
            );
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return $this->notFoundResponse(
                'Recurso no encontrado',
                'El recurso solicitado no existe'
            );
        }

        return $this->serverErrorResponse(
            'Error interno del servidor',
            app()->environment('production') ? null : $exception->getMessage()
        );
    }
}
