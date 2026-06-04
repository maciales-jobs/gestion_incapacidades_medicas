<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';
    public $timestamps = true;
    
    protected $fillable = [
        'nombres', 'apellidos', 'documento', 'correo',
        'telefono', 'cargo', 'area', 'fecha_ingreso', 'estado'
    ];
}