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

    // Asegúrate de que los campos de fecha se interpreten como objetos Carbon
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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


    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_horario', 'id_horario');
    }


    // Accesor para el rango de tiempo (opcional)
    public function getTimeRangeAttribute(): string
    {
        return $this->start_at . ' - ' . $this->end_at;
    }
}
