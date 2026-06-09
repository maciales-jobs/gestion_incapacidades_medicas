<?php

use App\Controllers\EmpleadoController;
use Slim\App;

return function (App $app) {

    $app->options('/{routes:.+}', function ($request, $response) {
    return $response->withStatus(200);
    });
    // Ruta de prueba
    $app->get('/', function ($request, $response) {
        $response->getBody()->write('{"mensaje": "ms-empleados funcionando"}');
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Listar todos
    $app->get('/empleados', function ($request, $response) {
        try {
            $controller = new EmpleadoController();
            $empleados = $controller->listar();
            
            $response->getBody()->write($empleados->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });


    $app->get('/empleados/buscar', function ($request, $response) {
        try {
            $params = $request->getQueryParams();
            $controller = new EmpleadoController();
            $empleados = $controller->buscar($params);
            
            $response->getBody()->write($empleados->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    // Obtener por ID
    $app->get('/empleados/{id}', function ($request, $response, $args) {
        try {
            $controller = new EmpleadoController();
            $empleado = $controller->obtener($args['id']);
            
            $response->getBody()->write($empleado->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Crear
    $app->post('/empleados', function ($request, $response) {
        try {
            $data = $request->getParsedBody();
            $controller = new EmpleadoController();
            $empleado = $controller->crear($data);
            
            $response->getBody()->write($empleado->toJson());
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Actualizar
    $app->put('/empleados/{id}', function ($request, $response, $args) {
        try {
            $data = $request->getParsedBody();
            $controller = new EmpleadoController();
            $empleado = $controller->actualizar($args['id'], $data);
            
            $response->getBody()->write($empleado->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });

    // Cambiar estado
    $app->patch('/empleados/{id}/estado', function ($request, $response, $args) {
        try {
            $data = $request->getParsedBody();
            $controller = new EmpleadoController();
            $empleado = $controller->cambiarEstado($args['id'], $data['estado']);
            
            $response->getBody()->write($empleado->toJson());
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus($code)->withHeader('Content-Type', 'application/json');
        }
    });
    
};