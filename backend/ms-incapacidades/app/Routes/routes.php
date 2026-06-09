<?php

use App\Controllers\IncapacidadController;
use Slim\App;

return function (App $app) {

    $app->options('/{routes:.+}', function ($request, $response) {
    return $response->withStatus(200);
    });
    
    // Ruta de prueba
    $app->get('/', function ($request, $response) {
        $response->getBody()->write('{"mensaje": "ms-incapacidades funcionando"}');
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Listar todas
    $app->get('/incapacidades', function ($request, $response) {
        try {
            $controller = new IncapacidadController();
            $incapacidades = $controller->listar();
            
            $response->getBody()->write($incapacidades->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    // ✅ BUSCAR - va ANTES de /{id}
    $app->get('/incapacidades/buscar', function ($request, $response) {
        try {
            $params = $request->getQueryParams();
            $controller = new IncapacidadController();
            $incapacidades = $controller->buscar($params);
            
            $response->getBody()->write($incapacidades->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    // ✅ OBTENER POR ID - va DESPUÉS de /buscar
    $app->get('/incapacidades/{id}', function ($request, $response, $args) {
        try {
            $controller = new IncapacidadController();
            $incapacidad = $controller->obtener($args['id']);
            
            $response->getBody()->write($incapacidad->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Crear
    $app->post('/incapacidades', function ($request, $response) {
        try {
            $data = $request->getParsedBody();
            $controller = new IncapacidadController();
            $incapacidad = $controller->crear($data);
            
            $response->getBody()->write($incapacidad->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Actualizar
    $app->put('/incapacidades/{id}', function ($request, $response, $args) {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);
            
            $controller = new IncapacidadController();
            $incapacidad = $controller->actualizar($args['id'], $data);
            
            $response->getBody()->write($incapacidad->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Cambiar estado
    $app->patch('/incapacidades/{id}/estado', function ($request, $response, $args) {
        try {
            $data = $request->getParsedBody();
            $controller = new IncapacidadController();
            $incapacidad = $controller->cambiarEstado($args['id'], $data['estado']);
            
            $response->getBody()->write($incapacidad->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Finalizar
    $app->patch('/incapacidades/{id}/finalizar', function ($request, $response, $args) {
        try {
            $controller = new IncapacidadController();
            $incapacidad = $controller->finalizar($args['id']);
            
            $response->getBody()->write($incapacidad->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });
    
};