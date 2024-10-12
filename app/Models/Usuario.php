<?php

// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    // Nombre de la tabla
    protected $table = 'usuarios'; // Ya tienes la tabla correcta

    // Clave primaria
    protected $primaryKey = 'id_usuario'; // Esto también ya lo has configurado

    // Atributos asignables en masa
    protected $fillable = [
        'nombre',
        'apellido',
        'correo_electronico',
        'telefono',
        'Direccion',
    ];

    // Atributos ocultos
    protected $hidden = [
        'remember_token',
    ];

    public $timestamps = true;
}
