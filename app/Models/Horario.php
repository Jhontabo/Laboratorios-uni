<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'horario';

    // Clave primaria
    protected $primaryKey = 'id_horario';

    // Atributos asignables en masa
    protected $fillable = [
        'id_user',        // ID del usuario relacionado
        'id_laboratorio', // ID del laboratorio relacionado
        'title',          // Título del evento
        'color',          // Color del evento
        'start_at',       // Fecha y hora de inicio
        'end_at',         // Fecha y hora de fin 
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con laboratorio
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    // Accesor para el rango de tiempo (opcional)
    public function getTimeRangeAttribute(): string
    {
        return $this->start_at . ' - ' . $this->end_at;
    }
}
