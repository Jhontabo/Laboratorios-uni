<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';
    protected $primaryKey = 'id_horario';

    protected $fillable = [
        'id_laboratorista',
        'id_laboratorio',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    // Relación con laboratorista
    public function laboratorista()
    {
        return $this->belongsTo(Laboratorista::class, 'id_laboratorista');
    }

    // Relación con laboratorio
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }
}
