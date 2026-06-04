<?php

namespace App\Controllers;

use App\Models\Incapacidad;
use Exception;

class IncapacidadController
{
    // Listar todas
    public function listar()
    {
        return Incapacidad::all();
    }

    // Obtener por ID
    public function obtener($id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            throw new Exception("Incapacidad no encontrada", 404);
        }
        return $incapacidad;
    }

    // Crear
    public function crear($data)
    {
        $this->validar($data);

        // Calcular días automáticamente
        $inicio = new \DateTime($data['fecha_inicio']);
        $fin = new \DateTime($data['fecha_fin']);
        $dias = $inicio->diff($fin)->days + 1; // +1 para incluir ambos días
        $data['dias_incapacidad'] = $dias;

        // Estado por defecto
        if (empty($data['estado'])) {
            $data['estado'] = 'registrada';
        }

        return Incapacidad::create($data);
    }

    // Actualizar
    public function actualizar($id, $data)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            throw new Exception("Incapacidad no encontrada", 404);
        }

        // Validar fechas si se envían
        if (isset($data['fecha_inicio']) && isset($data['fecha_fin'])) {
            $this->validarFechas($data['fecha_inicio'], $data['fecha_fin']);
            $inicio = new \DateTime($data['fecha_inicio']);
            $fin = new \DateTime($data['fecha_fin']);
            $data['dias_incapacidad'] = $inicio->diff($fin)->days + 1;
        }

        // Validar estado si se envía
        if (isset($data['estado'])) {
            $this->validarEstado($data['estado']);
        }

        // Validar tipo si se envía
        if (isset($data['tipo'])) {
            $this->validarTipo($data['tipo']);
        }

        $incapacidad->update($data);
        return $incapacidad;
    }

    // Cambiar estado
    public function cambiarEstado($id, $estado)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            throw new Exception("Incapacidad no encontrada", 404);
        }

        $this->validarEstado($estado);

        $incapacidad->estado = $estado;
        $incapacidad->save();
        return $incapacidad;
    }

    // Finalizar incapacidad
    public function finalizar($id)
    {
        $incapacidad = Incapacidad::find($id);
        if (!$incapacidad) {
            throw new Exception("Incapacidad no encontrada", 404);
        }

        $incapacidad->estado = 'finalizada';
        $incapacidad->save();
        return $incapacidad;
    }

    // Buscar por filtros
    public function buscar($filtro)
{
    $query = Incapacidad::query();

    if (isset($filtro['empleado_id'])) {
        $query->where('empleado_id', $filtro['empleado_id']);
    }

    if (isset($filtro['fecha_inicio'])) {
        $query->where('fecha_inicio', '>=', $filtro['fecha_inicio']);
    }

    if (isset($filtro['fecha_fin'])) {
        $query->where('fecha_fin', '<=', $filtro['fecha_fin']);
    }

    if (isset($filtro['estado'])) {
        $query->whereRaw("estado = ?", [$filtro['estado']]);
    }

    if (isset($filtro['tipo'])) {
        $query->where('tipo', $filtro['tipo']);
    }

    return $query->get();
}

    // Validaciones
    private function validar($data)
    {
        $campos = ['empleado_id', 'fecha_inicio', 'fecha_fin', 'tipo', 'diagnostico_general', 'entidad_medica'];

        foreach ($campos as $campo) {
            if (empty($data[$campo])) {
                throw new Exception("El campo $campo es obligatorio", 400);
            }
        }

        $this->validarFechas($data['fecha_inicio'], $data['fecha_fin']);
        $this->validarTipo($data['tipo']);
    }

    private function validarFechas($inicio, $fin)
    {
        $fechaInicio = strtotime($inicio);
        $fechaFin = strtotime($fin);

        if (!$fechaInicio || !$fechaFin) {
            throw new Exception("Fechas invalidas", 400);
        }

        if ($fechaFin < $fechaInicio) {
            throw new Exception("La fecha fin no puede ser menor a la fecha inicio", 400);
        }
    }

    private function validarTipo($tipo)
    {
        $tipos = ['enfermedad_general', 'accidente_laboral', 'licencia_medica', 'incapacidad_temporal'];
        if (!in_array($tipo, $tipos)) {
            throw new Exception("Tipo de incapacidad invalido", 400);
        }
    }

    private function validarEstado($estado)
    {
        $estados = ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada'];
        if (!in_array($estado, $estados)) {
            throw new Exception("Estado invalido", 400);
        }
    }
}