<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $table = 'seguimientos';
    public $timestamps = true;
    
    protected $fillable = [
        'incapacidad_id',
        'fecha',
        'comentario',
        'estado',
        'usuario_responsable'
    ];
}