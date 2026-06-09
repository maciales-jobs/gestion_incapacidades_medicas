<?php

use App\Controllers\AuthController;
use Slim\App;

return function (App $app) {

    $app->options('/{routes:.+}', function ($request, $response) {
        return $response->withStatus(200);
    });
    
    // Ruta de prueba
    $app->get('/', function ($request, $response) {
        $response->getBody()->write('{"mensaje": "ms-auth funcionando"}');
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Login
    $app->post('/login', function ($request, $response) {
        try {
            $data = $request->getParsedBody();
            $controller = new AuthController();
            $resultado = $controller->login($data);
            
            $response->getBody()->write(json_encode($resultado));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() == 1 ? 400 : 401;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Logout
    $app->post('/logout', function ($request, $response) {
        try {
            $headers = $request->getHeaderLine('Authorization');
            $token = str_replace('Bearer ', '', $headers);
            
            $controller = new AuthController();
            $controller->logout($token);
            
            $response->getBody()->write(json_encode(['mensaje' => 'Sesion cerrada']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    });

    // Validar sesion
    $app->get('/validar', function ($request, $response) {
        $headers = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $headers);
        
        $controller = new AuthController();
        $valido = $controller->validarToken($token);
        
        $response->getBody()->write(json_encode(['valido' => $valido]));
        return $response->withHeader('Content-Type', 'application/json');
    });
    
};