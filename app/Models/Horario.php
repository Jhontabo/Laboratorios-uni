<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';  // Nombre de la tabla
    protected $primaryKey = 'id_horario';  // Clave primaria

    protected $fillable = [
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];
}
