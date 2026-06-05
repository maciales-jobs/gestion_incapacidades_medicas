<?php

namespace App\Controllers;

use App\Models\Seguimiento;
use Exception;

class SeguimientoController
{
    // Listar todos
    public function listar()
    {
        return Seguimiento::all();
    }

    // Obtener por ID
    public function obtener($id)
    {
        $seguimiento = Seguimiento::find($id);
        if (!$seguimiento) {
            throw new Exception("Seguimiento no encontrado", 404);
        }
        return $seguimiento;
    }

    // Crear
    public function crear($data)
    {
        $this->validar($data);

        return Seguimiento::create($data);
    }

    // Actualizar
    public function actualizar($id, $data)
    {
        $seguimiento = Seguimiento::find($id);
        if (!$seguimiento) {
            throw new Exception("Seguimiento no encontrado", 404);
        }

        if (isset($data['estado'])) {
            $this->validarEstado($data['estado']);
        }

        $seguimiento->update($data);
        return $seguimiento;
    }

    // Eliminar
    public function eliminar($id)
    {
        $seguimiento = Seguimiento::find($id);
        if (!$seguimiento) {
            throw new Exception("Seguimiento no encontrado", 404);
        }

        $seguimiento->delete();
        return true;
    }

    // Buscar por incapacidad
    public function buscarPorIncapacidad($incapacidadId)
    {
        return Seguimiento::where('incapacidad_id', $incapacidadId)->get();
    }

    // Buscar por filtros
    public function buscar($filtro)
    {
        $query = Seguimiento::query();

        if (isset($filtro['incapacidad_id'])) {
            $query->where('incapacidad_id', $filtro['incapacidad_id']);
        }

        if (isset($filtro['estado'])) {
            $query->where('estado', $filtro['estado']);
        }

        if (isset($filtro['usuario_responsable'])) {
            $query->where('usuario_responsable', 'like', '%' . $filtro['usuario_responsable'] . '%');
        }

        if (isset($filtro['fecha'])) {
            $query->where('fecha', $filtro['fecha']);
        }

        return $query->get();
    }

    // Validaciones
    private function validar($data)
    {
        $campos = ['incapacidad_id', 'fecha', 'comentario', 'estado', 'usuario_responsable'];

        foreach ($campos as $campo) {
            if (empty($data[$campo])) {
                throw new Exception("El campo $campo es obligatorio", 400);
            }
        }

        $this->validarEstado($data['estado']);
    }

    private function validarEstado($estado)
    {
        $estados = ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada'];
        if (!in_array($estado, $estados)) {
            throw new Exception("Estado invalido", 400);
        }
    }
}