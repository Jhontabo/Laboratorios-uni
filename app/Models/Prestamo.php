<?php

// app/Models/Prestamo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_producto',
        'user_id',
        'cantidad',
        'estado',
        'fecha_solicitud',
        'fecha_aprobacion',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'observaciones'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}