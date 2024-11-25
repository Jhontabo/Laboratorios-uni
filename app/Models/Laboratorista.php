<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorista extends Model
{
    use HasFactory;

    protected $table = 'laboratorista';
    protected $primaryKey = 'id_laboratorista';

    protected $fillable = [
        'id_usuario',
        'estado',
        'fecha_ingreso'
    ];

    // Relación con la tabla usuarios
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Relación con la tabla horarios
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_laboratorista');
    }
}
