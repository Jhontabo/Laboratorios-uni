<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    // Especifica la tabla asociada
    protected $table = 'reservas';

    // Especifica la clave primaria personalizada
    protected $primaryKey = 'id_reserva';

    // Habilita o deshabilita el incremento automático si tu clave primaria lo requiere
    public $incrementing = true;

    // Especifica el tipo de la clave primaria
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'id_laboratorio',
        'id_horario',
        'id_laboratorista',
        'nombre_usuario',
        'correo_usuario',
        'apellido_usuario',
        'razon_rechazo',
        'estado',
    ];

    // Constantes para los posibles estados de la reserva
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_ACEPTADA = 'aceptada';
    const ESTADO_RECHAZADA = 'rechazada';

    // Relación con el modelo de usuarios (estudiantes o quien realiza la reserva)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relación con el modelo de laboratorios
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio', 'id_laboratorio');
    }

    // Relación con el modelo de horarios
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
    }

    // Relación con el modelo de laboratoristas (si aplica)
    public function laboratorista()
    {
        return $this->belongsTo(User::class, 'id_laboratorista', 'user_id');
    }

    // Método para obtener un estado legible
    public function getEstadoLegibleAttribute()
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_ACEPTADA => 'Aceptada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            default => 'Desconocido',
        };
    }
}
