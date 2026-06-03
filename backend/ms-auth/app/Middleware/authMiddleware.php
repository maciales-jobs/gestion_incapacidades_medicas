<?php

namespace App\Middleware;

use App\Controllers\AuthController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $headers = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $headers);

        $authController = new AuthController();
        
        if (!$authController->validarToken($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'No autorizado. Token invalido o sesion inactiva.'
            ]));
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}