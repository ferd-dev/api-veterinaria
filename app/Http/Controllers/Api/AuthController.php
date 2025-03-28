<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Obtener un JWT a través de las credenciales dadas.
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = Auth::attempt($credentials)) {
                return $this->authenticationErrorResponse(
                    'Credenciales inválidas',
                    'Email o contraseña incorrectos'
                );
            }

            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            return $this->serverErrorResponse(
                'Error al procesar la solicitud',
                app()->environment('production') ? null : $e->getMessage()
            );
        }
    }

    /**
     * Obtener un JWT a través de las credenciales dadas.
     * @unauthenticated
     */
    // public function login(LoginRequest $request): JsonResponse
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (! $token = Auth::attempt($credentials)) {
    //         return $this->errorResponse(null, 'No autorizado', 401);
    //     }

    //     return $this->respondWithToken($token);
    // }

    /**
     * Registrar un usuario.
     * @unauthenticated
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return $this->successResponse(new UserResource($user), 'Usuario registrado exitosamente', 201);
    }

    /**
     * Obtenga el usuario autenticado.
     */
    public function me(): JsonResponse
    {
        Gate::authorize('create', User::class);
        return $this->successResponse(new UserResource(Auth::user()), 'Datos de usuario recuperados correctamente');
    }

    /**
     * Cerrar la sesión del usuario (invalidar el token).
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->successResponse(null, 'Sesión cerrada correctamente');
    }

    /**
     * Refrescar un token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Obtener la estructura de tokens.
     * 
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => new UserResource(Auth::user()),
        ], 'Autenticación exitosa');
    }
}
