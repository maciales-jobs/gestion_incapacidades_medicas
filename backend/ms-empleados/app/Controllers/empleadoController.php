<?php

namespace App\Controllers;

use App\Models\Empleado;
use Exception;

class EmpleadoController
{
    // Listar todos
    public function listar()
    {
        return Empleado::all();
    }

    // Buscar por ID
    public function obtener($id)
    {
        $empleado = Empleado::find($id);
        if (!$empleado) {
            throw new Exception("Empleado no encontrado", 404);
        }
        return $empleado;
    }

    // Crear
    public function crear($data)
    {
        $this->validar($data);

        if (Empleado::where('documento', $data['documento'])->exists()) {
            throw new Exception("El documento ya existe", 409);
        }

        if (Empleado::where('correo', $data['correo'])->exists()) {
            throw new Exception("El correo ya existe", 409);
        }

        return Empleado::create($data);
    }

    // Actualizar
    public function actualizar($id, $data)
    {
        $empleado = Empleado::find($id);
        if (!$empleado) {
            throw new Exception("Empleado no encontrado", 404);
        }

        if (isset($data['documento']) && $data['documento'] !== $empleado->documento) {
            if (Empleado::where('documento', $data['documento'])->exists()) {
                throw new Exception("El documento ya existe", 409);
            }
        }

        if (isset($data['correo']) && $data['correo'] !== $empleado->correo) {
            if (Empleado::where('correo', $data['correo'])->exists()) {
                throw new Exception("El correo ya existe", 409);
            }
        }

        if (isset($data['estado']) && !in_array($data['estado'], ['activo', 'inactivo'])) {
            throw new Exception("Estado invalido", 400);
        }

        $empleado->update($data);
        return $empleado;
    }

    // Cambiar estado (activo/inactivo)
    public function cambiarEstado($id, $estado)
    {
        $empleado = Empleado::find($id);
        if (!$empleado) {
            throw new Exception("Empleado no encontrado", 404);
        }

        if (!in_array($estado, ['activo', 'inactivo'])) {
            throw new Exception("Estado invalido", 400);
        }

        $empleado->estado = $estado;
        $empleado->save();
        return $empleado;
    }

    // Buscar por documento, area, cargo o estado
    public function buscar($filtro)
    {
        $query = Empleado::query();

        if (isset($filtro['documento'])) {
            $query->where('documento', $filtro['documento']); 
        }

        if (isset($filtro['area'])) {
            $query->where('area', 'like', '%' . $filtro['area'] . '%');
        }

        if (isset($filtro['estado'])) {
            $query->where('estado', $filtro['estado']);
        }

        return $query->get();
    }

    // Validaciones
    private function validar($data)
    {
        $campos = ['nombres', 'apellidos', 'documento', 'correo', 'telefono', 'cargo', 'area', 'fecha_ingreso'];

        foreach ($campos as $campo) {
            if (empty($data[$campo])) {
                throw new Exception("El campo $campo es obligatorio", 400);
            }
        }

        if (!empty($data['fecha_ingreso']) && !strtotime($data['fecha_ingreso'])) {
            throw new Exception("Fecha de ingreso invalida", 400);
        }
    }
}