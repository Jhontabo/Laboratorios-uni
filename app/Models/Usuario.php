<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    // Definir la tabla asociada
    protected $table = 'usuarios';

    // Definir la clave primaria
    protected $primaryKey = 'id_usuario';

    // Definir los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre',
        'apellido',
        'correo_electronico',
        'telefono',
        'Direccion',
        'password', // Asegúrate de incluir la contraseña
    ];

    // Ocultar el remember_token para que no se exponga al serializar
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Asegúrate de que las marcas de tiempo estén activas si las usas
    public $timestamps = true;
}
