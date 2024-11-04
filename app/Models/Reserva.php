<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_usuario',
        'id_laboratorio',
        'id_horario',
        'id_laboratorista',
        'Estado'
    ];

    // Definir las relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario');
    }
}
