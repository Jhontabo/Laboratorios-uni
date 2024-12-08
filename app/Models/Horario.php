<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;


    protected $table = 'horarios'; // Nombre de la tabla
    protected $primaryKey = 'id_horario'; // Clave primaria

    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'color',
        'description',
        'is_available',
        'reservation_status',
        'id_laboratorio',
        'id_usuario',
    ];

    public function laboratorista()
    {
        return $this->belongsTo(User::class, 'id_usuario'); // 'id_usuario' debe ser la clave que conecta con el usuario
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
