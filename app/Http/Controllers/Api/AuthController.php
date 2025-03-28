<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Stmt\Return_;

class AuthController extends Controller
{
    use ApiResponseTrait;

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
     * Obtener un JWT a través de las credenciales dadas.
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::attempt($credentials)) {
            return $this->errorResponse(null, 'No autorizado', 401);
        }

        return $this->respondWithToken($token);
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
     * Obtenga la estructura de tokens.
     */
    protected function respondWithToken($token): JsonResponse
    {
        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => new UserResource(Auth::user()),
        ], 'Token generado exitosamente');
    }
}
