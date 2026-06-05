<?php

use App\Controllers\SeguimientoController;
use Slim\App;

return function (App $app) {
    
    // Ruta de prueba
    $app->get('/', function ($request, $response) {
        $response->getBody()->write('{"mensaje": "ms-seguimiento funcionando"}');
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Listar todos
    $app->get('/seguimientos', function ($request, $response) {
        try {
            $controller = new SeguimientoController();
            $seguimientos = $controller->listar();
            
            $response->getBody()->write($seguimientos->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
    // Buscar por filtros
    $app->get('/seguimientos/buscar', function ($request, $response) {
        try {
            $params = $request->getQueryParams();
            $controller = new SeguimientoController();
            $seguimientos = $controller->buscar($params);
            
            $response->getBody()->write($seguimientos->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
    // Obtener por ID
    $app->get('/seguimientos/{id}', function ($request, $response, $args) {
        try {
            $controller = new SeguimientoController();
            $seguimiento = $controller->obtener($args['id']);
            
            $response->getBody()->write($seguimiento->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Crear
    $app->post('/seguimientos', function ($request, $response) {
        try {
            $data = $request->getParsedBody();
            $controller = new SeguimientoController();
            $seguimiento = $controller->crear($data);
            
            $response->getBody()->write($seguimiento->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Actualizar
    $app->put('/seguimientos/{id}', function ($request, $response, $args) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new SeguimientoController();
            $seguimiento = $controller->actualizar($args['id'], $data);
            
            $response->getBody()->write($seguimiento->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Eliminar
    $app->delete('/seguimientos/{id}', function ($request, $response, $args) {
        try {
            $controller = new SeguimientoController();
            $controller->eliminar($args['id']);
            
            $response->getBody()->write(json_encode(['mensaje' => 'Seguimiento eliminado']));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Buscar por incapacidad
    $app->get('/seguimientos/incapacidad/{incapacidad_id}', function ($request, $response, $args) {
        try {
            $controller = new SeguimientoController();
            $seguimientos = $controller->buscarPorIncapacidad($args['incapacidad_id']);
            
            $response->getBody()->write($seguimientos->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    
    
};