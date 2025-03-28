<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Respuesta exitosa estándar
     *
     * @param mixed $data Datos a devolver
     * @param string $message Mensaje descriptivo
     * @param int $status Código de estado HTTP
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Respuesta de error estándar
     *
     * @param mixed $errors Detalles del error
     * @param string $message Mensaje de error
     * @param string $errorCode Código de error semántico
     * @param int $status Código de estado HTTP
     * @return JsonResponse
     */
    protected function errorResponse($errors = null, string $message = 'Error', string $errorCode = 'GENERAL_ERROR', int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
                'details' => $errors,
            ]
        ], $status);
    }

    /**
     * Respuesta de error de validación
     *
     * @param mixed $errors Errores de validación
     * @param string $message Mensaje de error
     * @param int $status Código de estado HTTP
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, string $message = 'Validation error', int $status = 422): JsonResponse
    {
        return $this->errorResponse($errors, $message, 'VALIDATION_ERROR', $status);
    }

    /**
     * Respuesta de error de autenticación
     *
     * @param string $message Mensaje de error
     * @param mixed $details Detalles adicionales
     * @param int $status Código de estado HTTP
     * @return JsonResponse
     */
    protected function authenticationErrorResponse(string $message = 'Unauthorized', $details = null, int $status = 401): JsonResponse
    {
        return $this->errorResponse($details, $message, 'UNAUTHORIZED', $status);
    }

    /**
     * Respuesta de error de recurso no encontrado
     *
     * @param string $message Mensaje de error
     * @param mixed $details Detalles adicionales
     * @param int $status Código de estado HTTP
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found', $details = null, int $status = 404): JsonResponse
    {
        return $this->errorResponse($details, $message, 'NOT_FOUND', $status);
    }

    /**
     * Respuesta de error de servidor
     *
     * @param string $message Mensaje de error
     * @param mixed $details Detalles adicionales
     * @param int $status Código de estado HTTP
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Server error', $details = null, int $status = 500): JsonResponse
    {
        return $this->errorResponse($details, $message, 'SERVER_ERROR', $status);
    }
}
