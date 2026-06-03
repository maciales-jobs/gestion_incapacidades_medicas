<?php

namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    // Login
    public function login($data)
    {
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            throw new Exception("Usuario y contraseña son obligatorios", 1);
        }

        $usuario = Usuario::where(function($query) use ($data) {
         $query->where('usuario', $data['usuario'])
        ->orWhere('correo', $data['usuario']);
    })
        ->where('estado', 'activo')
        ->first();

        if (!$usuario || $usuario->contrasena !== $data['contrasena']) {
            throw new Exception("Credenciales incorrectas", 2);
        }

        // Generar token simple
        $token = bin2hex(random_bytes(32));
        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'usuario' => $usuario->usuario,
            'rol' => $usuario->rol,
            'token' => $token
        ];
    }

    // Logout
    public function logout($token)
    {
        $usuario = Usuario::where('token', $token)->first();

        if (!$usuario) {
            throw new Exception("Token no valido", 3);
        }

        $usuario->token = null;
        $usuario->sesion_activa = false;
        $usuario->save();

        return true;
    }

    // Validar token
    public function validarToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->where('estado', 'activo')
            ->first();

        return $usuario ? true : false;
    }
}