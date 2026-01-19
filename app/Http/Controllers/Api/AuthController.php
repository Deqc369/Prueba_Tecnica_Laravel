<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = Usuario::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'fecha_registro' => now(),
                'estado' => 'activo',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'Usuario registrado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->sendError('Error al registrar usuario', [], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = Usuario::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->sendError('Credenciales inválidas', [], 401);
            }

            if ($user->estado !== 'activo') {
                return $this->sendError('Usuario inactivo', [], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'Login exitoso');
        } catch (\Exception $e) {
            return $this->sendError('Error en el login', [], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return $this->sendResponse(null, 'Logout exitoso');
        } catch (\Exception $e) {
            return $this->sendError('Error en el logout', [], 500);
        }
    }
}